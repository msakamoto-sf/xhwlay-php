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
 * @package Xhwlay
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Xhwlay_Runner_TestCase.php 55 2008-02-20 01:35:44Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/Bookmark/FileStoreContainer.php");
require_once("Xhwlay/Renderer/Serialize.php");
require_once("Xhwlay/Config/PHPArray.php");
require_once("Xhwlay/Runner.php");

// {{{ Test_PageFunc()/Test_EventFunc()/Test_BarrierFunc()/Test_GuardFunc()

$GLOBALS['Test_PageFunc_DebugVar'] = array();
function Test_PageFunc(&$runner, $page, &$bookmark, $params)
{
    $GLOBALS['Test_PageFunc_DebugVar']['page'] = $page;
    $GLOBALS['Test_PageFunc_DebugVar']['bookmark'] = &$bookmark;
    $GLOBALS['Test_PageFunc_DebugVar']['params'] = $params;
    if (isset($GLOBALS['Test_PageFunc_DebugVar']['return'])) {
        return $GLOBALS['Test_PageFunc_DebugVar']['return'];
    } else {
        return null;
    }
}

$GLOBALS['Test_EventFunc_DebugVar'] = array();
function Test_EventFunc(&$runner, $event, &$bookmark, $params)
{
    $GLOBALS['Test_EventFunc_DebugVar']['event'] = $event;
    $GLOBALS['Test_EventFunc_DebugVar']['bookmark'] = &$bookmark;
    $GLOBALS['Test_EventFunc_DebugVar']['params'] = $params;
    if (isset($GLOBALS['Test_EventFunc_DebugVar']['return'])) {
        return $GLOBALS['Test_EventFunc_DebugVar']['return'];
    } else {
        return null;
    }
}

$GLOBALS['Test_BarrierFunc_DebugVar'] = array();
function Test_BarrierFunc(&$runner, $current, $next, &$bookmark, $params)
{
    $GLOBALS['Test_BarrierFunc_DebugVar']['current'] = $current;
    $GLOBALS['Test_BarrierFunc_DebugVar']['next'] = $next;
    $GLOBALS['Test_BarrierFunc_DebugVar']['bookmark'] = &$bookmark;
    $GLOBALS['Test_BarrierFunc_DebugVar']['params'] = $params;
    if (isset($GLOBALS['Test_BarrierFunc_DebugVar']['return'])) {
        return $GLOBALS['Test_BarrierFunc_DebugVar']['return'];
    } else {
        return false;
    }
}

$GLOBALS['Test_GuardFunc_DebugVar'] = array();
function Test_GuardFunc(&$runner, $event, &$bookmark, $params)
{
    $GLOBALS['Test_GuardFunc_DebugVar']['event'] = $event;
    $GLOBALS['Test_GuardFunc_DebugVar']['bookmark'] = &$bookmark;
    $GLOBALS['Test_GuardFunc_DebugVar']['params'] = $params;
    if (isset($GLOBALS['Test_GuardFunc_DebugVar']['return'])) {
        return $GLOBALS['Test_GuardFunc_DebugVar']['return'];
    } else {
        return false;
    }
}

// }}}
// {{{ Xhwlay_Runner_callbacks

class Xhwlay_Runner_callbacks
{
    /**
     * @access public
     */
    var $debug_var = array();

    // {{{ getInstance()

    function &getInstance()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new Xhwlay_Runner_callbacks();
        }
        return $instance;
    }

    // }}}
    // {{{ reset()

    function reset()
    {
        $this->debug_var = array();
    }

    // }}}
    // {{{ page()

    function page(&$runner, $page, &$bookmark, $params)
    {
        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $xrc->debug_var['page']['page'] = $page;
        $xrc->debug_var['page']['bookmark'] = &$bookmark;
        $xrc->debug_var['page']['params'] = $params;
        if (isset($xrc->debug_var['page']['return'])) {
            return $xrc->debug_var['page']['return'];
        } else {
            return null;
        }
    }

    // }}}
    // {{{ event()

    function event(&$runner, $event, &$bookmark, $params)
    {
        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $xrc->debug_var['event']['event'] = $event;
        $xrc->debug_var['event']['bookmark'] = &$bookmark;
        $xrc->debug_var['event']['params'] = $params;
        if (isset($xrc->debug_var['event']['return'])) {
            return $xrc->debug_var['event']['return'];
        } else {
            return null;
        }
    }

    // }}}
    // {{{ barrier()

    function barrier(&$runner, $current, $next, &$bookmark, $params)
    {
        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $xrc->debug_var['barrier']['current'] = $current;
        $xrc->debug_var['barrier']['next'] = $next;
        $xrc->debug_var['barrier']['bookmark'] = &$bookmark;
        $xrc->debug_var['barrier']['params'] = $params;
        if (isset($xrc->debug_var['barrier']['return'])) {
            return $xrc->debug_var['barrier']['return'];
        } else {
            return false;
        }
    }

    // }}}
    // {{{ guard()

    function guard(&$runner, $event, &$bookmark, $params)
    {
        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $xrc->debug_var['guard']['event'] = $event;
        $xrc->debug_var['guard']['bookmark'] = &$bookmark;
        $xrc->debug_var['guard']['params'] = $params;
        if (isset($xrc->debug_var['guard']['return'])) {
            return $xrc->debug_var['guard']['return'];
        } else {
            return false;
        }
    }

    // }}}
    // {{{ event_skippage()

    function event_skippage(&$runner, $event, &$bookmark, $params)
    {
        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $xrc->debug_var['event']['event'] = $event;
        $xrc->debug_var['event']['bookmark'] = &$bookmark;
        $xrc->debug_var['event']['params'] = $params;

        $runner->skipPage();

        if (isset($xrc->debug_var['event']['return'])) {
            return $xrc->debug_var['event']['return'];
        } else {
            return null;
        }
    }

    // }}}
    // {{{ barrier_skippage()

    function barrier_skippage(&$runner, $current, $next, &$bookmark, $params)
    {
        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $xrc->debug_var['barrier']['current'] = $current;
        $xrc->debug_var['barrier']['next'] = $next;
        $xrc->debug_var['barrier']['bookmark'] = &$bookmark;

        $runner->skipPage();

        $xrc->debug_var['barrier']['params'] = $params;
        if (isset($xrc->debug_var['barrier']['return'])) {
            return $xrc->debug_var['barrier']['return'];
        } else {
            return false;
        }
    }

    // }}}
}

// }}}
// {{{ Xhwlay_Runner_filterTest

class Xhwlay_Runner_filterTest extends Xhwlay_Runner
{
    var $debug_var = array();

    function filterPageName($page)
    {
        $this->debug_var['filterPageName'] = $page;
        if (isset($this->debug_var['return']['filterPageName'])) {
            return $this->debug_var['return']['filterPageName'];
        } else {
            return $page;
        }
    }

