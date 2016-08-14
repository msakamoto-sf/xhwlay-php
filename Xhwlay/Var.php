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
 * Xhwlay Variable Stocks
 *
 * @package Xhwlay
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Var.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

// {{{ constants

/**
 * Special key name. This is used around manupilating key and values 
 * through all namespaces.
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_VAR_NAMESPACE_ALL')) {
    define('XHWLAY_VAR_NAMESPACE_ALL', '*');
}

/**
 * Key name of default namespace
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_VAR_NAMESPACE_DEFAULT')) {
    define('XHWLAY_VAR_NAMESPACE_DEFAULT', 'xhwlay');
}

/**
 * Key name of user(developer) namespace
 * (This is sample, not rule. Developers/Users can choice any key name 
 * they want freely.)
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_VAR_NAMESPACE_USER')) {
    define('XHWLAY_VAR_NAMESPACE_USER', 'user');
}

/**
 * Key name of requested RAW values
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_VAR_NAMESPACE_REQUEST')) {
    define('XHWLAY_VAR_NAMESPACE_REQUEST', 'request');
}

/**
 * Key name of xhwlay's special value for view renderer
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_VAR_NAMESPACE_VIEW')) {
    define('XHWLAY_VAR_NAMESPACE_VIEW', 'view');
}

/**
 * Key name of ACI (Xhwlay Access Control Identifier)
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_VAR_KEY_ACI')) {
    define('XHWLAY_VAR_KEY_ACI', 'aci');
}

/**
 * Key name of page name
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_VAR_KEY_PAGE')) {
    define('XHWLAY_VAR_KEY_PAGE', 'page');
}

/**
 * Key name of Bookmark Container ID
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_VAR_KEY_BCID')) {
    define('XHWLAY_VAR_KEY_BCID', 'bcid');
}

/**
 * Key name of event name
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_VAR_KEY_EVENT')) {
    define('XHWLAY_VAR_KEY_EVENT', 'event');
}

// }}}
// {{{ Xhwlay_Var

/**
 * Xhwlay Variable Container
 *
 * This class holds variables in Xhwlay applications through PHP execution.
 * Xhwlay applications use this class as "Global Scope" variable container.
 *
 * Values are associated with key name. These are separated by "namespace."
 * So, different namespace, then application can use same key with different 
 * values.
 *
 * <code>
 * Xhwlay_Var::set("key1", "val1", "namespace1");
 * Xhwlay_Var::set("key1", "val2", "namespace2");
 * Xhwlay_Var::set("key1", "val3");
 * // ...
 * $v1 = Xhwlay_Var::get("key1", "namespace1"); // returns "val1"
 * $v2 = Xhwlay_Var::get("key1", "namespace2"); // returns "val2"
 * $v_default = Xhwlay_Var::get("key1"); // returns "val3"
 * </code>
 *
 * NOTE: Most of methods has "namespace" arguments, and it's colud be omitted.
 * If omitted, default namespace is assumed.
 *
 * Xhwlay_Var has an unique idea, "ALL" namespace.
 * Application can manupilate values associated with same key name in 
 * different namespace at once by passing XHWLAY_VAR_NAMESPACE_ALL as 
 * "namespace" argument in get/set and other methods.
 * <code>
 * Xhwlay_Var::set("key1", "val1", "namespace1");
 * $v = Xhwlay_Var::get("key1", "namespace1"); // returns "val1"
 *
 * Xhwlay_Var::set("key1", "val2", XHWLAY_VAR_NAMESPACE_ALL);
 * $v = Xhwlay_Var::get("key1", "namespace1"); // returns "val2"!!
 *
 * // re-write "key1" in "namespace1"
 * Xhwlay_Var::set("key1", "val3", "namespace1");
 * $v = Xhwlay_Var::get("key1", "namespace1"); // returns "val3"
 * </code>
 *
 * Any other detailed usage can be shown in test case.
 * Please refer Xhwlay_Var_Test class.
 *
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @since 1.0.0
 */
class Xhwlay_Var
{
    // {{{ properties

    var $_vars = array();

    // }}}
    // {{{ constructor
    // }}}
    // {{{ getInstance()

    /**
     * Returns singleton instance of Xhwlay_Var.
     *
     * @static
     * @access public
     * @return object Xhwlay_Var
     * @since 1.0.0
     */
    function &getInstance()
    {
        static $_instance = null;
        if (is_null($_instance)) {
            $_instance = new Xhwlay_Var();
        }
        return $_instance;
    }

    // }}}
    // {{{ exists()

