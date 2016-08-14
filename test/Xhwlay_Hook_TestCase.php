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
 * @version $Id: Xhwlay_Hook_TestCase.php 43 2008-02-11 15:43:29Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/Hook.php");

class Xhwlay_Hook_TestCase extends UnitTestCase
{
    var $_hook_test_vars = array();

    function hook1($hook_name, $arg1, $arg2)
    {
        $this->_hook_test_vars['hook1']['arg1'] = $arg1;
        $this->_hook_test_vars['hook1']['arg2'] = $arg2;
        $this->_hook_test_vars['hook1']['count']++;
        return $this->_hook_test_vars['hook1']['result'];
    }
    function hook2($hook_name, $arg1, $arg2)
    {
        $this->_hook_test_vars['hook2']['arg1'] = $arg1;
        $this->_hook_test_vars['hook2']['arg2'] = $arg2;
        $this->_hook_test_vars['hook2']['count']++;
        return $this->_hook_test_vars['hook2']['result'];
    }
    function hook3($hook_name, $arg1, $arg2)
    {
        $this->_hook_test_vars['hook3']['arg1'] = $arg1;
        $this->_hook_test_vars['hook3']['arg2'] = $arg2;
        $this->_hook_test_vars['hook3']['count']++;
        return $this->_hook_test_vars['hook3']['result'];
    }
    function hook4($hook_name)
    {
        $this->_hook_test_vars['hook4']['count']++;

        $hook =& Xhwlay_Hook::getInstance($hook_name);
        $hook->pushCallback(array(&$this, 'hook1'));
        $hook->pushCallback(array(&$this, 'hook5'));
        $hook->pushCallback(array(&$this, 'hook3'));
    }
    function hook5($hook_name)
    {
        $this->_hook_test_vars['hook4']['count']++;

        $hook =& Xhwlay_Hook::getInstance($hook_name);
        // clear existing callback stacks.
        while ($hook->popCallback());

        $hook->pushCallback(array(&$this, 'hook3'));
        $hook->pushCallback(array(&$this, 'hook1'));
    }

    function hook_escape($hook_name)
    {
        $hook =& Xhwlay_Hook::getInstance($hook_name);
        $hook->escape();
    }

    // {{{ testInstanceAndPushAndPopCallback()

    function testInstanceAndPushAndPopCallback()
    {
        // {{{ [1] Check single callback push/pop

        $h1 =& Xhwlay_Hook::getInstance("h1");

        $h1->pushCallback(array("Xhwlay_Hook_Test", "hook1"));
        $cb = $h1->popCallback();
        $this->assertEqual($cb[0], "Xhwlay_Hook_Test");
        $this->assertEqual($cb[1], "hook1");
        $this->assertNull($h1->popCallback());

        // }}}
        // {{{ [2] Check multiple callback push/pop

        $h1->pushCallback(array("Xhwlay_Hook_Test", "hook1"));
        $h1->pushCallback(array("Xhwlay_Hook_Test", "hook2"));
        $h1->pushCallback(array("Xhwlay_Hook_Test", "hook3"));
        $cb = $h1->popCallback();
        $this->assertEqual($cb[0], "Xhwlay_Hook_Test");
        $this->assertEqual($cb[1], "hook3");
        $cb = $h1->popCallback();
        $this->assertEqual($cb[0], "Xhwlay_Hook_Test");
        $this->assertEqual($cb[1], "hook2");
        $cb = $h1->popCallback();
        $this->assertEqual($cb[0], "Xhwlay_Hook_Test");
        $this->assertEqual($cb[1], "hook1");
        $this->assertNull($h1->popCallback());

        // }}}
        // {{{ [3] Check multiple callback and multiple hook push/pop

        $h2 =& Xhwlay_Hook::getInstance("h2");
        $h1->pushCallback(array("Xhwlay_Hook_Test", "hook1"));
        $h1->pushCallback(array("Xhwlay_Hook_Test", "hook2"));
        $h2->pushCallback(array("Xhwlay_Hook_Test", "hook3"));
        $h2->pushCallback(array("Xhwlay_Hook_Test", "hook4"));
        $i = 2;
        while ($cb = $h1->popCallback()) {
            $this->assertEqual($cb[0], "Xhwlay_Hook_Test");
            $this->assertEqual($cb[1], "hook{$i}");
            $i--;
        }
        $i = 4;
        while ($cb = $h2->popCallback()) {
            $this->assertEqual($cb[0], "Xhwlay_Hook_Test");
            $this->assertEqual($cb[1], "hook{$i}");
            $i--;
        }
        $this->assertNull($h1->popCallback());
        $this->assertNull($h2->popCallback());

        // }}}
    }

