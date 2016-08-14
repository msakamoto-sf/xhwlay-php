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
 * Xhwlay Demo Example : Simple Login and Logout example
 *
 * This example shows basics of building Xhwlay application.
 *
 * Notice: Before running this example, check "datas" and "sess" directories
 * are writable for Apache or other web servers which runs this php script.
 *
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 * $Id: login.php 25 2007-10-10 10:11:50Z msakamoto-sf $
 */
$__base_dir = dirname(__FILE__);
if (file_exists(dirname(__FILE__) . '/../Xhwlay/Runner.php')) {
    set_include_path(realpath(dirname(__FILE__) . '/..')
        . PATH_SEPARATOR . get_include_path());
}
session_save_path($__base_dir . '/sess/');

// {{{ requires

require_once('Xhwlay/Runner.php');
require_once('Xhwlay/Bookmark/FileStoreContainer.php');
require_once('Xhwlay/Config/PHPArray.php');
require_once('Xhwlay/Renderer/Include.php');

// }}}
// {{{ Bookmark Container and Page Flow (Story) Configurations

$bookmarkContainerParams = array(
    "dataDir" => $__base_dir.'/datas',
    "gc_probability" => 1,
    "gc_divisor" => 1,
    "gc_maxlifetime" => 30,
);

$configP = array(
    "story" => array(
        "name" => "Login Example",
        "bookmark" => "on",
        ),
    "page" => array(
        "logout" => array(
            "user_function" => "demo_page_logout",
            "bookmark" => "last",
            ),
        "main" => array(
            "user_function" => "demo_page_main",
            "event" => array(
                "onLogout" => null,
                ),
            ),
        "*" => array(
            "user_function" => "demo_page_login",
            "event" => array(
                "onLogin" => "validateLogin",
                ),
            ),
        ),
    "event" => array(
        "onLogout" => array(
            "user_function" => "demo_event_onLogout",
            "transit" => array(
                "success" => "logout",
                ),
            ),
        "onLogin" => array(
            "user_function" => "demo_event_onLogin",
            "transit" => array(
                "success" => "main",
                ),
            ),
        ),
    "guard" => array(
        "validateLogin" => array(
            "user_function" => "demo_guard_validateLogin"
            ),
        ),
    );

// }}}
// {{{ main scripts

Xhwlay_ErrorStack::clear();
Xhwlay_ErrorStack::pushCallback("demo_errorhandler");

$renderer =& new Xhwlay_Renderer_Include();
$config =& new Xhwlay_Config_PHPArray($configP);

$h1 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_SETUP);
$h1->pushCallback("demo_hook_setup_session");

$h2 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_TERMINATE);
$h2->pushCallback("demo_hook_terminate");

$runner =& new Xhwlay_Runner();
$runner->setBookmarkContainerClassName("Xhwlay_Bookmark_FileStoreContainer");
$runner->setBookmarkContainerParams($bookmarkContainerParams);
$runner->setConfig($config);
$runner->setRenderer($renderer);

echo $runner->run();

// }}}
// {{{ demo_errorhandler()

/**
 * Error_Stack callback.
 *
 * @param mixed Error information
 * @return integer
 */
function demo_errorhandler($error)
{
    switch($error['level']) {
        case "debug":
        case "info":
            //trigger_error($error['message'], E_USER_NOTICE);
            break;
        case "warn":
            trigger_error($error['message'], E_USER_WARNING);
            break;
        default:
            trigger_error($error['message'], E_USER_ERROR);
    }

    return PEAR_ERRORSTACK_IGNORE;
}

// }}}
// {{{ demo_hook_setup_session()

/**
 * Retrieves parameters from requests, and passes to Xhwlay.
 *
 * @param string Hook name
 * @param object Instance of Xhwlay_Runner
 */
function demo_hook_setup_session($hook, &$runner)
{
    session_start();

    // get BCID from session variables.
    $bcid = isset($_SESSION['bcid']) ? $_SESSION['bcid'] : "";
    // get Event from request parameters.
    $event = isset($_REQUEST['_event_']) ? $_REQUEST['_event_'] : "";

    Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
    Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, $event);
}

// }}}
// {{{ demo_hook_terminate()

/**
 * Store Bookmakr Container ID (BCID) into session vars.
 *
 * @param string Hook name
 * @param object Instance of Xhwlay_Runner
 */
function demo_hook_terminate($hook, &$runner)
{
    $bcid = Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID);
    $sid = session_id();
    if (!empty($sid)) {
        $_SESSION['bcid'] = $bcid;
    }
}

