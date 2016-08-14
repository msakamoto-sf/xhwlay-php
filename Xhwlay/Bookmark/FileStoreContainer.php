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
 * Xhwlay Bookmark Default File-Store Container
 *
 * @package Xhwlay
 * @subpackage Bookmark
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: FileStoreContainer.php 58 2008-03-02 13:52:44Z msakamoto-sf $
 */

/**
 * requires
 */
require_once('File/Util.php');
require_once('Xhwlay/Bookmark/AbstractContainer.php');

/**
 * Xhwlay Bookmark Default File-Store Container
 *
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @subpackage Bookmark
 * @since 1.0.0
 */
class Xhwlay_Bookmark_FileStoreContainer
    extends Xhwlay_Bookmark_AbstractContainer
{
    // {{{ properties

    /**
     * Data Directory (directory where Bookmark datas is stored)
     *
     * @var string
     * @access protected
     * @since 1.0.0
     */
    var $_dataDir = ".";

    /**
     * Identify keys
     *
     * @var array
     * @access protected
     * @since 1.0.0
     */
    var $_identKeys = array();

    /**
     * Expire second (Default : 600seconds (10min)
     *
     * @var integer
     * @access protected
     * @since 1.0.0
     */
    var $_expire = 600;

    /**
     * Data file pointer (php resource)
     *
     * @var resource
     * @access protected
     * @since 1.0.0
     */
    var $_fp = false;

    /**
     * current time
     *
     * @var integer
     * @access protected
     * @since 1.0.0
     */
    var $_now = 0;

    // }}}
    // {{{ constructor

    /**
     * Parameters are acceptable for "dataDir", "identKeys".
     *
     * <code>
     * $manager =& new Xhwlay_Bookmark_FileStoreContainer(
     *     array("dataDir" => "../../tmp/",
     *           "expire" => 3600, // 1hour
     *           "identKeys" => array(
     *               "ip_addr" => $_SERVER['REMOTE_ADDR'],
     *               "session_id" => session_id()
     *           )
     *     ));
     * </code>
     *
     * 'dataDir' is used for storing Bookmark Container physical data as file.
     * 'identKeys' are used for validating current client is consistent 
     * with previous accessed client. (ex. ip address, session id)
     *
     * @access public
     * @see Xhwlay_Bookmark_AbstractContainer::Xhwlay_Bookmark_AbstractContainer()
     * @since 1.0.0
     */
    function Xhwlay_Bookmark_FileStoreContainer($params, $id = null)
    {
        /*
         * This uses flock() for sever way.
         * We set ignore_user_abort() true for php not terminate script
         * execution when client disconnect.
         * This prevents file data break down.
         */
        ignore_user_abort(true);

        $acceptable_keys = array("dataDir", "expire", "identKeys");
        foreach ($params as $key => $value) {
            if (in_array($key, $acceptable_keys)) {
                $prop = "_".$key;
                $this->{$prop} = $value;
            }
        }
        $this->_now = time();
        parent::Xhwlay_Bookmark_AbstractContainer($params, $id);
    }

    // }}}
    // {{{ dataDir()

    /**
     * Get/Set data directory
     *
     * @access public
     * @param string new data directory (optional)
     * @return string current(old) data directory
     * @since 1.0.0
     */
    function dataDir($new = null)
    {
        $ret = $this->_dataDir;
        if (!is_null($new)) {
            $this->_dataDir = $new;
        }
        return $ret;
    }

    // }}}
    // {{{ fileName()

    /**
     * Return "Bookmark Data File" full-realpath name.
     *
     * NOTICE: This method calls File_Util::realPath() (PEAR), 
     * not pure php realpath() function.
     * So, it can result NON-EXISTANT PATHNAME.
     *
     * @link http://pear.php.net/package/File/docs/latest/ 
     * @link http://www.php.net/manual/en/function.realpath.php 
     * @access public
     * @param string Container ID (when omitted:$this->_id)
     * @return string full-realpath name
     * @since 1.0.0
     */
    function fileName($id = null)
    {
        return File_Util::realPath(
            $this->_dataDir . "/xhwlay_bcdata_" 
            . ( (is_null($id)) ? $this->_id : $id ));
    }

    // }}}
    // {{{ fileExists()

    /**
     * Return "Bookmark Data File" associated with "Container ID" 
     * is exists or not.
     *
     * NOTE: This method only check "EXISTS OR NOT", not check
     * "Readable/Writable or NOT".
     * Be careful file permissions.
     *
     * @access public
     * @param string Container ID (when omitted:$this->_id)
     * @return boolean If TRUE, file is exists.
     *                 If FALSE, file is not exists.
     * @since 1.0.0
     */
    function fileExists($id = null)
    {
        $filename = $this->fileName($id);
        return file_exists($filename);
    }

    // }}}
    // {{{ _regenerate_new_id()

    /**
     * regenerate new id.
     *
     * @access protected
     * @since 1.0.0
     */
    function _regenerate_new_id()
    {
        // re-generate new id
        $_old_id = $this->_id;
        while ($this->_id == $_old_id || $this->fileExists($this->_id)) {
            $this->_id = Xhwlay_Util::bcid();
        }
    }

    // }}}
    // {{{ load()

    /**
     * Load Bookmark data from Bookmark data file associated with
     * own Container ID.
     *
     * If error occurs, check Xhwlay_ErrorStack.
     *
     * @access public
     * @see Xhwlay_Bookmark_AbstractContainer::load()
     * @since 1.0.0
     */
    function load($auto_create = true)
    {
        $ret = $this->_load_open_lock() && 
            $this->_load_read();
        if (!$ret) {
            return false;
        }

        $is_expired = false;
        if (!$this->_load_validate($is_expired)) {

            if ($is_expired) {
                if (!$this->invalidate()) {
                    return false;
                }
                $this->destroy();
            } else {
                @fclose($this->_fp);
            }

            $this->_regenerate_new_id();

            $ret = $this->_load_open_lock() && 
                $this->_load_read();
            if (!$ret) {
                return false;
            }
        }

        return true;
    }

    // }}}
    // {{{ _load_open_lock()

    /**
     * Open and lock data file.
     *
     * If error occurs, check Xhwlay_ErrorStack.
     *
     * @access protected
     * @return boolean if open and lock success, return true. else, any error
     *                 occurs, return false.
     * @since 1.0.0
     */
    function _load_open_lock()
    {
        $filename = $this->fileName();

        // open read/write('b' for Win32) mode, and, set file pointer 
        // at eof. if not exists, create.
        $this->_fp = @fopen($filename, 'a+b');
        if ($this->_fp === false) {

            // retry.
            $this->_regenerate_new_id();
            $filename = $this->fileName();
            $this->_fp = @fopen($filename, 'a+b');
            if ($this->_fp === false) {
                Xhwlay_ErrorStack::push(
                    XHWLAY_BOOKMARK_CONTAINER_EC_LOAD_FAILURE,
                    "[$filename] doesn't exist, create failure.",
                    XHWLAY_ERRORSTACK_EL_ERROR,
                    array("filename" => $filename));
                 return false;
            }
        }

        if (flock($this->_fp, LOCK_EX) === false) {
            Xhwlay_ErrorStack::push(
                XHWLAY_BOOKMARK_CONTAINER_EC_LOAD_FAILURE,
                "[$filename] flock() failed.",
                XHWLAY_ERRORSTACK_EL_ERROR,
                array("filename" => $filename));
            return false;
        }

        return true;
    }

    // }}}
    // {{{ _load_read()

    /**
     * Read data and initialized internal properties.
     *
     * If error occurs, check Xhwlay_ErrorStack.
     *
     * @access protected
     * @return boolean if read success, return true. else, any error
     *                 occurs, return false.
     * @since 1.0.0
     */
    function _load_read()
    {
        $filename = $this->fileName();

        if (fseek($this->_fp, 0, SEEK_SET) == -1) {
            Xhwlay_ErrorStack::push(
                XHWLAY_BOOKMARK_CONTAINER_EC_LOAD_FAILURE,
                "[$filename] read fseek(0) failed.",
                XHWLAY_ERRORSTACK_EL_ERROR,
                array("filename" => $filename));
            return false;
        }

        $data = '';
        while (!feof($this->_fp)) {
            $_buf = fread($this->_fp, 8192);
            if ($_buf === false) {
                Xhwlay_ErrorStack::push(
                    XHWLAY_BOOKMARK_CONTAINER_EC_LOAD_FAILURE,
                    "[$filename] fread() failed.",
                    XHWLAY_ERRORSTACK_EL_ERROR,
                    array("filename" => $filename));
                return false;
            }
            $data .= $_buf;
        }

        if (strlen($data) == 0) {
            // first creation, then, don't read data(size 0!), only 
            // initialize Bookmark Container's internal attributes.
            $this->setAttribute('atime', $this->_now);
            $this->setAttribute('identKeys', $this->_identKeys);
            $this->_bookmarks = array();
            return true;
        }

        $_data = @unserialize($data);
        if ($_data === false) {
            Xhwlay_ErrorStack::push(
                XHWLAY_BOOKMARK_CONTAINER_EC_UNSERIALIZE,
                "Unserializeng bookmark data was failed.",
                XHWLAY_ERRORSTACK_EL_ERROR,
                array(
                    "filename" => $filename,
                    "data" => $data,
                )
            );
            return false;
        }

        $this->_attributes = $_data['attrs'];
        $bookmarks = $_data['bookmarks'];

        // RESTORE Xhwlay_Bookmark::$_container reference
        foreach ($bookmarks as $s => $b) {
            // Be careful for PHP "copy on write" zval, "reference/alias"
            // problems.
            $bookmark = $b;
            $bookmark->setBookmarkContainer($this);
            $story = $bookmark->getStoryName();
            $this->_bookmarks[$story] =& $bookmark;
            unset($bookmark);
        }

        return true;
    }

    // }}}
    // {{{ _load_validate()

    /**
     * Validate expire limit, ident Keys.
     *
     * If error occurs, check Xhwlay_ErrorStack.
     *
     * @access protected
     * @return boolean
     * @since 1.0.0
     */
    function _load_validate(&$is_expired)
    {
        $atime = $this->getAttribute('atime');
        if (($this->_now - $atime) > $this->_expire) {
            $is_expired = true;
            return false;
        }
        $identKeys = $this->getAttribute('identKeys');
        foreach ($identKeys as $key => $val) {
            if (!isset($this->_identKeys[$key]) ||
                $this->_identKeys[$key] != $val) {
                return false;
            }
        }
        return true;
    }

    // }}}
    // {{{ save()

    /**
     * Save Bookmark data to Bookmark data file associated with 
     * own Container ID.
     *
     * @access public
     * @see Xhwlay_Bookmark_AbstractContainer::save()
     * @since 1.0.0
     */
    function save()
    {
        $data = array(
            'attrs' => $this->_attributes,
            'bookmarks' => $this->_bookmarks
        );
        $result = $this->_save($data);

        $filename = $this->fileName();
        if (fclose($this->_fp) === false) {
            Xhwlay_ErrorStack::push(
                XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_FAILURE,
                "[$filename] fclose() failed.",
                XHWLAY_ERRORSTACK_EL_WARN,
                array("filename" => $filename));
        }

        return $result;
    }
    // }}}
    // {{{ invalidate()

    /**
     * Invalid bookmark container.
     *
     * @access protected
     * @see Xhwlay_Bookmark_AbstractContainer::save()
     * @since 1.0.0
     */
    function invalidate()
    {
        $this->setAttribute('atime', 0);
        $data = array(
            'attrs' => $this->_attributes,
            'bookmarks' => array()
        );

        $result = $this->_save($data);

        $filename = $this->fileName();
        if (fclose($this->_fp) === false) {
            Xhwlay_ErrorStack::push(
                XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_FAILURE,
                "[$filename] fclose() failed.",
                XHWLAY_ERRORSTACK_EL_WARN,
                array("filename" => $filename));
        }
        $this->_fp = null;

        return $result;
    }
    // }}}
    // {{{ _save()

    /**
     *
     * @access protected
     * @see Xhwlay_Bookmark_AbstractContainer::save()
     * @since 1.0.0
     */
    function _save(&$data)
    {
        $_data = serialize($data);
        $sz = strlen($_data);
        $filename = $this->fileName();

        if (fseek($this->_fp, 0, SEEK_SET) == -1) {
            Xhwlay_ErrorStack::push(
                XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_FAILURE,
                "[$filename] write fseek(0) failed.",
                XHWLAY_ERRORSTACK_EL_ERROR,
                array("filename" => $filename));
            return false;
        }

        if (ftruncate($this->_fp, 0) === false) {
            Xhwlay_ErrorStack::push(
                XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_FAILURE,
                "[$filename] write ftruncate(0) failed.",
                XHWLAY_ERRORSTACK_EL_ERROR,
                array("filename" => $filename));
            return false;
        }

        $len = fwrite($this->_fp, $_data, $sz);
        if ($len === false || $len != $sz) {
            Xhwlay_ErrorStack::push(
                XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_FAILURE,
                "Storing serialized bookmark data to [$filename] was failed.",
                XHWLAY_ERRORSTACK_EL_ERROR,
                array(
                    "filename" => $filename,
                    "data" => $data,
                )
            );
            return false;
        }
        Xhwlay_ErrorStack::push(
            XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_SUCCESS,
            "Storing serialized bookmark data to [$filename] was succeeded.",
            XHWLAY_ERRORSTACK_EL_DEBUG,
            array(
                "filename" => $filename,
                "data" => $data,
                "size" => $len,
            )
        );
        return true;
    }
    // }}}
    // {{{ destroy()

    /**
     * Delete Bookmark data file associated with given Container ID.
     *
     * @access public
     * @see Xhwlay_Bookmark_AbstractContainer::destroy()
     * @since 1.0.0
     */
    function destroy($id = null)
    {
        if (is_null($id)) {
            $id = $this->_id;
        }

        $filename = $this->fileName($id);
        @fclose($this->_fp);
        @unlink($filename);
    }

    // }}}
    // {{{ gc()

    /**
     * Simply comparing filemtime() for each Bookmark data file. 
     *
     * This method generate INFO level message which contains 
     * data directory, total count of datafiles, count of destroyed
     * files.
     *
     * How detecting expiration data is shown in following actual source code.
     * {@source}
     *
     * @access public
     * @see Xhwlay_Bookmark_AbstractContainer::gc()
     * @since 1.0.0
     */
    function gc()
    {
        $now = time();
        $files = File_Util::listDir($this->_dataDir, FILE_LIST_FILES);
        $target = array();
        foreach ($files as $file) {
            $mtime = $file->date;
            if (strpos($file->name, 'xhwlay_bcdata_') === false) {
                continue;
            }
            $id = str_replace('xhwlay_bcdata_', '', $file->name);
            if ($now - $mtime > $this->_gc_maxlifetime) {
                // Current time - last modified time is over against
                // TTL, then delete bookmark data file.
                $target[] = $id;
                @unlink($this->fileName($id));
            }
        }
        Xhwlay_ErrorStack::push(
            XHWLAY_BOOKMARK_CONTAINER_EC_GC_INVOKED,
            "Bookmark Data Garbage Collection was Invoked.",
            XHWLAY_ERRORSTACK_EL_DEBUG,
            array(
                "dataDir" => $this->_dataDir,
                "total" => count($files),
                "target" => $target,
            )
        );
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