    function filterViewName($view)
    {
        $this->debug_var['filterViewName'] = $view;
        if (isset($this->debug_var['return']['filterViewName'])) {
            return $this->debug_var['return']['filterViewName'];
        } else {
            return $view;
        }
    }
}

// }}}
// {{{ Xhwlay_Runner_cleanup_outparamTest

class Xhwlay_Runner_cleanup_outparamTest extends Xhwlay_Runner
{
    function cleanup_outparam($v, $l = XHWLAY_RUNNER_OUTPARAM_MAX_LEN)
    {
        return $this->_cleanup_outparam($v, $l);
    }
}

// }}}
// {{{ Xhwlay_Runner_hooks

class Xhwlay_Runner_hooks
{
    /**
     * @access public
     */
    var $debug_var = array();

    // {{{ getInstance()

    function &getInstance()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new Xhwlay_Runner_hooks();
        }
        return $instance;
    }

    // }}}
    // {{{ reset()

    function reset()
    {
        $this->debug_var = array();
    }

    // }}}
    // {{{ setup()

    function setup($hook, &$runner)
    {
        $xrh =& Xhwlay_Runner_hooks::getInstance();
        $xrh->debug_var['setup']['hook'] = $hook;
    }

    // }}}
    // {{{ terminate()

    function terminate($hook, &$runner)
    {
        $xrh =& Xhwlay_Runner_hooks::getInstance();
        $xrh->debug_var['terminate']['hook'] = $hook;
    }

    // }}}
    // {{{ classload()

    function classload($hook, $params)
    {
        $xrh =& Xhwlay_Runner_hooks::getInstance();
        $xrh->debug_var['classload']['hook'] = $hook;
        $xrh->debug_var['classload']['params'] = $params;
    }

    // }}}
    // {{{ setup_wipeout()

    function setup_wipeout($hook, &$runner)
    {
        $xrh =& Xhwlay_Runner_hooks::getInstance();
        $xrh->debug_var['setup_wipeout']['hook'] = $hook;

        $runner->wipeout();
    }

    // }}}
}

// }}}

Mock::generate('Xhwlay_Renderer_AbstractRenderer');
$GLOBALS['Xhwlay_Runner_TestCase']['bcparams'] = array(
    "dataDir" => dirname(__FILE__).'/datas',
    "identKeys" => array(),
    "expire" => 3600,
    "gc_probability" => 1,
    "gc_divisor" => 1,
    "gc_maxlifetime" => 30,
);

class Xhwlay_Runner_TestCase extends UnitTestCase
{

    // {{{ handleErrorStack($error)

    function handleErrorStack($error)
    {
        return PEAR_ERRORSTACK_PUSH;
    }

    // }}}
    // {{{ handleErrorStack_IgnoreInfoDebug($error)

    function handleErrorStack_IgnoreInfoDebug($error)
    {
        switch($error['level']) {
            case XHWLAY_ERRORSTACK_EL_DEBUG:
            case XHWLAY_ERRORSTACK_EL_INFO:
                return PEAR_ERRORSTACK_IGNORE;
            default:
                return PEAR_ERRORSTACK_PUSH;
        }
    }

    // }}}
    // {{{ testBookmarkOff_SetupAndTerminateCalled

