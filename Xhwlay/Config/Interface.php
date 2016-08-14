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
 * Xhwlay Config Interface
 *
 * @package Xhwlay
 * @subpackage Config
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Interface.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

// {{{ constants
// }}}
// {{{ Xhwlay_Config_Interface

/**
 * Interface of Xhwlay Config Classes.
 *
 * This class defines Xhwlay Page Flow Configuration Interface.
 *
 * @abstract
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @subpackage Config
 * @since 1.0.0
 */
class Xhwlay_Config_Interface
{
    // {{{ properties
    // }}}
    // {{{ getStoryName();

    /**
     * Get Story name of configuration resource.
     *
     * @abstract
     * @access public
     * @return string stroy name
     * @since 1.0.0
     */
    function getStoryName()
    {
    }

    // }}}
    // {{{ needsBookmark()

    /**
     * Get Bookmark is ON or OFF about given $page or current story.
     *
     * Note: Defining Bookmark ON/OFF per page is deprecated.
     *
     * @abstract
     * @access public
     * @param string page name (omitted, current story is assumed)
     * @param string access control identifier(omitted, "*" is assumed)
     * @return boolean If bookmark is ON, return TRUE. If not, return FALSE.
     * @since 1.0.0
     */
    function needsBookmark($page = null, $aci = "*")
    {
    }

    // }}}
    // {{{ getPageParams()

    /**
     * Get page action parameters associated with $aci and $page.
     *
     * @abstract
     * @access public
     * @param string page name
     * @param string access control identifier(omitted, "*" is assumed)
     * @return mixed page action resource. If not defined or page action 
     *         should not be invoked, return null.
     * @since 1.0.0
     */
    function getPageParams($page, $aci = "*")
    {
    }

    // }}}
    // {{{ getEventParams()

    /**
     * Get event action parameters associated with $event.
     *
     * @abstract
     * @access public
     * @param string event name
     * @return mixed page action resource. If not defined or page action 
     *         should not be invoked, return null.
     * @since 1.0.0
     */
    function getEventParams($event)
    {
    }

    // }}}
    // {{{ getBarrierParams()

    /**
     * Get barrier action parameters associated with $aci and $page.
     *
     * @abstract
     * @access public
     * @param string current page name
     * @param string next page name
     * @param string access control identifier(omitted, "*" is assumed)
     * @return mixed barrier action resource. If not defined or barrier 
     *        should be skipped, return null.
     * @since 1.0.0
     */
    function getBarrierParams($current, $next, $aci = "*")
    {
    }

    // }}}
    // {{{ getGuardParams()

    /**
     * Get guard action parameters associated with $aci and $page.
     *
     * @abstract
     * @access public
     * @param string current page name
     * @param string event name
     * @param string access control identifier(omitted, "*" is assumed)
     * @return mixed barrier action resource. If not defined or guard 
     *        should be skipped, return null.
     * @since 1.0.0
     */
    function getGuardParams($page, $event, $aci = "*")
    {
    }

    // }}}
    // {{{ isLastPage()

    /**
     * Return whether given page is last page or not in the story.
     *
     * @abstract
     * @access public
     * @param string page name
     * @param string access control identifier(omitted, "*" is assumed)
     * @return boolean TRUE if last page, FALSE if not.
     * @since 1.0.0
     */
    function isLastPage($current, $aci = "*")
    {
    }

    // }}}
    // {{{ isNextPageOf()

    /**
     * Return whether given page is valid transition targets of current page.
     *
     * @abstract
     * @access public
     * @param string current page name
     * @param string next page name
     * @param string access control identifier(omitted, "*" is assumed)
     * @return boolean TRUE if valid, FALSE if not.
     * @since 1.0.0
     */
    function isNextPageOf($current, $next, $aci = "*")
    {
    }

    // }}}
    // {{{ isEventOf()

    /**
     * Return whether given event is defined in current page.
     *
     * @abstract
     * @access public
     * @param string current page name
     * @param string event name
     * @param string access control identifier(omitted, "*" is assumed)
     * @return boolean TRUE if valid, FALSE if not.
     * @since 1.0.0
     */
    function isEventOf($page, $event, $aci = "*")
    {
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