    // }}}
    // {{{ testAttributesManupilation()

    function testAttributesManupilation()
    {
        $h1 =& Xhwlay_Hook::getInstance("h1");
        $h2 =& Xhwlay_Hook::getInstance("h2");

        // {{{ [1] Check default attributes

        $this->assertTrue($h1->getAttribute('available'));
        $this->assertTrue($h2->getAttribute('available'));
        $this->assertTrue($h1->getAttribute('ignore_null_result'));
        $this->assertTrue($h2->getAttribute('ignore_null_result'));

        $this->assertNull($h1->getAttribute('FOO-BAR'));
        $this->assertNull($h2->getAttribute('FOO-BAR'));

        // }}}
        // {{{ [2] Check setAttributes() works correctly

        $old = $h1->setAttribute('available', false);
        $this->assertTrue($old);
        $old = $h2->setAttribute('available', false);
        $this->assertTrue($old);
        $old = $h1->setAttribute('ignore_null_result', false);
        $this->assertTrue($old);
        $old = $h2->setAttribute('ignore_null_result', false);
        $this->assertTrue($old);
        $old = $h1->setAttribute('FOO-BAR', 123);
        $this->assertNull($old);
        $h2->setAttribute('FOO-BAR', "ABCD");
        $this->assertNull($old);

        $this->assertFalse($h1->getAttribute('available'));
        $this->assertFalse($h2->getAttribute('available'));
        $this->assertFalse($h1->getAttribute('ignore_null_result'));
        $this->assertFalse($h2->getAttribute('ignore_null_result'));

        $this->assertEqual($h1->getAttribute('FOO-BAR'), 123);
        $this->assertEqual($h2->getAttribute('FOO-BAR'), "ABCD");

        // }}}
        // {{{ [3] Check listAttributes() works correctly

        $attrs = $h1->listAttributes();
        $this->assertFalse($attrs['available']);
        $this->assertFalse($attrs['ignore_null_result']);
        $this->assertEqual($attrs['FOO-BAR'], 123);

        $attrs = $h2->listAttributes();
        $this->assertFalse($attrs['available']);
        $this->assertFalse($attrs['ignore_null_result']);
        $this->assertEqual($attrs['FOO-BAR'], "ABCD");

        // }}}
        // restore.
        $h1->setAttribute('available', true);
        $h2->setAttribute('available', true);
    }

    // }}}
    // {{{ testArgumentsManupilation()

