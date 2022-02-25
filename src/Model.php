<?php
namespace me\model;
use ReflectionClass;
use ReflectionProperty;
use me\core\Cache;
use me\core\Component;
use me\core\components\Container;
use me\core\components\Security;
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
    //
    //
    //
    public function init() {
        $this->_key = Security::generateRandomString();
    }
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
        foreach ($validators as $validator) {
            $validator->validateAttributes($this, $this->_key, $attributes, $except);
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
    //
    //
    //
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
     * 
     */
    protected function getValidatorsMap() {
        return [
            'bool'     => 'me\model\validators\BooleanValidator',
            'boolean'  => 'me\model\validators\BooleanValidator',
            'int'      => 'me\model\validators\IntegerValidator',
            'integer'  => 'me\model\validators\IntegerValidator',
            'num'      => 'me\model\validators\NumberValidator',
            'number'   => 'me\model\validators\NumberValidator',
            'req'      => 'me\model\validators\RequiredValidator',
            'required' => 'me\model\validators\RequiredValidator',
            'str'      => 'me\model\validators\StringValidator',
            'string'   => 'me\model\validators\StringValidator',
        ];
    }
    /**
     * 
     */
    protected function createValidators() {
        $validators    = [];
        $rules         = $this->rules();
        $validatorsMap = $this->getValidatorsMap();
        foreach ($rules as $attribute => $rule) {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }
            if (!is_array($rule) || empty($rule)) {
                continue;
            }
            foreach ($rule as $config) {
                if (!isset($validators[$config])) {
                    $arConfig            = explode(':', $config);
                    $name                = strtolower($arConfig[0]);
                    $options             = $arConfig[1] ?? '';
                    $validators[$config] = Container::build(['class' => $validatorsMap[$name], 'options' => $options]);
                }
                $validators[$config]->addAttribute($attribute);
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
}