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
 * Xhwlay Bookmark Container Abstract Interface
 *
 * @package Xhwlay
 * @subpackage Bookmark
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: AbstractContainer.php 44 2008-02-11 17:48:42Z msakamoto-sf $
 */

/**
 * requires
 */
require_once('Xhwlay/Util.php');
require_once('Xhwlay/Bookmark.php');

// {{{ constants

/**
 * Xhwlay Bookmark Container Error Code:
 * loading Bookmark data was failed.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_BOOKMARK_CONTAINER_EC_LOAD_FAILURE')) {
    define('XHWLAY_BOOKMARK_CONTAINER_EC_LOAD_FAILURE', 0x200 | 0x01);
}

/**
 * Xhwlay Bookmark Container Error Code:
 * unserializing Bookmark data was failed.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_BOOKMARK_CONTAINER_EC_UNSERIALIZE')) {
    define('XHWLAY_BOOKMARK_CONTAINER_EC_UNSERIALIZE', 0x200 | 0x02);
}

/**
 * Xhwlay Bookmark Container Error Code:
 * storing Bookmark data was failed.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_FAILURE')) {
    define('XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_FAILURE', 0x200 | 0x03);
}

/**
 * Xhwlay Bookmark Container Error Code:
 * storing Bookmark data was succeeded.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_SUCCESS')) {
    define('XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_SUCCESS', 0x200 | 0x04);
}

/**
 * Xhwlay Bookmark Container Error Code:
 * GC invoked message.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_BOOKMARK_CONTAINER_EC_GC_INVOKED')) {
    define('XHWLAY_BOOKMARK_CONTAINER_EC_GC_INVOKED', 0x200 | 0x05);
}

// }}}
// {{{ Xhwlay_Bookmark_AbstractContainer

/**
 * Xhwlay Bookmark Container Abstract Base Class
 *
 * "Bookmark Container" manages multiple bookmarks with one "Container ID".
 * One "Bookmark" is associated with one "Story".
 * "Bookmark" knows what story associated with, what page is marked,
 * and other user datas in it.
 *
 * Relation among "Bookmark Container", "Container ID", "Bookmark" are
 * shown in the following figure.
 *<pre>
 * "Container ID" (Managed By "Bookmark-Container")('parent')
 *   |
 *   +-- "Bookmark"(1) (Xhwlay_Bookmark)('child')
 *   |     |
 *   |     +-- Story 
 *   |     +-- Page (which page currently refer)
 *   |     +-- Datas (PHP-Assoc-Array users can use freely)
 *   |
 *   +-- "Bookmark"(2) (Xhwlay_Bookmark)('child')
 *   |     |
 *   |     +-- Story 
 *   |     +-- Page (which page currently refer)
 *   |     +-- Datas (PHP-Assoc-Array users can use freely)
 *   =
 *   |
 *   +-- "Bookmark"(N) 
 *</pre>
 *
 * This class is abstract class of "Bookmakr Container".
 * Developers can extend/override own container which fit your
 * project's architecture.
 * Also, Bookmark Container can have 'Attributes' for container itselves.
 * You can add and use various values in it for your own architecture.
 *
 * When old bookmark container is deleted ?
 * Like many other languages and PHP session mechanism, 
 * "Bookmark Container" (should) implemets "Garbage Collection(GC)"
 * which deletes old bookmark containers.
 *
 * {@link Xhwlay_Bookmark_AbstractContainer} class only define interface for 
 * gc() method, but serves "GC invoking timing calculation" method.
 * "Now, gc() should be called" timing is decided probabilistic.
 * So, {@link Xhwlay_Bookmark_AbstractContainer::isGCTiming()} method 
 * is usable for deciding this.
 *
 * Usually, when and where gc() should be called is contorolled by
 * you, developer's application archtecture, not by framework.
 * See:
 * {@link Xhwlay_Bookmark_AbstractContainer::_gc_probability}
 * {@link Xhwlay_Bookmark_AbstractContainer::_gc_divisor}
 * {@link Xhwlay_Bookmark_AbstractContainer::_gc_maxlifetime}
 * {@link Xhwlay_Bookmark_AbstractContainer::gc()}
 * {@link Xhwlay_Bookmark_AbstractContainer::isGCTiming()}
 *
 * @author FengJing <feng-jing-gsyc-2s@glamenv-septzen.net>
 * @package Xhwlay
 * @subpackage Bookmark
 * @since 1.0.0
 */
class Xhwlay_Bookmark_AbstractContainer
{
    // {{{ properties

