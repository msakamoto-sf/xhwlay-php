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
 * @version $Id: Xhwlay_Renderer_AbstractRenderer_TestCase.php 39 2008-02-11 14:17:34Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/ErrorStack.php");
require_once("Xhwlay/Renderer/AbstractRenderer.php");

class Xhwlay_Renderer_AbstractRenderer_TestCase extends UnitTestCase
{
    // {{{ Test All Methods except render().

    function testAbstractRenderer()
    {
        $r =& new Xhwlay_Renderer_AbstractRenderer();

        $vs = $r->getAll();
        $this->assertEqual(count($vs), 0);
        $this->assertNull($r->get("key1"));

        // {{{ [1] manupilate with assoc-array

        $r->set("key1", 123);
        $r->set("key2", 456);
        $this->assertEqual($r->get("key1"), 123);
        $this->assertEqual($r->get("key2"), 456);

        $vs = $r->getAll();
        $this->assertEqual(count($vs), 2);
        $this->assertEqual($vs["key1"], 123);
        $this->assertEqual($vs["key2"], 456);

        $r->setAll(array("key3" => 789, "key4" => 321));
        $this->assertEqual($r->get("key3"), 789);
        $this->assertEqual($r->get("key4"), 321);

        $r->remove("key3");
        $r->remove("key5");
        $this->assertNull($r->get("key3"));
        $vs = $r->getAll();
        $this->assertFalse(isset($vs["key3"]));
        $this->assertEqual(count($vs), 1);
        $this->assertEqual($vs["key4"], 321);

        $r->clear();
        $this->assertNull($r->get("key4"));
        $vs = $r->getAll();
        $this->assertEqual(count($vs), 0);

        // }}}
        // {{{ [2] manupilate with object

        $o = new stdClass();
        $o->name = "abc";
        $o->age = 32;

        $r->setAll($o);
        $v = $r->getAll();

        $this->assertEqual($o->name, "abc");
        $this->assertEqual($o->age, 32);

        // }}}
        // {{{ [3] set/getViewName()

        $r->setViewName("view1");
        $this->assertEqual($r->getViewName(), "view1");

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
