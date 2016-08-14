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
 * Xhwlay Simple JSON Output Renderer
 *
 * @package Xhwlay
 * @subpackage Renderer
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Json.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

/**
 * requires
 */
require_once('Xhwlay/Renderer/AbstractRenderer.php');
require_once('Jsphon.php');

// {{{ Xhwlay_Renderer_Json

/**
 * JSON output renderer
 *
 * It requires Jsphon package(pear.hawklab.jp), PHP mb_string() module.
 *
 * By default, don't convert to UTF-8. If you output JSON 
 * as UTF-8, your code like below:
 *
 * <code>
 * $renderer =& new Xhwlay_Renderer_Json();
 * $renderer->autoUTF8 = true;
 * $renderer->escapeNonASCII = true;
 * $renderer->set('key1', "(multibye character)");
 *
 * echo $renderer->render();
 * </code>
 * 
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @subpackage Renderer
 * @since 1.0.0
 * @link http://www.hawklab.jp/jsonencoder/ Hawklab, Jsphon
 * @link http://pear.hawklab.jp/ Hawklab, PEAR Channel
 */
class Xhwlay_Renderer_Json extends Xhwlay_Renderer_AbstractRenderer
{
    // {{{ properties

    /**
     * If this flag is TRUE, then, before Jsphon::encode() calling, 
     * template variables are converted to UTF-8 by 
     * mb_convert_variables().
     *
     * @var boolean
     * @access public
     * @since 1.0.0
     */
    var $autoUTF8 = false;

    /**
     * If true, convert UTF-8 multibyte characters to 
     * Unicode escape sequences.
     *
     * @var boolean
     * @access public
     * @since 1.0.0
     * @see Jsphon::encode()
     */
    var $escapeNonASCII = false;

    /**
     * @var boolean
     * @access public
     * @since 1.0.0
     * @see Jsphon::encode()
     */
    var $escapeOverUCS2 = false;

    // }}}
    // {{{ render()

    /**
     * Return JSON Data as string
     *
     * @return string returns Jsphon::encode() output.
     * @see Xhwlay_Renderer_AbstractRenderer::render()
     */
    function render()
    {

        if ($this->autoUTF8) {
            mb_convert_variables(
                'UTF-8', 
                mb_internal_encoding(), 
                $this->_vars
                );
        }
        $json = Jsphon::encode(
            $this->_vars,
            $this->escapeNonASCII,
            $this->escapeOverUCS2);
        return $json;
    }

    // }}}
}

// }}}

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