    function testBookmarkOff_SetupAndTerminateCalled()
    {
        // Flow: simple transition, no barriers.
        $configP = array(
            "story" => array(
                "name" => "Test Story",
                "bookmark" => "off",
                ),
            );
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");
        $xrh =& Xhwlay_Runner_hooks::getInstance();
        $xrh->reset();

        // push callbacks to setup/terminate hook
        $h1 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_SETUP);
        $h1->pushCallback(array('Xhwlay_Runner_hooks', 'setup'));
        $h2 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_TERMINATE);
        $h2->pushCallback(array('Xhwlay_Runner_hooks', 'terminate'));

        $runner =& $this->_createRunner($configP);
        $runner->run();

        // check setup/terminate hooks are invoked.
        $this->assertEqual($xrh->debug_var['setup']['hook'],
            XHWLAY_RUNNER_HOOK_SETUP);
        $this->assertEqual($xrh->debug_var['terminate']['hook'],
            XHWLAY_RUNNER_HOOK_TERMINATE);
    }

    // }}}
    // {{{ testBookmarkOff_ClassLoadHookCalled

    function testBookmarkOff_ClassLoadHookCalled()
    {
        // Flow: simple transition, no barriers.
        $configP = array(
            "story" => array(
                "name" => "Test Story",
                "bookmark" => "off",
                ),
            "page" => array(
                "*" => array(
                    "user_function" => "Test_PageFunc",
                    "class" => "Xhwlay_Runner_callbacks",
                    "method" => "page",
                    ),
                ),
            );
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");
        $xrh =& Xhwlay_Runner_hooks::getInstance();
        $xrh->reset();

        // push callbacks to classload hook
        $h1 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_CLASSLOAD);
        $h1->pushCallback(array('Xhwlay_Runner_hooks', 'classload'));

        $runner =& $this->_createRunner($configP);
        $runner->run();

        // check classload hooks are invoked.
        $this->assertEqual($xrh->debug_var['classload']['hook'],
            XHWLAY_RUNNER_HOOK_CLASSLOAD);
        $params = $xrh->debug_var['classload']['params'];
        $this->assertEqual($params['user_function'], 'Test_PageFunc');
        $this->assertEqual($params['class'], 'Xhwlay_Runner_callbacks');
        $this->assertEqual($params['method'], 'page');
    }

    // }}}
    // {{{ testBookmarkOff_PageAndViewNameFiltered

    function testBookmarkOff_PageAndViewNameFiltered()
    {
        // Flow: simple transition, no barriers.
        $configP = array(
            "story" => array(
                "name" => "Test Story",
                "bookmark" => "off",
                ),
            "page" => array(
                "page2.aci1" => array(
                    "user_function" => "Test_PageFunc",
                    ),
                ),
            );

        $bcid = "id1";
        $aci = "aci1";
        // request page is "page1". After filtering, result will be "page2"
        $page_before = "page1";
        $page_after = "page2";
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, $aci);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, $page_before);

        $view_before = "view1";
        // set page returns "view1"
        $GLOBALS['Test_PageFunc_DebugVar']['return'] = $view_before;

        $view_after = "view2";

        $renderer =& new MockXhwlay_Renderer_AbstractRenderer();
        $config =& new Xhwlay_Config_PHPArray($configP);
        $runner =& new Xhwlay_Runner_filterTest();
        $runner->setConfig($config);
        $runner->setRenderer($renderer);

        // set filtered page name
        $runner->debug_var['return']['filterPageName'] = $page_after;
        // set filtered view name
        $runner->debug_var['return']['filterViewName'] = $view_after;

        $runner->run();

        // {{{ [0] : Check Xhwlay_Var was set correctly
        $this->assertEqual(
            Xhwlay_Var::get(XHWLAY_VAR_KEY_ACI, XHWLAY_VAR_NAMESPACE_VIEW),
            $aci);
        $this->assertEqual(
            Xhwlay_Var::get(XHWLAY_VAR_KEY_PAGE, XHWLAY_VAR_NAMESPACE_VIEW),
            $page_after);
        $this->assertEqual(
            Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID, XHWLAY_VAR_NAMESPACE_VIEW),
            $bcid);
        // }}}
        // {{{ [1] : Check filter methods retrieve correct 'name's
        $vars = $runner->debug_var;
        $vars['filterPageName'] = $page_before;
        $vars['filterViewName'] = $view_before;
        // }}}
        // {{{ [2] : Check view name was filtered and render() was called.
        $renderer->expectOnce('setViewName', array($view_after));
        $renderer->expectOnce('render');
        // }}}
        // {{{ [3] : Check filtered page name was passed to page callback.
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'],
            $page_after);
        $this->assertNull(
            $GLOBALS['Test_PageFunc_DebugVar']['bookmark']);
        // }}}

        $renderer->tally();
    }

    // }}}
    // {{{ testBookmarkOff_cleanup_outparam

    function testBookmarkOff_cleanup_outparam()
    {
        // Flow: simple transition, no barriers.
        $configP = array(
            "story" => array(
                "name" => "Test Story",
                "bookmark" => "off",
                ),
            "page" => array(
                "*.*" => array(
                    "user_function" => "Test_PageFunc",
                    ),
                ),
            );

        // set INVALID request
        $bcid = "//";
        $aci = "//";
        $page= "//";
        $event = "//";
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, $aci);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, $page);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, $event);

        $view = "view1";
        $GLOBALS['Test_PageFunc_DebugVar']['return'] = $view;
        $renderer =& new Xhwlay_Renderer_Serialize();
        $config =& new Xhwlay_Config_PHPArray($configP);
        $runner =& new Xhwlay_Runner_cleanup_outparamTest();
        $runner->setConfig($config);
        $runner->setRenderer($renderer);
        $runner->run();

        // {{{ [0] : Check invalid request is altered to ""

        // old values is backuped in request name space
        $this->assertEqual(Xhwlay_Var::get(
            XHWLAY_VAR_KEY_ACI, XHWLAY_VAR_NAMESPACE_REQUEST),
            $aci);
        $this->assertEqual(Xhwlay_Var::get(
            XHWLAY_VAR_KEY_PAGE, XHWLAY_VAR_NAMESPACE_REQUEST),
            $page);
        $this->assertEqual(Xhwlay_Var::get(
            XHWLAY_VAR_KEY_EVENT, XHWLAY_VAR_NAMESPACE_REQUEST),
            $event);
        $this->assertEqual(Xhwlay_Var::get(
            XHWLAY_VAR_KEY_BCID, XHWLAY_VAR_NAMESPACE_REQUEST),
            $bcid);

        // default name space
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_ACI), "");
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_PAGE), "");
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_EVENT), "");
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), "");

        // view name spaece
        $this->assertEqual(Xhwlay_Var::get(
            XHWLAY_VAR_KEY_ACI, XHWLAY_VAR_NAMESPACE_VIEW),
            "");
        $this->assertEqual(Xhwlay_Var::get(
            XHWLAY_VAR_KEY_PAGE, XHWLAY_VAR_NAMESPACE_VIEW),
            "");
        $this->assertEqual(Xhwlay_Var::get(
            XHWLAY_VAR_KEY_EVENT, XHWLAY_VAR_NAMESPACE_VIEW),
            "");
        $this->assertEqual(Xhwlay_Var::get(
            XHWLAY_VAR_KEY_BCID, XHWLAY_VAR_NAMESPACE_VIEW),
            "");

        // }}}
        // {{{ [1] : Check request length

        // custom length (normal)
        $str = str_repeat("A", 3);
        $this->assertEqual($runner->cleanup_outparam($str, 3), $str);
        // xhwlay default length (normal)
        $str = str_repeat("A", XHWLAY_RUNNER_OUTPARAM_MAX_LEN);
        $this->assertEqual($runner->cleanup_outparam($str), $str);

        // custom length
        $over_length = str_repeat("A", 3);
        $this->assertEqual($runner->cleanup_outparam($over_length, 2), "");
        // xhwlay default length
        $over_length = str_repeat("A", XHWLAY_RUNNER_OUTPARAM_MAX_LEN + 1);
        $this->assertEqual($runner->cleanup_outparam($over_length), "");

        // }}}
        // {{{ [2] : Check legal characters detection
        $str = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $this->assertEqual($runner->cleanup_outparam($str), $str);
        $str = "abcdefghijklmnopqrstuvwxyz_-,";
        $this->assertEqual($runner->cleanup_outparam($str), $str);
        // }}}
        // {{{ [3] : Check illegal characters detection
        $str = "0%00ABC";
        $str = rawurldecode($str);
        $this->assertEqual($runner->cleanup_outparam($str), "");

        $str = "0%20ABC";
        $str = rawurldecode($str);
        $this->assertEqual($runner->cleanup_outparam($str), "");

        $str = "0/ABC";
        $this->assertEqual($runner->cleanup_outparam($str), "");
        $str = "0.ABC";
        $this->assertEqual($runner->cleanup_outparam($str), "");
        $str = "0 ABC";
        $this->assertEqual($runner->cleanup_outparam($str), "");
        $str = "0\rABC";
        $this->assertEqual($runner->cleanup_outparam($str), "");
        $str = "0\nABC";
        $this->assertEqual($runner->cleanup_outparam($str), "");
        $str = "0\r\nABC";
        $this->assertEqual($runner->cleanup_outparam($str), "");
        $str = "0\tABC";
        $this->assertEqual($runner->cleanup_outparam($str), "");
        // }}}
    }

    // }}}
    // {{{ [SP1] (Story Pattern 1: simple) Definition

    /**
     * route:
     * (1) Page oriented:
     * * -> page1 -> * -> barrier1 -> * -> barrier1-> page2 (terminate)
     * (2) Event oriented:
     * * -> event1 -> page3(*) -> event1 -> * -> event1 -> page1 ->
     *      guard1 -> page1 -> guard1 -> event2 -> * -> guard2 -> * ->
     *      event3 -> page2 (terminate)
     */
    var $config_pattern1 = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "on",
            ),
        "page" => array(
            "page2" => array(
                "user_function" => "Test_PageFunc",
                "bookmark" => "last",
                ),
            "page1" => array(
                "user_function" => "Test_PageFunc",
                "next" => array(
                    "page3" => null,
                    ),
                "event" => array(
                    "event2" => "guard1",
                    ),
                ),
            "*" => array(
                "user_function" => "Test_PageFunc",
                "next" => array(
                    "page1" => null,
                    "page2" => "barrier1",
                    ),
                "event" => array(
                    "event1" => null,
                    "event3" => "guard2",
                    ),
                ),
            ),
        "event" => array(
            "event1" => array(
                "user_function" => "Test_EventFunc",
                "transit" => array(
                    "transit1" => "page3",
                    "transit2" => "page1",
                    "transit3" => "*",
                    ),
                ),
            "event2" => array(
                "user_function" => "Test_EventFunc",
                "transit" => array(
                    "transit1" => "*",
                    ),
                ),
            "event3" => array(
                "user_function" => "Test_EventFunc",
                "transit" => array(
                    "transit1" => "page2",
                    ),
                ),
            ),
        "barrier" => array(
            "barrier1" => array("user_function" => "Test_BarrierFunc"),
            ),
        "guard" => array(
            "guard1" => array("user_function" => "Test_GuardFunc"),
            "guard2" => array("user_function" => "Test_GuardFunc"),
            ),
        );

    // }}}
    // {{{ testSP1_route1(Page Oriented Flow)

    function testSP1_route1()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern1);

        // {{{ [0] "*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        // }}}
        // {{{ [1] "*" -> "page1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page1");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page1");
        // }}}
        // {{{ [2] "page1" -> "*"(page3)
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page3");
        $runner->run();
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page3");
        // }}}
        // {{{ [3] "*"(page3) -> "barrier1" -> "*"
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page2");
        $runner->run();
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page3");
        $this->assertEqual(
            $GLOBALS['Test_BarrierFunc_DebugVar']['current'], "page3");
        $this->assertEqual(
            $GLOBALS['Test_BarrierFunc_DebugVar']['next'], "page2");
        $bookmark =& $GLOBALS['Test_BarrierFunc_DebugVar']['bookmark'];
        $this->assertEqual($bookmark->getContainerId(), $bcid);
        // }}}
        // {{{ [4] "*"(page3) -> "barrier1" -> "page2"
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar']['return'] = true;
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page2");
        $runner->run();
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page2");
        $this->assertEqual(
            $GLOBALS['Test_BarrierFunc_DebugVar']['current'], "page3");
        $this->assertEqual(
            $GLOBALS['Test_BarrierFunc_DebugVar']['next'], "page2");
        // }}}
        // {{{ [5] Check Bookmark file is deleted
        $bookmarkContainer =& new Xhwlay_Bookmark_FileStoreContainer(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams'], $bcid);
        $this->assertFalse($bookmarkContainer->fileExists());
        // }}}
        // {{{ [6] "page2" -> ... reset to "*" (bookmark was cleared)
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page2");
        $runner->run();
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual($bookmark->getContainerId(), $bcid);
        // }}}
        // {{{ [7] Check Bookmark file is created and delete it.
        $bookmarkContainer =& new Xhwlay_Bookmark_FileStoreContainer(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams'], $bcid);
        $this->assertTrue($bookmarkContainer->fileExists());
        $bookmarkContainer->destroy();
        $this->assertFalse($bookmarkContainer->fileExists());
        // }}}
    }

    // }}}
    // {{{ testSP1_route2(Event Oriented Flow)

    function testSP1_route2()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern1);

        // {{{ [0] "*" (-> event4(undef) -> "*")
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");

        // (-> event4(undef) -> "*")
        Xhwlay_ErrorStack::pushCallback(
            array('Xhwlay_Runner_Test', 'handleErrorStack'));
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event4");
        $runner->run();
        // check Event arguments are correct
        $this->assertEqual(
            count($GLOBALS['Test_EventFunc_DebugVar']), 0);
        // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");

        $error = Xhwlay_ErrorStack::pop(); // "bookmark save succeeded"
        if ($error['code'] == XHWLAY_BOOKMARK_CONTAINER_EC_GC_INVOKED) {
            Xhwlay_ErrorStack::pop();
        }
        $error = Xhwlay_ErrorStack::pop();
        $this->assertEqual($error['level'], XHWLAY_ERRORSTACK_EL_DEBUG);
        $this->assertEqual($error['code'], XHWLAY_RUNNER_EC_NOT_VALID_EVENT);
        Xhwlay_ErrorStack::popCallback();
        // }}}
        // {{{ [1] "*" -> event1 -> "*"(page3)
        $GLOBALS['Test_EventFunc_DebugVar']['return'] = "transit1";
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $runner->run();
        // check Event arguments are correct
        $bookmark =& $GLOBALS['Test_EventFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_EventFunc_DebugVar']['event'], "event1");
        // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page3");
        // }}}
        // {{{ [2] "*"(page3) -> event1 -> "*"
        $GLOBALS['Test_EventFunc_DebugVar']['return'] = "transit3";
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $runner->run();
        // check Event arguments are correct
        $bookmark =& $GLOBALS['Test_EventFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_EventFunc_DebugVar']['event'], "event1");
        // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "*");
        // }}}
        // {{{ [3] "*" -> event1 -> "page1"
        $GLOBALS['Test_EventFunc_DebugVar']['return'] = "transit2";
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $runner->run();
        // check Event arguments are correct
        $bookmark =& $GLOBALS['Test_EventFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_EventFunc_DebugVar']['event'], "event1");
        // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page1");
        // }}}
        // {{{ [4] "page1" -> guard1 -> "page1"
        Xhwlay_ErrorStack::clear();
        Xhwlay_ErrorStack::pushCallback(
            array('Xhwlay_Runner_Test', 'handleErrorStack'));
        $GLOBALS['Test_GuardFunc_DebugVar']['return'] = false;
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event2");
        $runner->run();
        // check Guard arguments are correct
        $bookmark =& $GLOBALS['Test_GuardFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_GuardFunc_DebugVar']['event'], "event2");
        // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page1");

        $error = Xhwlay_ErrorStack::pop(); // "bookmark save succeeded"
        if ($error['code'] == XHWLAY_BOOKMARK_CONTAINER_EC_GC_INVOKED) {
            Xhwlay_ErrorStack::pop();
        }
        $error = Xhwlay_ErrorStack::pop();
        $this->assertEqual($error['level'], XHWLAY_ERRORSTACK_EL_DEBUG);
        $this->assertEqual($error['code'],
            XHWLAY_RUNNER_EC_GUARD_DISALLOW_EVENT_INVOCATION);
        Xhwlay_ErrorStack::popCallback();
        // }}}
        // {{{ [5] "page1" -> guard1 -> event2 -> "*"
        Xhwlay_ErrorStack::clear();
        $GLOBALS['Test_GuardFunc_DebugVar']['return'] = true;
        $GLOBALS['Test_EventFunc_DebugVar']['return'] = "transit1";
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event2");
        $runner->run();
        // check Guard arguments are correct
        $bookmark =& $GLOBALS['Test_GuardFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_GuardFunc_DebugVar']['event'], "event2");
        // check Event arguments are correct
        $bookmark =& $GLOBALS['Test_EventFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_EventFunc_DebugVar']['event'], "event2");
         // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "*");
        // }}}
        // {{{ [6] "*" -> guard2 -> "*"
        Xhwlay_ErrorStack::clear();
        $GLOBALS['Test_GuardFunc_DebugVar']['return'] = false;
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event3");
        $runner->run();
        // check Guard arguments are correct
        $bookmark =& $GLOBALS['Test_GuardFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_GuardFunc_DebugVar']['event'], "event3");
         // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "*");
        // }}}
        // {{{ [7] "*" -> guard2 -> event3 -> "page2"(terminated)
        Xhwlay_ErrorStack::clear();
        $GLOBALS['Test_GuardFunc_DebugVar']['return'] = true;
        $GLOBALS['Test_EventFunc_DebugVar']['return'] = "transit1";
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event3");
        $runner->run();
        // check Guard arguments are correct
        $bookmark =& $GLOBALS['Test_GuardFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_GuardFunc_DebugVar']['event'], "event3");
        // check Event arguments are correct
        $bookmark =& $GLOBALS['Test_EventFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_EventFunc_DebugVar']['event'], "event3");
         // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page2");
        // }}}
        // {{{ [8] Check Bookmark file is deleted
        $bookmarkContainer =& new Xhwlay_Bookmark_FileStoreContainer(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams'], $bcid);
        $this->assertFalse($bookmarkContainer->fileExists());
        // }}}
        // {{{ [9] "page2" -> ... reset to "*" (bookmark was cleared)
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page2");
        $runner->run();
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual($bookmark->getContainerId(), $bcid);
        // }}}
        // {{{ [10] Check Bookmark file is created and delete it.
        $bookmarkContainer =& new Xhwlay_Bookmark_FileStoreContainer(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams'], $bcid);
        $this->assertTrue($bookmarkContainer->fileExists());
        $bookmarkContainer->destroy();
        $this->assertFalse($bookmarkContainer->fileExists());
        // }}}
    }

    // }}}
    // {{{ [SP2] (Story Pattern 2: NO Bookmark + Page Oriented + ACI) Definition

    /**
     * route:
     * (1) ACI = ""
     * *.* -> page1.* -> page2.* (fallbacks to *.*)
     * (2) ACI = "aci1"
     * *.aci1 -> page1.aci1 -> page2.aci1
     */
    var $config_pattern2 = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "off",
            ),
        "page" => array(
            "page2.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page2.aci1",
                ),
            "page1.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page1.aci1",
                ),
            "page1.*" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page1.*",
                ),
            "*.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "*.aci1",
                ),
            "*.*" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "*.*",
                ),
            ),
        );

    // }}}
    // {{{ testSP2_route1(ACI = "")

    function testSP2_route1()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern2);

        // {{{ [0] "*.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
        // {{{ [1] "*.*" -> "page1.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page1");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page1");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "page1.*");
        // }}}
        // {{{ [2] "page1.*" -> "page2.*" (fallbacks to "*.*")
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page2");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page2");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
    }

    // }}}
    // {{{ testSP2_route2(ACI = "aci1")

    function testSP2_route2()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern2);

        // {{{ [0] "*.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.aci1");
        // }}}
        // {{{ [1] "*.aci1" -> "page1.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page1");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page1");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "page1.aci1");
        // }}}
        // {{{ [2] "page1.aci1" -> "page2.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page2");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page2");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "page2.aci1");
        // }}}
    }

    // }}}
    // {{{ [SP3] (Story Pattern 3: Bookmark + Page Oriented + ACI) Definition

    /**
     * route:
     * (1) ACI = ""
     * *.* -> page1.* -> page2.* (fallbacks to *.*)
     * (2) ACI = "aci1"
     * *.aci1 -> page1.aci1 -> page2.aci1
     */
    var $config_pattern3 = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "on",
            ),
        "page" => array(
            "page2.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page2.aci1",
                ),
            "page1.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page1.aci1",
                "next" => array(
                    "page2" => null,
                    ),
                ),
            "page1.*" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page1.*",
                "next" => array(
                    "page2" => null,
                    ),
                ),
            "*.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "*.aci1",
                "next" => array(
                    "page1" => null,
                    ),
                ),
            "*.*" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "*.*",
                "next" => array(
                    "page1" => null,
                    ),
                ),
            ),
        );

    // }}}
    // {{{ testSP3_route1(ACI = "")

    function testSP3_route1()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern3);

        // {{{ [0] "*.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
        // {{{ [1] "*.*" -> "page1.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page1");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page1");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "page1.*");
        // }}}
        // {{{ [2] "page1.*" -> "page2.*" (fallbacks to "*.*")
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page2");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page2");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
        // {{{ [3] Delete Bookmark file.
        $bookmarkContainer =& new Xhwlay_Bookmark_FileStoreContainer(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams'], $bcid);
        $this->assertTrue($bookmarkContainer->fileExists());
        $bookmarkContainer->destroy();
        $this->assertFalse($bookmarkContainer->fileExists());
        // }}}
    }

    // }}}
    // {{{ testSP3_route2(ACI = "aci1")

    function testSP3_route2()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern3);

        // {{{ [0] "*.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.aci1");
        // }}}
        // {{{ [1] "*.aci1" -> "page1.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page1");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page1");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "page1.aci1");
        // }}}
        // {{{ [2] "page1.aci1" -> "page2.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page2");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page2");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "page2.aci1");
        // }}}
        // {{{ [3] Delete Bookmark file.
        $bookmarkContainer =& new Xhwlay_Bookmark_FileStoreContainer(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams'], $bcid);
        $this->assertTrue($bookmarkContainer->fileExists());
        $bookmarkContainer->destroy();
        $this->assertFalse($bookmarkContainer->fileExists());
        // }}}
    }

    // }}}
    // {{{ [SP4] (Story Pattern 4: NO Bookmark + Event Oriented + ACI) Definition

    /**
     * route:
     * (1) ACI = ""
     * (all requests fallback to "*.*")
     * (2) ACI = "aci1"
     * (all requests fallback to "*.aci1")
     */
    var $config_pattern4 = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "off",
            ),
        "page" => array(
            "page1.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page1.aci1",
                ),
            "page1.*" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page1.*",
                ),
            "*.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "*.aci1",
                "event" => array(
                    "event1" => null,
                    ),
                ),
            "*.*" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "*.*",
                "event" => array(
                    "event1" => null,
                    ),
                ),
            ),
        "event" => array(
            "event1" => array(
                "user_function" => "Test_EventFunc",
                "transit" => array(
                    "transit1" => "page1",
                    ),
                ),
            ),
        );

    // }}}
    // {{{ testSP4_route1(all requests fallback to "*.*")

    function testSP4_route1()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern4);

        // {{{ [0] "*.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        $runner->run();
        $this->assertEqual(count($GLOBALS['Test_EventFunc_DebugVar']), 0);
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
        // {{{ [1] "*.*" -> event1 -> "*.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $runner->run();
        $this->assertEqual(count($GLOBALS['Test_EventFunc_DebugVar']), 0);
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
    }

    // }}}
    // {{{ testSP4_route2(all requests fallback to "*.aci1")

    function testSP4_route2()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern4);

        // {{{ [0] "*.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        $runner->run();
        $this->assertEqual(count($GLOBALS['Test_EventFunc_DebugVar']), 0);
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.aci1");
        // }}}
        // {{{ [1] "*.aci1" -> event1 -> "*.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $runner->run();
        $this->assertEqual(count($GLOBALS['Test_EventFunc_DebugVar']), 0);
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertNull($bookmark);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.aci1");
        // }}}
    }

    // }}}
    // {{{ [SP5] (Story Pattern 5: Bookmark + Event Oriented + ACI) Definition

    /**
     * route:
     * (1) ACI = ""
     * *.* -> event1 -> page1.*
     * (2) ACI = "aci1"
     * *.aci1 -> event1 -> page1.aci1
     */
    var $config_pattern5 = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "on",
            ),
        "page" => array(
            "page1.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page1.aci1",
                ),
            "page1.*" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "page1.*",
                ),
            "*.aci1" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "*.aci1",
                "event" => array(
                    "event1" => null,
                    ),
                ),
            "*.*" => array(
                "user_function" => "Test_PageFunc",
                "__id" => "*.*",
                "event" => array(
                    "event1" => null,
                    ),
                ),
            ),
        "event" => array(
            "event1" => array(
                "user_function" => "Test_EventFunc",
                "transit" => array(
                    "transit1" => "page1",
                    ),
                ),
            ),
        );

    // }}}
    // {{{ testSP5_route1(ACI = "")

    function testSP5_route1()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern5);

        // {{{ [0] "*.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
        // {{{ [1] "*.*" -> event1 -> "page1.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $GLOBALS['Test_EventFunc_DebugVar']['return'] = "transit1";
        $runner->run();
        // check Event arguments are correct
        $bookmark =& $GLOBALS['Test_EventFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_EventFunc_DebugVar']['event'], "event1");
        // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page1");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "page1.*");
        // }}}
        // {{{ [2] Delete Bookmark file.
        $bookmarkContainer =& new Xhwlay_Bookmark_FileStoreContainer(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams'], $bcid);
        $this->assertTrue($bookmarkContainer->fileExists());
        $bookmarkContainer->destroy();
        $this->assertFalse($bookmarkContainer->fileExists());
        // }}}
    }

    // }}}
    // {{{ testSP5_route2(ACI = "aci1")

    function testSP5_route2()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern5);

        // {{{ [0] "*.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.aci1");
        // }}}
        // {{{ [1] "*.aci1" -> event1 -> "page1.aci1"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "aci1");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $GLOBALS['Test_EventFunc_DebugVar']['return'] = "transit1";
        $runner->run();
        // check Event arguments are correct
        $bookmark =& $GLOBALS['Test_EventFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_EventFunc_DebugVar']['event'], "event1");
        // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "page1");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "page1.aci1");
        // }}}
        // {{{ [2] Delete Bookmark file.
        $bookmarkContainer =& new Xhwlay_Bookmark_FileStoreContainer(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams'], $bcid);
        $this->assertTrue($bookmarkContainer->fileExists());
        $bookmarkContainer->destroy();
        $this->assertFalse($bookmarkContainer->fileExists());
        // }}}
    }

    // }}}
    // {{{ [SP6] (Story Pattern 6: B + EO + start page = end page) Definition

    /**
     * route:
     * *.* -> event1 -> *.*
     */
    var $config_pattern6 = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "on",
            ),
        "page" => array(
            "*.*" => array(
                "__id" => "*.*",
                "user_function" => "Test_PageFunc",
                "bookmark" => "last",
                "event" => array(
                    "event1" => null,
                    ),
                ),
            ),
        "event" => array(
            "event1" => array(
                "user_function" => "Test_EventFunc",
                "transit" => array(
                    "transit1" => "*",
                    ),
                ),
            ),
        );

    // }}}
    // {{{ testSP6_route1()

    function testSP6_route1()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();
        Xhwlay_ErrorStack::clear();
        Xhwlay_Var::clear();
        $runner =& $this->_createRunner($this->config_pattern6);

        // {{{ [0] "*.*" (1st run : event1 enforce, but not invoked event)
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $runner->run();
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // event is not invoked.
        $this->assertEqual(count($GLOBALS['Test_EventFunc_DebugVar']), 0);
        // }}}
        // {{{ [1] "*.*" -> event1 -> "*.*" (2nd run : invoke event)
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $GLOBALS['Test_EventFunc_DebugVar']['return'] = "transit1";
        $runner->run();
        // check Event arguments are correct
        $bookmark =& $GLOBALS['Test_EventFunc_DebugVar']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual(
            $GLOBALS['Test_EventFunc_DebugVar']['event'], "event1");
        // check Page arguments are correct
        $bookmark =& $GLOBALS['Test_PageFunc_DebugVar']['bookmark'];
        $this->assertEqual(
            $bookmark->getContainerId(), Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $this->assertEqual(
            $GLOBALS['Test_PageFunc_DebugVar']['page'], "*");
        $params = $GLOBALS['Test_PageFunc_DebugVar']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
        // {{{ [2] Delete Bookmark file.
        $bookmarkContainer =& new Xhwlay_Bookmark_FileStoreContainer(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams'], $bcid);
        $this->assertEqual(count($bookmarkContainer->getAllBookmarks()), 0);
        // already destroyed at 2nd run.
        $this->assertFalse($bookmarkContainer->fileExists());
        // }}}
    }

    // }}}
    // {{{ testPageAndBarrierCallbackByClassMethod

    function testPageAndBarrierCallbackByClassMethod()
    {
        $configP = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "on",
            ),
        "page" => array(
            "page1.*" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "page",
                "bookmark" => "last",
                "__id" => "page1.*",
                ),
            "*.*" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "page",
                "__id" => "*.*",
                "next" => array(
                    "page1" => "barrier1",
                    ),
                ),
            ),
        "barrier" => array(
            "barrier1" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "barrier",
                "__id" => "barrier1",
                ),
            ),
        );

        // {{{ [0] "*.*"
        $this->_resetAnythings();
        $runner =& $this->_createRunner($configP);
        $runner->run();

        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $bookmark =& $xrc->debug_var['page']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual($xrc->debug_var['page']['page'], "");
        $params = $xrc->debug_var['page']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
        // {{{ [1] "*.*" -> barrier1 -> "page1.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page1");
        $xrc->debug_var['barrier']['return'] = true;
        $runner->run();

        $bookmark =& $xrc->debug_var['page']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);

        $this->assertEqual($xrc->debug_var['barrier']['current'], "");
        $this->assertEqual($xrc->debug_var['barrier']['next'], "page1");
        $params = $xrc->debug_var['barrier']['params'];
        $this->assertEqual($params['__id'], "barrier1");

        $this->assertEqual($xrc->debug_var['page']['page'], "page1");
        $params = $xrc->debug_var['page']['params'];
        $this->assertEqual($params['__id'], "page1.*");
        // }}}
    }

    // }}}
    // {{{ testEventAndGuardCallbackByClassMethod

    function testEventAndGuardCallbackByClassMethod()
    {
        $configP = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "on",
            ),
        "page" => array(
            "page1.*" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "page",
                "bookmark" => "last",
                "__id" => "page1.*",
                ),
            "*.*" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "page",
                "__id" => "*.*",
                "event" => array(
                    "event1" => "guard1",
                    ),
                ),
            ),
        "event" => array(
            "event1" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "event",
                "__id" => "event1",
                "transit" => array(
                    "transit1" => "page1",
                    ),
                ),
            ),
        "guard" => array(
            "guard1" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "guard",
                "__id" => "guard1",
                ),
            ),
        );

        // {{{ [0] "*.*"
        $this->_resetAnythings();
        $runner =& $this->_createRunner($configP);
        $runner->run();

        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $bookmark =& $xrc->debug_var['page']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual($xrc->debug_var['page']['page'], "");
        $params = $xrc->debug_var['page']['params'];
        $this->assertEqual($params['__id'], "*.*");
        // }}}
        // {{{ [1] "*.*" -> guard1 -> event1 -> "page1.*"
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $xrc->debug_var['guard']['return'] = true;
        $xrc->debug_var['event']['return'] = "transit1";
        $runner->run();

        $bookmark =& $xrc->debug_var['page']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);

        // check guard parameters are correct
        $this->assertEqual($xrc->debug_var['guard']['event'], "event1");
        $bookmark =& $xrc->debug_var['guard']['bookmark'];
        $this->assertEqual($bookmark->getContainerId(), $bcid);
        $params = $xrc->debug_var['guard']['params'];
        $this->assertEqual($params['__id'], "guard1");

        // check event parameters are correct
        $this->assertEqual($xrc->debug_var['event']['event'], "event1");
        $bookmark =& $xrc->debug_var['event']['bookmark'];
        $this->assertEqual($bookmark->getContainerId(), $bcid);
        $params = $xrc->debug_var['guard']['params'];
        $this->assertEqual($params['__id'], "guard1");

        // check page parameters are correct
        $this->assertEqual($xrc->debug_var['page']['page'], "page1");
        $bookmark =& $xrc->debug_var['event']['bookmark'];
        $this->assertEqual($bookmark->getContainerId(), $bcid);
        $params = $xrc->debug_var['page']['params'];
        $this->assertEqual($params['__id'], "page1.*");
        // }}}
    }

    // }}}
    // {{{ testSkipPage

    function testSkipPage()
    {
        // {{{ [0] config and initialize
        $configP = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "on",
            ),
        "page" => array(
            "page2" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "page",
                "bookmark" => "last",
                ),
            "page1" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "page",
                "next" => array(
                    "page2" => "barrier1",
                    ),
                ),
            "*" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "page",
                "event" => array(
                    "event1" => "guard1",
                    ),
                ),
            ),
        "barrier" => array(
            "barrier1" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "barrier_skippage",
                ),
            ),
        "event" => array(
            "event1" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "event_skippage",
                "transit" => array(
                    "transit1" => "page1",
                    ),
                ),
            ),
        "guard" => array(
            "guard1" => array(
                "class" => "Xhwlay_Runner_callbacks",
                "method" => "guard",
                ),
            ),
        );
        $this->_resetAnythings();
        $runner =& $this->_createRunner($configP);
        // }}}
        // {{{ [1] "*"
        $runner->run();

        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $bookmark =& $xrc->debug_var['page']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);
        $this->assertEqual($xrc->debug_var['page']['page'], "");
        // }}}
        // {{{ [2] "*" -> guard1 -> event1 -> (skip page)"page1"
        Xhwlay_Var::clear();
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "event1");
        $xrc->reset();
        $xrc->debug_var['guard']['return'] = true;
        $xrc->debug_var['event']['return'] = "transit1";
        $runner->run();

        // check guard parameters are correct
        $this->assertEqual($xrc->debug_var['guard']['event'], "event1");
        $bookmark =& $xrc->debug_var['guard']['bookmark'];
        $bcid = $bookmark->getContainerId();
        $this->assertEqual(Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID), $bcid);

        // check event parameters are correct
        $this->assertEqual($xrc->debug_var['event']['event'], "event1");
        $bookmark =& $xrc->debug_var['event']['bookmark'];
        $this->assertEqual($bookmark->getContainerId(), $bcid);
        $this->assertEqual($bookmark->getPageName(), "page1");

        // check page action is skipped
        $this->assertFalse(isset($xrc->debug_var['page']));

        // }}}
        // {{{ [3] "page1" -> barrier1 -> (skip page)"page2"
        Xhwlay_Var::clear();
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "page2");
        $xrc->reset();
        $xrc->debug_var['barrier']['return'] = true;
        $runner->run();

        // check barrier parameters are correct
        $this->assertEqual($xrc->debug_var['barrier']['current'], "page1");
        $this->assertEqual($xrc->debug_var['barrier']['next'], "page2");
        $bookmark =& $xrc->debug_var['barrier']['bookmark'];
        $this->assertEqual($bookmark->getContainerId(), $bcid);
        $this->assertEqual($bookmark->getPageName(), "page2");

        // check page action is skipped
        $this->assertFalse(isset($xrc->debug_var['page']));

        // }}}
    }

    // }}}
    // {{{ testWipeout

    function testWipeout()
    {
        $configP = array(
        "story" => array(
            "name" => "Test Story",
            "bookmark" => "off",
            ),
        "page" => array(
            "*" => array(
                "user_function" => 'Test_PageFunc',
                ),
            ),
        );
        $this->_resetAnythings();
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");

        // push callbacks to setup(wipeout)/terminate hook
        $h1 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_SETUP);
        $h1->pushCallback(array('Xhwlay_Runner_hooks', 'setup_wipeout'));
        $h2 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_TERMINATE);
        $h2->pushCallback(array('Xhwlay_Runner_hooks', 'terminate'));

        $runner =& $this->_createRunner($configP);
        $runner->run();

        $xrh =& Xhwlay_Runner_hooks::getInstance();
        // check setup_wipeout was called.
        $this->assertEqual($xrh->debug_var['setup_wipeout']['hook'],
            XHWLAY_RUNNER_HOOK_SETUP);
        // check terminate hook was called.
        $this->assertEqual($xrh->debug_var['terminate']['hook'],
            XHWLAY_RUNNER_HOOK_TERMINATE);
        // check no pages are invoked.
        $this->assertEqual(count($GLOBALS['Test_PageFunc_DebugVar']), 0);
    }

    // }}}
    // {{{ testBookmarkOff_WhenCallbackFunctionIsNotFound

    function testBookmarkOff_WhenCallbackFunctionIsNotFound()
    {
        $configP = array(
            "story" => array(
                "name" => "Test Story",
                "bookmark" => "off",
                ),
            "page" => array(
                "*" => array(
                    "user_function" => "______DUMMY...",
                    ),
                ),
            );
        $this->_resetAnythings();
        Xhwlay_ErrorStack::pushCallback(
            array('Xhwlay_Runner_Test', 'handleErrorStack_IgnoreInfoDebug'));

        $runner =& $this->_createRunner($configP);
        $runner->run();

        // check function is not callable and class/method is not defined.
        $error = Xhwlay_ErrorStack::pop();
        $this->assertEqual($error['level'], XHWLAY_ERRORSTACK_EL_WARN);
        $this->assertEqual($error['code'],
            XHWLAY_RUNNER_EC_CLASS_OR_METHOD_NOT_DEFINED);
        Xhwlay_ErrorStack::popCallback();
    }

    // }}}
    // {{{ testBookmarkOff_CallbackFunctionIsPriorThanClassMethod

    function testBookmarkOff_CallbackFunctionIsPriorThanClassMethod()
    {
        $configP = array(
            "story" => array(
                "name" => "Test Story",
                "bookmark" => "off",
                ),
            "page" => array(
                "*" => array(
                    "user_function" => "Test_PageFunc",
                    "class" => "Xhwlay_Runner_callbacks",
                    "method" => "page",
                    ),
                ),
            );
        $this->_resetAnythings();
        $runner =& $this->_createRunner($configP);
        $runner->run();

        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        // Check function is called, not class-method.
        $this->assertNotEqual(count($GLOBALS['Test_PageFunc_DebugVar']), 0);
        $this->assertEqual(count($xrc->debug_var), 0);
    }

    // }}}
    // {{{ testBookmarkOff_WhenCallbackClassIsNotFound

    function testBookmarkOff_WhenCallbackClassIsNotFound()
    {
        $configP = array(
            "story" => array(
                "name" => "Test Story",
                "bookmark" => "off",
                ),
            "page" => array(
                "*" => array(
                    "class" => "Xhwlay_Runner_callbacks_XXXX",
                    "method" => "page",
                    ),
                ),
            );
        $this->_resetAnythings();
        Xhwlay_ErrorStack::pushCallback(
            array('Xhwlay_Runner_Test', 'handleErrorStack_IgnoreInfoDebug'));
        $runner =& $this->_createRunner($configP);
        $runner->run();

        // check function is not callable and class/method is not defined.
        $error = Xhwlay_ErrorStack::pop();
        $this->assertEqual($error['level'], XHWLAY_ERRORSTACK_EL_WARN);
        $this->assertEqual($error['code'],
            XHWLAY_RUNNER_EC_CLASS_NOT_EXISTS);
        Xhwlay_ErrorStack::popCallback();
    }

    // }}}
    // {{{ testBookmarkOff_WhenCallbackMethodIsNotFound

    function testBookmarkOff_WhenCallbackMethodIsNotFound()
    {
        $configP = array(
            "story" => array(
                "name" => "Test Story",
                "bookmark" => "off",
                ),
            "page" => array(
                "*" => array(
                    "class" => "Xhwlay_Runner_callbacks",
                    "method" => "page_XXXX",
                    ),
                ),
            );
        $this->_resetAnythings();
        Xhwlay_ErrorStack::pushCallback(
            array('Xhwlay_Runner_Test', 'handleErrorStack_IgnoreInfoDebug'));
        $runner =& $this->_createRunner($configP);
        $runner->run();

        // check function is not callable and class/method is not defined.
        $error = Xhwlay_ErrorStack::pop();
        $this->assertEqual($error['level'], XHWLAY_ERRORSTACK_EL_WARN);
        $this->assertEqual($error['code'],
            XHWLAY_RUNNER_EC_METHOD_NOT_EXISTS);
        Xhwlay_ErrorStack::popCallback();
    }

    // }}}
    // {{{ _createRunner($runnerP, $configP)

    /**
     * Create test runner.
     *
     * <code>
     * $configP = array(
     *     'story' => array(...),
     *     'page' => array(...),
     *     'barrier' => array(...),
     *     );
     * $runner =& _createRunner($runnerP, $configP);
     * $runner->run();
     * </code>
     *
     * @access private
     * @param array $runnerP
     * @param array $configP
     * @return Xhwlay_Runner
     * @since 1.0.0
     */
    function &_createRunner($configP)
    {
        $renderer =& new Xhwlay_Renderer_Serialize();
        $config =& new Xhwlay_Config_PHPArray($configP);

        $runner =& new Xhwlay_Runner();
        $runner->setBookmarkContainerClassName(
            "Xhwlay_Bookmark_FileStoreContainer");
        $runner->setBookmarkContainerParams(
            $GLOBALS['Xhwlay_Runner_TestCase']['bcparams']);
        $runner->setConfig($config);
        $runner->setRenderer($renderer);
        return $runner;
    }

    // }}}
    // {{{ _resetAnythings()

    /**
     * Reset various globals, internal vars of hooks, callbacks, stacks.
     */
    function _resetAnythings()
    {
        $GLOBALS['Test_PageFunc_DebugVar'] = array();
        $GLOBALS['Test_EventFunc_DebugVar'] = array();
        $GLOBALS['Test_BarrierFunc_DebugVar'] = array();
        $GLOBALS['Test_GuardFunc_DebugVar'] = array();

        Xhwlay_ErrorStack::clear();

        Xhwlay_Var::clear();
        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_ACI, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, "");
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, "");

        $xrh =& Xhwlay_Runner_hooks::getInstance();
        $xrh->reset();

        $xrc =& Xhwlay_Runner_callbacks::getInstance();
        $xrc->reset();

        $h =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_SETUP);
        while($h->popCallback());
        $h =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_TERMINATE);
        while($h->popCallback());
        $h =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_CLASSLOAD);
        while($h->popCallback());

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
