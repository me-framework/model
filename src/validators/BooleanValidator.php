<?php
namespace me\model\validators;
use me\model\Validator;
class BooleanValidator extends Validator {
    /**
     * @param \me\model\Model $model Model
     * @param string $attribute Attribute Name
     */
    public function validateAttribute($model, $attribute) {
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
        else if (is_null($value)) {
            return null;
        }
        else if ($value === 1 || $value === '1' || $value === 'true') {
            return true;
        }
        return false;
    }
}