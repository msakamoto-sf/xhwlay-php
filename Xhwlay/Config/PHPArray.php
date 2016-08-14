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
 * Xhwlay Config Default PHP-Array Implementation
 *
 * @package Xhwlay
 * @subpackage Config
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: PHPArray.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

/**
 * requires
 */
require_once('Xhwlay/Config/Interface.php');
require_once('Xhwlay/Util.php');
// {{{ constants
// }}}
// {{{ Xhwlay_Config_PHPArray

/**
 * Basically implementation class of Xhwlay_Config_Interface.
 * It use global PHP-Array variable as configuration resource.
 *
 * Example code is shown below. "(*)" mark indicates item can be omitted.
 * <code>
 * $configVars = array(
 *     "story" => array(
 *         "name" => "story name",
 *         "bookmark" => "off", // (*):if omitted, default "on".
 *         ),
 *     "page" => array(
 *         "page1.aci1" => array(
 *             ...,
 *             "bookmark" => "on", // (*):if omitted, story scope is assumed.
 *             "next" => array(
 *                 "page2" => "barrier1",
 *                 "page3" => null,
 *                 ),
 *             "event" => array(
 *                 "event1" => "guard1",
 *                 "event2" => null,
 *                 ),
 *             ),
 *         "page1.aci2" => array(...),
 *         "page2.aci3" => array(...),
 *         "page2.*" => array(...),
 *         "page2" => array(...),
 *         "*.*" => array(...),
 *     "event" => array(
 *         "event1" => array(
 *             ...,
 *             "transit" => array(
 *                 "success" => "page2",
 *                 "error" => "page4",
 *                 ),
 *             ),
 *         ),
 *     "barrier" => array(
 *         "barrier1" => array(...),
 *         "barrier2" => array(...),
 *         ),
 *     "guard" => array(
 *         "guard1" => array(...),
 *         "guard2" => array(...),
 *         ),
 *     );
 * $config =& new Xhwlay_Config_PHPArray($configVars);
 * </code>
 *
 * Asterisk "*" means wildcad character.
 * <code>
 * "page1.user1" => array(...), // (1)
 * "*.user1" => array(...), // (2)
 * "*" => array(...), // (3)
 * </code>
 * If page is "page1" and aci is "user1", then (1).
 * If page is not "page1" and aci is "user1", then (2).
 * If page is not "page1" and aci is not "user1", then (3).
 *
 * ".aci" can be omitted. But be careful about priority.
 * <code>
 * "page1.user1" => array(...), // (1)
 * "page1.*" => array(...), // (2)
 * "page1" => array(...), // (3)
 * </code>
 * In this code, if page is "page1" and aci is "user1", then (1) will be 
 * page parameter, sure.
 * If aci is default, then (2) will be, not (3).
 * If (2) item is not defined and aci is default, (3) will be.
 *
 * <code>
 * "*.*" => array(...), // (1)
 * "*" => array(...), // (2)
 * </code>
 * Priority is higher in (1) rather than (2).
 *
 * More detailed and complicated pattern, see UnitTest source code.
 *
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @subpackage Config
 * @since 1.0.0
 * @see Xhwlay_Config_Interface
 */
class Xhwlay_Config_PHPArray extends Xhwlay_Config_Interface
{
    // {{{ properties

    /**
     * Configuration Array
     *
     * @var array
     * @access protected
     * @since 1.0.0
     */
    var $_config;

    // }}}
    // {{{ constructor

    /**
     * Constructor
     *
     * @access public
     * @param array Configuration array
     * @since 1.0.0
     */
    function Xhwlay_Config_PHPArray($config)
    {
        $this->_config = $config;
    }

    // }}}
    // {{{ getKeys()

    /**
     * Get page/barrier key variation
     *
     * @access public
     * @param string page or barrier name
     * @param string ACI
     * @return array
     * @since 1.0.0
     */
    function getKeys($key = null, $aci = "*")
    {
        if (empty($key)) {
            $key = "*";
        }
        $ret = array("*.*");
        if ($aci !== "*") {
            array_unshift($ret, "*.{$aci}");
        }
        if ($key === "*") {
            return $ret;
        }
        array_unshift($ret, "{$key}");
        array_unshift($ret, "{$key}.*");
        if ($aci === "*") {
            return $ret;
        }
        array_unshift($ret, "{$key}.{$aci}");
        return $ret;
    }

    // }}}
    // {{{ getStoryName();

    /**
     * @see Xhwlay_Config_Interface::getStoryName()
     */
    function getStoryName()
    {
        return @$this->_config['story']['name'];
    }

    // }}}
    // {{{ needsBookmark()

