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
 * @version $Id: Xhwlay_Var_TestCase.php 43 2008-02-11 15:43:29Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/Var.php");

class Xhwlay_Var_TestCase extends UnitTestCase
{
    // {{{ testDefaultNameSpace()

    function testDefaultNameSpac()
    {
        $val = "scalar1";
        $key = "key1";
        Xhwlay_Var::set($key, $val);
        $this->assertEqual(Xhwlay_Var::get($key), $val);
        $this->assertTrue(Xhwlay_Var::exists($key));
        Xhwlay_Var::remove($key);
        $this->assertFalse(Xhwlay_Var::exists($key));
        $this->assertNull(Xhwlay_Var::get($key));

        // defaule value parameter
        $this->assertEqual(
            Xhwlay_Var::get($key, XHWLAY_VAR_NAMESPACE_DEFAULT, "abc"), 
            "abc");
    }

    // }}}
    // {{{ testCustomNameSpaceAndMixedValue()

    function testCustomNameSpaceAndMixedValue()
    {
        $key = "key1";
        $namespace = "ns1";

        // mixed value
        $obj = new stdClass();
        $obj->p1 = "val1";
        $obj->p2 = "val2";
        $val = array("abc", &$obj, 789);

        // set mixed value
        Xhwlay_Var::set($key, $val, $namespace);

        // get mixed value
        $this->assertTrue(Xhwlay_Var::exists($key, $namespace));
        $val2 = Xhwlay_Var::get($key, $namespace);
        $this->assertEqual($val2[0], "abc");
        $obj2 =& $val2[1];
        $this->assertEqual($obj2->p1, $obj->p1);
        $this->assertEqual($obj2->p2, $obj->p2);
        $this->assertEqual($val2[2], 789);

        // remove mixed value
        Xhwlay_Var::remove($key, $namespace);
        $this->assertFalse(Xhwlay_Var::exists($key, $namespace));
        $this->assertNull(Xhwlay_Var::get($key, $namespace));
    }

    // }}}
    // {{{ testNameSpaceMixedIn()

    function testNameSpaceMixedIn()
    {
        $ns_1 = "ns1";
        $ns_2 = "ns2";
        $v_1 = 123;
        $v_2 = 456;
        $v_3 = 789;
        $key = "key1";

        Xhwlay_Var::set($key, $v_1, $ns_1);
        Xhwlay_Var::set($key, $v_2, $ns_2);
        Xhwlay_Var::set($key, $v_3);
        $this->assertEqual(Xhwlay_Var::get($key, $ns_1), $v_1);
        $this->assertEqual(Xhwlay_Var::get($key, $ns_2), $v_2);
        $this->assertEqual(Xhwlay_Var::get($key), $v_3);
        $this->assertTrue(Xhwlay_Var::exists($key, $ns_1));
        $this->assertTrue(Xhwlay_Var::exists($key, $ns_2));
        $this->assertTrue(Xhwlay_Var::exists($key));

        Xhwlay_Var::remove($key);
        $this->assertTrue(Xhwlay_Var::exists($key, $ns_1));
        $this->assertTrue(Xhwlay_Var::exists($key, $ns_2));
        $this->assertFalse(Xhwlay_Var::exists($key));
        $this->assertNull(Xhwlay_Var::get($key));

        Xhwlay_Var::remove($key, $ns_1);
        $this->assertFalse(Xhwlay_Var::exists($key, $ns_1));
        $this->assertTrue(Xhwlay_Var::exists($key, $ns_2));
        $this->assertFalse(Xhwlay_Var::exists($key));
        $this->assertNull(Xhwlay_Var::get($key));
        $this->assertNull(Xhwlay_Var::get($key, $ns_1));

        Xhwlay_Var::remove($key, $ns_2);
        $this->assertFalse(Xhwlay_Var::exists($key, $ns_1));
        $this->assertFalse(Xhwlay_Var::exists($key, $ns_2));
        $this->assertFalse(Xhwlay_Var::exists($key));
        $this->assertNull(Xhwlay_Var::get($key));
        $this->assertNull(Xhwlay_Var::get($key, $ns_1));
        $this->assertNull(Xhwlay_Var::get($key, $ns_2));
    }

