<?php
namespace me\model\validators;
use me\model\Validator;
class IntegerValidator extends Validator {
    public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;
        if ($value !== null && (!is_scalar($value) || !is_numeric($value))) {
            $model->addError($attribute, 'integer');
        }
        else {
            $model->$attribute = $value === null ? null : (int) $value;
        }
    }
}