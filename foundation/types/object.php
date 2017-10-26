<?php

/**
 *
 * Copyright (c) 2017 Marc Giroux, Mathieu St-Vincent.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

class FFObject implements JsonSerializable
{
    protected $_type   = 'stdClass';
    protected $_value;

    public function __construct($var=null)
    {
        if (empty($var)) { return; }

        $this->_type = gettype($var);
        $validTypes     = ['string', 'integer', 'double', 'array', 'object'];

        switch ($this->_type)
        {
            case 'integer':
            case 'double':
                $this->_value = new FFNumber($var);
                break;

            case 'string':
                $this->_value = new FFString($var);
                break;

            case 'array':
                $this->_value = new FFArray($var);
                break;

            case 'object':
                $this->_type = get_class($var);
                $this->_value = $var;
                break;

            default:
                $this->_value = $var;
                break;
        }

    }

    function __get($name)
    {
        if (isset($this->_value->{$name})) {
            return $this->_value->{$name};
        }

        throw new \Exception('Property \'' . $name . '\' does not exist in this object');
    }

    function __set($name, $value)
    {
        $type = gettype($value);

        if ($type == 'string') {
            $val = new FFString($value);
        } elseif ($type == 'double' || $type == 'integer') {
            $val = new FFNumber($value);
        } elseif ($type == 'array') {
            $val = new FFArray($value);
        } elseif ($type == 'object') {
            $class = get_class($value);

            if ($class != 'FFObject') {
                $val = new FFObject();

                foreach ($value as $k => $v) {
                    $val->{$k} = $v;
                }
            } else {
                $val = $value;
            }
        } else {
            $val = $value;
        }

        $this->_value->{$name} = $val;
    }

    /**
     *
     * Get the php raw value of this object
     *
     * @return  mixed
     *
     */
    final public function raw()
    {
        echo '<pre>';

        foreach ($this as $key => $value) {
            print_r($value);

            $parent = get_parent_class($value);

            print_r($parent);
        }
    }

    /**
     *
     * Output the object as if user that request "echo" to be displayed like print_r
     *
     * @return string
     *
     */
    public function __toString()
    {
       ob_start();
       print_r($this);
       $out = ob_get_clean();
       return $out;
    }

    /**
     *
     * Serialize to json using php's interface
     *
     */
    final public function jsonSerialize()
    {

    }
}