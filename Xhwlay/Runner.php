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
 * Xhwlay Page Flow Engine (Basic Implementation)
 *
 * @package Xhwlay
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Runner.php 55 2008-02-20 01:35:44Z msakamoto-sf $
 */

/**
 * requires
 */
require_once(dirname(__FILE__).'/ErrorStack.php');
require_once(dirname(__FILE__).'/Var.php');
require_once(dirname(__FILE__).'/Hook.php');
require_once(dirname(__FILE__).'/Bookmark.php');
// {{{ constants
// {{{ XHWLAY_RUNNER_EC_NOT_VALID_TRANSITION
/**
 * Requested page name is not valid transition targets of current page.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_EC_NOT_VALID_TRANSITION')) {
    define('XHWLAY_RUNNER_EC_NOT_VALID_TRANSITION', 0x0400 | 0x01);
}
// }}}
// {{{ XHWLAY_RUNNER_EC_BARRIER_RESULTS_NOT_NEXT
/**
 * Barrier action results not to transit requested page.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_EC_BARRIER_RESULTS_NOT_NEXT')) {
    define('XHWLAY_RUNNER_EC_BARRIER_RESULTS_NOT_NEXT', 0x0400 | 0x02);
}
// }}}
// {{{ XHWLAY_RUNNER_EC_CLASS_OR_METHOD_NOT_DEFINED
/**
 * Xhwlay Runner Error Code :
 * Valid class or method was not found for Barrier/Page callback.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_EC_CLASS_OR_METHOD_NOT_DEFINED')) {
    define('XHWLAY_RUNNER_EC_CLASS_OR_METHOD_NOT_DEFINED', 0x0400 | 0x03);
}
// }}}
// {{{ XHWLAY_RUNNER_EC_CLASS_NOT_EXISTS
/**
 * Xhwlay Runner Error Code :
 * Specified class doesn't exists for Barrier/Page callback.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_EC_CLASS_NOT_EXISTS')) {
    define('XHWLAY_RUNNER_EC_CLASS_NOT_EXISTS', 0x0400 | 0x04);
}
// }}}
// {{{ XHWLAY_RUNNER_EC_METHOD_NOT_EXISTS
/**
 * Xhwlay Runner Error Code :
 * Specified method doesn't exists in specified class 
 * for Barrier/Page callback.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_EC_METHOD_NOT_EXISTS')) {
    define('XHWLAY_RUNNER_EC_METHOD_NOT_EXISTS', 0x0400 | 0x05);
}
// }}}
// {{{ XHWLAY_RUNNER_EC_NOT_VALID_EVENT
/**
 * Xhwlay Runner Error Code :
 * Requested event name is not valid eventts of current page.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_EC_NOT_VALID_EVENT')) {
    define('XHWLAY_RUNNER_EC_NOT_VALID_EVENT', 0x0400 | 0x06);
}
// }}}
// {{{ XHWLAY_RUNNER_EC_GUARD_DISALLOW_EVENT_INVOKE
/**
 * Xhwlay Runner Error Code :
 * Guard action results disallowing event invocation 
 *  and rollback to previous page.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_EC_GUARD_DISALLOW_EVENT_INVOCATION')) {
    define('XHWLAY_RUNNER_EC_GUARD_DISALLOW_EVENT_INVOCATION', 0x0400 | 0x07);
}
// }}}
// {{{ XHWLAY_RUNNER_OUTPARAM_MAX_LEN
/**
 * Xhwlay ACI, PageName, BookmarkContainerID 's input string max length.
 *
 * Default is 64.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_OUTPARAM_MAX_LEN')) {
    define('XHWLAY_RUNNER_OUTPARAM_MAX_LEN', 64);
}
// }}}
// {{{ XHWLAY_RUNNER_HOOK_SETUP
/**
 * Xhwlay Hook Name : setup hook
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_HOOK_SETUP')) {
    define('XHWLAY_RUNNER_HOOK_SETUP', "xhwlay_runner_setup");
}
// }}}
// {{{ XHWLAY_RUNNER_HOOK_TERMINATE
/**
 * Xhwlay Hook Name : terminate hook
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_HOOK_TERMINATE')) {
    define('XHWLAY_RUNNER_HOOK_TERMINATE', "xhwlay_runner_terminate");
}
// }}}
// {{{ XHWLAY_RUNNER_HOOK_CLASSLOAD
/**
 * Xhwlay Hook Name : class loading hook
 *
 * @var string
 * @since 1.0.0
 */
