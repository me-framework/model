<?php
namespace me\model;
use me\core\Component;
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
     * @param string $modelKey
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
     * @param array|null $attributes attributes
     * @param array $except except
     */
    public function validateAttributes($model, $modelKey, $attributes = null, $except = []) {
        foreach ($this->_attributes as $attribute) {
            if ($attributes === null && !in_array($attribute, $except, true)) {
                $this->validateAttribute($model, $attribute, $modelKey);
            }
            else if ($attributes !== null && in_array($attribute, $attributes, true) && !in_array($attribute, $except, true)) {
                $this->validateAttribute($model, $attribute, $modelKey);
            }
        }
    }
}