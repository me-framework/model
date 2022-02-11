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
     */
    public function validateAttribute($model) {
        $value = $model->{$this->attribute};
        if ($value === null || $value === '' || $value === []) {
            $model->addError($this->attribute, 'required');
        }
    }
}