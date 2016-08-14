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
 * @version $Id: Xhwlay_Renderer_Include_TestCase.php 39 2008-02-11 14:17:34Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/ErrorStack.php");
require_once("Xhwlay/Renderer/Include.php");

class Xhwlay_Renderer_Include_TestCase extends UnitTestCase
{
    // {{{ Test All Methods.

    function testInclude()
    {
        $r =& new Xhwlay_Renderer_Include();

        // {{{ [1] Normal pass

        $r->set("key1", 123);
        $r->set("key2", 456);
        $r->setViewName(
            dirname(__FILE__).'/Xhwlay_Renderer_Include_TestCase.txt');

        $output = $r->render();
        $this->assertPattern('/key1=123/', $output);
        $this->assertPattern('/key2=456/', $output);

        // }}}
        // {{{ [2] Check Template not found

        $error_template = "no_exista_template";
        $r->setViewName($error_template);
        $this->assertNull($r->render());
        $error = Xhwlay_ErrorStack::pop();
        $this->assertEqual(
            $error['code'], XHWLAY_RENDERER_EC_TEMPLATE_NOT_FOUND);
        $this->assertEqual(
            $error['level'], XHWLAY_ERRORSTACK_EL_ERROR);
        $args = $error["params"];
        $this->assertEqual($args["name"], $error_template);
        $this->assertEqual(Xhwlay_ErrorStack::count(), 0);

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
