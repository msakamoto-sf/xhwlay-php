<?php
/*
 *   Copyright (c) 2007 msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.*
 */

/**
 * Xhwlay ErrorStack
 *
 * @package Xhwlay
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Hook.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

/**
 * requires
 */
require_once('PEAR/ErrorStack.php');

// {{{ constants
// }}}
// {{{ Xhwlay_Hook

/**
 * Xhwlay Hook System
 *
 * This class supports "First-In-First-Called"(FIFC) hook callback invoking
 * feature.
 *
 * <b>Simple Usage is like below:</b>
 * <code>
 * $h =& Xhwlay_Hook::getInstance('sample');
 * $h->pushCallback('hook1');
 * $h->pushCallback('hook2');
 * ...
 * $h->invoke();
 * $results = $h->allResult(true);
 * var_dump($results[0]); // result of 'hook1'
 * var_dump($results[1]); // result of 'hook2'
 * </code>
 *
 * <b>Interface of hook-callback is like below:</b>
 * <code>
 * function func($hook_name, $arg1, $arg2, ...) { ... }
 * </code>
 * $hook_name is argument of getInstance(). If upper example, 
 * 'hook1' and 'hook2' retrieves 'sample' string value when invoking
 * 'sample' hooks.
 * $arg1, $arg2, ... can specified by setArguments().
 * <code>
 * $h =& Xhwlay_Hook::getInstance('sample');
 * $h->setArgument(array($arg1, $arg2, ... ));
 * </code>
 *
 * <b>Can I ENABLE/DISABLE actual invoking when calling invoke() ?</b>
 * Yes. Set 'available' attribute to FALSE.
 * <code>
 * $h =& Xhwlay_Hook::getInstance('sample');
 * $h->setAttrubute('available', false);
 * $h->pushCallback(...); // many times.
 * ...
 * $h->invoke();
 * // Don't invoke any hook-callbacks actually.
 * </code>
 *
 * <b>In one hook invoking, can I STOP following hooks and return 
 * from invoke()? </b>
 * Yes. Call escape() in your hook code.
 * <code>
 * function your_hook($hook_name) {
 *     $h =& Xhwlay_Hook::getInstance($hook_name);
 *     $h->escape();
 * }
 * $h =& Xhwlay_Hook('sample');
 * $h->pushCallback('hook_before');
 * $h->pushCallback('your_hook');
 * $h->pushCallback('hook_after');
 * $h->invoke();
 * </code>
 * Then, 'hook_before' and 'your_hook' is invoked, 'hook_after' not invoked.
 *
 * <b>How can I retrieve return values of hook callbacks ? </b>
 * Use allResult() or some short cut methods like firstResult()/lastResult().
 * <code>
 * function hook1($hook_name) { return 'value_1'; }
 * function hook2($hook_name) {} // return null
 * function hook3($hook_name) { return 'value_3'; }
 *
 * $h =& Xhwlay_Hook('sample');
 * $h->pushCallback('hook1');
 * $h->pushCallback('hook2');
 * $h->pushCallback('hook3');
 * $h->invoke();
 *
 * $results = $h->allResults(true);
 * echo count($results); // 2
 * echo count($results[0]); // 'value_1'
 * echo count($results[1]); // 'value_2'
 * </code>
 * Notice that null return value is ignored in internal result stacks.
 * So, in this example, return value of 'hook2' (null) is not available 
 * in allResults().
 * But you can change this behaviour and retrieve null value.
 * See next description.
 *
 * <b>How can I retrieve NULL value from hook callbacks?</b>
 * Set 'ignore_null_result' attribute to TRUE.
 * <code>
 * function hook1($hook_name) { return 'value_1'; }
 * function hook2($hook_name) {} // return null
 * function hook3($hook_name) { return 'value_3'; }
 *
 * $h =& Xhwlay_Hook('sample');
 * $h1->setAttribute('ignore_null_result', true);
 * $h->pushCallback('hook1');
 * $h->pushCallback('hook2');
 * $h->pushCallback('hook3');
 * $h->invoke();
 *
 * $results = $h->allResults(true);
 * echo count($results); // 3
 * echo count($results[0]); // 'value_1'
 * echo count($results[1]); // null
 * echo count($results[2]); // 'value_2'
 * </code>
 *
 * <b>OTHER NOTICE</b>
 *
 * - Pushed callbacks remains after invoke().
 * - allResult() don't clear result stack. If you want clear, 
 *   call allResult(true).
 * - Don't use reference in setArgument(). It leads
 *   'Call-time pass-by-reference' errors. 
 *
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @since 1.0.0
 */
class Xhwlay_Hook
{
    // {{{ properties

    /**
     * @var string Hook name
     * @access protected
     * @since 1.0.0
     */
    var $_name = "";

    /**
     * @var boolean Escapement flag.
     * @access protected
     * @since 1.0.0
     */
    var $_escape = false;

    /**
     * @var array Stack of hook callbacks.
     * @access protected
     * @since 1.0.0
     */
    var $_callbacks = array();

    /**
     * These attributes are supported.
     *
     * - 'available' : Hook is enable or not.
     * - 'ignore_null_result' : Hook returns null, it's valid result or not.
     *
     * These are boolean type.
     *
     * @var array Attributes of hook instance.
     * @access protected
     * @since 1.0.0
     */
    var $_attributes = array();

