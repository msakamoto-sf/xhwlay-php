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
 * @version $Id: Xhwlay_Bookmark_AbstractContainer_TestCase.php 39 2008-02-11 14:17:34Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/ErrorStack.php");
require_once("Xhwlay/Bookmark/AbstractContainer.php");

class Xhwlay_Bookmark_AbstractContainer_TestCase extends UnitTestCase
{
    // {{{ Test All Methods.

    function testAbstractContainer()
    {
        // {{{ [1] Constructor parameters test

        $c =& new Xhwlay_Bookmark_AbstractContainer(array(
            "gc_probability" => 3,
            "gc_divisor" => 10,
            "gc_maxlifetime" => 30
            ));
        // TODO Bad Practice. Only PHP4. If you knows good idea for
        // accessing private/protected property, pleas teach me.
        $this->assertEqual($c->_gc_probability, 3);
        $this->assertEqual($c->_gc_divisor, 10);
        $this->assertEqual($c->_gc_maxlifetime, 30);
        $bcid = Xhwlay_Util::bcid();
        $c =& new Xhwlay_Bookmark_AbstractContainer(array(), $bcid);
        $this->assertEqual($c->getId(), $bcid);

        // }}}
        // {{{ [2] Bookmark manupilation

        $this->assertEqual($c->countBookmarks(), 0);

        $this->assertFalse($c->hasBookmark("story1"));
        $b1 =& $c->getBookmark("story1", false); // disable auto gen
        $this->assertNull($b1);

        $b1 =& $c->getBookmark("story1"); // enable auto gen
        $this->assertTrue($c->hasBookmark("story1"));
        $this->assertEqual($b1->getStoryName(), "story1");
        $this->assertEqual($c->countBookmarks(), 1);
        $b2 =& $c->getBookmark("story2");
        $this->assertEqual($b2->getStoryName(), "story2");
        $this->assertEqual($c->countBookmarks(), 2);

        $bs =& $c->getAllBookmarks();
        $this->assertTrue(isset($bs["story1"]));
        $this->assertTrue(isset($bs["story2"]));
        $this->assertEqual($bs["story1"]->getStoryName(), "story1");
        $this->assertEqual($bs["story2"]->getStoryName(), "story2");

        $c->dropBookmark("story1");
        $bs =& $c->getAllBookmarks();
        $this->assertFalse(isset($bs["story1"]));
        $this->assertTrue(isset($bs["story2"]));
        $this->assertEqual($c->countBookmarks(), 1);

        $b3 =& $c->getBookmark("story3");
        $this->assertEqual($c->countBookmarks(), 2);
        $b3->set("a", 1);
        $b3->set("b", 2);

        $b3_2 =& $c->getBookmark("story3");
        $this->assertEqual($b3_2->get("a"), $b3->get("a"));
        $this->assertEqual($b3_2->get("b"), $b3->get("b"));

        $c->dropBookmark("story1");
        $c->dropBookmark("story2");
        $c->dropBookmark("story3");
        $c->dropBookmark("story4"); // illegal story
        $this->assertEqual($c->countBookmarks(), 0);

        // }}}
        // {{{ [3] GC Probability test

        // probability = 0, then, always NOT GC Timing.
        $c =& new Xhwlay_Bookmark_AbstractContainer(
            array("gc_probability" => 0)
            );
        for ($i = 0; $i < 10; $i++) {
            $this->assertFalse($c->isGCTiming());
        }

        // probability = divisor, always GC Timing.
        $c =& new Xhwlay_Bookmark_AbstractContainer(
            array("gc_probability" => 1, "gc_divisor" => 1)
            );
        for ($i = 0; $i < 10; $i++) {
            $this->assertTrue($c->isGCTiming());
        }

        // probability > divisor, always GC Timing.
        $c =& new Xhwlay_Bookmark_AbstractContainer(
            array("gc_probability" => 3, "gc_divisor" => 2)
            );
        for ($i = 0; $i < 10; $i++) {
            $this->assertTrue($c->isGCTiming());
        }

        // probability = 0, divisor = 0, always NOT GC Timing.
        $c =& new Xhwlay_Bookmark_AbstractContainer(
            array("gc_probability" => 0, "gc_divisor" => 0)
            );
        for ($i = 0; $i < 10; $i++) {
            $this->assertFalse($c->isGCTiming());
        }

        // Check manullay.
        $c =& new Xhwlay_Bookmark_AbstractContainer(
            array("gc_probability" => 3)
            );
        $s = 0;
        for($i = 0; $i < 1000; $i++) {
            if ($c->isGCTiming()) {
                $s++;
            }
        }
        echo "Expective probability : 3 / 100 percent.\n";
        echo "Actual probability : $s / 1000 percent.\n";
        // }}}
        // {{{ [4] Bookmark Container Attributes manupilation

        $c =& new Xhwlay_Bookmark_AbstractContainer(array(), $bcid);

        $attrs = $c->getAttributes();
        $this->assertEqual(count($attrs), 0);
        $this->assertFalse($c->hasAttribute("attr1"));
        $this->assertNull($c->getAttribute("attr1"));
        $this->assertFalse($c->hasAttribute("attr2"));
        $this->assertNull($c->getAttribute("attr2"));

        $c->setAttribute("attr1", 123);
        $this->assertTrue($c->hasAttribute("attr1"));
        $this->assertEqual($c->getAttribute("attr1"), 123);
        $this->assertFalse($c->hasAttribute("attr2"));
        $this->assertNull($c->getAttribute("attr2"));

        $c->setAttribute("attr2", 'ABC');
        $this->assertTrue($c->hasAttribute("attr1"));
        $this->assertEqual($c->getAttribute("attr1"), 123);
        $this->assertTrue($c->hasAttribute("attr2"));
        $this->assertEqual($c->getAttribute("attr2"), 'ABC');

        $attrs = $c->getAttributes();
        $this->assertEqual(count($attrs), 2);
        $this->assertEqual($attrs['attr1'], 123);
        $this->assertEqual($attrs['attr2'], 'ABC');

        $c->removeAttribute("attr1");
        $this->assertFalse($c->hasAttribute("attr1"));
        $this->assertNull($c->getAttribute("attr1"));
        $this->assertTrue($c->hasAttribute("attr2"));
        $this->assertEqual($c->getAttribute("attr2"), 'ABC');

        $attrs = $c->getAttributes();
        $this->assertEqual(count($attrs), 1);
        $this->assertEqual($attrs['attr2'], 'ABC');

        $c->removeAttribute("attr2");
        $this->assertFalse($c->hasAttribute("attr1"));
        $this->assertNull($c->getAttribute("attr1"));
        $this->assertFalse($c->hasAttribute("attr2"));
        $this->assertNull($c->getAttribute("attr2"));

        $attrs = $c->getAttributes();
        $this->assertEqual(count($attrs), 0);

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