if (!defined('XHWLAY_RUNNER_HOOK_CLASSLOAD')) {
    define('XHWLAY_RUNNER_HOOK_CLASSLOAD', "xhwlay_runner_classload");
}
// }}}
// }}}

/**
 * Abstract Runner class of Xhwlay.
 *
 * Base class of Xhwlay Runner.
 *
 * @static
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @since 1.0.0
 */
class Xhwlay_Runner
{
    // {{{ properties

    /**
     * Xhwlay Config
     *
     * @var Xhwlay_Config_Interface
     * @access protected
     * @since 1.0.0
     */
    var $_config = null;

    /**
     * Xhwlay Renderer
     *
     * @var Xhwlay_Renderer_AbstractRenderer
     * @access protected
     * @since 1.0.0
     */
    var $_renderer = null;

    /**
     * Xhwlay Bookmark Container
     *
     * @var Xhwlay_Bookmark_AbstractContainer
     * @access protected
     * @since 1.0.0
     */
    var $_bookmarkContainer = null;

    /**
     * Implement class name of Xhwlay_Bookmark_AbstractContainer
     *
     * @var string
     * @access protected
     * @since 1.0.0
     */
    var $_bookmarkContainerClassName = "";

    /**
     * Constructor args parameters of Xhwlay_Bookmark_AbstractContainer
     *
     * @var array
     * @access protected
     * @since 1.0.0
     */
    var $_bookmarkContainerParams = array();

    /**
     * If true, skip page invocation.
     *
     * @var boolean
     * @access protected
     * @since 1.0.0
     */
    var $_skip_page = false;

    /**
     * If true, skip page flow processor(_run() method).
     *
     * @var boolean
     * @access protected
     * @since 1.0.0
     */
    var $_wipeout = false;

    // }}}
    // {{{ constructor
    // }}}
    // {{{ setConfig()

    /**
     * Set config instance
     *
     * @final
     * @access public
     * @param Xhwlay_Config_Interface
     * @since 1.0.0
     */
    function setConfig(&$config)
    {
        $this->_config =& $config;
    }

    // }}}
    // {{{ getConfig()

    /**
     * Return config reference
     *
     * @final
     * @access public
     * @return Reference of Xhwlay_Config_Interface
     * @since 1.0.0
     */
    function &getConfig()
    {
        return $this->_config;
    }

    // }}}
    // {{{ setRenderer()

    /**
     * Set renderer instance
     *
     * @final
     * @access public
     * @param Xhwlay_Renderer_AbstractRenderer
     * @since 1.0.0
     */
    function setRenderer(&$renderer)
    {
        $this->_renderer =& $renderer;
    }

    // }}}
    // {{{ getRenderer()

    /**
     * Return renderer reference
     *
     * @final
     * @access public
     * @return Reference of Xhwlay_Renderer_AbstractRenderer
     * @since 1.0.0
     */
    function &getRenderer()
    {
        return $this->_renderer;
    }

    // }}}
    // {{{ setBookmarkContainerClassName()

    /**
     * Set Bookmark Container Class Name
     *
     * @final
     * @access public
     * @param string class name of Xhwlay_Bookmark_AbstractContainer 
     *                             Implements class
     * @since 1.0.0
     */
    function setBookmarkContainerClassName($class)
    {
        $this->_bookmarkContainerClassName = $class;
    }