    /**
     * Container ID
     *
     * @var string
     * @access protected
     * @since 1.0.0
     */
    var $_id = null;

    /**
     * Attributes
     *
     * @var array Assoc-Array of Bookmark Container Attributes
     * @access protected
     * @since 1.0.0
     */
    var $_attributes = array();

    /**
     * Bookmarks
     *
     * @var array Assoc-Array of "StoryName" => Xhwlay_Bookmark
     * @access protected
     * @since 1.0.0
     */
    var $_bookmarks = array();

    /**
     * "Bookmark Container" controls "Garbage Collection" invoking timing by
     * convination of 
     * {@link Xhwlay_Bookmark_AbstractContainer::$_gc_probability}
     * and {@link Xhwlay_Bookmark_AbstractContainer::$_gc_divisor}.
     *
     * Default {@link Xhwlay_Bookmark_AbstractContainer::$_gc_probability} is 
     * 1.
     *
     * More detail, see 
     * {@link Xhwlay_Bookmark_AbstractContainer::$_gc_divisor}.
     *
     * If 0 is given, "Garbage Collection" never invoke.
     *
     * @var integer
     * @access protected
     * @since 1.0.0
     */
    var $_gc_probability = 1;

    /**
     * Probability of invoking "Garbage Collection" is calculated by 
     * following expression.
     *
     * {@link Xhwlay_Bookmark_AbstractContainer::$_gc_probability} / 
     * {@link Xhwlay_Bookmark_AbstractContainer::$_gc_divisor}
     *
     * Default {@link Xhwlay_Bookmark_AbstractContainer::$_gc_divisor} is 100.
     *
     * So, by default values, probability is 1/100 (= 1%).
     * This means that "Garbage Collection" invoke 1 time per 100 
     * request.
     *
     * If {@link Xhwlay_Bookmark_AbstractContainer::$_gc_divisor} <= 
     * {@link Xhwlay_Bookmark_AbstractContainer::$_gc_probability}, 
     * always GC timing.
     *
     * Exactly, CALCULATION AND GC INVOKE TIMING ARE FULLY DEPEND ON
     * ACTUAL BOOKMARK CONTAINER CLASS.
     * Because actual implementation is done by your BookmarkContainer 
     * and your {@link Xhwlay_Controller} class.
     * Xhwlay_Bookmark_AbstractContainer class services only calculation 
     * method of gc probability.
     * When and where calculation should be done and gc should be called 
     * are known by ONLYL ACTUAL BOOKMARK CONTAINER (equals yours, developer) 
     * CLASS, not AbstractContainer.
     *
     * It's minimal responsibility policy is most important concept
     * of Xhwlay. Developers should be FREE FROM FLAMEWORK, GET LOGIC
     * CONTROLLS BACK FROM FLAMEWORK!!
     *
     * @var integer
     * @access protected
     * @since 1.0.0
     */
    var $_gc_divisor = 100;

    /**
     * Time To Live by second.
     * Default value is 1 day (86400 seconds).
     *
     * {@link Xhwlay_Bookmark_AbstractContainer} doesn't know how this ttl 
     * is handled, calculated with, and effective.
     * ACTUAL BOOKMARK CONTAINER CLASS, implements AbstractContainer, only
     * knows that.
     *
     * @var integer by seconds
     * @access protected
     * @since 1.0.0
     */
    var $_gc_maxlifetime = 86400; 

    // }}}
    // {{{ constructor

