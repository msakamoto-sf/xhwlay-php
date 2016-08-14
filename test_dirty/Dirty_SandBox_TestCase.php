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
 * This test case is prepared for whitebox testing with changing internal
 * source code.
 * This test case is separated from normal test cases.
 * So, we call this test case file as "Dirty SandBox".
 *
 * This file is controlled by Suvbersion, but each downloaded file 
 * is not stable because each developers change, break, various codes 
 * in it. So, there's no mean to controll by svn and trust stability.
 *
 * @package Xhwlay
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Dirty_SandBox_TestCase.php 41 2008-02-11 15:35:08Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/ErrorStack.php");
require_once("Xhwlay/Bookmark/FileStoreContainer.php");

class Dirty_SandBox_TestCase extends UnitTestCase
{
    /**
     * data directory for FileStoreContainer test.
     *
     * NOTICE: set "datas" directory permissions writable.
     * NOTICE: This unit test check gc() behaviour. So, Take few seconds
     * to generate expired datas.
     **/
    var $dataDir = null;

    // {{{ setUp()

    function setUp()
    {
        $this->dataDir = dirname(__FILE__) . '/datas';
        Xhwlay_ErrorStack::clear();
        ob_end_flush();
    }

    // }}}
    // {{{ tearDown()

    function tearDown()
    {
        Xhwlay_ErrorStack::clear();
        ob_start();
    }

    // }}}
    // {{{ testErrors()

    /**
     * OKay. Here, UNLIMITED space.
     * Break source code, data, and find bugs. Or else, get away from here.
     *
     * :P
     *
     */
    function testErrors()
    {
        $expire = 300;
        $attrs = array(
            'dataDir' => $this->dataDir,
            'identKeys' => array(
                'ip_addr' => '127.0.0.1',
                'session_id' => 'ABC',
            ),
            'expire' => $expire,
        );
        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs);

        /*
        $GLOBALS['DIRTY_SANDBOX'] = 0;
        $this->assertTrue($c->load());
        $id = $c->getId();
        $this->assertTrue($c->save());
        Xhwlay_ErrorStack::pop();

        $GLOBALS['DIRTY_SANDBOX'] = 1;
        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs, $id);
        $this->assertFalse($c->load());
        */

        $this->assertFalse($c->load());
        $this->assertFalse($c->save());
        $error = Xhwlay_ErrorStack::pop();
        $this->assertEqual($error["code"], 
            XHWLAY_BOOKMARK_CONTAINER_EC_SAVE_FAILURE
        );
        var_dump($error);
        Xhwlay_ErrorStack::clear();

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