    // }}}
    // {{{ setBookmarkContainerParams()

    /**
     * Set Bookmark Container constructor args parameters
     *
     * @final
     * @access public
     * @param array
     * @since 1.0.0
     */
    function setBookmarkContainerParams($params)
    {
        $this->_bookmarkContainerParams = $params;
    }

    // }}}
    // {{{ skipPage()

    /**
     * Skip page invocation.
     *
     * @access public
     * @since 1.0.0
     */
    function skipPage()
    {
        $this->_skip_page = true;
    }

    // }}}
    // {{{ wipeout()

    /**
     * Skip page flow processor(_run() method).
     *
     * (Why calls 'wipeout'? :
     *  This method makes all-Xhwlay's features disalbed.
     *  [state-full page flow, page/event oriented]
     *  It seems that a runner(Xhwlay page flow) gets off to a bad start, 
     *  and falls over at start time whistle.
     *  So, I decide this name.)
     *
     * @access public
     * @since 1.0.0
     */
    function wipeout()
    {
        $this->_wipeout = true;
    }

    // }}}
    // {{{ invokePage()

    /**
     * Execute page action, return view name.
     *
     * Page interface is shown in following code:
     * <code>
     * function page_func(&$runner, $pageName,&$bookmark, $params)
     * {
     *    ...
     *    // return View name
     *    return "viewName";
     *    // If you want to skip rendering process, return null.
     *    return null;
     * }
     * </code>
     * Each argument is same of this method.
     *
     * And this method invokes PRE and POST hooks which name are:
     * - "xhwlay_runner_pre_invokePage"
     * - "xhwlay_runner_post_invokePage"
     *
     * 1st, "xhwlay_runner_pre_invokePage" invokes.
     * 2nd, Actuall page callback is called.
     * 3rd, "xhwlay_runner_post_invokePage" invokes.
     *
     * Argument of these hooks is also same of this method.
     *
     * @access protected
     * @param Xhwlay_Runner reference of runner instance
     * @param string Page name of current, not request nor bookmarked.
     * @param Xhwlay_Bookmark reference. If null, not in Bookmarked Story.
     * @param mixed Page Info.
     * @return string Result of page action (View Name)
     * @since 1.0.0
     */
    function invokePage(&$runner, $pageName, &$bookmark, $params)
    {
        $args = array(&$runner, $pageName, &$bookmark, $params);

        // PRE HOOK
        $h_pre =& Xhwlay_Hook::getInstance(
            "xhwlay_runner_pre_invokePage");
        $h_pre->setArgument($args);
        $h_pre->invoke();

        $result = false;
        $cb = $this->_getCallback($params);
        if (!is_null($cb)) {
            $result = call_user_func_array($cb, $args);
        }

        // POST HOOK
        $h_post =& Xhwlay_Hook::getInstance(
            "xhwlay_runner_post_invokePage");
        $h_post->setArgument($args);
        $h_post->invoke();

        return $result;
    }

    // }}}
    // {{{ invokeEvent()