    /**
     * @see Xhwlay_Config_Interface::needsBookmark()
     */
    function needsBookmark($page = null, $aci = "*")
    {
        if (is_null($page)) {
            // if $page is omitted, story scope bookmark mode is returned.
            return $this->_storyNeedsBookmark();
        }

        // if $page is given, return page scope bookmark mode.
        $params = $this->getPageParams($page, $aci);
        if (!is_null($params) && isset($params['bookmark'])) {
            return Xhwlay_Util::isTrue($params['bookmark']) ||
                $this->isLastPage($page, $aci);
        }

        // if no pages are defined, return story scope bookmark mode.
        return $this->_storyNeedsBookmark();
    }

    // }}}
    // {{{ _storyNeedsBookmark()

    /**
     * Return story needs bookmark or not.
     *
     * @access protected
     * @return boolean
     * @since 1.0.0
     */
    function _storyNeedsBookmark()
    {
        if (isset($this->_config['story']['bookmark'])) {
            return Xhwlay_Util::isTrue($this->_config['story']['bookmark']);
        } else {
            // NOTE: story->bookmark key is not specified, default, true.
            return true;
        }
    }

    // }}}
    // {{{ getPageParams()

    /**
     * @see Xhwlay_Config_Interface::getPageParams()
     */
    function getPageParams($page = null, $aci = "*")
    {
        $keys = $this->getKeys($page, $aci);
        reset($keys);
        foreach ($keys as $key) {
            if (isset($this->_config['page'][$key])) {
                return $this->_config['page'][$key];
            }
        }
        if (isset($this->_config['page']['*'])) {
            return $this->_config['page']['*'];
        }
        return null;
    }

    // }}}
    // {{{ getEventParams()

    /**
     * @see Xhwlay_Config_Interface::getEventParams()
     */
    function getEventParams($event)
    {
        if (isset($this->_config['event'][$event])) {
            return $this->_config['event'][$event];
        }
        return null;
    }

    // }}}
    // {{{ getBarrierParams()

    /**
     * @see Xhwlay_Config_Interface::getBarrierParams()
     */
    function getBarrierParams($current, $next, $aci = "*")
    {
        $params = $this->getPageParams($current, $aci);
        $barrier = $params['next'];
        if (is_null($barrier) || !is_array($barrier) || count($barrier) == 0) {
            return null;
        }

        if (!isset($barrier[$next])) {
            return null;
        }

        $b = $barrier[$next];
        if (empty($b)) {
            return null;
        }

        if (!isset($this->_config['barrier'][$b])) {
            return null;
        }
        return $this->_config['barrier'][$b];
    }

    // }}}
    // {{{ getGuardParams()

    /**
     * @see Xhwlay_Config_Interface::getGuardParams()
     */
    function getGuardParams($page, $event, $aci = "*")
    {
        $params = $this->getPageParams($page, $aci);
        $events = $params['event'];
        if (is_null($events) || !is_array($events) || count($events) == 0) {
            return null;
        }

        if (!isset($events[$event])) {
            return null;
        }

        $g = $events[$event];
        if (empty($g)) {
            return null;
        }

        if (!isset($this->_config['guard'][$g])) {
            return null;
        }
        return $this->_config['guard'][$g];
    }

    // }}}
    // {{{ isLastPage()

    /**
     * @see Xhwlay_Config_Interface::isLastPage()
     */
    function isLastPage($current, $aci = "*")
    {
        $params = $this->getPageParams($current, $aci);
        $b = strtolower(trim(@$params['bookmark']));
        return ($b === "last") || ($b === ".");
    }

    // }}}
    // {{{ isNextPageOf()

    /**
     * @see Xhwlay_Config_Interface::isNextPageOf()
     */
    function isNextPageOf($current, $next, $aci = "*")
    {
        // get page params
        $params = $this->getPageParams($current, $aci);
        // if any page not found, return false.
        if (is_null($params)) {
            return false;
        }
        // no next targets specified, return false.
        if (!isset($params['next']) || !is_array($params['next'])) {
            return false;
        }
        return array_key_exists($next, $params['next']);
    }

    // }}}
    // {{{ isEventOf()

    /**
     * @see Xhwlay_Config_Interface::isEventOf()
     */
    function isEventOf($page, $event, $aci = "*")
    {
        // get page params
        $params = $this->getPageParams($page, $aci);
        // if any page not found, return false.
        if (is_null($params)) {
            return false;
        }
        // no event defined, return false.
        if (!isset($params['event']) || !is_array($params['event'])) {
            return false;
        }
        return array_key_exists($event, $params['event']);
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
