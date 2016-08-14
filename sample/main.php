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
 * Xhwlay Demo Example : Simple Wizard Example (Event Driven type)
 *
 * This example shows wizard like application with Xhwlay.
 *
 * Notice: Before running this example, check "datas" and "sess" directories
 * are writable for Apache or other web servers which runs this php script.
 *
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 * $Id: main.php 48 2008-02-12 06:12:11Z msakamoto-sf $
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
        "name" => "Wizard Example",
        "bookmark" => "on",
        ),
    "page" => array(
        "page4" => array(
            "user_function" => "demo_page_page4",
            "bookmark" => "last",
            ),
        "page3" => array(
            "user_function" => "demo_page_page3",
            "event" => array(
                "onSubmitPage4" => null,
                "onBacktoPage2" => null,
                ),
            ),
        "page2" => array(
            "user_function" => "demo_page_page2",
            "event" => array(
                "onSubmitPage3" => null,
                "onBacktoPage1" => null,
                ),
            ),
        "page1" => array(
            "user_function" => "demo_page_page1",
            "event" => array(
                "onSubmitPage2" => null,
                "onBacktoPage0" => null,
                ),
            ),
        "*" => array(
            "user_function" => "demo_page_page0",
            "event" => array(
                "onSubmitPage1" => null,
                ),
            ),
        ),
    "event" => array(
        // go to next page
        "onSubmitPage1" => array(
            "user_function" => "demo_event_onSubmit",
            "transit" => array("success" => "page1")),
        "onSubmitPage2" => array(
            "user_function" => "demo_event_onSubmit",
            "transit" => array("success" => "page2")),
        "onSubmitPage3" => array(
            "user_function" => "demo_event_onSubmit",
            "transit" => array("success" => "page3")),
        "onSubmitPage4" => array(
            "user_function" => "demo_event_onSubmit",
            // If page3 -> page4, page4 is end point, so, 
            // sending location header leads finally "page0" to browser.
            // So, now we implements "send_location_header" parameter and
            // If this exists and is false, don't send location header.
            "send_location_header" => false,
            "transit" => array("success" => "page4")),

        // back to previous page
        "onBacktoPage2" => array(
            "user_function" => "demo_event_onSubmit",
            "transit" => array("success" => "page2")),
        "onBacktoPage1" => array(
            "user_function" => "demo_event_onSubmit",
            "transit" => array("success" => "page1")),
        "onBacktoPage0" => array(
            "user_function" => "demo_event_onSubmit",
            "transit" => array("success" => "*")),
        ),
    );

// }}}
// {{{ main scripts

Xhwlay_ErrorStack::clear();
Xhwlay_ErrorStack::pushCallback("demo_errorhandler");

$renderer =& new Xhwlay_Renderer_Include();
$config =& new Xhwlay_Config_PHPArray($configP);

// setup "setup" hooks (executed before xhwlay)
$h1 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_SETUP);
$h1->pushCallback("demo_hook_setup_session");
//$h1->pushCallback("demo_hook_setup_auth");

// setup "terminate" hooks (executed after xhwlay)
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
 * (same as login.php)
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
 * (same as login.php)
 */
function demo_hook_setup_session($hook, &$runner)
{
    session_start();

    // get BCID from session variables.
    $bcid = isset($_SESSION['bcid']) ? $_SESSION['bcid'] : "";
    // get Event from request parameters.
    $event = '';
    foreach ($_REQUEST as $_k => $_v) {
        if (preg_match('/^_event_(\w+)$/', $_k, $m)) {
            $event = $m[1];
        }
    }

    Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
    Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, $event);
}

// }}}
// {{{ demo_hook_terminate()

/**
 * (same as login.php)
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
// {{{ demo_hook_setup_auth()

/**
 * Send "Location" header which redirects to "login.php".
 *
 * If not logined yet, redirects to login page.
 *
 * @param string Hook name
 * @param object Instance of Xhwlay_Runner
 */
function demo_hook_setup_auth($hook, &$runner)
{
    if (!isset($_SESSION['user_id'])) {
        // If not logined yet, send "Location" header and ...
        header("Location: http://xhwlay-tutorial/login.php");
        // and, restrain continuous page action invoking, terminate.
        $runner->wipeout();
    }
}

// }}}
// {{{ Page actions (page0 - page4)

function demo_page_page4(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('page', 'page4');
    $renderer->set('bcid', $bookmark->getContainerId());

    return "templates/main_page4.html";
}

function demo_page_page3(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('page', 'page3');
    $renderer->set('bcid', $bookmark->getContainerId());
    $renderer->set('f_name', $bookmark->get('name'));
    $renderer->set('f_email', $bookmark->get('email'));
    $renderer->set('f_zip', $bookmark->get('zip'));
    $renderer->set('f_address', $bookmark->get('address'));
    $renderer->set('f_telephone', $bookmark->get('telephone'));
    $renderer->set('f_age', $bookmark->get('age'));
    $renderer->set('f_hobby', $bookmark->get('hobby'));

    return "templates/main_page3.html";
}

function demo_page_page2(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('page', 'page2');
    $renderer->set('bcid', $bookmark->getContainerId());
    $renderer->set('f_age', $bookmark->get('age'));
    $renderer->set('f_hobby', $bookmark->get('hobby'));

    return "templates/main_page2.html";
}

function demo_page_page1(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('page', 'page1');
    $renderer->set('bcid', $bookmark->getContainerId());
    $renderer->set('f_zip', $bookmark->get('zip'));
    $renderer->set('f_address', $bookmark->get('address'));
    $renderer->set('f_telephone', $bookmark->get('telephone'));

    return "templates/main_page1.html";
}

function demo_page_page0(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('page', 'start');
    $renderer->set('bcid', $bookmark->getContainerId());
    $renderer->set('f_name', $bookmark->get('name'));
    $renderer->set('f_email', $bookmark->get('email'));

    return "templates/main_page0.html";
}

// }}}
// {{{ demo_event_onSubmit()

/**
 * Common Event Handler on Submit
 *
 * Search requests, and stores parameters into bookmark.
 * Demonstrate how to send "Location" header and restrain page action invoking
 * by calling $runner->wipeout().
 */
function demo_event_onSubmit(&$runner, $event, &$bookmark, $params)
{
    $vars = array(
        "name", "email", // input at "*"
        "zip", "address", "telephone", // input at "page1"
        "age", "hobby", // input at "page2"
        );

    foreach ($vars as $_k) {
        if (isset($_REQUEST[$_k])) {
            $bookmark->set($_k, $_REQUEST[$_k]);
        }
    }

    if (!isset($params['send_location_header']) || 
        $params['send_location_header'] == true) {
        header("Location: http://xhwlay-tutorial/main.php");
        $runner->wipeout();
    }

    return "success";
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
