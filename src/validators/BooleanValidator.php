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
     */
    public function validateAttribute($model) {
        $value = $model->{$this->attribute};
        if (
                $value !== null   &&
                $value !== true   && $value !== false &&
                $value !== 1      && $value !== 0     &&
                $value !== '1'    && $value !== '0'   &&
                $value !== 'true' && $value !== 'false'
        ) {
            $model->addError($this->attribute, 'boolean');
        }
        else {
            $model->{$this->attribute} = $this->cast($value);
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