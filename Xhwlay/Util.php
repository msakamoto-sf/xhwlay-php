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
 * Xhwlay Utilities
 *
 * @package Xhwlay
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Util.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

/**
 * Utility collection.
 *
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @since 1.0.0
 */
class Xhwlay_Util
{
    // {{{ properties
    // }}}
    // {{{ uuid()

    /**
     * RFC 4122 UUID Generator
     *
     * "mimec" version in
     * {@link http://jp.php.net/manual/ja/function.uniqid.php }.
     *
     * @static
     * @access public
     * @return string uuid
     * @since 1.0.0
     */
    function uuid()
    {
        return 
            strtoupper(sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) 
            ));
    }

    // }}}
    // {{{ bcid()

    /**
     * generate random id for bookmark container id
     *
     * @static
     * @access public
     * @return string random id
     * @since 1.0.0
     */
    function bcid()
    {
        return sha1(Xhwlay_Util::uuid());
    }

    // }}}
    // {{{ diceRoller()

    /**
     * Get true or false by probability.
     *
     * Return true in $probability/$divisor percent probability.
     * Example:
     * <code>
     * Xhwlay_Util::diceRoller(1, 100)
     * </code>
     * This return true one time in 100 calls.
     * 99 times in 100 calls return false.
     *
     * If $probability = 0, always return false.
     * If $probability >= $divisor, always return true.
     *
     * @static
     * @access public
     * @param integer probability
     * @param integer divisor
     * @return boolean
     * @since 1.0.0
     */
    function diceRoller($probability, $divisor)
    {
        if ($probability <= 0) {
            return false;
        }
        $r = (integer)mt_rand(1, $divisor);
        return ($r <= $probability);
    }

    // }}}
    // {{{ isTrue()

    /**
     * Determine various value is TRUE or FALSE.
     * "OK", "ON", "TRUE", "ENABLE" (case ignored) returns true.
     *
     * @static
     * @access public
     * @param mixed check value
     * @return boolean
     */
    function isTrue($v)
    {
        if (is_null($v)) { return false; }

        if (is_array($v)) { return true; }
        if (is_object($v)) { return true; }
        if (is_resource($v)) { return true; }

        if ($v === true) { return true; }
        if ($v === 1) { return true; }
        if ($v === "1") { return true; }
        $v = strtoupper(trim($v));
        if ($v === "OK") { return true; }
        if ($v === "ON") { return true; }
        if ($v === "TRUE") { return true; }
        if ($v === "ENABLE") { return true; }

        return false;
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
