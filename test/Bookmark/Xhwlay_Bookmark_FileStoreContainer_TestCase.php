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
 * @package Xhwlay
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Xhwlay_Bookmark_FileStoreContainer_TestCase.php 58 2008-03-02 13:52:44Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/ErrorStack.php");
require_once("Xhwlay/Bookmark/FileStoreContainer.php");

class Xhwlay_Bookmark_FileStoreContainer_TestCase extends UnitTestCase
{
    /**
     * data directory for test.
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
    // {{{ Test constructors and other helper methods.

    function testBasicsAndHelper()
    {
        // {{{ [1] Check Default data directory

        $c =& new Xhwlay_Bookmark_FileStoreContainer(array());
        $this->assertEqual($c->dataDir(), ".");

        // }}}
        // {{{ [2] Check Constructor parameters

        $bcid = Xhwlay_Util::bcid();
        $c =& new Xhwlay_Bookmark_FileStoreContainer(
            array(
                "dataDir" => $this->dataDir,
                "identKeys" => array(
                    'ip_addr' => '127.0.0.1',
                    'session_id' => 'ABCDEFG',
                    ),
                "expire" => 300,
            ), 
            $bcid
        );
        $this->assertEqual($c->getId(), $bcid);
        $this->assertEqual($c->dataDir(), $this->dataDir);
        // TODO below fields are PRIVATE, so, we should not write these codes.
        $this->assertEqual($c->_identKeys['ip_addr'], '127.0.0.1');
        $this->assertEqual($c->_identKeys['session_id'], 'ABCDEFG');
        $this->assertEqual($c->_expire, 300);

        // }}}
        // {{{ [3] Check Data Directory manupilation and fileName()

        $newDataDir = dirname(__FILE__).'/data2';
        $c->dataDir($newDataDir);
        $this->assertEqual($c->dataDir(), $newDataDir);

        // self id
        $id = $c->getId();
        $rp = File_Util::realPath($newDataDir.'/xhwlay_bcdata_'.$id);
        $this->assertEqual($c->fileName(), $rp);

        // outer id
        $id = "bcid";
        $rp = File_Util::realPath($newDataDir.'/xhwlay_bcdata_'.$id);
        $this->assertEqual($c->fileName($id), $rp);

        // }}}
        // {{{ [4] Check fileExists(), destroy()

        $c->dataDir($this->dataDir); // resotre existing directory.

        // self id
        $filename = $c->fileName();
        $this->assertFalse($c->fileExists());

        touch($filename);
        $mt = filemtime($filename);
        $this->assertTrue($c->fileExists());

        $c->destroy();
        $this->assertFalse(file_exists($filename));
        $this->assertFalse($c->fileExists());

        // outer id
        $id = Xhwlay_Util::bcid();
        $filename = $c->fileName($id);
        $this->assertFalse($c->fileExists($id));

        touch($filename);
        $mt = filemtime($filename);
        $this->assertTrue($c->fileExists($id));

        $c->destroy($id);
        $this->assertFalse(file_exists($filename));
        $this->assertFalse($c->fileExists($id));

        // }}}
    }

    // }}}
    // {{{ Test load() and save() Normal route.

    function testNormalLoadSave()
    {
        $attrs = array(
            'dataDir' => $this->dataDir,
            'identKeys' => array(
                'ip_addr' => '127.0.0.1',
                'session_id' => 'ABC',
            ),
            'expire' => 10
        );
        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs);

        // {{{ [1] yet not save(), load(). file must not be exists.

        $this->assertFalse($c->fileExists());
        $id = $c->getId(); // save id for later load() test

        // }}}
        // {{{ [2] first load, save() was called, file must be exists.

        $this->assertTrue($c->load());
        $this->assertTrue($c->fileExists());

        // }}}
        // {{{ [3] create some bookmarks and save().

        $b1 =& $c->getBookmark("story1");
        $b1->set("key1_1", 123);
        $b1->set("key1_2", array(1, 2, 3));
        $b2 =& $c->getBookmark("story2");
        $b2->set("key2_1", 456);
        $b2->set("key2_2", array(4, 5, 6));
        $b3 =& $c->getBookmark("story3");
        $b3->set("key3_1", "789");
        $b3->set("key3_2", array(7, 8, 9));
        $this->assertTrue($c->save());

        // }}}
        // {{{ [4] load and check bookmarks (in expire limit).

        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs, $id);
        $this->assertTrue($c->load());
        $this->assertEqual($id, $c->getId());

        $b1 =& $c->getBookmark("story1");
        $b2 =& $c->getBookmark("story2");
        $b3 =& $c->getBookmark("story3");
        $this->assertEqual($b1->get("key1_1"), 123);
        $arr = $b1->get("key1_2");
        $this->assertEqual(count($arr), 3);
        $this->assertEqual($arr[0], 1);
        $this->assertEqual($arr[1], 2);
        $this->assertEqual($arr[2], 3);

        $this->assertEqual($b2->get("key2_1"), 456);
        $arr = $b2->get("key2_2");
        $this->assertEqual(count($arr), 3);
        $this->assertEqual($arr[0], 4);
        $this->assertEqual($arr[1], 5);
        $this->assertEqual($arr[2], 6);

        $this->assertEqual($b3->get("key3_1"), "789");
        $arr = $b3->get("key3_2");
        $this->assertEqual(count($arr), 3);
        $this->assertEqual($arr[0], 7);
        $this->assertEqual($arr[1], 8);
        $this->assertEqual($arr[2], 9);

        // }}}
        // {{{ [5] Check Xhwlay_Bookmark::$_container reference restored.

        // check bookmark reference behaviour is correct.
        $b1->set("key_4", "abc");
        $bx =& $c->getBookmark("story1");
        $this->assertEqual($bx->get("key_4"), "abc");

        // destory 1st bookmark, save and restore check.
        $b1->destroy();
        $bs =& $c->getAllBookmarks();
        $this->assertFalse(isset($bs["story1"]));
        $this->assertTrue(isset($bs["story2"]));
        $this->assertTrue(isset($bs["story3"]));
        $c->save();
        $c->load();
        $bs =& $c->getAllBookmarks();
        $this->assertFalse(isset($bs["story1"]));
        $this->assertTrue(isset($bs["story2"]));
        $this->assertTrue(isset($bs["story3"]));

        // destroy 2nd bookmark, save and restore check.
        $b2 =& $c->getBookmark("story2");
        $b2->destroy();
        $bs =& $c->getAllBookmarks();
        $this->assertFalse(isset($bs["story1"]));
        $this->assertFalse(isset($bs["story2"]));
        $this->assertTrue(isset($bs["story3"]));
        $c->save();
        $c->load();
        $bs =& $c->getAllBookmarks();
        $this->assertFalse(isset($bs["story1"]));
        $this->assertFalse(isset($bs["story2"]));
        $this->assertTrue(isset($bs["story3"]));

        // destroy last bookmark, save and restore check.
        $b3 =& $c->getBookmark("story3");
        $b3->destroy();
        $bs =& $c->getAllBookmarks();
        $this->assertFalse(isset($bs["story1"]));
        $this->assertFalse(isset($bs["story2"]));
        $this->assertFalse(isset($bs["story3"]));
        $c->save();
        $c->load();
        $bs =& $c->getAllBookmarks();
        $this->assertFalse(isset($bs["story1"]));
        $this->assertFalse(isset($bs["story2"]));
        $this->assertFalse(isset($bs["story3"]));

        $c->destroy();
        $this->assertEqual($id, $c->getId());
        $this->assertFalse($c->fileExists());

        // }}}
    }

    // }}}
    // {{{ Expire and invalid request check test

    function testExpireAndInvalidCheck()
    {
        $expire = 3;
        $attrs = array(
            'dataDir' => $this->dataDir,
            'identKeys' => array(
                'ip_addr' => '127.0.0.1',
                'session_id' => 'ABC',
            ),
            'expire' => $expire,
        );
        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs);
        $id = $c->getId();
        $this->assertTrue($c->load());
        $b =& $c->getBookmark('story1');
        $b->set('key1', 'ABCDEFG');
        $this->assertTrue($c->save());

        // {{{ [1] 'ip_addr' key is not same.

        $attrs['identKeys']['ip_addr'] = '127.0.0.2';
        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs, $id);
        $this->assertEqual($id, $c->getId());
        $this->assertTrue($c->load());
        $this->assertNotEqual($id, $c->getId());
        $bs = $c->getAllBookmarks();
        $this->assertEqual(count($bs), 0);
        $c->destroy();

        // }}}
        // {{{ [2] 'session_id' key is not same.

        $attrs['identKeys']['ip_addr'] = '127.0.0.1';
        $attrs['identKeys']['session_id'] = '123';
        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs, $id);
        $this->assertEqual($id, $c->getId());
        $this->assertTrue($c->load());
        $this->assertNotEqual($id, $c->getId());
        $bs = $c->getAllBookmarks();
        $this->assertEqual(count($bs), 0);
        $c->destroy();

        // }}}
        // {{{ [3] all identKeys are same.

        $attrs['identKeys']['ip_addr'] = '127.0.0.1';
        $attrs['identKeys']['session_id'] = 'ABC';
        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs, $id);
        $this->assertEqual($id, $c->getId());
        $this->assertTrue($c->load());
        $this->assertEqual($id, $c->getId());
        $b =& $c->getBookmark('story1');
        $this->assertEqual($b->get('key1'), 'ABCDEFG');
        $this->assertTrue($c->save());

        // }}}
        echo "now, check expire limit ...wait for {$expire} + 1 seconds.\n";
        sleep($expire + 1);
        // {{{ [4] expire limit over : destroyed and regenerated.

        $attrs['identKeys']['ip_addr'] = '127.0.0.1';
        $attrs['identKeys']['session_id'] = 'ABC';
        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs, $id);
        $this->assertEqual($id, $c->getId());
        $this->assertTrue($c->load());
        $this->assertNotEqual($id, $c->getId());
        $bs = $c->getAllBookmarks();
        $this->assertEqual(count($bs), 0);
        $c->destroy();

        // old expired id data file was destroyed
        $this->assertFalse($c->fileExists($id));

        // }}}
    }

    // }}}
    // {{{ Invalid enforce test

    function testEnforceInvalid()
    {
        $attrs = array(
            'dataDir' => $this->dataDir,
            'identKeys' => array(
                'ip_addr' => '127.0.0.1',
                'session_id' => 'ABC',
            ),
            'expire' => 300,
        );
        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs);
        $id = $c->getId();
        $this->assertTrue($c->load());
        $b =& $c->getBookmark('story1');
        $b->set('key1', 'ABCDEFG');
        $this->assertTrue($c->save());
        // {{{ [1] invalid manually.

        $c =& new Xhwlay_Bookmark_FileStoreContainer($attrs, $id);
        $this->assertTrue($c->load());
        $this->assertEqual($id, $c->getId());
        $this->assertTrue($c->invalidate());
        // invalidate, but file exist yet.
        $this->assertTrue($c->fileExists($id));

        // }}}
        // {{{ [2] another client request same identKeys, bcid

        $c2 =& new Xhwlay_Bookmark_FileStoreContainer($attrs, $id);
        $this->assertEqual($id, $c2->getId());
        $this->assertTrue($c2->load());
        // invalidated, new id is generated.
        $this->assertNotEqual($id, $c2->getId());
        $bs = $c2->getAllBookmarks();
        $this->assertEqual(count($bs), 0);
        $c2->destroy();

        // surely, orriginal id doesn't exist no more.
        $this->assertFalse($c->fileExists($id));

        // }}}
    }

    // }}}
    // {{{ Test gc() behaviours.

    function testGC()
    {
        $lifetime = 10; // 10 seconds expires
        $cnt = 10; // Container counts for test

        // {{{ [1] Setup : Generate GC Target data files.

        $ids1 = array(); // gc target IDs.
        for ($i = 0; $i < $cnt; $i++) {
            $c =& new Xhwlay_Bookmark_FileStoreContainer(array(
                "dataDir" => $this->dataDir,
                "gc_probability" => 1,
                "gc_divisor" => 1,
                "gc_maxlifetime" => $lifetime,
                ));
            $ids1[] = $c->getId();
            // create and save empty data file.
            $this->assertTrue($c->load());
            $this->assertTrue($c->save());
        }
        $files = File_Util::listDir($c->dataDir(), FILE_LIST_FILES);
        // check count of created files.
        $c1 = count($files);
        $this->assertEqual(count($files), $c1);

        // }}}
        echo "now, gc() invoking timer...wait for {$lifetime} + 1 seconds.\n";
        sleep($lifetime + 1);
        // {{{ [2] Setup : Generate NO GC Target data files.

        $ids2 = array(); // NO gc target IDs.
        for ($i = 0; $i < $cnt; $i++) {
            $c =& new Xhwlay_Bookmark_FileStoreContainer(array(
                "dataDir" => $this->dataDir,
                "gc_probability" => 1,
                "gc_divisor" => 1,
                "gc_maxlifetime" => 10,
                ));
            $ids2[] = $c->getId();
            // create and save empty data file.
            $this->assertTrue($c->load());
            $this->assertTrue($c->save());
        }
        $files = File_Util::listDir($c->dataDir(), FILE_LIST_FILES);
        // check count of created files.
        $c2 = count($files);
        $this->assertEqual(count($files), $c2);

        // }}}
        // {{{ [3] Invoke GC
        $c->gc();

        // check invoking INFO level message
        $error = Xhwlay_ErrorStack::pop();
        $this->assertEqual($error["code"],
            XHWLAY_BOOKMARK_CONTAINER_EC_GC_INVOKED);
        $this->assertEqual($error["level"],
            XHWLAY_ERRORSTACK_EL_DEBUG);
        $this->assertEqual($error["params"]["dataDir"],
            $c->dataDir());
        $this->assertEqual($error["params"]["total"],
            $c2);

        // check target id is correct.
        $results = $error["params"]["target"];
        $this->assertEqual(count($results), $c1);
        sort($results);
        sort($ids1);
        $this->assertEqual(count(array_diff($results, $ids1)), 0);

        // check NON target id is correct.
        $files2 = File_Util::listDir($c->dataDir(), FILE_LIST_FILES);
        $results = array();
        foreach ($files2 as $f) {
            $__fname = str_replace('xhwlay_bcdata_', '', $f->name);
            $results[] = $__fname;
        }
        sort($results);
        sort($ids2);
        $this->assertEqual(count(array_diff($results, $ids2)), 0);
        Xhwlay_ErrorStack::clear();

        // clean up
        foreach ($ids2 as $id) {
            $c->destroy($id);
        }

        // }}}
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