// }}}
// {{{ demo_page_logout()

/**
 * Page action of "logout" page.
 *
 * @param object Instance of Xhwlay_Runner
 * @param string Page name
 * @param object Bookmark instance
 * @param mixed Configuration parameters
 * @return string View name
 */
function demo_page_logout(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('page', 'logout');
    $renderer->set('bcid', $bookmark->getContainerId());
    return "templates/login_logout.html";
}

// }}}
// {{{ demo_page_main()

/**
 * Page action of "main" page.
 *
 * @param object Instance of Xhwlay_Runner
 * @param string Page name
 * @param object Bookmark instance
 * @param mixed Configuration parameters
 * @return string View name
 */
function demo_page_main(&$runner, $page, &$bookmark, $params)
{
    $user_id = $_SESSION['user_id'];
    // count up demo.
    $count = $_SESSION['count'];
    $count++;
    $_SESSION['count'] = $count;

    $renderer =& $runner->getRenderer();

    $renderer->set('page', 'main');
    $renderer->set('user_id', $user_id);
    $renderer->set('count', $count);
    $renderer->set('bcid', $bookmark->getContainerId());
    return "templates/login_main.html";
}

// }}}
// {{{ demo_page_login()

/**
 * Page action of "login" page.
 *
 * Simply demostration how to assign values to renderer.
 *
 * @param object Instance of Xhwlay_Runner
 * @param string Page name
 * @param object Bookmark instance
 * @param mixed Configuration parameters
 * @return string View name
 */
function demo_page_login(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('page', 'login');
    $renderer->set('bcid', $bookmark->getContainerId());

    return "templates/login_login.html";
}

// }}}
// {{{ demo_event_onLogout()

/**
 * Event handler of "onLogout" event.
 *
 * Simply reset session variables by calling session_destroy().
 *
 * @param object Instance of Xhwlay_Runner
 * @param string Event name
 * @param object Bookmark instance
 * @param mixed Configuration parameters
 * @return string Transit name
 */
function demo_event_onLogout(&$runner, $event, &$bookmark, $params)
{
    session_destroy();
    return "success";
}

// }}}
// {{{ demo_event_onLogin()

/**
 * Event handler of "onLogin" event.
 *
 * Setup "user_id" session variable, and initialize demo session vars counter.
 *
 * @param object Instance of Xhwlay_Runner
 * @param string Event name
 * @param object Bookmark instance
 * @param mixed Configuration parameters
 * @return string Transit name
 */
function demo_event_onLogin(&$runner, $event, &$bookmark, $params)
{
    // These are stored in demo_guard_validateLogin().
    $user_name = $bookmark->get("user_name");
    $password =$bookmark->get("password");

    // demo codes.
    $user_id = md5( $user_name . $password );
    $bookmark->remove("user_name");
    $bookmark->remove("password");
    // stores user_id into a session variable.
    $_SESSION['user_id'] = $user_id;
    // demo counter.
    $_SESSION['count'] = 0;

    return "success";
}

// }}}
// {{{ demo_guard_validateLogin()

/**
 * Guard function when login event occurs.
 *
 * Simply, Check request parameters must not be empty.
 *
 * @param object Instance of Xhwlay_Runner
 * @param string Event name
 * @param object Bookmark instance
 * @param mixed Configuration parameters
 * @return boolean TRUE if login requests are correct, or else, FALSE.
 */
function demo_guard_validateLogin(&$runner, $event, &$bookmark, $params)
{
    $_user_name = @$_REQUEST['user_name'];
    $_password = @$_REQUEST['password'];
    if (empty($_user_name) || empty($_password)) {
        return false;
    }
    // Store into Bookmark user data area.
    $bookmark->set("user_name", $_user_name);
    $bookmark->set("password", $_password);

    /*
     * Against session fixation attacks : generate new session id and 
     * destroy old one.
     *
     * see the article posted by "Nicolas dot Chachereau at Infomaniak dot ch"
     * at 03-Jun-2005 03:40 in following url:
     * http://jp.php.net/manual/ja/function.session-regenerate-id.php
     */
    $sid_old = session_id(); // save old sid.
    session_regenerate_id(); // generate new sid.
    $sid_new = session_id(); // save it.
    session_id($sid_old);    // now, set current as saved old sid.
    session_destroy();       // destroy current (equals old sid).
    session_id($sid_new);    // re-set current as new sid.
    session_start();         // re-start session.

    return true;
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
?>
