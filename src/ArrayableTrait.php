<?php
namespace me\model;
trait ArrayableTrait {
    public function toArray() {
        $attributes = $this->attributes();
        $array = [];
        foreach ($attributes as $attribute) {
            $array[$attribute] = $this->$attribute;
        }
        return $array;
    }
}