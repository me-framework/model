<?php
namespace me\model;
use me\core\Component;
/**
 * 
 */
abstract class Validator extends Component {
    /**
     * @var string Attribute Name
     */
    private $_attributes;
    /**
     * @param string $options
     */
    abstract public function setOptions($options);
    /**
     * @param \me\model\Model $model Model
     * @param string $attribute Attribute Name
     * @param string $modelKey Model Key
     */
    abstract public function validateAttribute($model, $attribute, $modelKey);
    /**
     * @param string $attribute Attribute Name
     */
    public function addAttribute($attribute) {
        $this->_attributes[] = $attribute;
    }
    /**
     * @param \me\model\Model $model Model
     * @param string $modelKey Model Key
     * @param array|null $attributes attributes
     * @param array $except except
     */
    public function validateAttributes($model, $modelKey, $attributes, $except) {
        foreach ($this->_attributes as $attribute) {
            if (is_null($attributes) && !in_array($attribute, $except, true)) {
                $this->validateAttribute($model, $attribute, $modelKey);
            }
            else if (is_array($attributes) && in_array($attribute, $attributes, true) && !in_array($attribute, $except, true)) {
                $this->validateAttribute($model, $attribute, $modelKey);
            }
        }
    }
}