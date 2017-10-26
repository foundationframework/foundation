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

class FFArray
{
    public function __construct($var)
    {
        foreach ($var as $k => $v) {
            $type = gettype($v);

            if ($type == 'string') {
                $val = new FFString($v);
            } elseif ($type == 'double' || $type == 'integer') {
                $val = new FFNumber($v);
            } elseif ($type == 'array') {
                $val = new FFArray($v);
            } elseif ($type == 'object') {
                $class = get_class($v);

                if ($class == 'stdClass') {
                    $val = new FFObject($v);
                } else {
                    $val = $v;
                }
            } else {
                $val = $v;
            }

            $var[$k] = $val;
        }

        $this->_value = $var;
    }

    public function index($index)
    {
        if (isset($this->_value[$index])) {
            return $this->_value[$index];
        }

        Foundation\Application::dieWithStack('Requested index does not exist, (request: <b>' . $index . '</b> limit is <b>' . count($this->_value) . '</b>)');
        return null;
    }
}