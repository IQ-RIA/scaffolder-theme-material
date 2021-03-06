<?php

namespace ScaffolderTheme;

use Collective\Html\FormBuilder;

class MaterialThemeForm extends FormBuilder
{
    protected $prefix = 'material';

    public function __call($method, $arguments)
    {
        //convert materialText -> text and materialSomeControl -> someControl
        $method = str_replace($this->prefix, '', $method);
        $method = strtolower($method[0]) . substr($method, 1);
        if (!method_exists($this, $method)) {
            return false;
        }
        $arguments = $this->adjustArguments($method, $arguments);
        $input = call_user_func_array([$this, $method], $arguments);
        //assuming that $name is always the first argument and $options is always last one
        return $this->wrapField($input, $arguments[0], $arguments[sizeof($arguments) - 1]);
    }

    protected function adjustArguments($method, $arguments)
    {
        //last argument is always $options which should be an array
        //this may not be array if it was not passed. In this case we add it.
        if (!is_array($arguments[sizeof($arguments) - 1])) {
            $arguments[] = [];
        }
        //making sure that each form element has id defined
        $options = $arguments[sizeof($arguments) - 1];
        $options['id'] = isset($options['id']) ? $options['id'] : $arguments[0];// setting id=field_name if ID was not defined already
        $arguments[sizeof($arguments) - 1] = $options;
        //modifications for each specific control
        switch ($method) {
            case 'textarea':
                //injecting class=materialize-textarea
                if (isset($arguments[sizeof($arguments) - 1]['class'])) {
                    $className = $arguments[sizeof($arguments) - 1]['class'];
                    if ($className) {
                        if (strpos($className, 'materialize-textarea') === false) {
                            $className .= ' materialize-textarea';
                        }
                    } else {
                        $className = 'materialize-textarea';
                    }
                } else {
                    $className = 'materialize-textarea';
                }
                $arguments[sizeof($arguments) - 1]['class'] = $className;
                break;
        }
        return $arguments;
    }

    protected function wrapField($field, $labelName, $fieldOptions)
    {
        $labelText = isset($fieldOptions['label']) ? $fieldOptions['label'] : null;
        //making sure that we match field's ID even when it is custom
        $labelName = isset($field['id']) ? $fieldOptions['id'] : $labelName;
        return sprintf('
        <div class="input-field col l3 m4 s8 offset-l2 offset-m1 offset-s2">
            %s
            %s
        </div>', $field, parent::label($labelName, $labelText, []));
    }

    public function checkbox($name, $value = 1, $checked = null, $options = [])
    {
        return parent::hidden($name, 0) . call_user_func_array('parent::' . __FUNCTION__, func_get_args());
    }

    public function materialRadio($name, $value = null, $checked = null, $options = [])
    {
        $input = call_user_func_array('parent::' . __FUNCTION__, func_get_args());
        $options['label'] = isset($options['label']) ? $options['label'] : $value;
        return $this->wrapField($input, $options['id'] ?: $name, $options);
    }

    public function usState($name = 'state', $selected = null, $options = [])
    {
        $states = [
            'AL'=>'Alabama',
            'AK'=>'Alaska',
            'AZ'=>'Arizona',
            'AR'=>'Arkansas',
            'CA'=>'California',
            'CO'=>'Colorado',
            'CT'=>'Connecticut',
            'DE'=>'Delaware',
            'DC'=>'District of Columbia',
            'FL'=>'Florida',
            'GA'=>'Georgia',
            'HI'=>'Hawaii',
            'ID'=>'Idaho',
            'IL'=>'Illinois',
            'IN'=>'Indiana',
            'IA'=>'Iowa',
            'KS'=>'Kansas',
            'KY'=>'Kentucky',
            'LA'=>'Louisiana',
            'ME'=>'Maine',
            'MD'=>'Maryland',
            'MA'=>'Massachusetts',
            'MI'=>'Michigan',
            'MN'=>'Minnesota',
            'MS'=>'Mississippi',
            'MO'=>'Missouri',
            'MT'=>'Montana',
            'NE'=>'Nebraska',
            'NV'=>'Nevada',
            'NH'=>'New Hampshire',
            'NJ'=>'New Jersey',
            'NM'=>'New Mexico',
            'NY'=>'New York',
            'NC'=>'North Carolina',
            'ND'=>'North Dakota',
            'OH'=>'Ohio',
            'OK'=>'Oklahoma',
            'OR'=>'Oregon',
            'PA'=>'Pennsylvania',
            'RI'=>'Rhode Island',
            'SC'=>'South Carolina',
            'SD'=>'South Dakota',
            'TN'=>'Tennessee',
            'TX'=>'Texas',
            'UT'=>'Utah',
            'VT'=>'Vermont',
            'VA'=>'Virginia',
            'WA'=>'Washington',
            'WV'=>'West Virginia',
            'WI'=>'Wisconsin',
            'WY'=>'Wyoming',
        ];
        return $this->select($name, $states, $selected, $options);
    }

    public function collectionSelect($name, \Illuminate\Support\Collection $collection, $selected, $options)
    {
        $key = isset($options['key']) ? $options['key'] : 'id';
        $val = isset($options['val']) ? $options['val'] : 'name';
        $data = [];
        foreach ($collection as $item) {
            $data[$item->{$key}] = $item->{$val};
        }
        return $this->select($name, $data, $selected, $options);
    }
}