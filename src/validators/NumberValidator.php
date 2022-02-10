<?php
namespace me\model\validators;
use me\model\Validator;
class NumberValidator extends Validator {
    public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;
        if ($value !== null && (!is_scalar($value) || !is_numeric($value))) {
            $model->addError($attribute, 'number');
        }
    }
}