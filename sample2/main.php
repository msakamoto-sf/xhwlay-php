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
 * Xhwlay Demo Example 2 : Simple Wizard Example (Event Driven type)
 * with login/logout feature (more secured).
 *
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 * $Id: main.php 50 2008-02-14 06:35:23Z msakamoto-sf $
 */
require_once('controller.php');
// {{{ Page Flow (Story) Configurations

$configP = array(
    "story" => array(
        "name" => "Wizard Example",
        "bookmark" => "on",
        ),
    "page" => array(
        "page4" => array(
            "user_function" => "page_page4",
            "bookmark" => "last",
            ),
        "page3" => array(
            "user_function" => "page_page3",
            "event" => array(
                "onSubmitPage4" => null,
                "onBacktoPage2" => null,
                ),
            ),
        "page2" => array(
            "user_function" => "page_page2",
            "event" => array(
                "onSubmitPage3" => null,
                "onBacktoPage1" => null,
                ),
            ),
        "page1" => array(
            "user_function" => "page_page1",
            "event" => array(
                "onSubmitPage2" => null,
                "onBacktoPage0" => null,
                ),
            ),
        "*" => array(
            "user_function" => "page_page0",
            "event" => array(
                "onSubmitPage1" => null,
                ),
            ),
        ),
    "event" => array(
        // go to next page
        "onSubmitPage1" => array(
            "user_function" => "event_onSubmit",
            "transit" => array("success" => "page1")),
        "onSubmitPage2" => array(
            "user_function" => "event_onSubmit",
            "transit" => array("success" => "page2")),
        "onSubmitPage3" => array(
            "user_function" => "event_onSubmit",
            "transit" => array("success" => "page3")),
        "onSubmitPage4" => array(
            "user_function" => "event_onSubmit",
            // If page3 -> page4, page4 is end point, so, 
            // sending location header leads finally "page0" to browser.
            // So, now we implements "send_location_header" parameter and
            // If this exists and is false, don't send location header.
            "send_location_header" => false,
            "transit" => array("success" => "page4")),

        // back to previous page
        "onBacktoPage2" => array(
            "user_function" => "event_onSubmit",
            "transit" => array("success" => "page2")),
        "onBacktoPage1" => array(
            "user_function" => "event_onSubmit",
            "transit" => array("success" => "page1")),
        "onBacktoPage0" => array(
            "user_function" => "event_onSubmit",
            "transit" => array("success" => "*")),
        ),
    );

// }}}
// {{{ Page actions (page0 - page4)

function page_page4(&$runner, $page, &$bookmark, $params)
{
    return "templates/main_page4.html";
}

function page_page3(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('f_name', $bookmark->get('name'));
    $renderer->set('f_email', $bookmark->get('email'));
    $renderer->set('f_zip', $bookmark->get('zip'));
    $renderer->set('f_address', $bookmark->get('address'));
    $renderer->set('f_telephone', $bookmark->get('telephone'));
    $renderer->set('f_age', $bookmark->get('age'));
    $renderer->set('f_hobby', $bookmark->get('hobby'));

    return "templates/main_page3.html";
}

function page_page2(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('f_age', $bookmark->get('age'));
    $renderer->set('f_hobby', $bookmark->get('hobby'));

    return "templates/main_page2.html";
}

function page_page1(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('f_zip', $bookmark->get('zip'));
    $renderer->set('f_address', $bookmark->get('address'));
    $renderer->set('f_telephone', $bookmark->get('telephone'));

    return "templates/main_page1.html";
}

function page_page0(&$runner, $page, &$bookmark, $params)
{
    $renderer =& $runner->getRenderer();
    $renderer->set('f_name', $bookmark->get('name'));
    $renderer->set('f_email', $bookmark->get('email'));

    return "templates/main_page0.html";
}

// }}}
// {{{ event_onSubmit()

function event_onSubmit(&$runner, $event, &$bookmark, $params)
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
        header('Location: ' . SAMPLE2_URL . 'main.php?_bcid_=' 
            . Xhwlay_Var::get(XHWLAY_VAR_KEY_BCID));
        $runner->wipeout();
    }

    return "success";
}

// }}}
sample2_controller::run($configP);

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
