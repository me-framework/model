<?php
namespace me\model\validators;
use me\model\Validator;
class StringValidator extends Validator {
    /**
     * @param \me\model\Model $model Model
     * @param string $attribute Attribute Name
     */
    public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;
        if ($value !== null && (!is_scalar($value) || !is_string($value))) {
            $model->addError($attribute, 'string');
        }
        else {
            $model->$attribute = $value === null ? null : (string) $value;
        }
    }
}