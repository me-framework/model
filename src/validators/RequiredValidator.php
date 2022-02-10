<?php
namespace me\model\validators;
use me\model\Validator;
class RequiredValidator extends Validator {
    /**
     * @param \me\model\Model $model Model
     * @param string $attribute Attribute Name
     */
    public function validateAttribute($model, $attribute) {
        $value = $model->$attribute;
        if ($value === null || $value === '' || $value === []) {
            $model->addError($attribute, 'required');
        }
    }
}