    function testArgumentsManupilation()
    {
        $h1 =& Xhwlay_Hook::getInstance("h1");
        $h2 =& Xhwlay_Hook::getInstance("h2");
        $h3 =& Xhwlay_Hook::getInstance("h3");

        // {{{ [1] 1st setArgument(), returned old arg is empty array().

        $args1 = array(1, 2);
        $args2 = array("abc", "def", "ghi");
        $args3 = array(array(1, 2, 3), array(4, 5, 6));
        $args1_ = $h1->setArgument($args1);
        $args2_ = $h2->setArgument($args2);
        $args3_ = $h3->setArgument($args3);
        $this->assertEqual(count($args1_), 0);
        $this->assertEqual(count($args2_), 0);
        $this->assertEqual(count($args3_), 0);

        // }}}
        // {{{ [2] 2nd setArgument(), Check returned old arg is correct.

        $args1 = array(2, 1);
        $args2 = array("ghi", "abc", "def");
        $obj = new stdClass();
        $obj->p1 = array(9, 8, 7);
        $obj->p2 = "sample string";
        $args3 = array($obj, array(1, 2, 3));
        $args1_ = $h1->setArgument($args1);
        $args2_ = $h2->setArgument($args2);
        $args3_ = $h3->setArgument($args3);
        $this->assertEqual($args1_[0], 1);
        $this->assertEqual($args1_[1], 2);
        $this->assertEqual($args2_[0], "abc");
        $this->assertEqual($args2_[1], "def");
        $this->assertEqual($args2_[2], "ghi");
        $arr = $args3_[0];
        $this->assertEqual($arr[0], 1);
        $this->assertEqual($arr[1], 2);
        $this->assertEqual($arr[2], 3);
        $arr = $args3_[1];
        $this->assertEqual($arr[0], 4);
        $this->assertEqual($arr[1], 5);
        $this->assertEqual($arr[2], 6);

        $args1 = $h1->getArgument();
        $args2 = $h2->getArgument();
        $args3 = $h3->getArgument();
        $this->assertEqual($args1[0], 2);
        $this->assertEqual($args1[1], 1);
        $this->assertEqual($args2[0], "ghi");
        $this->assertEqual($args2[1], "abc");
        $this->assertEqual($args2[2], "def");
        $obj_ = $args3[0];
        $this->assertEqual($obj_->p1, $obj->p1);
        $this->assertEqual($obj_->p2, $obj->p2);
        $arr = $args3[1];
        $this->assertEqual($arr[0], 1);
        $this->assertEqual($arr[1], 2);
        $this->assertEqual($arr[2], 3);

        // }}}
        // {{{ [3] Check clearArgument() works correctly

        $h1->clearArgument();
        $h2->clearArgument();
        $h3->clearArgument();
        $this->assertEqual(count($h1->getArgument()), 0);
        $this->assertEqual(count($h2->getArgument()), 0);
        $this->assertEqual(count($h3->getArgument()), 0);

        // }}}

    }

    // }}}
    // {{{ testSimpleInvokeIgnoreNull()

    function testSimpleInvokeIgnoreNull()
    {
        $h1 =& Xhwlay_Hook::getInstance("h1");
        $h2 =& Xhwlay_Hook::getInstance("h2");
        // clear callbacks.
        while ($h1->popCallback());
        while ($h2->popCallback());

        $this->_hook_test_vars['hook1']['count'] = 0;
        $this->_hook_test_vars['hook2']['count'] = 0;
        $this->_hook_test_vars['hook3']['count'] = 0;
        $this->_hook_test_vars['hook1']['result'] = 'hook1';
        $this->_hook_test_vars['hook2']['result'] = null;
        $this->_hook_test_vars['hook3']['result'] = 'hook3';

        $h1->pushCallback(array(&$this, 'hook1'));
        $h1->pushCallback(array(&$this, 'hook2'));
        $h1->pushCallback(array(&$this, 'hook3'));
        $h1->pushCallback(array(&$this, 'hook1'));
        $h1->pushCallback(array(&$this, 'hook3'));
        $h1->pushCallback(array(&$this, 'hook2'));
        $h1->setAttribute('ignore_null_result', true);
        $h1->setArgument(array("arg1", "arg2"));

        $h1->invoke();

        $this->assertEqual($this->_hook_test_vars['hook1']['arg1'], "arg1");
        $this->assertEqual($this->_hook_test_vars['hook1']['arg2'], "arg2");
        $this->assertEqual($this->_hook_test_vars['hook1']['count'], 2);
        $this->assertEqual($this->_hook_test_vars['hook2']['arg1'], "arg1");
        $this->assertEqual($this->_hook_test_vars['hook2']['arg2'], "arg2");
        $this->assertEqual($this->_hook_test_vars['hook2']['count'], 2);
        $this->assertEqual($this->_hook_test_vars['hook3']['arg1'], "arg1");
        $this->assertEqual($this->_hook_test_vars['hook3']['arg2'], "arg2");
        $this->assertEqual($this->_hook_test_vars['hook3']['count'], 2);

        $first = $h1->firstResult();
        $last = $h1->lastResult();
        $this->assertEqual($first, 'hook1');
        $this->assertEqual($last, 'hook3');
        $results = $h1->allResults();
        // NOTICE: callbacks are called in order by "LIFC"
        // (Last In (=pushCallback), First Called)
        $this->assertEqual($results[0], 'hook1');
        $this->assertEqual($results[1], 'hook3');
        $this->assertEqual($results[2], 'hook1');
        $this->assertEqual($results[3], 'hook3');

        // clear results
        $h1->allResults(true);
        $this->assertNull($h1->firstResult());
        $this->assertNull($h1->lastResult());

        $results = $h1->allResults();
        $this->assertEqual(count($results), 0);


    }

