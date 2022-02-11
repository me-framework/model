<?php
namespace me\model;
use me\core\Component;
abstract class Validator extends Component {
    /**
     * @var string Attribute Name
     */
    public $attribute;
    /**
     * @param string $options
     */
    abstract public function setOptions($options);
    /**
     * @param \me\model\Model $model Model
     */
    abstract public function validateAttribute($model);
}