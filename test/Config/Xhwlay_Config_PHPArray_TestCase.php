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
 * @version $Id: Xhwlay_Config_PHPArray_TestCase.php 39 2008-02-11 14:17:34Z msakamoto-sf $
 */

/**
 * requires
 */
require_once("Xhwlay/Config/PHPArray.php");

class Xhwlay_Config_PHPArray_TestCase extends UnitTestCase
{
    // {{{ testStoryBookmarkIsOnExplicitly

    function testStoryBookmarkIsOnExplicitly()
    {
        // {{{ [0] configuration
        $configs = array(
            "story" => array(
                "name" => "story1",
                "bookmark" => "on",
                ),
            "page" => array(
                "page1.user1" => array(
                    "component" => "page1_user1",
                    "next" => array(),
                    "event" => array(),
                    ),
                "page1.user2" => array(
                    "component" => "page1_user2",
                    "bookmark" => "off",
                    "next" => array(
                        "pageA" => "barrier1",
                        "pageB" => "barrier2",
                        "pageC" => "",
                        "pageD" => null,
                        ),
                    "event" => array(
                        "eventA" => "guard1",
                        "eventB" => "guard2",
                        "eventC" => "",
                        "eventD" => null,
                        ),
                    ),
                "page2.userX" => array(
                    "component" => "page2_userX",
                    ),
                "page2.*" => array(
                    "component" => "page2",
                    "bookmark" => "LAST",
                    ),
                "*.user1" => array(
                    "component" => "default_user1",
                    ),
                "*.*" => array(
                    "component" => "default",
                    "bookmark" => ".",
                    ),
                "*" => array(
                    "component" => "default2",
                    ),
                ),
            "barrier" => array(
                "barrier1" => array(
                    "component" => "barrier1",
                    ),
                ),
            "guard" => array(
                "guard1" => array(
                    "component" => "guard1",
                    ),
                ),
            "event" => array(
                "eventA" => array(
                    "component" => "eventA",
                    "transit" => array(
                        "transit1" => "transit_page1",
                        ),
                    ),
                ),
            );
        $config =& new Xhwlay_Config_PHPArray($configs);
        // }}}
        // {{{ [1] Check getKeys() variations

        $keys = $config->getKeys();
        $this->assertEqual(count($keys), 1);
        $this->assertEqual($keys[0], "*.*");

        $keys = $config->getKeys("*");
        $this->assertEqual(count($keys), 1);
        $this->assertEqual($keys[0], "*.*");

        $keys = $config->getKeys("*", "*");
        $this->assertEqual(count($keys), 1);
        $this->assertEqual($keys[0], "*.*");

        $keys = $config->getKeys("*", "user1");
        $this->assertEqual(count($keys), 2);
        $this->assertEqual($keys[0], "*.user1");
        $this->assertEqual($keys[1], "*.*");

        $keys = $config->getKeys("page1");
        $this->assertEqual(count($keys), 3);
        $this->assertEqual($keys[0], "page1.*");
        $this->assertEqual($keys[1], "page1");
        $this->assertEqual($keys[2], "*.*");

        $keys = $config->getKeys("page1", "*");
        $this->assertEqual(count($keys), 3);
        $this->assertEqual($keys[0], "page1.*");
        $this->assertEqual($keys[1], "page1");
        $this->assertEqual($keys[2], "*.*");

        $keys = $config->getKeys("page1", "aci1");
        $this->assertEqual(count($keys), 5);
        $this->assertEqual($keys[0], "page1.aci1");
        $this->assertEqual($keys[1], "page1.*");
        $this->assertEqual($keys[2], "page1");
        $this->assertEqual($keys[3], "*.aci1");
        $this->assertEqual($keys[4], "*.*");

        // }}}
        // {{{ [2] Check story name and needsBookmark()

        $this->assertEqual($config->getStoryName(), $configs['story']['name']);

        // check story scope bookmark
        $this->assertTrue($config->needsBookmark());
        // check bookmark is omitted in page, story scope is used
        $this->assertTrue($config->needsBookmark("page1", "user1"));
        // check bookmark is specified.
        $this->assertFalse($config->needsBookmark("page1", "user2"));
        // last bookmark page must be bookmark "ON".
        $this->assertTrue($config->needsBookmark("page2"));
        $this->assertTrue($config->needsBookmark("*"));

        // }}}
        // {{{ [3] Check getPageParams()

        $params = $config->getPageParams();
        $this->assertEqual($params['component'], "default");
        $params = $config->getPageParams("*", "user1");
        $this->assertEqual($params['component'], "default_user1");
        $params = $config->getPageParams("page3");
        $this->assertEqual($params['component'], "default");
        $params = $config->getPageParams("page3", "user1");
        $this->assertEqual($params['component'], "default_user1");
        $params = $config->getPageParams("page3", "user2");
        $this->assertEqual($params['component'], "default");

        $params = $config->getPageParams("page2");
        $this->assertEqual($params['component'], "page2");
        $params = $config->getPageParams("page2", "user1");
        $this->assertEqual($params['component'], "page2");

        $params = $config->getPageParams("page1");
        $this->assertEqual($params['component'], "default");
        $params = $config->getPageParams("page1", "user1");
        $this->assertEqual($params['component'], "page1_user1");
        $params = $config->getPageParams("page1", "user2");
        $this->assertEqual($params['component'], "page1_user2");

        // }}}
        // {{{ [4] isLastPage()

        $this->assertTrue($config->isLastPage("page1"));
        $this->assertTrue($config->isLastPage("page2"));
        $this->assertTrue($config->isLastPage("*"));
        $this->assertFalse($config->isLastPage("page2", "userX"));
        $this->assertFalse($config->isLastPage("page1", "user1"));
        $this->assertFalse($config->isLastPage("page1", "user2"));

        // }}}
        // {{{ [5] isNextPageOf()

        $this->assertFalse($config->isNextPageOf("page1", "pageA", "user1"));
        $this->assertFalse($config->isNextPageOf("page2", "pageA", "user1"));
        $this->assertFalse($config->isNextPageOf("page2", "pageA"));

        $this->assertTrue($config->isNextPageOf("page1", "pageA", "user2"));
        $this->assertTrue($config->isNextPageOf("page1", "pageB", "user2"));

        // }}}
        // {{{ [6] getBarrierParams()

        // not defined "next" configs
        $params = $config->getBarrierParams("page1", "pageA", "user1");
        $this->assertNull($params);

        // normal route.
        $params = $config->getBarrierParams("page1", "pageA", "user2");
        $this->assertEqual($params['component'], "barrier1");

        // not defined in "barrier" section
        $params = $config->getBarrierParams("page1", "pageB", "user2");
        $this->assertNull($params);

        // defined barrier value is empty
        $params = $config->getBarrierParams("page1", "pageC", "user2");
        $this->assertNull($params);

        // defined barrier value is null
        $params = $config->getBarrierParams("page1", "pageD", "user2");
        $this->assertNull($params);

        // given next page is not defined.
        $params = $config->getBarrierParams("page1", "pageE", "user2");
        $this->assertNull($params);

        // }}}
        // {{{ [7] isEventOf()

        $this->assertFalse($config->isEventOf("page1", "eventA", "user1"));
        $this->assertFalse($config->isEventOf("page2", "eventA", "user1"));
        $this->assertFalse($config->isEventOf("page2", "eventA"));

        $this->assertTrue($config->isEventOf("page1", "eventA", "user2"));
        $this->assertTrue($config->isEventOf("page1", "eventA", "user2"));

        // }}}
        // {{{ [8] getGuardParams()

        // not defined "event" configs
        $params = $config->getGuardParams("page1", "eventA", "user1");
        $this->assertNull($params);

        // normal route.
        $params = $config->getGuardParams("page1", "eventA", "user2");
        $this->assertEqual($params['component'], "guard1");

        // not defined in "guard" section
        $params = $config->getGuardParams("page1", "eventB", "user2");
        $this->assertNull($params);

        // defined guard value is empty
        $params = $config->getGuardParams("page1", "eventC", "user2");
        $this->assertNull($params);

        // defined guard value is null
        $params = $config->getGuardParams("page1", "eventD", "user2");
        $this->assertNull($params);

        // given guard is not defined.
        $params = $config->getGuardParams("page1", "eventE", "user2");
        $this->assertNull($params);

        // }}}
        // {{{ [9] getEventParams()

        // normal
        $params = $config->getEventParams("eventA");
        $this->assertEqual($params['component'], "eventA");
        $this->assertEqual($params['transit']['transit1'], "transit_page1");

        // not defined in "event" section
        $params = $config->getEventParams("eventB");
        $this->assertNull($params);

        // }}}
    }

