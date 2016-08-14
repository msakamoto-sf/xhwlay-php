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
 * Xhwlay Renderer Abstract Interface
 *
 * @package Xhwlay
 * @subpackage Renderer
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id: AbstractRenderer.php 40 2008-02-11 14:23:04Z msakamoto-sf $
 */

// {{{ constants

/**
 * Xhwlay Renderer Error Code :
 * given template name was not found or readable.
 *
 * @var integer
 * @since 1.0.0
 */
if (!defined('XHWLAY_RENDERER_EC_TEMPLATE_NOT_FOUND')) {
    define('XHWLAY_RENDERER_EC_TEMPLATE_NOT_FOUND', 0x0100 | 0x01);
}

// }}}
// {{{ Xhwlay_Renderer_AbstractRenderer

/**
 * AbstractRenderer of Xhwlay Renderer Classes.
 *
 * This class implements "Template Variables Manupilation" methods.
 * But core "render()" methods is not implemented yet. Only definition.
 *
 * So, developers must create class extend this, and implements
 * {@link Xhwlay_Renderer_AbstractRenderer::render()} method.
 *
 * @abstract
 * @author FengJing <feng-jing-gsyc-2s@glamenv-septzen.net>
 * @package Xhwlay
 * @subpackage Renderer
 * @since 1.0.0
 */
class Xhwlay_Renderer_AbstractRenderer
{
    // {{{ properties

    /**
     * template variables
     *
     * @var array
     * @access protected
     * @since 1.0.0
     */
    var $_vars = array();

    /**
     * view name
     *
     * @var string
     * @access protected
     * @since 1.0.0
     */
    var $_viewName = "";

    // }}}
    // {{{ get()

    /**
     * Get value associated with $key in template variables.
     *
     * @final
     * @access public
     * @param string
     * @return mixed
     * @since 1.0.0
     */
    function get($key)
    {
        return @$this->_vars[$key];
    }

    // }}}
    // {{{ getAll()

    /**
     * Get all(raw) template variables
     *
     * @final
     * @access public
     * @return mixed
     * @since 1.0.0
     */
    function getAll()
    {
        return $this->_vars;
    }

    // }}}
    // {{{ set()

    /**
     * Set $key => $value to template variables.
     *
     * @final
     * @access public
     * @param string
     * @param mixed
     * @since 1.0.0
     */
    function set($name, $value)
    {
        $this->_vars[$name] = $value;
    }

    // }}}
    // {{{ setAll()

    /**
     * Set all(raw) template variables as associated array.
     * NOTICE: This method DON'T MERGE existing variables.
     * This OVERWRITE ALL VARIABLES.
     *
     * @final
     * @access public
     * @param mixed
     * @since 1.0.0
     */
    function setAll($values)
    {
        $this->_vars = $values;
    }

    // }}}
    // {{{ remove()

    /**
     * remove specified value by key from template variables.
     *
     * @final
     * @access public
     * @param string key
     * @since 1.0.0
     */
    function remove($key)
    {
        if (!isset($this->_vars[$key])) {
            return;
        }
        unset($this->_vars[$key]);
    }

    // }}}
    // {{{ clear()

    /**
     * Clear all template variables.
     *
     * @final
     * @access public
     * @since 1.0.0
     */
    function clear()
    {
        unset($this->_vars);
        $this->_vars = array();
    }

    // }}}
    // {{{ setViewName()

    /**
     * Set View Name
     *
     * @final
     * @access public
     * @param string "Template Name" : Depends on how renderer is 
     *                implemented.
     *               It maybe "template file name", "view name", 
     *               "primary key for db table storing template text", 
     *               ... e.t.c.
     *               Please confirm renderer class document which you use.
     *               This parameter can be omitted.
     * @since 1.0.0
     */
    function setViewName($viewName)
    {
        $this->_viewName = $viewName;
    }

    // }}}
    // {{{ getViewName()

    /**
     * Get View Name
     *
     * @final
     * @access public
     * @return string view name
     * @since 1.0.0
     */
    function getViewName()
    {
        return $this->_viewName;
    }

    // }}}
    // {{{ render()

    /**
     * Interface for rendering handler
     *
     * @abstract
     * @return mixed Depends on implements/extends class.
     * @access public
     * @since 1.0.0
     */
    function render()
    {
        die(__CLASS__.'::render() must be overriden');
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