    /**
     * Constructor
     *
     * Constructor can accept variable parameters through PHP-Assoc-Array.
     * By default, Xhwlay_Bookmark_AbstractContainer accept arrays 
     * like following example:
     *
     * <code>
     * $manager =& new Xhwlay_Bookmark_AbstractContainer(
     *                  array(
     *                      "gc_probability" => 3,
     *                      "gc_divisor" => 20,
     *                      "gc_maxlifetime" => 3600,
     *                  ));
     * </code>
     *
     * If any other key is specified, Xhwlay_Bookmark_AbstractContainer
     * only ignores these "key => value" pairs.
     *
     * Key name of array should be string stripped headding "_" from 
     * property name of class.
     * Previous example means, 
     *
     * <code>
     * $manager->_gc_probability = 3;
     * $manager->_gc_divisor = 20;
     * $manager->_gc_maxlifetime = 3600;
     * </code>
     *
     * Should developers follow this rule ? NO, IT'S FREE !!
     * But don't forget passing array parameters ('array()' as dummy, 
     * okay ? sure! :-) ) to parent constructor (sure, is 
     * Xhwlay_Bookmark_AbstractContainer::Xhwlay_Bookmark_AbstractContainer())
     * in your own "Bookmark Container" class implementation.
     *
     * Here, example constructor code.
     * <code>
     * class Your_Own_Bookmark_Container 
     *     extends Xhwlay_Bookmark_AbstractContainer
     * {
     *     var $_prop1 = "";
     *     var $_prop2 = "";
     *     ...
     *     function Your_Own_Bookmark_Container($params, $id = null)
     *     {
     *         $acceptable_keys = array("prop1", "prop2", ...);
     *         foreach ($params as $key => $value) {
     *             if (in_array($key, $acceptable_keys)) {
     *                 $prop = "_".$key;
     *                 $this->{$prop} = $value;
     *             }
     *         }
     *         parent::Xhwlay_Bookmark_AbstractContainer($params, $id);
     *     }
     * ...
     * </code>
     *
     * NOTICE: Never use "id" and "bookmarks" key in $params.
     * "_id" and "_bookmarks" is specail and important property 
     * of Xhwlay_Bookmark_AbstractContainer. There's NO GUARANTEE when 
     * you set these keys and overwrite these properties.
     * See:
     * {@link Xhwlay_Bookmark_AbstractContainer::$_id}
     * {@link Xhwlay_Bookmark_AbstractContainer::$_bookmarks}
     *
     * @access public
     * @param mixed User defined parameters to configuring Bookmark
     *              Container. 
     * @param string Bookmark ID. if not given (default), new Bookmark ID is
     *               set.
     * @since 1.0.0
     */
    function Xhwlay_Bookmark_AbstractContainer($params, $id = null)
    {
        $this->_id = ( (empty($id)) ? Xhwlay_Util::bcid() : $id);
        $keys = array("gc_probability", "gc_divisor", "gc_maxlifetime");
        foreach ($params as $key => $value) {
            if (in_array($key, $keys)) {
                $prop = "_".$key;
                $this->{$prop} = $value;
            }
        }
    }

    // }}}
    // {{{ getId()

    /**
     * Get Container ID
     *
     * @access public
     * @return string
     * @since 1.0.0
     */
    function getId()
    {
        return $this->_id;
    }

    // }}}
    // {{{ load()

    /**
     * Load and restore Bookmark datas from somewhere.
     *
     * You may need to "transaction-lock" features between 'load' and 'save'.
     * And you may implement "id-validation" features which checks given 
     * Bookmark-Container-Id is correct.
     * Then, you can implement various logics, lock process, in 'load' method.
     *
     * @abstract
     * @access public
     * @param boolean If true(default) and datasource doesn't exists 
     *                (for example, 1st call of load()), automatically 
     *                calls save(), return its result.
     *                If false given, maybe false is returned when 1st
     *                load() because data storage may not exists yet.
     * @return boolean TRUE if loading and restoring complete successfully.
     *                 If error occurs, return FALSE 
     *                 (check Xhwlay_ErrorStack).
     * @since 1.0.0
     */
    function load($auto_create = true)
    {
        die(__CLASS__.'::load($auto_create) must be overriden');
    }

    // }}}
    // {{{ save()

    /**
     * Save Bookmark datas.
     *
     * @abstract
     * @access public
     * @return boolean TRUE if saving complete successfully.
     *                 If error occurs, return FALSE
     *                 (check Xhwlay_ErrorStack).
     * @since 1.0.0
     */
    function save()
    {
        die(__CLASS__.'::save() must be overriden');
    }

    // }}}
    // {{{ hasAttribute()

    /**
     * Return given attribute is set or not.
     *
     * @access public
     * @param string attribute name
     * @return boolean
     * @since 1.0.0
     */
    function hasAttribute($key)
    {
        return isset($this->_attributes[$key]);
    }

    // }}}
    // {{{ getAttribute()

    /**
     * Return given attribute.
     *
     * NOTE: This DOESN'T return reference.
     * Bookmark-Container's attributes are prepared for scalar/array values.
     * Currently, we don't support reference value like object instance, 
     * specially, in PHP4.
     *
     * @access public
     * @param string attribute name
     * @return mixed If not defined, return null.
     * @since 1.0.0
     */
    function getAttribute($key)
    {
        if (isset($this->_attributes[$key])) {
            return $this->_attributes[$key];
        } else {
            return null;
        }
    }

    // }}}
    // {{{ setAttribute()

    /**
     * Set a value for attribute.
     *
     * @access public
     * @param string attribute name
     * @param mixed attribute value
     * @since 1.0.0
     */
    function setAttribute($key, $value)
    {
        $this->_attributes[$key] = $value;
    }

