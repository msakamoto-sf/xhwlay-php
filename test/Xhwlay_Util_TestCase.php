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
 * @version $Id: Xhwlay_Util_TestCase.php 42 2008-02-11 15:39:30Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/ErrorStack.php");
require_once("Xhwlay/Util.php");

class Xhwlay_Util_TestCase extends UnitTestCase
{
    // {{{ uuid()

    function testUuid()
    {

        $s = 10000; // + 0 ? no! hungup! maybe.
        $arr = array();
        for ($i = 0; $i < $s; $i++) {
            $arr[] = Xhwlay_Util::uuid();
        }

        // Check all of generated uuid is unique.
        $cnt1 = count($arr);
        // delete duplicated uuid
        $arr2 = array_unique($arr);
        $cnt2 = count($arr2);

        $this->assertEqual($cnt1, $cnt2);
    }

    // }}}
    // {{{ bcid()

    function testBcid()
    {

        $s = 10000; // + 0 ? no! hungup! maybe.
        $arr = array();
        for ($i = 0; $i < $s; $i++) {
            $arr[] = Xhwlay_Util::bcid();
        }

        // Check all of generated uuid is unique.
        $cnt1 = count($arr);
        // delete duplicated uuid
        $arr2 = array_unique($arr);
        $cnt2 = count($arr2);

        $this->assertEqual($cnt1, $cnt2);
    }

    // }}}
    // {{{ diceRoller()

    function testDiceRoller()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->assertFalse(Xhwlay_Util::diceRoller(0, 3));
        }
        for ($i = 0; $i < 10; $i++) {
            $this->assertTrue(Xhwlay_Util::diceRoller(3, 3));
            $this->assertTrue(Xhwlay_Util::diceRoller(4, 3));
        }

        $sum = 0;
        for ($i = 0; $i < 1000; $i++) {
            if (Xhwlay_Util::diceRoller(1, 10)) {
                $sum++;
            }
        }
        echo "Xhwlay_Util::diceRoller() returns $sum times true. in 1000 calls.\n";
    }

    // }}}
    // {{{ isTrue()

    function testIsTrue()
    {
        $this->assertFalse(Xhwlay_Util::isTrue(@$hoge));
        $this->assertFalse(Xhwlay_Util::isTrue(null));
        $this->assertFalse(Xhwlay_Util::isTrue(0));
        $this->assertFalse(Xhwlay_Util::isTrue(-1));
        $this->assertFalse(Xhwlay_Util::isTrue(""));

        $this->assertTrue(Xhwlay_Util::isTrue(array()));
        $this->assertTrue(Xhwlay_Util::isTrue(new stdClass()));
        $this->assertTrue(Xhwlay_Util::isTrue(true));
        $this->assertTrue(Xhwlay_Util::isTrue(1));
        $this->assertTrue(Xhwlay_Util::isTrue("1"));
        $this->assertTrue(Xhwlay_Util::isTrue("ON"));
        $this->assertTrue(Xhwlay_Util::isTrue("on"));
        $this->assertTrue(Xhwlay_Util::isTrue("OK"));
        $this->assertTrue(Xhwlay_Util::isTrue("ok"));
        $this->assertTrue(Xhwlay_Util::isTrue("true"));
        $this->assertTrue(Xhwlay_Util::isTrue("True"));
        $this->assertTrue(Xhwlay_Util::isTrue("TRUE"));
        $this->assertTrue(Xhwlay_Util::isTrue("enable"));
        $this->assertTrue(Xhwlay_Util::isTrue("Enable"));
        $this->assertTrue(Xhwlay_Util::isTrue("ENABLE"));
        $this->assertTrue(Xhwlay_Util::isTrue(" ok"));
        $this->assertTrue(Xhwlay_Util::isTrue("ok "));
        $this->assertTrue(Xhwlay_Util::isTrue("ok\r"));
        $this->assertTrue(Xhwlay_Util::isTrue("ok\n"));
        $this->assertTrue(Xhwlay_Util::isTrue("ok\r\n"));
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
