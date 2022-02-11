<?php
namespace me\model;
trait ArrayableTrait {
    public function toArray() {
        $attributes = $this->fields();
        $array = [];
        foreach ($attributes as $attribute) {
            $array[$attribute] = $this->$attribute;
        }
        return $array;
    }
}