    // }}}
    // {{{ removeAttribute()

    /**
     * Unset given attribute.
     *
     * @access public
     * @param string attribute name
     * @since 1.0.0
     */
    function removeAttribute($key)
    {
        unset($this->_attributes[$key]);
    }

    // }}}
    // {{{ getAttributes()

    /**
     * Return all attributes.
     *
     * @access public
     * @return array all attributes.
     * @since 1.0.0
     */
    function getAttributes()
    {
        return $this->_attributes;
    }

    // }}}
    // {{{ hasBookmark()

    /**
     * Get a bookmark exists or not which associated with given storyName.
     *
     * @access public
     * @param string story name
     * @return boolean
     * @since 1.0.0
     */
    function hasBookmark($storyName)
    {
        return isset($this->_bookmarks[$storyName]);
    }

    // }}}
    // {{{ getBookmark()

    /**
     * Get Bookmark instance from unserialized stored data.
     * If instance doesn't exist and $auto_gen = true(default), 
     * container creates new instance, associates with storyName, 
     * and return it.
     *
     * @access public
     * @param string story name
     * @param boolean auto generate (default: true)
     * @return Xhwlay_Bookmark (If not exist and $auto_gen = false, null)
     * @since 1.0.0
     */
    function &getBookmark($storyName, $auto_gen = true)
    {
        $ret = null;
        if (!isset($this->_bookmarks[$storyName])) {
            if (!$auto_gen) {
                return $ret;
            }
            $this->_bookmarks[$storyName] =& 
                new Xhwlay_Bookmark($this, $storyName);
        }
        return $this->_bookmarks[$storyName];
    }

    // }}}
    // {{{ dropBookmark()

    /**
     * Drop Bookmark instance specified by given story name.
     * It is done "in-memory", so, you can restore by calling load()
     * method.
     *
     * @access public
     * @param string stroy name
     * @since 1.0.0
     */
    function dropBookmark($storyName)
    {
        unset($this->_bookmarks[$storyName]);
    }

    // }}}
    // {{{ getAllBookmarks()

    /**
     * Get All Bookmark datas.
     * NOTE: return value is REFERENCE. Be careful for operating among
     * Bookmark datas.
     *
     * @access public
     * @return array Assoc-Array of "StoryName" => Xhwlay_Bookmark
     * @since 1.0.0
     */
    function &getAllBookmarks()
    {
        return $this->_bookmarks;
    }

    // }}}
    // {{{ countBookmarks()

    /**
     * Count Bookmarks.
     *
     * This method is short cut of following code.
     * <code>
     * $bookmarks =& $bookmarkContainer->getAllBookmarks();
     * $countBookmarks = count($bookmarks);
     * </code>
     *
     * @access public
     * @return integer count of bookmarks.
     * @since 1.0.0
     */
    function countBookmarks()
    {
        return count($this->_bookmarks);
    }

    // }}}
    // {{{ destroy()

    /**
     * Destroy Bookmark datas specified by Container ID.
     * If Container ID is not specified, its own Container ID is 
     * assumed.
     *
     * Why this method should accept other Container ID, not only own ID ?
     * Because gc() calls this method internally passing Container ID 
     * which expire TTL
     * ({@link Xhwlay_Bookmark_AbstractContainer::_gc_maxlifetime}).
     *
     * @abstract
     * @access public
     * @param string Container ID. If not given (default), 
     *               its own Container ID is used.
     * @since 1.0.0
     */
    function destroy($id = null)
    {
        die(__CLASS__.'::destroy($id) must be overriden');
    }

    // }}}
    // {{{ gc()

    /**
     * Garbage collect other expired bookmark datasources.
     * {@link Xhwlay_Bookmark_AbstractContainer} doesn't implement actual 
     * logic and process.
     * Developers and users must extends/implements AbstractContainer, 
     * implements their own solution.
     *
     * Developers should call destroy() method for each expired "Container ID"
     * after gc() invoking().
     *
     * @abstract
     * @access protected
     * @since 1.0.0
     */
    function gc()
    {
        die(__CLASS__.'::gc() must be overriden');
    }

    // }}}
    // {{{ isGCTiming()

    /**
     * Decide gc timing is now or not now.
     * If TRUE returned, {@link Xhwlay_Bookmark_AbstractContainer::gc()}
     * should be called.
     * If FALSE returned, that method should not be called.
     *
     * @access public
     * @return boolean
     * @since 1.0.0
     */
    function isGCTiming()
    {
        return Xhwlay_Util::diceRoller(
            $this->_gc_probability,
            $this->_gc_divisor);
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
