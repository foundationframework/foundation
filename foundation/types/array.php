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

    /**
     *
     * Return a php array
     *
     * @return array
     *
     */
    public function toNative()
    {
        $out = [];

        foreach ($this->_value as $key => $value) {
            $type = gettype($value);

            if ($type == 'object') {
                $class = get_class($value);

                if ($class === 'stdClass') {
                    $out[$key] = $value;
                } else {
                    switch ($class) {
                        case 'FFNumber':
                            $out[$key] = $value->toNative();
                            break;

                        case 'FFObject':
                            $out[$key] = $value->toNative();
                            break;

                        case 'FFString':
                            $out[$key] = (string)$value;
                            break;

                        default:
                            $out[$key] = $value;
                            break;
                    }
                }
            } else {
                $out[$key] = $value;
            }
        }

        return $out;
    }

    /**
     *
     * Get value at index (can be an int or string)
     *
     * @param  int $index
     * @return mixed
     *
     */
    public function at(int $index)
    {
        if (isset($this->_value[$index])) {
            return $this->_value[$index];
        }

        Foundation\Application::dieWithStack('Requested index does not exist, (request: <b>' . $index . '</b> limit is <b>' . count($this->_value) . '</b>)');
        return null;
    }

    /**
     *
     * turn object into JSON string
     *
     * @return  String
     *
     */
    public function json(): string
    {
        return json_encode($this->toNative());
    }

    /**
     *
     * Number of elements in array
     *
     * @return int
     *
     */
    public function count(): int
    {
        return count($this->_value);
    }

    /**
     *
     * Join the array into a string
     *
     * @param  string $glue
     * @return FFString
     *
     */
    public function join(string $glue): FFString
    {
        $out = '';

        foreach ($this->_value as $key => $value) {
            if (empty($out)) {
                $out = $value;
            } else {
                $out .= $glue . $value;
            }
        }

        return new FFString($out);
    }

    /**
     *
     * Shuffle
     *
     * @return FFArray
     *
     */
    public function shuffle(): FFArray
    {
        shuffle($this->_value);
        return $this;
    }

    /**
     *
     * Split array into chunks of arrays
     *
     * @param  int $size
     * @param  bool $keepKeys
     * @return FFArray
     *
     */
    public function split(int $size, bool $keepKeys = false): FFArray
    {
        $value = array_chunk($this->_value, $size, $keepKeys);
        return new FFArray($value);
    }

    /**
     *
     * Find differences between the array and given array
     *
     * @param array|FFArray $array
     * @param bool $assoc
     * @return FFArray
     *
     */
    public function differences($array, bool $assoc = false): FFArray
    {
        $array1 = $this->toNative();
        $array2 = (is_array($array)) ? $array : $array->toNative();
        $array3 = [];

        if ($assoc) {
            $array3 = array_diff_assoc($array1, $array2);
        } else {
            $array3 = array_diff($array1, $array2);
        }

        return new FFArray($array3);
    }

    /**
     *
     * Find elements that are the same in both arrays
     *
     * @param array|FFArray $array
     * @param bool $assoc
     * @return FFArray
     *
     */
    public function intersect($array, bool $assoc = false): FFArray
    {
        $array1 = $this->toNative();
        $array2 = (is_array($array)) ? $array : $array->toNative();
        $array3 = [];

        if ($assoc) {
            $array3 = array_intersect_assoc($array1, $array2);
        } else {
            $array3 = array_intersect($array1, $array2);
        }

        return new FFArray($array3);
    }

    /*

    reverse
    filter
    map
    keyExists
    merge
    push
    pop
    slice
    unique
    in (recursive)
    sortByKeys
    sort
    reverseSortByKeys
    reverseSort
    userSort
    userReverseSort
    values
    keys




    */
}