    /**
     * Execute event, return transit name.
     *
     * Event interface is shown in following code:
     * <code>
     * function event_func(&$runner, $event ,&$bookmark, $params)
     * {
     *    ...
     *    // return new page name
     *    return "pageName";
     *    // If you return null, current page name is holded up.
     *    return null;
     * }
     * </code>
     * Each argument is same of this method.
     *
     * And this method invokes PRE and POST hooks which name are:
     * - "xhwlay_runner_pre_invokeEvent"
     * - "xhwlay_runner_post_invokeEvent"
     *
     * 1st, "xhwlay_runner_pre_invokeEvent" invokes.
     * 2nd, Actuall page callback is called.
     * 3rd, "xhwlay_runner_post_invokeEvent" invokes.
     *
     * Argument of these hooks is also same of this method.
     *
     * @access protected
     * @param Xhwlay_Runner reference of runner instance
     * @param string Event name
     * @param Xhwlay_Bookmark reference.
     * @param mixed Event Info.
     * @return string Result of event (transit name)
     * @since 1.0.0
     */
    function invokeEvent(&$runner, $eventName, &$bookmark, $params)
    {
        $args = array(&$runner, $eventName, &$bookmark, $params);

        // PRE HOOK
        $h_pre =& Xhwlay_Hook::getInstance(
            "xhwlay_runner_pre_invokeEvent");
        $h_pre->setArgument($args);
        $h_pre->invoke();

        $result = false;
        $cb = $this->_getCallback($params);
        if (!is_null($cb)) {
            $result = call_user_func_array($cb, $args);
        }

        // POST HOOK
        $h_post =& Xhwlay_Hook::getInstance(
            "xhwlay_runner_post_invokeEvent");
        $h_post->setArgument($args);
        $h_post->invoke();

        return $result;
    }

    // }}}
    // {{{ invokeGuard()

    /**
     * Execute Guard action, return guard result.
     *
     * Guard interface is shown in following code:
     * <code>
     * function guard_func(&$runner, $eventName, &$bookmark, $params)
     * {
     *    ...
     *    // allow $event Event invocation
     *    return true;
     *    // disallow $event Event invocation
     *    return false;
     * }
     * </code>
     * Each argument is same of this method.
     *
     * And this method invokes PRE and POST hooks which name are:
     * - "xhwlay_runner_pre_invokeGuard"
     * - "xhwlay_runner_post_invokeGuard"
     *
     * 1st, "xhwlay_runner_pre_invokeGuard" invokes.
     * 2nd, Actuall barrier callback is called.
     * 3rd, "xhwlay_runner_post_invokeGuard" invokes.
     *
     * Argument of these hooks is also same of this method.
     *
     * @access protected
     * @param Xhwlay_Runner reference of runner instance
     * @param string Event name
     * @param Xhwlay_Bookmark reference
     * @param mixed Barrier Info (came from Config)
     * @return boolean
     * @since 1.0.0
     */
    function invokeGuard(&$runner, $eventName, &$bookmark, $params)
    {
        $args = array(&$runner, $eventName, &$bookmark, $params);

        // PRE HOOK
        $h_pre =& Xhwlay_Hook::getInstance(
            "xhwlay_runner_pre_invokeGuard");
        $h_pre->setArgument($args);
        $h_pre->invoke();

        $result = false;
        $cb = $this->_getCallback($params);
        if (!is_null($cb)) {
            $result = call_user_func_array($cb, $args);
        }

        // POST HOOK
        $h_post =& Xhwlay_Hook::getInstance(
            "xhwlay_runner_post_invokeGuard");
        $h_post->setArgument($args);
        $h_post->invoke();

        return $result;
    }

    // }}}
    // {{{ invokeBarrier()

    /**
     * Execute barrier action, return barrier result.
     *
     * Barrier interface is shown in following code:
     * <code>
     * function barrier_func(&$runner, $current, $next, &$bookmark, $params)
     * {
     *    ...
     *    // transition is OK from $current to $next
     *    return true;
     *    // transition is NG from $current to $next
     *    return false;
     * }
     * </code>
     * Each argument is same of this method.
     *
     * And this method invokes PRE and POST hooks which name are:
     * - "xhwlay_runner_pre_invokeBarrier"
     * - "xhwlay_runner_post_invokeBarrier"
     *
     * 1st, "xhwlay_runner_pre_invokeBarrier" invokes.
     * 2nd, Actuall barrier callback is called.
     * 3rd, "xhwlay_runner_post_invokeBarrier" invokes.
     *
     * Argument of these hooks is also same of this method.
     *
     * @access protected
     * @param Xhwlay_Runner reference of runner instance
     * @param string Bookmarked page name
     * @param string Next page name
     * @param Xhwlay_Bookmark reference
     * @param mixed Barrier Info (came from Config)
     * @return boolean
     * @since 1.0.0
     */
    function invokeBarrier(&$runner, $current, $next, &$bookmark, $params)
    {
        $args = array(&$runner, $current, $next, &$bookmark, $params);

        // PRE HOOK
        $h_pre =& Xhwlay_Hook::getInstance(
            "xhwlay_runner_pre_invokeBarrier");
        $h_pre->setArgument($args);
        $h_pre->invoke();

        $result = false;
        $cb = $this->_getCallback($params);
        if (!is_null($cb)) {
            $result = call_user_func_array($cb, $args);
        }

        // POST HOOK
        $h_post =& Xhwlay_Hook::getInstance(
            "xhwlay_runner_post_invokeBarrier");
        $h_post->setArgument($args);
        $h_post->invoke();

        return $result;
    }

