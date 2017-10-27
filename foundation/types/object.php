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

use Foundation\Application;

class FFObject
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
            case 'string':
                Application::dieWithStack('Cannot convert ' . $this->_type . ' to generic object');
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

    /**
     *
     * Easily add a property to the object
     *
     * @param string $name
     * @param mixed $value
     *
     */
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

            if ($class == 'stdClass') {
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
     * Return a standard class
     *
     * @return stdClass
     *
     */
    public function toNative()
    {
        $out = new stdClass();

        foreach ($this->_value as $key => $value) {
            $type = gettype($value);

            if ($type == 'object') {
                $class = get_class($value);

                if ($class === 'stdClass') {
                    $out[$key] = $value;
                } else {
                    switch ($class) {
                        case 'FFNumber':
                            $out->$key = $value->toNative();
                            break;

                        case 'FFObject':
                            $out->$key = $value->toNative();
                            break;

                        case 'FFString':
                            $out->$key = (string)$value;
                            break;

                        default:
                            $out->$key = $value;
                            break;
                    }
                }
            } else {
                $out->$key = $value;
            }
        }

        return $out;
    }

    /**
     *
     * turn object into JSON string
     *
     * @return  String
     *
     */
    public function json()
    {
        return json_encode($this->toNative());
    }
}