    // }}}
    // {{{ testStoryBookmarkIsOnImplicitly

    function testStoryBookmarkIsOnImplicitly()
    {
        // {{{ [0] configuration
        $configs = array(
            "story" => array(
                "name" => "story1",
                ),
            "page" => array(
                "page1" => array(
                    "bookmark" => "on",
                    ),
                "page2" => array(
                    "bookmark" => "off",
                    ),
                "page3" => array(
                    ),
                "*" => array(
                    "component" => "default_1",
                    ),
                ),
            );
        $config =& new Xhwlay_Config_PHPArray($configs);
        $params = $config->getPageParams();
        $this->assertEqual($params['component'], "default_1");
        // }}}
        // {{{ [1] needsBookmark() (story scope)

        $this->assertTrue($config->needsBookmark());
        $this->assertTrue($config->needsBookmark("page1"));
        $this->assertFalse($config->needsBookmark("page2"));
        $this->assertTrue($config->needsBookmark("page3"));

        $this->assertTrue($config->needsBookmark("*"));
        $this->assertTrue($config->needsBookmark("*", "*"));
        $this->assertTrue($config->needsBookmark("page1", "*"));
        $this->assertTrue($config->needsBookmark("page1", "user1"));

        // }}}
    }

    // }}}
    // {{{ testStoryBookmarkIsOffExplicity

