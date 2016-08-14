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
 * @version $Id: ErrorStack.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

/**
 * requires
 */
require_once('PEAR/ErrorStack.php');

// {{{ constants

/**
 * PEAR_ErrorStack Package Name for Xhwlay_Error
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_ERRORSTACK_PACKAGE')) {
    define('XHWLAY_ERRORSTACK_PACKAGE', 'Xhwlay');
}

/**
 * Xhwlay Error Level : ERROR
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_ERRORSTACK_EL_ERROR')) {
    define('XHWLAY_ERRORSTACK_EL_ERROR', 'error');
}

/**
 * Xhwlay Error Level : WARN
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_ERRORSTACK_EL_WARN')) {
    define('XHWLAY_ERRORSTACK_EL_WARN', 'warn');
}

/**
 * Xhwlay Error Level : INFO
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_ERRORSTACK_EL_INFO')) {
    define('XHWLAY_ERRORSTACK_EL_INFO', 'info');
}

/**
 * Xhwlay Error Level : DEBUG
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_ERRORSTACK_EL_DEBUG')) {
    define('XHWLAY_ERRORSTACK_EL_DEBUG', 'debug');
}

// }}}
// {{{ Xhwlay_ErrorStack

/**
 * PEAR_ErrorStack wrapper for Xhwlay.
 *
 * All methods are static.
 *
 * @static
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @since 1.0.0
 */
class Xhwlay_ErrorStack
{
    // {{{ properties
    // }}}
    // {{{ constructor
    // }}}
    // {{{ push()

    /**
     * Adds an error to the stack for the package.
     * This method is a wrapper for
     * PEAR_ErrorStack::staticPush() method.
     *
     * @static
     * @param integer $code
     * @param string  $message
     * @param string  $level (default: 'exception')
     * @param array   $params
     * @param array   $repackage (default: false)
     * @param array   $backtrace (default: false)
     * @access public
     * @see PEAR_ErrorStack::staticPush()
     * @since 1.0.0
     */
    function push(
        $code, 
        $message = false, 
        $level = 'exception', 
        $params = array(),
        $repackage = false, 
        $backtrace = false)
    {
        if (!$backtrace) {
            $backtrace = debug_backtrace();
        }
        PEAR_ErrorStack::staticPush(
            XHWLAY_ERRORSTACK_PACKAGE, 
            $code, 
            $level, 
            $params, 
            $message, 
            $repackage, 
            $backtrace);
    }

    // }}}
    // {{{ pushCallback()

    /**
     * Pushes a callback. This method is a wrapper for
     * PEAR_ErrorStack::staticPushCallback() method.
     *
     * $callback method/function is like below:
     * <code>
     * function XXXX($error) // 1 arg is error info(assoc-array)
     * {
     *  ... (your own codes) ...
     *  return PEAR_ERRORSTACK_IGNORE; // Don't stack, ignored.
     *  return PEAR_ERRORSTACK_PUSH; // Stack.
     *  return PEAR_ERRORSTACK_DIE; // die(). 
     * }
     * </code>
     *
     * @static
     * @param callback $callback
     * @access public
     * @see PEAR_ErrorStack::staticPushCallback()
     * @since 1.0.0
     */
    function pushCallback($callback)
    {
        PEAR_ErrorStack::staticPushCallback($callback);
    }

    // }}}
    // {{{ popCallback()

    /**
     * Pops a callback. This method is a wrapper for
     * PEAR_ErrorStack::staticPopCallback() method.
     *
     * @static
     * @return callback
     * @access public
     * @see PEAR_ErrorStack::staticPopCallback()
     * @since 1.0.0
     */
    function popCallback()
    {
        return PEAR_ErrorStack::staticPopCallback();
    }

    // }}}
    // {{{ all()

    /**
     * Get errors, but not clean stacks.
     *
     * @static
     * @param string $level Specify level. If not spcified, 
     *                      All level are counted.
     * @return array array of error (assoc-array)
     * @access public
     * @see PEAR_ErrorStack::getErrors()
     * @since 1.0.0
     */
    function all($level = false)
    {
        $stack =& PEAR_ErrorStack::singleton(XHWLAY_ERRORSTACK_PACKAGE);
        // get errors, not clear(1st arg = false).
        $errors = $stack->getErrors(false, $level);
        usort($errors, array(__CLASS__, '_sortErrors'));
        return $errors;
    }

    // }}}
    // {{{ _sortErrors()

    /**
     * Error sorting function, sorts by time
     *
     * @static
     * @access protected
     * @see PEAR_ErrorStack::_sortErrors()
     * @since 1.0.0
     */
    function _sortErrors($a, $b)
    {
        if ($a['time'] == $b['time']) {
            return 0;
        }
        if ($a['time'] < $b['time']) {
            return 1;
        }
        return -1;
    }

    // }}}
    // {{{ count()

    /**
     * Returns error count in stack.
     *
     * @static
     * @param string $level Specify level. If not spcified, 
     *                      All level are counted.
     * @return integer Error count in stack.
     * @access public
     * @see Xhwlay_ErrorStack::all()
     * @since 1.0.0
     */
    function count($level = false)
    {
        return count(Xhwlay_ErrorStack::all($level));
    }

    // }}}
    // {{{ pop()

    /**
     * Pops an error off of the error stack for the package. 
     * This method is a wrapper for 
     * PEAR_ErrorStack::pop() method.
     *
     * @static
     * @return array
     * @access public
     * @see PEAR_ErrorStack::pop()
     * @since 1.0.0
     */
    function pop()
    {
        $stack = &PEAR_ErrorStack::singleton(XHWLAY_ERRORSTACK_PACKAGE);
        return $stack->pop();
    }

    // }}}
    // {{{ clear()

    /**
     * Clears the error stack for the package.
     *
     * @static
     * @access public
     * @see PEAR_ErrorStack::getErrors()
     * @since 1.0.0
     */
    function clear()
    {
        $stack = &PEAR_ErrorStack::singleton(XHWLAY_ERRORSTACK_PACKAGE);
        // get errors, clear(1st arg = true).
        $stack->getErrors(true);
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
