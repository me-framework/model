<?php
namespace me\model\validators;
use me\model\Validator;
class IntegerValidator extends Validator {
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
            $this->min = intval($config[0]);
        }
        if (isset($config[1])) {
            $this->max = intval($config[1]);
        }
    }
    /**
     * @param \me\model\Model $model Model
     */
    public function validateAttribute($model) {
        $value = $model->{$this->attribute};
        if ($value !== null && (!is_scalar($value) || !is_numeric($value))) {
            $model->addError($this->attribute, 'integer');
            return;
        }
        $model->{$this->attribute} = $value === null ? null : intval($value);
        if ($this->min !== null && $model->{$this->attribute} !== null && $model->{$this->attribute} < $this->min) {
            $model->addError($this->attribute, 'too small');
            return;
        }
        if ($this->max !== null && $model->{$this->attribute} !== null && $model->{$this->attribute} > $this->max) {
            $model->addError($this->attribute, 'too big');
            return;
        }
    }
}