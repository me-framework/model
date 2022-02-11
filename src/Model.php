<?php
namespace me\model;
use ReflectionClass;
use ReflectionProperty;
use me\core\Component;
use me\core\components\Container;
class Model extends Component {
    use ArrayableTrait;
    /**
     * @return array Attributes Names
     */
    public function attributes() {
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
     * @param array $values Values
     * @return bool Loaded
     */
    public function load($values = []) {
        if (empty($values)) {
            return false;
        }
        $loaded     = false;
        $attributes = array_flip($this->attributes());
        foreach ($values as $name => $value) {
            if (isset($attributes[$name])) {
                $this->$name = $value;
                $loaded      = true;
            }
        }
        return $loaded;
    }
    //
    //
    //
    private $_validatorsMap    = [
        'required' => 'me\model\validators\RequiredValidator',
        'string'   => 'me\model\validators\StringValidator',
        'number'   => 'me\model\validators\NumberValidator',
        'integer'  => 'me\model\validators\IntegerValidator',
        'boolean'  => 'me\model\validators\BooleanValidator',
    ];
    private $_activeValidators = [];
    /**
     * @return array Attributes Rules For Validation
     */
    public function rules() {
        return [];
    }
    /**
     * 
     */
    private function createValidators() {
        $validators = [];
        $rules      = $this->rules();
        foreach ($rules as $attribute => $rule) {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }
            if (!is_array($rule) || empty($rule)) {
                continue;
            }
            foreach ($rule as $name) {
                $name = strtolower($name);
                if (!isset($validators[$name])) {
                    $validators[$name] = Container::build(['class' => $this->_validatorsMap[$name]]);
                }
                $validators[$name]->addAttribute($attribute);
            }
        }
        return $validators;
    }
    /**
     * @return \me\model\Validator[] Validators
     */
    private function getValidators() {
        if (empty($this->_activeValidators)) {
            $this->_activeValidators = $this->createValidators();
        }
        return $this->_activeValidators;
    }
    /**
     * @param bool $clearErrors Clear Errors
     * @return bool
     */
    public function validate($clearErrors = true) {
        if ($clearErrors) {
            $this->clearErrors();
        }
        $validators = $this->getValidators();
        foreach ($validators as $validator) {
            $validator->validateAttributes($this);
        }
        return !$this->hasErrors();
    }
    //
    private $_errors = [];
    public function clearErrors() {
        $this->_errors = [];
    }
    public function hasErrors() {
        return !empty($this->_errors);
    }
    public function getErrors($attribute = null) {
        return $attribute === null ? $this->_errors : $this->_errors[$attribute];
    }
    public function addError($attribute, $error) {
        if (isset($this->_errors[$attribute])) {
            array_push($this->_errors[$attribute], $error);
        }
        else {
            $this->_errors[$attribute] = [$error];
        }
    }
    public function fields() {
        return $this->attributes();
    }
}