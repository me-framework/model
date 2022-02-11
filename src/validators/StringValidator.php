<?php
namespace me\model\validators;
use me\model\Validator;
class StringValidator extends Validator {
    /**
     * @var int min
     */
    private $min;
    /**
     * @var int max
     */
    private $max;
    /**
     * @param string $options Options
     */
    public function setOptions($options) {
        $config = explode(',', $options);
        if (isset($config[0])) {
            $this->min = $config[0];
        }
        if (isset($config[1])) {
            $this->max = $config[1];
        }
    }
    /**
     * @param \me\model\Model $model Model
     */
    public function validateAttribute($model) {
        $value = $model->{$this->attribute};
        if ($value !== null && (!is_scalar($value) || !is_string($value))) {
            $model->addError($this->attribute, 'string');
        }
        else {
            $model->{$this->attribute} = $value === null ? null : (string) $value;
        }
        if ($this->min !== null && $model->{$this->attribute} !== null && $model->{$this->attribute} < $this->min) {
            $model->addError($this->attribute, 'too short');
            return;
        }
        if ($this->max !== null && $model->{$this->attribute} !== null && $model->{$this->attribute} > $this->max) {
            $model->addError($this->attribute, 'too long');
            return;
        }
    }
}