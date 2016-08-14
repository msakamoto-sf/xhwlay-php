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
 * Xhwlay Simple PHP serialize() Funciton Output Renderer
 *
 * @package Xhwlay
 * @subpackage Renderer
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: Serialize.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

/**
 * requires
 */
require_once('Xhwlay/Renderer/AbstractRenderer.php');

// {{{ Xhwlay_Renderer_Serialize

/**
 * PHP serialize() output renderer
 *
 * MAYBE, useful.
 *
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @subpackage Renderer
 * @since 1.0.0
 * @link http://www.hawklab.jp/jsonencoder/ Hawklab, Jsphon
 * @link http://pear.hawklab.jp/ Hawklab, PEAR Channel
 */
class Xhwlay_Renderer_Serialize extends Xhwlay_Renderer_AbstractRenderer
{
    // {{{ render()

    /**
     * Return PHP serialize() function output of template variables.
     *
     * @return string returns serialize().
     * @see Xhwlay_Renderer_AbstractRenderer::render()
     */
    function render()
    {
        return serialize($this->_vars);
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
