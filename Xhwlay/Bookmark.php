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
 * Xhwlay Bookmark
 *
 * @package Xhwlay
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Bookmark.php 49 2008-02-14 04:00:34Z msakamoto-sf $
 */

/**
 * Xhwlay Bookmark
 *
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @since 1.0.0
 */
class Xhwlay_Bookmark
{
    // {{{ properties

    /**
     * Story name which associated with.
     *
     * @var string
     * @access private
     * @since 1.0.0
     */
    var $_storyName = null;

    /**
     * Page name currently refer.
     *
     * @var string
     * @access private
     * @since 1.0.0
     */
    var $_pageName = null;

    /**
     * User data.
     *
     * @var array
     * @access private
     * @since 1.0.0
     */
    var $_data = array();

    /**
     * parent Bookmark Container
     *
     * @var Xhwlay_Bookmark_AbstractContainer
     * @access private
     * @since 1.0.0
     */
    var $_container = null;

    /**
     * first created flip-flop flag
     *
     * @var boolean
     * @access private
     * @since 1.0.0
     */
    var $_first_created = true;

    // }}}
    // {{{ constructor

    /**
     * Constructor
     *
     * @access public
     * @param Xhwlay_Bookmark_AbstractContainer
     * @param string Story name which associate with
     * @since 1.0.0
     */
    function Xhwlay_Bookmark(&$container, $storyName)
    {
        $this->_container =& $container;
        $this->_storyName = $storyName;
    }

    // }}}
    // {{{ getContainerId()

    /**
     * Get ID of parent Bookmark Container
     *
     * @access public
     * @return string Container ID
     * @since 1.0.0
     */
    function getContainerId()
    {
        return $this->_container->getId();
    }

    // }}}
    // {{{ getBookmarkContainer()

    /**
     * Return bookmark container instance.
     *
     * @access public
     * @return object Xhwlay_Bookmark_AbstractContainer instance reference.
     * @since 1.0.0
     */
    function &getBookmarkContainer()
    {
        return $this->_container;
    }

    // }}}
    // {{{ setBookmarkContainer()

    /**
     * Set Bookmark Container
     *
     * @access public
     * @param Xhwlay_Bookmark_AbstractContainer
     * @since 1.0.0
     */
    function setBookmarkContainer(&$container)
    {
        $this->_container =& $container;
    }

    // }}}
    // {{{ getStoryName()

    /**
     * Get story name associated with.
     *
     * @access public
     * @return string story name
     * @since 1.0.0
     */
    function getStoryName()
    {
        return $this->_storyName;
    }

    // }}}
    // {{{ getPageName()

    /**
     * Get current page name.
     *
     * @access public
     * @return string page name
     * @since 1.0.0
     */
    function getPageName()
    {
        return $this->_pageName;
    }

    // }}}
    // {{{ setPageName()

    /**
     * Set new page name.
     *
     * @access public
     * @param string page name
     * @since 1.0.0
     */
    function setPageName($pageName)
    {
        $this->_pageName = $pageName;
    }

    // }}}
    // {{{ get()

    /**
     * Get user data associated with key.
     *
     * @access public
     * @param string key
     * @return mixed user data
     *               (If given key is not exists, return null)
     * @since 1.0.0
     */
    function get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return null;
        }
    }

    // }}}
    // {{{ set()

    /**
     * Set user data associated with key.
     *
     * @access public
     * @param string key
     * @param mixed user data
     * @since 1.0.0
     */
    function set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    // }}}
    // {{{ remove()

    /**
     * Remove user data associated with key.
     *
     * @access public
     * @param string key which you want to remove
     * @since 1.0.0
     */
    function remove($key)
    {
        if (!isset($this->_data[$key])) { return; }
        unset($this->_data[$key]);
    }

    // }}}
    // {{{ clear()

    /**
     * Clear ALL user datas.
     *
     * @access public
     * @since 1.0.0
     */
    function clear()
    {
        unset($this->_data);
        $this->_data = array();
    }

    // }}}
    // {{{ destroy()

    /**
     * DESTROY this Bookmark.
     *
     * If you call this, Bookmark data is destroyed only "on memory".
     * So, actual data still remains in Bookmark Container data storing
     * system, unless anyone calls "Bookmark Container"::save() method.
     *
     * Even though you can restore Bookmarks by "Bookmark Container"::load()
     * method, we STRONGLY recommend once you call destroy(), you sholudn't
     * shouldn't touch destroyed Bookmark instance.
     *
     * (destory() doesn't call its own clear() method, and, doesn't set
     *  null to '$this'.)
     *
     * @access public
     * @since 1.0.0
     */
    function destroy()
    {
        $this->_container->dropBookmark($this->_storyName);
    }

    // }}}
    // {{{ first_created()

    /**
     * Get first created or not , and, unset first_created flag.
     *
     * @access public
     * @since 1.0.0
     */
    function first_created()
    {
        $ret = $this->_first_created;
        $this->_first_created = false;
        return $ret;
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