    // }}}
    // {{{ _getCallback()

    /**
     * Get callback value for call_user_func_array() from $params.
     *
     * $params should be one of following two patterns.
     * <code>
     * // pt1. invoke user function
     * $params = array(
     *     'user_function' => (your function name),
     *     ...);
     * // pt2. invoke class method statically
     * $params = array(
     *     'class'= > (your class name),
     *     'method' => (method name of your class),
     *     ...);
     * </code>
     * How you configure your story configuration, see your Config class 
     * Documents.
     *
     * If both of pt1 and pt2 is defined, "user_function" is used.
     *
     * If valid callback value is not found in $params, some WARN level
     * message is thrown.
     *
     * @access protected
     * @param mixed Assoc array ('user_function', 'class', 'method')
     * @return callback If valid callback isn't found, return NULL.
     * @since 1.0.0
     */
    function _getCallback($params)
    {
        $h =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_CLASSLOAD);
        $h->setArgument(array($params));
        $h->invoke();

        if (isset($params['user_function']) &&
            is_callable($params['user_function'])) {
            return $params['user_function'];
        }
        if (!isset($params['class']) || !isset($params['method'])) {
            Xhwlay_ErrorStack::push(
                XHWLAY_RUNNER_EC_CLASS_OR_METHOD_NOT_DEFINED,
                "Class name or method name is not found in params.",
                XHWLAY_ERRORSTACK_EL_WARN,
                $params
                );
            return null;
        }
        $klass = $params['class'];
        $method = $params['method'];
        if (!class_exists($klass)) {
            Xhwlay_ErrorStack::push(
                XHWLAY_RUNNER_EC_CLASS_NOT_EXISTS,
                "Class[$klass] not exists",
                XHWLAY_ERRORSTACK_EL_WARN,
                $params
                );
            return null;
        }
        $methods = get_class_methods($klass);
        $_cb = array($klass, $method);
        if (is_callable($_cb)) {
            return $_cb;
        }
        Xhwlay_ErrorStack::push(
            XHWLAY_RUNNER_EC_METHOD_NOT_EXISTS,
            "Method[$method] not exists in class[$klass].",
            XHWLAY_ERRORSTACK_EL_WARN,
            $params
            );
        return null;
    }

    // }}}
    // {{{ setup()

    /**
     * Set up Xhwlay_Runner.
     *
     * By default, invoke "xhwlay_runner_setup" hook.
     * Developers <b>SHOULD</b> setup aci, pageName, bookmarkContainerId
     * at this hook point. (or, before calling run() method)
     *
     * Interface of hook is shown like below:
     * <code>
     * function setup_hook($hook_name, &$runner)
     * </code>
     * $hook_name is set to "xhwlay_runner_setup".
     * $runner is set to reference of Xhwlay_Runner instance.
     *
     * @access protected
     * @since 1.0.0
     */
    function setup()
    {
        $h =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_SETUP);
        $args = array(&$this);
        $h->setArgument($args);
        $h->invoke();
    }

    // }}}
    // {{{ terminate()

    /**
     * terminate event of Xhwlay_Runner.
     *
     * By default, invoke "xhwlay_runner_terminate" hook.
     * Interface of hook is shown like below:
     * <code>
     * function terminate_hook($hook_name, &$runner)
     * </code>
     * $hook_name is set to "xhwlay_runner_terminate".
     * $runner is set to reference of Xhwlay_Runner instance.
     *
     * @access protected
     * @since 1.0.0
     */
    function terminate()
    {
        $h =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_TERMINATE);
        $args = array(&$this);
        $h->setArgument($args);
        $h->invoke();
    }

    // }}}
    // {{{ filterPageName()

    /**
     * Page name trapper.
     *
     * This method provides a chanse for developers to customize 
     * page name.
     *
     * @access protected
     * @param string decided page name
     * @return string final page name
     * @since 1.0.0
     */
    function filterPageName($page)
    {
        return $page;
    }

    // }}}
    // {{{ filterViewName()

    /**
     * View name trapper.
     *
     * This method provides a chanse for developers to customize 
     * view name.
     *
     * @access protected
     * @param string decided view name
     * @return string final view name
     * @since 1.0.0
     */
    function filterViewName($view)
    {
        return $view;
    }

    // }}}
    // {{{ run()

    /**
     * Xhwlay Runner's entry point.
     *
     * Call xhwlay runner's core page flow processor.
     *
     * @access public
     * @return string output data. If NO-OUTPUT, then returns null.
     * @since 1.0.0
     */
    function run()
    {
        $this->setup();

        $output = null;
        if (!$this->_wipeout) {
            $output = $this->_run();
        }

        $this->terminate();

        if (is_object($this->_bookmarkContainer) && 
            $this->_bookmarkContainer->isGCTiming()) {
            $this->_bookmarkContainer->gc();
        }

        // restore controll flags to default.
        $this->_skip_page = false; // skip page invocation flag
        $this->_wipeout = false; // skip page flow processor flag

        return $output;
    }

    // }}}
    // {{{ _run()

    /**
     * Xhwlay Runner's core.
     *
     * Core page flow processor.
     *
     * @access protected
     * @return string output data. If NO-OUTPUT, then returns null.
     * @since 1.0.0
     */
    function _run()
    {
        $_is_first_created = false;
        $_requests = array(
            Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID),
            Xhwlay_Var::get(XHWLAY_VAR_KEY_ACI),
            Xhwlay_Var::get(XHWLAY_VAR_KEY_PAGE),
            Xhwlay_Var::get(XHWLAY_VAR_KEY_EVENT));
        // cleanup overlength or invalid characters.
        $_requests = array_map(array($this, '_cleanup_outparam'), $_requests);
        list($id, $aci, $pageName, $eventName) = $_requests;

        // backups raw-old values to request name space
        $__keys = array('BCID', 'ACI', 'PAGE', 'EVENT');
        foreach ($__keys as $__key) {
            $___k = constant('XHWLAY_VAR_KEY_' . $__key);
            $___v = Xhwlay_Var::get($___k);
            Xhwlay_Var::set($___k, $___v, XHWLAY_VAR_NAMESPACE_REQUEST);
        }

        // Current Story name in configuration
        $storyName = $this->_config->getStoryName();

        $pageBookmark = null;
        $needsBookmark = false;
        if ($this->_config->needsBookmark()) {
            // If bookmark is ON about current story.
            $needsBookmark = true;

            $klass = $this->_bookmarkContainerClassName;
            // create Bookmark Container instance
            $this->_bookmarkContainer =&
                new $klass($this->_bookmarkContainerParams, $id);

            if (!$this->_bookmarkContainer->load()) {
                return null;
            }
            $id = $this->_bookmarkContainer->getId();

            $pageBookmark =& 
                $this->_bookmarkContainer->getBookmark($storyName);

            if ($pageBookmark->first_created()) {
                $_is_first_created = true;
                // don't turn the page/invoke event.
                // simply, page name is start page.
                $pageName = '';
            } else {
                // if not first access, invoke event.
                if (!empty($eventName)) {
                    $pageName = $this->_triggerEvent($storyName);
                } else {
                    // Turn the page.
                    $pageName = $this->_turnPage($storyName);
                }
            }
        }

        $viewName = "";
        $pageName = $this->filterPageName($pageName);

        if ( !$this->_skip_page ) {
            // ready for page action
            $pageParams = $this->_config->getPageParams($pageName, $aci);
        }

        // reset Xhwlay_Var
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $id);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, $aci);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, $pageName);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, $eventName);

        if ( !$this->_skip_page ) {
            // invoke page action
            if ( !empty($pageParams) ) {
                $viewName = $this->invokePage(
                    $this, $pageName, $pageBookmark, $pageParams);
            }
            $viewName = $this->filterViewName($viewName);
        }

        $output = null;
        if ( !empty($viewName) ) {
            $this->_renderer->setViewName($viewName);
            Xhwlay_Var::set(
                XHWLAY_VAR_KEY_ACI, $aci, XHWLAY_VAR_NAMESPACE_VIEW);
            Xhwlay_Var::set(
                XHWLAY_VAR_KEY_PAGE, $pageName, XHWLAY_VAR_NAMESPACE_VIEW);
            Xhwlay_Var::set(
                XHWLAY_VAR_KEY_BCID, $id, XHWLAY_VAR_NAMESPACE_VIEW);
            Xhwlay_Var::set(
                XHWLAY_VAR_KEY_EVENT, $eventName, XHWLAY_VAR_NAMESPACE_VIEW);
            $output = $this->_renderer->render();
        }

        if ( $needsBookmark ) {
            // Re-get page name.
            $bookmark =& $this->_bookmarkContainer->getBookmark($storyName);
            $current = $bookmark->getPageName();

            if (!$_is_first_created && 
                $this->_config->isLastPage($current, $aci)) {
                $bookmark->destroy();
            }

            if ( $this->_bookmarkContainer->countBookmarks() ) {
                $this->_bookmarkContainer->save();
            } else {
                // if all bookmark reached to end page and empty, 
                // then clear bookmark container.
                $this->_bookmarkContainer->invalidate();
                $this->_bookmarkContainer->destroy();
            }
        }

        return $output;
    }

    // }}}
    // {{{ _triggerEvent()

    /**
     * Check requested page name is valid and decide page name.
     *
     * @access protected
     * @param string current story name
     * @return string page name
     * @since 1.0.0
     */
    function _triggerEvent($story)
    {
        // get bookmark about current story
        $bookmark =& $this->_bookmarkContainer->getBookmark($story);

        $event = Xhwlay_Var::get(XHWLAY_VAR_KEY_EVENT);
        $aci = Xhwlay_Var::get(XHWLAY_VAR_KEY_ACI);

        // get "current" page name saved in bookmark
        $page = $bookmark->getPageName();

        if ( !$this->_config->isEventOf($page, $event, $aci) ) {
            // requested event name is not valid events of current page.

            Xhwlay_ErrorStack::push(
                XHWLAY_RUNNER_EC_NOT_VALID_EVENT,
                "Invalid Event in [$page]",
                XHWLAY_ERRORSTACK_EL_DEBUG,
                array("event" => $event, "page" => $page)
                );
            // return current page name
            return $page;
        }

        // get Guard parameters of requested Event
        $guardParams = $this->_config->getGuardParams($page, $event, $aci);

        if ( !empty($guardParams) &&
            !$this->invokeGuard($this, $event, $bookmark, $guardParams) ) {
            // If guard is defined and guard results disallowing transition,
            // returns current page.

            Xhwlay_ErrorStack::push(
                XHWLAY_RUNNER_EC_GUARD_DISALLOW_EVENT_INVOCATION,
                "Guard denied event invocation.",
                XHWLAY_ERRORSTACK_EL_DEBUG,
                array(
                    "guard" => $guardParams,
                    "page" => $page,
                    "event" => $event,
                )
                );
            return $page;
        }

        // If guard is not defined or guard results transition, 
        // invoke event.
        $eventParams = $this->_config->getEventParams($event);
        if (empty($eventParams)) {
            // If event is not defined, return current page.
            return $page;
        }

        // invoke event and get event returns (=transition)
        $transition =
            $this->invokeEvent($this, $event, $bookmark, $eventParams);

        if ( empty($transition) ||
            !isset($eventParams['transit'][$transition]) ) {
            // If transition is empty or undefined, return current page.
            return $page;
        }

        $new_page = $eventParams['transit'][$transition];
        // save new page name
        $bookmark->setPageName($new_page);
        // returns page name of transition
        return $new_page;
    }

    // }}}
    // {{{ _turnPage()

    /**
     * Check requested page name is valid and decide page name.
     *
     * @access protected
     * @param string current story name
     * @return string page name
     * @since 1.0.0
     */
    function _turnPage($story)
    {
        // get bookmark about current story
        $bookmark =& $this->_bookmarkContainer->getBookmark($story);

        $request = Xhwlay_Var::get(XHWLAY_VAR_KEY_PAGE);
        $aci = Xhwlay_Var::get(XHWLAY_VAR_KEY_ACI);

        // get "current" page name saved in bookmark
        $current = $bookmark->getPageName();

        if ( empty($request) || $current == $request) {
            // Return current page name if page name is not requested 
            // or requested page name equals current page name.
            return $current;
        }

        /*
         * If requested page is new page name, then ...
         */

        if ( !$this->_config->isNextPageOf($current, $request, $aci) ) {
            // requested page name is not valid transition targets 
            // of current page.

            Xhwlay_ErrorStack::push(
                XHWLAY_RUNNER_EC_NOT_VALID_TRANSITION,
                "Invalid Next page from [$current]",
                XHWLAY_ERRORSTACK_EL_DEBUG,
                array("current" => $current, "next" => $request)
                );

            // return current page name
            return $current;
        }

        // get Barrier parameters of transition 
        // from current to requested next page.
        $barrierParams = $this->_config->getBarrierParams(
            $current, $request, $aci);

        if ( empty($barrierParams) ) {
            // If barrier is not defined, no check, return requested page name.
            // Save next page name in bookmark.
            $bookmark->setPageName($request);
            return $request;
        }

        if ( !$this->invokeBarrier(
            $this, $current, $request, $bookmark, $barrierParams) ) {
            // If barrier results disallowing transition, 
            // returns current page.
            Xhwlay_ErrorStack::push(
                XHWLAY_RUNNER_EC_BARRIER_RESULTS_NOT_NEXT,
                "Barrier denied page transitioin.",
                XHWLAY_ERRORSTACK_EL_DEBUG,
                array(
                    "barrier" => $barrierParams,
                    "current" => $current,
                    "request" => $request,
                )
                );
            return $current;
        }

        // If barrier returns true, save next page in bookmark, 
        // returns requested page name.
        $bookmark->setPageName($request);
        return $request;
    }

    // }}}
    // {{{ _cleanup_outparam()

    /**
     * Clean up ACI, pageName, BookmarkContainerId strings.
     *
     * @access protected
     * @param string value which came from out of codes.
     * @param integer maximum length of value. If omitted, 
     *                XHWLAY_RUNNER_OUTPARAM_MAX_LEN is assumed.
     * @return string clean uped string
     * @since 1.0.0
     */
    function _cleanup_outparam($v, $len = XHWLAY_RUNNER_OUTPARAM_MAX_LEN)
    {
        $pattern = '/^[0-9A-Za-z_\-,]{1,' . $len . '}$/';
        return preg_match($pattern, $v) ? $v : "";
    }

    // }}}
}

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
