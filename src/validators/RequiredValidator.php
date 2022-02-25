<?php
namespace me\model\validators;
use me\model\Validator;
class RequiredValidator extends Validator {
    /**
     * @param string $options Options
     */
    public function setOptions($options) {
        
    }
    /**
     * @param \me\model\Model $model Model
     * @param string $attribute Attribute Name
     * @param string $modelKey
     */
    public function validateAttribute($model, $attribute, $modelKey) {
        $value = $model->$attribute;
        if ($value === null || $value === '' || $value === []) {
            $model->$attribute = null;
            $model->addError($attribute, 'required');
        }
    }
}