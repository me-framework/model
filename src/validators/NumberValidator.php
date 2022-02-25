<?php
namespace me\model\validators;
use me\model\Validator;
class NumberValidator extends Validator {
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
        if (isset($config[0]) && !empty($config[0])) {
            $this->min = $config[0];
        }
        if (isset($config[1]) && !empty($config[1])) {
            $this->max = $config[1];
        }
    }
    /**
     * @param \me\model\Model $model Model
     * @param string $attribute Attribute Name
     * @param string $modelKey
     */
    public function validateAttribute($model, $attribute, $modelKey) {
        $value = $model->$attribute;
        if ($value !== null && (!is_scalar($value) || !is_numeric($value))) {
            $model->addError($attribute, 'number');
            return;
        }
        if ($this->min !== null && $model->$attribute !== null && $model->$attribute < $this->min) {
            $model->addError($attribute, 'too small');
            return;
        }
        if ($this->max !== null && $model->$attribute !== null && $model->$attribute > $this->max) {
            $model->addError($attribute, 'too big');
            return;
        }
    }
}