    /**
     * Returns specified key entry is exists or not in given namespace.
     * If ALL namespace is given, returns exists or not in 
     * XHWLAY_VAR_NAMESPACE_ALL namespace.
     *
     * @static
     * @access public
     * @param string key
     * @param string namespace
     * @return boolean exists or not
     * @since 1.0.0
     */
    function exists($key, $namespace = XHWLAY_VAR_NAMESPACE_DEFAULT)
    {
        $zv =& Xhwlay_Var::getInstance();
        if (isset($zv->_vars[$namespace][$key])) {
            return true;
        }
        if ($namespace == XHWLAY_VAR_NAMESPACE_ALL && 
            isset($zv->_vars[XHWLAY_VAR_NAMESPACE_ALL][$key])) {
            return true;
        }
        return false;
    }

    // }}}
    // {{{ remove()

    /**
     * Remove value and delete entry specified key name in given namespace.
     * If ALL namespace is given, delete entry in all namespaces.
     *
     * @static
     * @access public
     * @param string key
     * @param string namespace
     * @since 1.0.0
     */
    function remove($key, $namespace = XHWLAY_VAR_NAMESPACE_DEFAULT)
    {
        $zv =& Xhwlay_Var::getInstance();
        if ($namespace == XHWLAY_VAR_NAMESPACE_ALL) {
            foreach ($zv->_vars as $ns_ => $vs) {
                unset($zv->_vars[$ns_][$key]);
            }
        }
        unset($zv->_vars[$namespace][$key]);
    }

    // }}}
    // {{{ set()

    /**
     * Assign new value and associate with specified key name in given 
     * namespace.
     * If ALL namespace is given, assing in XHWLAY_VAR_NAMESPACE_ALL namespace
     * and overwrite other all namespace variables.
     *
     * @static
     * @param string key
     * @param mixed value
     * @param string namespace
     * @access public
     * @since 1.0.0
     */
    function set($key, $value, $namespace = XHWLAY_VAR_NAMESPACE_DEFAULT)
    {
        $zv =& Xhwlay_Var::getInstance();
        if ($namespace == XHWLAY_VAR_NAMESPACE_ALL) {
            foreach ($zv->_vars as $ns_ => $vs) {
                $zv->_vars[$ns_][$key] = $value;
            }
            $zv->_vars[XHWLAY_VAR_NAMESPACE_ALL][$key] = $value;
        } else {
            $zv->_vars[$namespace][$key] = $value;
        }
    }

    // }}}
    // {{{ get()

    /**
     * Returns value associated with specified key name in given namespace.
     * If ALL namespace is given, return XHWLAY_VAR_NAMESPACE_ALL namespace 
     * value.
     *
     * @static
     * @param string key
     * @param string namespace
     * @param string default value (if omitted, assumes null)
     * @return mixed value
     * @access public
     * @since 1.0.0
     */
    function get(
        $key, $namespace = XHWLAY_VAR_NAMESPACE_DEFAULT, $default = null)
    {
        $zv =& Xhwlay_Var::getInstance();
        if ($namespace == XHWLAY_VAR_NAMESPACE_ALL) {
            if (isset($zv->_vars[XHWLAY_VAR_NAMESPACE_ALL][$key])) {
                return $zv->_vars[XHWLAY_VAR_NAMESPACE_ALL][$key];
            } else {
                return $default;
            }
        }
        if (isset($zv->_vars[$namespace][$key])) {
            return $zv->_vars[$namespace][$key];
        } else {
            return $default;
        }
    }

    // }}}
    // {{{ clear()

    /**
     * Remove all variables in given namespace.
     * If ALL namespace is given, clear all namespace variables.
     *
     * @static
     * @access public
     * @param string namespace
     * @since 1.0.0
     */
    function clear($namespace = XHWLAY_VAR_NAMESPACE_DEFAULT)
    {
        $zv =& Xhwlay_Var::getInstance();
        if ($namespace == XHWLAY_VAR_NAMESPACE_ALL) {
            foreach ($zv->_vars as $ns_ => $vs) {
                unset($zv->_vars[$ns_]);
            }
            return;
        }
        unset($zv->_vars[$namespace]);
    }

    // }}}
    // {{{ export()

    /**
     * Returns given namespace variables as assoc-arrays(key => value).
     * If given namespace is not exists, returns null.
     * If ALL namespace is given, returns following array :
     * <code>
     * array(
     *     "namespace1" => array("key" => "value", ...),
     *     "namespace2" => array("key" => "value", ...),
     *     ...
     * );
     * </code>
     *
     * @static
     * @access public
     * @param string namespace
     * @return mixed
     * @since 1.0.0
     */
    function export($namespace = XHWLAY_VAR_NAMESPACE_DEFAULT)
    {
        $zv =& Xhwlay_Var::getInstance();
        if ($namespace == XHWLAY_VAR_NAMESPACE_ALL) {
            return $zv->_vars;
        }
        return isset($zv->_vars[$namespace]) ? $zv->_vars[$namespace] : null;
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
