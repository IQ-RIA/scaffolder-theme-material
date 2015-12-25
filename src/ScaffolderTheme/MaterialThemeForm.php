<?php

namespace ScaffolderTheme;

use Collective\Html\FormBuilder;

class MaterialThemeForm extends FormBuilder
{
    public function __call($method, $arguments) {
        if (!method_exists($this, $method)) {
            return false;
        }
        $input = call_user_func_array([$this, $method], $arguments);
        if (!in_array($method, ['text', 'password', 'number', 'email', 'tel', 'date', 'datetime'])) { //we might be trying to wrap some other method that is not field
            return $input;
        }
        //assuming that $name is always the first argument and $options is always last one
        return $this->wrapField($input, $arguments[0], $arguments[sizeof($arguments) - 1]);
    }

    protected function wrapField($field, $name, $options)
    {
        $labelText = isset($options['label']) ? $options['label'] : null;
        return sprintf('
        <div class="input-field col l6 s8 offset-l3 offset-s2">
            %s
            %s
        </div>', $field, parent::label($name, $labelText, []));
    }

    /* public function text($name, $value = null, $options = [])
     {
         return $this->commonInputs('text', $name, $value, $options);
     }

     public function password($name, $options = [])
     {
         return $this->commonInputs('password', $name, '', $options);
     }

     public function number($name, $value = null, $options = [])
     {
         return $this->commonInputs('number', $name, $value, $options);
     }*/

    public function textarea($name, $value = null, $options = [])
    {
        return sprintf('
        <div class="input-field col l6 s8 offset-l3 offset-s2">
            %s
            %s
        </div>', parent::textarea($name, $value, ['class' => 'materialize-textarea']), parent::label($name, null, []));
    }

    public function select($name, $list = [], $selected = null, $options = [])
    {
        return sprintf('
        <div class="input-field col l6 s8 offset-l3 offset-s2">
            %s
            %s
        </div>', parent::select($name, $list, $selected, $options), parent::label($name, null, []));
    }

    public function checkbox($name, $value = 1, $checked = null, $options = [])
    {
        $options['id'] = $name;

        return sprintf('
        <div class="input-field col l6 s8 offset-l3 offset-s2">
            %s
            %s
            %s
        </div>', parent::hidden($name, 0), parent::checkbox($name, $value, $checked, $options), parent::label($name, null, []));
    }

    public function radio($name, $value = null, $checked = null, $options = [])
    {
        return sprintf('
        <div class="input-field col l6 s8 offset-l3 offset-s2">
            %s
            %s
        </div>', parent::radio($name, $value, $checked, $options), parent::label($options['id'], $value, []));
    }

    /*private function commonInputs($type, $name, $value = null, $options = [])
    {
        $labelText = isset($options['label']) ? $options['label'] : null;
        return sprintf('
        <div class="input-field col l6 s8 offset-l3 offset-s2">
            %s
            %s
        </div>', parent::input($type, $name, $value, $options), parent::label($name, $labelText, []));
    }*/
}