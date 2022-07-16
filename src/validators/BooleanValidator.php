<?php
namespace me\model\validators;
use me\model\Validator;
class BooleanValidator extends Validator {
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
        $value = $this->cast($model->$attribute);
        if (is_null($value)) {
            return;
        }
        if (!is_bool($value)) {
            return $model->addError($attribute, 'boolean');
        }
        $model->$attribute = $value;
    }
    private function cast($value) {
        if (is_null($value)) {
            return null;
        }
        if (is_bool($value)) {
            return $value;
        }
        return ($value === 1 || $value === '1' || $value === 'true');
    }
}