    function testStoryBookmarkIsOffExplicity()
    {
        // {{{ [0] configuration
        $configs = array(
            "story" => array(
                "name" => "story1",
                "bookmark" => "off",
                ),
            "page" => array(
                "page1" => array(
                    "bookmark" => "on",
                    ),
                "page2" => array(
                    "bookmark" => "off",
                    ),
                "page3" => array(
                    ),
                ),
            );
        $config =& new Xhwlay_Config_PHPArray($configs);
        // }}}
        // {{{ [1] needsBookmark() (story scope)

        $this->assertFalse($config->needsBookmark());
        $this->assertTrue($config->needsBookmark("page1"));
        $this->assertFalse($config->needsBookmark("page2"));
        $this->assertFalse($config->needsBookmark("page3"));

        $this->assertFalse($config->needsBookmark("*"));
        $this->assertFalse($config->needsBookmark("*", "*"));
        $this->assertTrue($config->needsBookmark("page1", "*"));
        $this->assertTrue($config->needsBookmark("page1", "user1"));

        // }}}
    }

    // }}}
    // {{{ testPageParamAppendTest

    function testPageParamAppendTest()
    {
        // {{{ [0] appendix 1
        $configs = array(
            "page" => array(
                "page1.user1" => array(
                    "component" => "p1u1",
                    ),
                "*.user1" => array(
                    "component" => "u1",
                    ),
                "*.*" => array(
                    "component" => "*.*",
                    ),
                ),
            );
        $config =& new Xhwlay_Config_PHPArray($configs);

        $params = $config->getPageParams("page1", "user1");
        $this->assertEqual($params['component'], "p1u1");

        $params = $config->getPageParams("page1", "user2");
        $this->assertEqual($params['component'], "*.*");

        $params = $config->getPageParams("page1");
        $this->assertEqual($params['component'], "*.*");

        $params = $config->getPageParams("page2", "user1");
        $this->assertEqual($params['component'], "u1");

        $params = $config->getPageParams("page2", "user2");
        $this->assertEqual($params['component'], "*.*");

        $params = $config->getPageParams("page2");
        $this->assertEqual($params['component'], "*.*");

        // }}}
        // {{{ [1] appendix 2
        $configs = array(
            "page" => array(
                "page1.user1" => array(
                    "component" => "p1u1",
                    ),
                "*.user1" => array(
                    "component" => "u1",
                    ),
                "*" => array(
                    "component" => "*.*",
                    ),
                ),
            );
        $config =& new Xhwlay_Config_PHPArray($configs);

        $params = $config->getPageParams("page1", "user1");
        $this->assertEqual($params['component'], "p1u1");

        $params = $config->getPageParams("page1", "user2");
        $this->assertEqual($params['component'], "*.*");

        $params = $config->getPageParams("page1");
        $this->assertEqual($params['component'], "*.*");

        $params = $config->getPageParams("page2", "user1");
        $this->assertEqual($params['component'], "u1");

        $params = $config->getPageParams("page2", "user2");
        $this->assertEqual($params['component'], "*.*");

        $params = $config->getPageParams("page2");
        $this->assertEqual($params['component'], "*.*");

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
