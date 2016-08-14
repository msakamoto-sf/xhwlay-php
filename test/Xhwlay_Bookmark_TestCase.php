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
 * @version $Id: Xhwlay_Bookmark_TestCase.php 49 2008-02-14 04:00:34Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/ErrorStack.php");
require_once("Xhwlay/Bookmark.php");

class Xhwlay_Bookmark_TestCase_AbstractContainer
{
    function getId() {}
    function dropBookmark($storyName_) {}
}
Mock::generate('Xhwlay_Bookmark_TestCase_AbstractContainer');

class Xhwlay_Bookmark_TestCase extends UnitTestCase
{
    // {{{ Test All Methods.

    function testBookmark()
    {
        $container =& new MockXhwlay_Bookmark_TestCase_AbstractContainer();
        $testId = "ABCD-1234";
        $testStoryName = "STORYNAME";
        $container->setReturnValue('getId', $testId);
        $container->expectOnce('dropBookmark', array($testStoryName));

        $b =& new Xhwlay_Bookmark($container, $testStoryName);

        // {{{ [1] Basic Setter/Getter

        $this->assertEqual($b->getContainerId(), $testId);
        $this->assertEqual($b->getStoryName(), $testStoryName);
        $this->assertNull($b->getPageName());
        $b->setPageName('pageName');
        $this->assertEqual($b->getPageName(), 'pageName');

        // }}}
        // {{{ [2] User Data Manupilation

        $this->assertNull($b->get("key1"));
        $b->set("key1", 123);
        $this->assertEqual($b->get("key1"), 123);
        $b->set("key2", 456);
        $this->assertEqual($b->get("key2"), 456);
        $b->remove("key1");
        $this->assertNull($b->get("key1"));
        $this->assertEqual($b->get("key2"), 456);
        $b->clear();
        $this->assertNull($b->get("key2"));

        // }}}
        // {{{ [3] Bookmark Destruction

        $b->destroy();

        // }}}

        $container->tally();

        // {{{ [4] {set|get}BookmarkContainer()

        $c1 =& new MockXhwlay_Bookmark_TestCase_AbstractContainer();
        $id1 = "abc";
        $c1->setReturnValue('getId', $id1);

        $b =& new Xhwlay_Bookmark($c1, "storyName");
        $this->assertEqual($b->getContainerId(), $id1);

        // test first_created flag.
        $this->assertTrue($b->first_created());
        $this->assertFalse($b->first_created());
        $this->assertFalse($b->first_created());

        $c2 =& new MockXhwlay_Bookmark_TestCase_AbstractContainer();
        $id2 = "def";
        $c2->setReturnValue('getId', $id2);
        $b->setBookmarkContainer($c2);
        $this->assertEqual($b->getContainerId(), $id2);

        $b->setBookmarkContainer($c1);
        $this->assertEqual($b->getContainerId(), $id1);

        $bc =& $b->getBookmarkContainer();
        $this->assertEqual($bc->getId(), $id1);

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
