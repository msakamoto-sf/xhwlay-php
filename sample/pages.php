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
 * Xhwlay Demo Example : Page oriented flow example.
 *
 * This example shows very simple demo for page oriented flow.
 * It also shows "Bookmark OFF" demo.
 *
 * Notice: Before running this example, check "datas" and "sess" directories
 * are writable for Apache or other web servers which runs this php script.
 *
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 * $Id: pages.php 25 2007-10-10 10:11:50Z msakamoto-sf $
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
        "name" => "Page Oriented Example",
        "bookmark" => "on",
        //"bookmark" => "off",
        ),
    "page" => array(
        "page3" => array(
            "class" => "InnerKlass",
            "method" => "staticMethod",
            "bookmark" => "last",
            ),
        "page2" => array(
            "class" => "Tutorial_PageActions_Page2",
            "method" => "staticMethod",
            "next" => array(
                "page3" => null,
                "page1" => null,
                ),
            ),
        "page1" => array(
            "class" => "Tutorial_PageActions_Page1",
            "method" => "staticMethod",
            "next" => array(
                "page2" => "barrier_sample",
                "page0" => null,
                ),
            ),
        "*" => array(
            "user_function" => "demo_page_userfunc",
            "next" => array(
                "page1" => null,
                ),
            ),
        ),
    "barrier" => array(
        "barrier_sample" => array(
            "user_function" => "demo_barrier",
            ),
        ),
    );

// }}}
// {{{ InnerKlass

class InnerKlass
{
    function staticMethod(&$runner, $page, &$bookmark, $params)
    {
        // lazy job :p
        return demo_page_userfunc($runner, $page, $bookmark, $params);
    }
}

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

$h3 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_CLASSLOAD);
$h3->pushCallback("demo_hook_classload");

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
    // get Page from request parameters.
    $page = isset($_REQUEST['_page_']) ? $_REQUEST['_page_'] : "";

    Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
    Xhwlay_Var::set(XHWLAY_VAR_KEY_PAGE, $page);
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
// {{{ demo_hook_classload()

/**
 * Custom class loading hook
 *
 * @param string Hook name
 * @param array Callback array
 */
function demo_hook_classload($hook, $params)
{
    $__basedir = dirname(__FILE__);
    if (!isset($params['class'])) {
        return;
    }
    $klass = $params['class'];
    // translate PEAR-like class name to actual file path
    $klass = strtr($klass, "_", "/");
    $file = $__basedir . "/classes/" . $klass . ".php";
    if (is_readable($file)) {
        require_once(realpath($file));
    }
}

// }}}
// {{{ demo_page_userfunc()

function demo_page_userfunc(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('page', $page);
    $config =& $runner->getConfig();
    $bm = $config->needsBookmark() ? "on" : "off";
    $renderer->set('bookmark', $bm);
    if ($config->needsBookmark()) {
        $renderer->set('bcid', $bookmark->getContainerId());
    } else {
        $renderer->set('bcid', '(none)');
    }

    return "templates/pages_default.html";
}

// }}}
// {{{ demo_barrier()

/**
 * Barrier Example
 */
function demo_barrier(&$runner, $current, $next, &$bookmark, $params)
{
    return isset($_REQUEST['barrier']) && $_REQUEST['barrier'] == "pass";
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