    // }}}
    // {{{ testSimpleInvokeNotIgnoreNull()

    function testSimpleInvokeNotIgnoreNull()
    {
        $h1 =& Xhwlay_Hook::getInstance("h1");
        $h2 =& Xhwlay_Hook::getInstance("h2");
        // clear callbacks.
        while ($h1->popCallback());
        while ($h2->popCallback());

        $this->_hook_test_vars['hook1']['count'] = 0;
        $this->_hook_test_vars['hook2']['count'] = 0;
        $this->_hook_test_vars['hook3']['count'] = 0;
        $this->_hook_test_vars['hook1']['result'] = 'hook1';
        $this->_hook_test_vars['hook2']['result'] = null;
        $this->_hook_test_vars['hook3']['result'] = 'hook3';

        $h1->pushCallback(array(&$this, 'hook1'));
        $h1->pushCallback(array(&$this, 'hook2'));
        $h1->pushCallback(array(&$this, 'hook3'));
        $h1->pushCallback(array(&$this, 'hook1'));
        $h1->pushCallback(array(&$this, 'hook3'));
        $h1->pushCallback(array(&$this, 'hook2'));
        $h1->setAttribute('ignore_null_result', false);
        $h1->setArgument(array("arg1", "arg2"));

        $h1->invoke();

        $first = $h1->firstResult();
        $last = $h1->lastResult();
        $this->assertEqual($first, 'hook1');
        $this->assertEqual($last, null);
        // get all results and clear.
        $results = $h1->allResults(true);
        $this->assertEqual($results[0], 'hook1');
        $this->assertEqual($results[1], null);
        $this->assertEqual($results[2], 'hook3');
        $this->assertEqual($results[3], 'hook1');
        $this->assertEqual($results[4], 'hook3');
        $this->assertEqual($results[5], null);

        $this->assertNull($h1->firstResult());
        $this->assertNull($h1->lastResult());
        $results = $h1->allResults();
        $this->assertEqual(count($results), 0);

    }

    // }}}
    // {{{ testEscapeWhileInvoking()

    function testEscapeWhileInvoking()
    {
        $h1 =& Xhwlay_Hook::getInstance("h1");
        while ($h1->popCallback());

        $this->_hook_test_vars['hook1']['count'] = 0;
        $this->_hook_test_vars['hook3']['count'] = 0;
        $this->_hook_test_vars['hook1']['result'] = 'hook1';
        $this->_hook_test_vars['hook3']['result'] = 'hook3';

        // before hook3, hook_escape calls "escape()". 
        // so, hook3 is not called.
        $h1->pushCallback(array(&$this, 'hook1'));
        $h1->pushCallback(array(&$this, 'hook_escape'));
        $h1->pushCallback(array(&$this, 'hook3'));
        $h1->setAttribute('ignore_null_result', true);

        $h1->invoke();

        $first = $h1->firstResult();
        $last = $h1->lastResult();
        // hook1 is called, not hook3.
        $this->assertEqual($first, 'hook1');
        $this->assertEqual($last, 'hook1');
        $this->assertEqual($this->_hook_test_vars['hook1']['count'], 1);
        $this->assertEqual($this->_hook_test_vars['hook3']['count'], 0);
        // get all results and clear.
        $results = $h1->allResults(true);
        $this->assertEqual(count($results), 1);
        $this->assertEqual($results[0], 'hook1');

    }

    // }}}
    // {{{ testAvailableAttributeIsFlase()

    function testAvailableAttributeIsFlase()
    {
        $h1 =& Xhwlay_Hook::getInstance("h1");
        while ($h1->popCallback());

        $this->_hook_test_vars['hook1']['count'] = 0;
        $this->_hook_test_vars['hook1']['result'] = 'hook1';

        $h1->pushCallback(array(&$this, 'hook1'));
        $old = $h1->setAttribute('available', false);
        $h1->invoke();

        $this->assertNull($h1->firstResult());
        $this->assertNull($h1->lastResult());
        $this->assertEqual($this->_hook_test_vars['hook1']['count'], 0);
        // get all results and clear.
        $results = $h1->allResults(true);
        $this->assertEqual(count($results), 0);

        // restore available attribute.
        $h1->setAttribute('available', $old);
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