    /**
     * @var array Argments passed to hook callbacks.
     * @access protected
     * @since 1.0.0
     */
    var $_args = array();

    /**
     * @var array Stack of hook callback's return values.
     * @access protected
     * @since 1.0.0
     */
    var $_results = array();

    // }}}
    // {{{ constructor

    /**
     * Constructor. Initialize properties.
     *
     * @access protected
     * @param string hook name
     * @since 1.0.0
     */
    function Xhwlay_Hook($name)
    {
        $this->_name = $name;
        $this->_attributes['available'] = true;
        $this->_attributes['ignore_null_result'] = true;
    }

    // }}}
    // {{{ &getInstance()

    /**
     * Factory Interface of Xhwlay_Hook.
     *
     * @static
     * @access public
     * @param string hook name
     * @return object instance of Xhwlay_Hook (By Ref)
     * @since 1.0.0
     */
    function &getInstance($name)
    {
        static $_instance = array();
        if (!isset($_instance[$name])) {
            $_instance[$name] = new Xhwlay_Hook($name);
        }
        return $_instance[$name];
    }

    // }}}
    // {{{ pushCallback()

    /**
     * Push hook callback to internal callback stack.
     *
     * @access public
     * @param callback
     * @since 1.0.0
     */
    function pushCallback($callback)
    {
        $this->_attributes['available'] = true;
        array_push($this->_callbacks, $callback);
    }

    // }}}
    // {{{ popCallback()

    /**
     * Pop hook callback from internal callback stack.
     *
     * @access public
     * @return callback
     * @since 1.0.0
     */
    function popCallback()
    {
        return array_pop($this->_callbacks);
    }

    // }}}
    // {{{ getAttribute()

    /**
     * @access public
     * @param string attribute name
     * @return mixed attribute value
     * @since 1.0.0
     */
    function getAttribute($attr)
    {
        return isset($this->_attributes[$attr]) ? 
            $this->_attributes[$attr] : null;
    }

    // }}}
    // {{{ setAttribute()

    /**
     * @access public
     * @param string attribute name
     * @param mixed attribute value
     * @return mixed old attribute value
     * @since 1.0.0
     */
    function setAttribute($attr, $value)
    {
        $ret = @$this->_attributes[$attr];
        $this->_attributes[$attr] = $value;
        return $ret;
    }

    // }}}
    // {{{ listAttributes()

    /**
     * @access public
     * @return array Array of attributes (Exports of attributes)
     * @since 1.0.0
     */
    function listAttributes()
    {
        return $this->_attributes;
    }

    // }}}
    // {{{ getArgument()

    /**
     * @access public
     * @return array arguments
     * @since 1.0.0
     */
    function getArgument()
    {
        return $this->_args;
    }

    // }}}
    // {{{ setArgument()

    /**
     * @access public
     * @param array arguments
     * @return array old arguments
     * @since 1.0.0
     */
    function setArgument($args)
    {
        $ret = $this->_args;
        $this->_args = $args;
        return $ret;
    }

    // }}}
    // {{{ clearArgument()

    /**
     * Clear argument
     *
     * @access public
     * @since 1.0.0
     */
    function clearArgument()
    {
        $this->_args = array();
    }

    // }}}
    // {{{ firstResult()

    /**
     * Get return value of hook-callback which was called first.
     *
     * @return mixed hook result (or null)
     * @access public
     * @since 1.0.0
     */
    function firstResult()
    {
        return (count($this->_results) == 0) ? 
            null : $this->_results[0];
    }

    // }}}
    // {{{ lastResult()

    /**
     * Get return value of hook-callback which was called last.
     *
     * @return mixed hook result (or null)
     * @access public
     * @since 1.0.0
     */
    function lastResult()
    {
        $c = count($this->_results);
        return ($c == 0) ? null : $this->_results[$c - 1];
    }

    // }}}
    // {{{ allResults()

    /**
     * Retrieve all return value of hook-callbacks.
     *
     * @param boolean If TRUE is given, clear results.
     *                If omitted or FALSE is given, don't clear results.
     * @return mixed
     * @access public
     * @since 1.0.0
     */
    function allResults($clear = false)
    {
        $ret = $this->_results;
        if ($clear) {
            $this->_results = array();
        }
        return $ret;
    }

    // }}}
    // {{{ invoke()

    /**
     * Invoke hook-callbacks in order by First-In-First-Called.
     *
     * @access public
     * @since 1.0.0
     */
    function invoke()
    {
        if (!$this->_attributes['available']) {
            return;
        }

        $args = $this->_args;
        array_unshift($args, $this->_name);
        // 'copy' callbacks.
        // guard from pop/pushCallback() call in hooks callbacks.
        $copy_callbacks = $this->_callbacks;
        foreach ($copy_callbacks as $cb) {
            $ret = call_user_func_array($cb, $args);
            if (is_null($ret)) {
                if ($this->_attributes['ignore_null_result'] === false) {
                    array_push($this->_results, null);
                }
            } else {
                array_push($this->_results, $ret);
            }
            if ($this->_escape) {
                $this->_escape = false; // restore
                break;
            }
        }
    }

    // }}}
    // {{{ escape()

    /**
     * Escape from current hook.
     * This should be called in hook callbacks.
     *
     * @access public
     * @since 1.0.0
     */
    function escape()
    {
        $this->_escape = true;
    }

    // }}}


}

// }}}

/**
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 * vim: set expandtab tabstop=4 shiftwidth=4:
 */
