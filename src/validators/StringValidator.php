<?php
namespace me\model\validators;
use me\model\Validator;
/**
 * 
 */
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
     * @param string $modelKey Model Key
     */
    public function validateAttribute($model, $attribute, $modelKey) {
        $value = $model->$attribute;
        if (is_null($value)) {
            return;
        }
        if (!is_scalar($value) || !is_string($value)) {
            return $model->addError($attribute, 'string');
        }
        $model->$attribute = (string) $value;
        if ($this->min !== null && mb_strlen($model->$attribute) < $this->min) {
            $model->addError($attribute, 'too short');
            return;
        }
        if ($this->max !== null && mb_strlen($model->$attribute) > $this->max) {
            $model->addError($attribute, 'too long');
            return;
        }
    }
}