    // }}}
    // {{{ testAllNameSpaceOverWrite()

    function testAllNameSpaceOverWrite()
    {
        $ns = "ns3";
        $val_1 = "abc";
        $val_2 = 123;
        $key = "key1";

        // 1st set(), normal namespace.
        Xhwlay_Var::set($key, $val_1, $ns);
        $this->assertEqual(Xhwlay_Var::get($key, $ns), $val_1);
        // 2nd set(), ALL namespace -> overrite 1st namespace.
        Xhwlay_Var::set($key, $val_2, XHWLAY_VAR_NAMESPACE_ALL);
        $this->assertEqual(
            Xhwlay_Var::get($key, XHWLAY_VAR_NAMESPACE_ALL), 
            $val_2);
        $this->assertEqual(
            Xhwlay_Var::get($key, $ns), 
            $val_2);

        // remove normal namespace -> target namespace is unsetted, 
        // ALL namespace remains still.
        Xhwlay_Var::remove($key, $ns);
        $this->assertFalse(Xhwlay_Var::exists($key, $ns));
        $this->assertNull(Xhwlay_Var::get($key, $ns));
        $this->assertTrue(Xhwlay_Var::exists($key, XHWLAY_VAR_NAMESPACE_ALL));
        $this->assertEqual(
            Xhwlay_Var::get($key, XHWLAY_VAR_NAMESPACE_ALL), 
            $val_2);

        // Already setted in ALL namespace. Then set normal namespace agein.
        // -> Don't overwrite ALL namespace, only given namespace.
        Xhwlay_Var::set($key, $val_1, $ns);
        $this->assertEqual(
            Xhwlay_Var::get($key, $ns),
            $val_1);
        $this->assertEqual(
            Xhwlay_Var::get($key, XHWLAY_VAR_NAMESPACE_ALL), 
            $val_2);

        // remove ALL namespace effects all namespace.
        Xhwlay_Var::remove($key, XHWLAY_VAR_NAMESPACE_ALL);
        $this->assertFalse(Xhwlay_Var::exists($key, $ns));
        $this->assertFalse(Xhwlay_Var::exists($key, XHWLAY_VAR_NAMESPACE_ALL));
        $this->assertNull(Xhwlay_Var::get($key, $ns));
        $this->assertNull(Xhwlay_Var::get($key, XHWLAY_VAR_NAMESPACE_ALL));

    }

    // }}}
    // {{{ testClear()

