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
 * @version $Id: Xhwlay_Renderer_Json_TestCase.php 39 2008-02-11 14:17:34Z msakamoto-sf $
 */

/**
 * requires
 */
//NOTICE: This file is EUC-JP.
//NOTICE: This test needs PHP mb_string module.
require_once("Xhwlay/ErrorStack.php");
require_once("Xhwlay/Renderer/Json.php");

class Xhwlay_Renderer_Json_TestCase extends UnitTestCase
{
    // {{{ EUC-JP

    function testJsonEUCJP()
    {
        $r =& new Xhwlay_Renderer_Json();
        $mb = "日本語マルチバイト文字列";

        $r->set("key1", 123);
        $r->set("key2", $mb);
        $json = $r->render();
        $this->assertPattern("/123/", $json);
        $this->assertPattern("/$mb/", $json);
        $var = Jsphon::decode($json);
        $this->assertEqual($var["key1"], 123);
        $this->assertEqual($var["key2"], $mb);
    }

    // }}}
    // {{{ UTF-8

    function testJsonUTF8()
    {
        $old = mb_internal_encoding();
        mb_internal_encoding('EUC-JP');

        $r =& new Xhwlay_Renderer_Json();
        $r->autoUTF8 = true;
        $r->escapeNonASCII = true;
        $mb = "日本語マルチバイト文字列";

        $r->set("key1", 123);
        $r->set("key2", $mb);
        $json = $r->render();
        $var = Jsphon::decode($json);
        mb_convert_variables('EUC-JP', 'UTF-8', $var);
        $this->assertEqual($var["key1"], 123);
        $this->assertEqual($var["key2"], $mb);

        mb_internal_encoding($old);
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
