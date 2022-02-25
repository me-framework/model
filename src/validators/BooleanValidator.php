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
     * @param string $modelKey
     */
    public function validateAttribute($model, $attribute, $modelKey) {
        $value = $model->$attribute;
        if (
                $value !== null   &&
                $value !== true   && $value !== false &&
                $value !== 1      && $value !== 0     &&
                $value !== '1'    && $value !== '0'   &&
                $value !== 'true' && $value !== 'false'
        ) {
            $model->addError($attribute, 'boolean');
        }
        else {
            $model->$attribute = $this->cast($value);
        }
    }
    private function cast($value) {
        if (is_bool($value)) {
            return $value;
        }
        if (is_null($value)) {
            return null;
        }
        return ($value === 1 || $value === '1' || $value === 'true');
    }
}