    function testClear()
    {
        $ns_1 = "ns1";
        $ns_2 = "ns2";
        $v_1_1 = "val1_1";
        $v_1_2 = "val1_2";
        $v_2_1 = "val2_1";
        $v_2_2 = "val2_2";
        $v_3_1 = "val3_1";
        $v_3_2 = "val3_2";
        $k1 = "key1";
        $k2 = "key2";

        Xhwlay_Var::set($k1, $v_1_1, $ns_1);
        Xhwlay_Var::set($k2, $v_1_2, $ns_1);
        Xhwlay_Var::set($k1, $v_2_1, $ns_2);
        Xhwlay_Var::set($k2, $v_2_2, $ns_2);
        Xhwlay_Var::set($k1, $v_3_1);
        Xhwlay_Var::set($k2, $v_3_2);

        // [1] : Check default namespace must be cleared.
        Xhwlay_Var::clear();
        $this->assertFalse(Xhwlay_Var::exists($k1));
        $this->assertFalse(Xhwlay_Var::exists($k2));
        $this->assertTrue(Xhwlay_Var::exists($k1, $ns_1));
        $this->assertTrue(Xhwlay_Var::exists($k2, $ns_1));
        $this->assertTrue(Xhwlay_Var::exists($k1, $ns_2));
        $this->assertTrue(Xhwlay_Var::exists($k2, $ns_2));
        // [2] : Check "ns1" namespace must be cleared.
        Xhwlay_Var::clear($ns_1);
        $this->assertFalse(Xhwlay_Var::exists($k1, $ns_1));
        $this->assertFalse(Xhwlay_Var::exists($k2, $ns_1));
        $this->assertTrue(Xhwlay_Var::exists($k1, $ns_2));
        $this->assertTrue(Xhwlay_Var::exists($k2, $ns_2));

        // [3] : Check cleared value and namespace is registered again.
        Xhwlay_Var::set($k1, $v_3_1);
        Xhwlay_Var::set($k2, $v_3_2);
        $this->assertTrue(Xhwlay_Var::exists($k1));
        $this->assertTrue(Xhwlay_Var::exists($k2));
        Xhwlay_Var::set($k1, $v_1_1, $ns_1);
        Xhwlay_Var::set($k2, $v_1_2, $ns_1);
        $this->assertTrue(Xhwlay_Var::exists($k1, $ns_1));
        $this->assertTrue(Xhwlay_Var::exists($k2, $ns_1));

        // [4] : Check ALL namespace is cleared, all vars are cleared.
        Xhwlay_Var::clear(XHWLAY_VAR_NAMESPACE_ALL);
        $this->assertFalse(Xhwlay_Var::exists($k1));
        $this->assertFalse(Xhwlay_Var::exists($k2));
        $this->assertFalse(Xhwlay_Var::exists($k1, $ns_1));
        $this->assertFalse(Xhwlay_Var::exists($k2, $ns_1));
        $this->assertFalse(Xhwlay_Var::exists($k1, $ns_2));
        $this->assertFalse(Xhwlay_Var::exists($k2, $ns_2));

        // [5] : Check no-exist namespace is cleared, no error occurs.
        Xhwlay_Var::clear("ns3");
    }

    // }}}
    // {{{ testExport()

    function testExport()
    {
        $ns_1 = "ns1";
        $ns_2 = "ns2";
        $v_1_1 = "val1_1";
        $v_1_2 = "val1_2";
        $v_2_1 = "val2_1";
        $v_2_2 = "val2_2";
        $v_3_1 = "val3_1";
        $v_3_2 = "val3_2";
        $k1 = "key1";
        $k2 = "key2";

        Xhwlay_Var::set($k1, $v_1_1, $ns_1);
        Xhwlay_Var::set($k2, $v_1_2, $ns_1);
        Xhwlay_Var::set($k1, $v_2_1, $ns_2);
        Xhwlay_Var::set($k2, $v_2_2, $ns_2);
        Xhwlay_Var::set($k1, $v_3_1);
        Xhwlay_Var::set($k2, $v_3_2);

        // [1] : Check "ns1" namespace exporting is correct.
        $exp = Xhwlay_Var::export($ns_1);
        $this->assertEqual($exp[$k1], $v_1_1);
        $this->assertEqual($exp[$k2], $v_1_2);

        // [2] : Check "ns2" namespace exporting is correct.
        $exp = Xhwlay_Var::export($ns_2);
        $this->assertEqual($exp[$k1], $v_2_1);
        $this->assertEqual($exp[$k2], $v_2_2);

        // [3] : Check default namespace exporting is correct.
        $exp = Xhwlay_Var::export();
        $this->assertEqual($exp[$k1], $v_3_1);
        $this->assertEqual($exp[$k2], $v_3_2);

        // [4] : Check ALL namespace exporting results all vars.
        $exp = Xhwlay_Var::export(XHWLAY_VAR_NAMESPACE_ALL);
        $this->assertEqual($exp[$ns_1][$k1], $v_1_1);
        $this->assertEqual($exp[$ns_1][$k2], $v_1_2);
        $this->assertEqual($exp[$ns_2][$k1], $v_2_1);
        $this->assertEqual($exp[$ns_2][$k2], $v_2_2);
        $this->assertEqual($exp[XHWLAY_VAR_NAMESPACE_DEFAULT][$k1], $v_3_1);
        $this->assertEqual($exp[XHWLAY_VAR_NAMESPACE_DEFAULT][$k2], $v_3_2);

        // [5] : Check no-exist namespace exporting results null.
        $exp = Xhwlay_Var::export('nsX');
        $this->assertNull($exp);
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
