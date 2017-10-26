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

namespace Foundation;

use \FFObject as FFObject;
use \FFString as FFString;
use \FFArray as FFArray;

class Application extends FFObject
{
    public function launch()
    {
        $class       = new \stdClass();
        $class->test = 'test';

        $arr = new FFArray(['hello', 'world', '!', 25, 52.52, $class]);

        echo $arr->index(5);
    }

    /**
     *
     * Print the stack with a message and kill the app
     *
     * @param $message  string
     *
     */
    final static function dieWithStack($message)
    {
        $data  = debug_backtrace(null, 5);
        $trace = '<h2>Fatal Error:</h2>' . $message . '<br/><br/>Trace:<br/>';

        unset($data[0]);

        foreach ($data as $t) {
            $trace .= "&nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp;&nbsp;" . $t['file'] . ' -> <b>' . $t['function'] . '()</b> on <b>line ' . $t['line'] . '</b><br/>';
        }

        $trace .= "\n";

        die($trace);
    }
}