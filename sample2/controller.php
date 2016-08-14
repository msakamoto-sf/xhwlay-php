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
 * Xhwlay Demo Example 2 : common controller file
 *
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 * $Id: controller.php 50 2008-02-14 06:35:23Z msakamoto-sf $
 */
if (file_exists(dirname(__FILE__) . '/../Xhwlay/Runner.php')) {
    set_include_path(realpath(dirname(__FILE__) . '/..')
        . PATH_SEPARATOR . get_include_path());
}
session_save_path(dirname(__FILE__) . '/sess/');

define('SAMPLE2_URL', 'http://xhwlay-tutorial2/');

require_once('Xhwlay/Runner.php');
require_once('Xhwlay/Bookmark/FileStoreContainer.php');
require_once('Xhwlay/Config/PHPArray.php');
require_once('Xhwlay/Renderer/Include.php');

session_cache_limiter('none');
session_start();

Xhwlay_ErrorStack::clear();
Xhwlay_ErrorStack::pushCallback(array('sample2_controller', 'handleError'));

class sample2_controller
{
    // {{{ handleError()

    function handleError($error)
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
    // {{{ hookSetup()

    function hookSetup($hook, &$runner)
    {
        // get BCID from session variables.
        $bcid = isset($_REQUEST['_bcid_']) ? $_REQUEST['_bcid_'] : "";
        // get Event from request parameters.
        $event = '';
        foreach ($_REQUEST as $_k => $_v) {
            if (preg_match('/^_event_(\w+)$/', $_k, $m)) {
                $event = $m[1];
            }
        }
        if (empty($event) && isset($_REQUEST['_event_'])) {
            $event = $_REQUEST['_event_'];
        }

        if ($_SERVER['SCRIPT_NAME'] != '/login.php' && 
            !isset($_SESSION['user_id'])) {
            // If not logined yet, send "Location" header and ...
            header('Location: ' . SAMPLE2_URL . 'login.php');
            // and, restrain continuous page action invoking, terminate.
            $runner->wipeout();
        }

        Xhwlay_Var::set(XHWLAY_VAR_KEY_BCID, $bcid);
        Xhwlay_Var::set(XHWLAY_VAR_KEY_EVENT, $event);
    }

    // }}}
    // {{{ run()

    function run($configP)
    {
        $bookmarkContainerParams = array(
            "dataDir" => dirname(__FILE__).'/datas',
            "expire" => 600,
            "identKeys" => array(
                'ip_addr' => $_SERVER['REMOTE_ADDR'],
                'session_id' => session_id(),
                ),
            "gc_probability" => 1,
            "gc_divisor" => 1,
            "gc_maxlifetime" => 3600, // NOTE: should be greater than 'expire'
        );

        $renderer =& new Xhwlay_Renderer_Include();
        $config =& new Xhwlay_Config_PHPArray($configP);

        // setup "setup" hooks (executed before xhwlay)
        $h1 =& Xhwlay_Hook::getInstance(XHWLAY_RUNNER_HOOK_SETUP);
        $h1->pushCallback(array('sample2_controller', 'hookSetup'));

        $runner =& new Xhwlay_Runner();
        $runner->setBookmarkContainerClassName(
            "Xhwlay_Bookmark_FileStoreContainer");
        $runner->setBookmarkContainerParams($bookmarkContainerParams);
        $runner->setConfig($config);
        $runner->setRenderer($renderer);

        echo $runner->run();
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
