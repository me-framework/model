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
     */
    abstract public function validateAttribute($model, $attribute);
    /**
     * @param string $attribute Attribute Name
     */
    public function addAttribute($attribute) {
        $this->_attributes[] = $attribute;
    }
    /**
     * @param \me\model\Model $model Model
     */
    public function validateAttributes($model) {
        foreach ($this->_attributes as $attribute) {
            $this->validateAttribute($model, $attribute);
        }
    }
}