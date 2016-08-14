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
 * Xhwlay Demo Example 2 : Simple Login and Logout example (more secured)
 *
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 * $Id: login.php 50 2008-02-14 06:35:23Z msakamoto-sf $
 */
require_once('controller.php');
// {{{ Page Flow (Story) Configurations

$configP = array(
    "story" => array(
        "name" => "Login Example",
        "bookmark" => "on",
        ),
    "page" => array(
        "logout" => array(
            "user_function" => "page_logout",
            "bookmark" => "last",
            ),
        "main" => array(
            "user_function" => "page_main",
            "event" => array(
                "onLogout" => null,
                ),
            ),
        "*" => array(
            "user_function" => "page_login",
            "event" => array(
                "onLogin" => "validateLogin",
                ),
            ),
        ),
    "event" => array(
        "onLogout" => array(
            "user_function" => "event_onLogout",
            "transit" => array(
                "success" => "logout",
                ),
            ),
        "onLogin" => array(
            "user_function" => "event_onLogin",
            "transit" => array(
                "success" => "main",
                ),
            ),
        ),
    "guard" => array(
        "validateLogin" => array(
            "user_function" => "guard_validateLogin"
            ),
        ),
    );

// }}}
// {{{ page_login()

function page_login(&$runner, $page, &$bookmark, $params)
{
    return "templates/login_login.html";
}

// }}}
// {{{ page_main()

function page_main(&$runner, $page, &$bookmark, $params)
{
    $user_id = $_SESSION['user_id'];
    // count up demo.
    $count = $_SESSION['count'];
    $count++;
    $_SESSION['count'] = $count;

    $renderer =& $runner->getRenderer();

    $renderer->set('user_id', $user_id);
    $renderer->set('count', $count);
    return "templates/login_main.html";
}

// }}}
// {{{ page_logout()

function page_logout(&$runner, $page, &$bookmark, $params)
{
    return "templates/login_logout.html";
}

// }}}
// {{{ event_onLogout()

function event_onLogout(&$runner, $event, &$bookmark, $params)
{
    session_destroy();
    return "success";
}

// }}}
// {{{ event_onLogin()

function event_onLogin(&$runner, $event, &$bookmark, $params)
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
// {{{ guard_validateLogin()

function guard_validateLogin(&$runner, $event, &$bookmark, $params)
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

    // update Bookmark Container's 'identKey' attributes
    // (only for FileStoreContainer)
    $bc =& $bookmark->getBookmarkContainer();
    $ikeys = $bc->getAttribute('identKeys');
    $ikeys['session_id'] = $sid_new;
    $bc->setAttribute('identKeys', $ikeys);

    return true;
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
