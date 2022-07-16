<?php
namespace me\model\validators;
use me\model\Validator;
class NationalValidator extends Validator {
    /**
     * @param string $options Options
     */
    public function setOptions($options) {
        
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
        if (!is_string($value) || !preg_match('/^[0-9]{10}$/', $value)) {
            return $model->addError($attribute, 'national');
        }
        for ($i = 0; $i < 10; $i++) {
            if (preg_match('/^' . $i . '{10}$/', $value)) {
                return $model->addError($attribute, 'national');
            }
        }
        for ($i = 0, $sum = 0; $i < 9; $i++) {
            $sum += ((10 - $i) * intval(substr($value, $i, 1)));
        }
        $ret    = $sum % 11;
        $parity = intval(substr($value, 9, 1));
        if (!(($ret < 2 && $ret == $parity) || ($ret >= 2 && $ret == 11 - $parity))) {
            return $model->addError($attribute, 'national');
        }
    }
}