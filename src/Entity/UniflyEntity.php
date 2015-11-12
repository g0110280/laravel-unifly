<?php

namespace Unisharp\Unifly\Entity;

use Illuminate\Database\Eloquent\Model;

class UniflyEntity extends Model
{
    protected $translatedAttributes = [];

    // TODO Write your autocreate attribute here
    protected $should_be_created_attr = [];

    // TODO Write your autoupdate attribute here
    protected $should_be_updated_attr = [];

    public function getAttrs($mode)
    {
        $attrs = [];

        if ($mode === 'create') {
            $attrs = $this->should_be_created_attr;
        } else {
            $attrs = $this->should_be_updated_attr;
        }

        // it's a reminder exception, if you don't add any attribute
        if (empty($attrs)) {
            throw new \InvalidArgumentException("Are you sure you don't want set anything?");
        }

        return $attrs;
    }

    public function pullTransInputs(&$input, $attrs_create)
    {
        $translatedAttributes = $this->translatedAttributes ?: [];
        $translatedInput = [];

        // Should remove input attribute which not necessary
        foreach ($input as $k => $attr) {
            // check auto set attribute is exist in input array
            if (!in_array($k, $attrs_create)) {
                unset($input[$k]);
            } elseif (in_array($k, $translatedAttributes)) {
                $translatedInput[$k] = $attr;
                unset($input[$k]);
            }
        }

        return $translatedInput;
    }

    public function checkInputExist($input, $attrs_list)
    {
        // if attr not in input, it should be add default value
        foreach ($attrs_list as $attr) {
            // check auto set attribute is exist in input array
            if (!array_key_exists($attr, $input) && !in_array($attr, $this->translatedAttributes)) {
                // do more guard here
                $input[$attr] = null;
            }
        }

        return $input;
    }
}