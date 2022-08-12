<?php
namespace me\model;
use ReflectionClass;
use ReflectionProperty;
use me\core\Cache;
use me\core\Component;
use me\core\Container;
use me\core\Security;
use me\model\validators;
use me\exceptions\Exception;
/**
 * 
 */
class Model extends Component {
    /**
     * 
     */
    protected $_errors = [];
    /**
     * 
     */
    protected $_key;
    //
    /**
     * @param array $values Values
     * @return bool Loaded
     */
    public function load($values = []) {
        if (empty($values)) {
            return false;
        }
        $loaded     = false;
        $attributes = $this->rules();
        foreach ($values as $name => $value) {
            if (isset($attributes[$name])) {
                $this->$name = $value;
                $loaded      = true;
            }
        }
        return $loaded;
    }
    /**
     * @param bool $clearErrors Clear Errors
     * @param array|null $attributes attributes
     * @param array $except except
     * @return bool
     */
    public function validate($clearErrors = true, $attributes = null, $except = []) {
        if ($clearErrors) {
            $this->clearErrors();
        }
        $validators = $this->getValidators();
        foreach ($validators as $rule => $validator) {
            $this->validateAttributes($rule, $validator, $attributes, $except);
        }
        return !$this->hasErrors();
    }
    /**
     * 
     */
    public function clearErrors() {
        $this->_errors = [];
    }
    /**
     * 
     */
    public function hasErrors() {
        return !empty($this->_errors);
    }
    /**
     * 
     */
    public function getErrors($attribute = null) {
        return $attribute === null ? $this->_errors : $this->_errors[$attribute];
    }
    /**
     * 
     */
    public function addError($attribute, $error) {
        if (isset($this->_errors[$attribute])) {
            array_push($this->_errors[$attribute], $error);
        }
        else {
            $this->_errors[$attribute] = [$error];
        }
    }
    /**
     * 
     */
    public function addErrors($attribute, $errors) {
        if ($errors === null) {
            unset($this->_errors[$attribute]);
        }
        else {
            $this->_errors[$attribute] = $errors;
        }
    }
    /**
     * 
     */
    public function toArray() {
        $attributes = $this->attributes();
        $array      = [];
        foreach ($attributes as $attribute) {
            $value = $this->$attribute;
            if ($value instanceof self) {
                $value = $value->toArray();
            }
            elseif (is_array($value)) {
                foreach ($value as &$val) {
                    if ($val instanceof self) {
                        $val = $val->toArray();
                    }
                }
            }
            $array[$attribute] = $value;
        }
        return $array;
    }
    //
    /**
     * 
     */
    protected function init() {
        parent::init();
        $this->_key = Security::generateRandomString();
    }
    /**
     * @return array Attributes Rules For Validation
     */
    protected function rules() {
        return [];
    }
    /**
     * @return array Attributes Names
     */
    protected function attributes() {
        $class = new ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }
        return $names;
    }
    /**
     * @return bool
     */
    protected function hasMethod($name) {
        $class = new ReflectionClass($this);
        return $class->hasMethod($name) && $class->getMethod($name)->isPublic();
    }
    /**
     * 
     */
    protected function getValidatorsMap() {
        return [
            'array'     => validators\ArrayValidator::class,
            'bigint'    => validators\BigintValidator::class,
            'boolean'   => validators\BooleanValidator::class,
            'default'   => validators\DefaultValidator::class,
            'each'      => validators\EachValidator::class,
            'email'     => validators\EmailValidator::class,
            'float'     => validators\FloatValidator::class,
            'gdate'     => validators\GdateValidator::class,
            'gdatetime' => validators\GdatetimeValidator::class,
            'in'        => validators\InValidator::class,
            'integer'   => validators\IntegerValidator::class,
            'jdate'     => validators\JdateValidator::class,
            'jdatetime' => validators\JdatetimeValidator::class,
            'national'  => validators\NationalValidator::class,
            'nullable'  => validators\NullableValidator::class,
            'number'    => validators\NumberValidator::class,
            'required'  => validators\RequiredValidator::class,
            'string'    => validators\StringValidator::class,
            'time'      => validators\TimeValidator::class,
            'trim'      => validators\TrimValidator::class,
        ];
    }
    /**
     * @return \me\model\Validator Validator
     */
    protected function createRule(&$validators, $attribute, $rule) {
        $config  = explode(':', $rule, 2);
        $name    = $config[0];
        $options = $config[1] ?? '';
        if ($this->hasMethod($name)) {
            if (!isset($validators[$name])) {
                $validators[$name] = [];
            }
            $validators[$name][] = [$attribute, $options];
            return;
        }
        if (!isset($validators[$name])) {
            $validatorsMap = $this->getValidatorsMap();
            if (!isset($validatorsMap[$name])) {
                throw new Exception("Validator '$name' Not Found");
            }
            $validators[$name] = Container::build($validatorsMap[$name], ['options' => $options]);
        }
        $validators[$name]->addAttribute($attribute);
    }
    /**
     * 
     */
    protected function createValidators() {
        $validators = [];
        foreach ($this->rules() as $attribute => $rules) {
            if (is_string($rules)) {
                $rules = explode('|', $rules);
            }
            if (!is_array($rules) || empty($rules)) {
                continue;
            }
            foreach ($rules as $rule) {
                $this->createRule($validators, $attribute, $rule);
            }
        }
        return $validators;
    }
    /**
     * @return \me\model\Validator[] Validators
     */
    protected function getValidators() {
        $activeValidators = Cache::getCache([$this->_key, 'activeValidators']);
        if ($activeValidators === null) {
            $activeValidators = $this->createValidators();
            Cache::setCache([$this->_key, 'activeValidators'], $activeValidators);
        }
        return $activeValidators;
    }
    //
    /**
     * 
     */
    private function validateAttributes($rule, $validator, $attributes, $except) {
        if ($validator instanceof Validator) {
            return $validator->validateAttributes($this, $this->_key, $attributes, $except);
        }
        if (!is_array($validator)) {
            return;
        }
        foreach ($validator as [$attribute, $options]) {
            if (is_null($attributes) && !in_array($attribute, $except, true)) {
                call_user_func([$this, $rule], $attribute, $this->$attribute, $options);
            }
            else if (is_array($attributes) && in_array($attribute, $attributes, true) && !in_array($attribute, $except, true)) {
                call_user_func([$this, $rule], $attribute, $this->$attribute, $options);
            }
        }
    }
}