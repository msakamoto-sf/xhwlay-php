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
 * @version $Id: Xhwlay_Renderer_VarDump_TestCase.php 39 2008-02-11 14:17:34Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/ErrorStack.php");
require_once("Xhwlay/Renderer/VarDump.php");

class Xhwlay_Renderer_VarDump_TestCase extends UnitTestCase
{
    // {{{ Test All Methods.

    function testVarDump()
    {
        $r =& new Xhwlay_Renderer_VarDump();

        echo "Check Manually...\n";

        $r->set("key1", 123);
        $r->set("key2", 456);
        echo $r->render();
        $this->assertPattern("/123/", $r->render());
        $this->assertPattern("/456/", $r->render());

        $r->clear();

        $o =& new stdClass();
        $o->key3 = "abc";
        $o->key4 = "def";
        $r->setAll($o);
        echo $r->render();
        $this->assertPattern("/abc/", $r->render());
        $this->assertPattern("/def/", $r->render());
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
