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
 * Xhwlay Simple PHP var_dump() Funciton Output Renderer
 *
 * @package Xhwlay
 * @subpackage Renderer
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: VarDump.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

/**
 * requires
 */
require_once('Xhwlay/Renderer/AbstractRenderer.php');

// {{{ Xhwlay_Renderer_VarDump

/**
 * var_dump() output renderer (for trying, testing, tasting, learning)
 *
 * <code>
 * $renderer =& new Xhwlay_Renderer_VarDump();
 * $renderer->set("key1", 123);
 * echo $renderer->render();
 * </code>
 *
 * @author FengJing<feng-jing-gsyc-2s@glamenv-septzen.net
 * @package Xhwlay
 * @subpackage Renderer
 * @since 1.0.0
 */
class Xhwlay_Renderer_VarDump extends Xhwlay_Renderer_AbstractRenderer
{
    // {{{ render()

    /**
     * Returns var_dump() output as string
     *
     * @return string var_dump() result of assigned variables.
     * @see Xhwlay_Renderer_AbstractRenderer::render()
     */
    function render()
    {
        ob_start();
        var_dump($this->_vars);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
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
