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
 * @version $Id: Xhwlay_ErrorStack_TestCase.php 43 2008-02-11 15:43:29Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/ErrorStack.php");

class Xhwlay_ErrorStack_Callback
{
    function callback($error) {}
    function callback2($error) {}
}

Mock::generate('Xhwlay_ErrorStack_Callback');

class Xhwlay_ErrorStack_TestCase extends UnitTestCase
{
    // {{{ Test All Methods.

    function testErrorStack()
    {
        $callbacker =& new MockXhwlay_ErrorStack_Callback();
        $callbacker->setReturnValue('callback', PEAR_ERRORSTACK_PUSH);
        $callbacker->expectCallCount("callback", 4);
        $callbacker->expectCallCount("callback2", 1);

        Xhwlay_ErrorStack::pushCallback(array(&$callbacker, "callback"));
        Xhwlay_ErrorStack::push(1, "abc");
        Xhwlay_ErrorStack::push(2, "def", 'custom');
        Xhwlay_ErrorStack::push(3, "ghi", 'custom', array(1, 2, 3));
        Xhwlay_ErrorStack::push(4, "jkl", 'custom');

        // {{{ [1] : Get all Errors (not clear), check contents of errors.
        $all = Xhwlay_ErrorStack::all();
        $this->assertEqual($all[0]['code'], 4);
        $this->assertEqual($all[0]['level'], 'custom');
        $this->assertEqual($all[0]['message'], 'jkl');
        $this->assertEqual($all[0]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual($all[1]['code'], 3);
        $this->assertEqual($all[1]['level'], 'custom');
        $this->assertEqual($all[1]['message'], 'ghi');
        $this->assertEqual($all[1]['package'], XHWLAY_ERRORSTACK_PACKAGE);
        $args = $all[1]['params'];
        $this->assertTrue(is_array($args));
        $this->assertEqual(count($args), 3);
        $this->assertEqual($args[0], 1);
        $this->assertEqual($args[1], 2);
        $this->assertEqual($args[2], 3);

        $this->assertEqual($all[2]['code'], 2);
        $this->assertEqual($all[2]['level'], 'custom');
        $this->assertEqual($all[2]['message'], 'def');
        $this->assertEqual($all[2]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual($all[3]['code'], 1);
        $this->assertEqual($all[3]['level'], 'exception');
        $this->assertEqual($all[3]['message'], 'abc');
        $this->assertEqual($all[3]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual(count($all), 4);

        // }}}
        // {{{ [2] : Get 'custom' Errors (not clear), check.

        $all = Xhwlay_ErrorStack::all('custom');
        $this->assertEqual($all[0]['code'], 4);
        $this->assertEqual($all[0]['level'], 'custom');
        $this->assertEqual($all[0]['message'], 'jkl');
        $this->assertEqual($all[0]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual($all[1]['code'], 3);
        $this->assertEqual($all[1]['level'], 'custom');
        $this->assertEqual($all[1]['message'], 'ghi');
        $this->assertEqual($all[1]['package'], XHWLAY_ERRORSTACK_PACKAGE);
        $args = $all[1]['params'];
        $this->assertTrue(is_array($args));
        $this->assertEqual(count($args), 3);
        $this->assertEqual($args[0], 1);
        $this->assertEqual($args[1], 2);
        $this->assertEqual($args[2], 3);

        $this->assertEqual($all[2]['code'], 2);
        $this->assertEqual($all[2]['level'], 'custom');
        $this->assertEqual($all[2]['message'], 'def');
        $this->assertEqual($all[2]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual(count($all), 3);

        // }}}
        // {{{ [3] : Re-Get All Errors
        // check contents is not effected by 2nd gets.
        $all = Xhwlay_ErrorStack::all();
        $this->assertEqual($all[0]['code'], 4);
        $this->assertEqual($all[0]['level'], 'custom');
        $this->assertEqual($all[0]['message'], 'jkl');
        $this->assertEqual($all[0]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual($all[1]['code'], 3);
        $this->assertEqual($all[1]['level'], 'custom');
        $this->assertEqual($all[1]['message'], 'ghi');
        $this->assertEqual($all[1]['package'], XHWLAY_ERRORSTACK_PACKAGE);
        $args = $all[1]['params'];
        $this->assertTrue(is_array($args));
        $this->assertEqual(count($args), 3);
        $this->assertEqual($args[0], 1);
        $this->assertEqual($args[1], 2);
        $this->assertEqual($args[2], 3);

        $this->assertEqual($all[2]['code'], 2);
        $this->assertEqual($all[2]['level'], 'custom');
        $this->assertEqual($all[2]['message'], 'def');
        $this->assertEqual($all[2]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual($all[3]['code'], 1);
        $this->assertEqual($all[3]['level'], 'exception');
        $this->assertEqual($all[3]['message'], 'abc');
        $this->assertEqual($all[3]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual(count($all), 4);

        // }}}
        // {{{ [4] : Check count() method. 
        $this->assertEqual(Xhwlay_ErrorStack::count(), 4);
        $this->assertEqual(Xhwlay_ErrorStack::count('custom'), 3);
        // confirm not-effected by upper method calls.
        $this->assertEqual(Xhwlay_ErrorStack::count(), 4);
        $this->assertEqual(Xhwlay_ErrorStack::count('custom'), 3);
        // }}}
        // {{{ [5] : Check pop(), all(), count()
        $cnt1 = Xhwlay_ErrorStack::count();
        $cnt2 = Xhwlay_ErrorStack::count('custom');
        $error = Xhwlay_ErrorStack::pop();
        $this->assertEqual($error['code'], 4);
        $this->assertEqual($error['level'], 'custom');
        $this->assertEqual($error['message'], 'jkl');
        $this->assertEqual($error['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual(Xhwlay_ErrorStack::count(), $cnt1 - 1);
        $this->assertEqual(Xhwlay_ErrorStack::count('custom'), $cnt2 - 1);

        // Check ALL Errors.
        $all = Xhwlay_ErrorStack::all();
        $this->assertEqual(count($all), $cnt1 - 1);

        $this->assertEqual($all[0]['code'], 3);
        $this->assertEqual($all[0]['level'], 'custom');
        $this->assertEqual($all[0]['message'], 'ghi');
        $this->assertEqual($all[0]['package'], XHWLAY_ERRORSTACK_PACKAGE);
        $args = $all[0]['params'];
        $this->assertTrue(is_array($args));
        $this->assertEqual(count($args), 3);
        $this->assertEqual($args[0], 1);
        $this->assertEqual($args[1], 2);
        $this->assertEqual($args[2], 3);

        $this->assertEqual($all[1]['code'], 2);
        $this->assertEqual($all[1]['level'], 'custom');
        $this->assertEqual($all[1]['message'], 'def');
        $this->assertEqual($all[1]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        $this->assertEqual($all[2]['code'], 1);
        $this->assertEqual($all[2]['level'], 'exception');
        $this->assertEqual($all[2]['message'], 'abc');
        $this->assertEqual($all[2]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        // Check 'custom' Errors.
        $all = Xhwlay_ErrorStack::all('custom');
        $this->assertEqual(count($all), $cnt2 - 1);

        $this->assertEqual($all[0]['code'], 3);
        $this->assertEqual($all[0]['level'], 'custom');
        $this->assertEqual($all[0]['message'], 'ghi');
        $this->assertEqual($all[0]['package'], XHWLAY_ERRORSTACK_PACKAGE);
        $args = $all[0]['params'];
        $this->assertTrue(is_array($args));
        $this->assertEqual(count($args), 3);
        $this->assertEqual($args[0], 1);
        $this->assertEqual($args[1], 2);
        $this->assertEqual($args[2], 3);

        $this->assertEqual($all[1]['code'], 2);
        $this->assertEqual($all[1]['level'], 'custom');
        $this->assertEqual($all[1]['message'], 'def');
        $this->assertEqual($all[1]['package'], XHWLAY_ERRORSTACK_PACKAGE);

        // }}}
        // {{{ [6] : Check clear()
        Xhwlay_ErrorStack::clear();
        $all = Xhwlay_ErrorStack::all();
        $this->assertEqual(count($all), 0);
        $all = Xhwlay_ErrorStack::all('custom');
        $this->assertEqual(count($all), 0);
        $cnt = Xhwlay_ErrorStack::count();
        $this->assertEqual($cnt, 0);
        $cnt = Xhwlay_ErrorStack::count('custom');
        $this->assertEqual($cnt, 0);
        // }}}
        // {{{ [7] : popCallback(), pushCallback()

        // clear old callback
        Xhwlay_ErrorStack::popCallback();
        // set new callback return IGNORE
        $callbacker->setReturnValue('callback2', PEAR_ERRORSTACK_IGNORE);
        // register new callback
        Xhwlay_ErrorStack::pushCallback(array(&$callbacker, "callback2"));

        Xhwlay_ErrorStack::push(1, "test");
        // check pushed error was ignored.
        $this->assertEqual(Xhwlay_ErrorStack::count(), 0);

        // clear old callback
        Xhwlay_ErrorStack::popCallback();
        // }}}

        $callbacker->tally();

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
