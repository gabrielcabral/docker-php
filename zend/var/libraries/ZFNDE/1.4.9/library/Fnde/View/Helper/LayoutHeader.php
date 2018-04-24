<?php
/**
 * @category   Fnde
 * @package    View
 * @subpackage Helper_LayoutHeader
 */

class Fnde_View_Helper_LayoutHeader extends Zend_View_Helper_Abstract
{
    /**
     * @param  array  $messages
     * @param  string $class
     * @param  string $title
     * @return string
     */
    function layoutHeader($title = null , $subtitle = null)
    {
        if (!is_null($title)) {
            $this->view->headTitle()->append($title);
        }
        if (!is_null($subtitle)) {
            $this->view->headTitle()->append($subtitle);
        }
        
        return '<div id="conteudoCabecalho">'
                . (is_null($title) ? "Título":"<h1>{$title}</h1>")
                . (is_null($subtitle) ? "Subtítulo":"<h2>{$subtitle}</h2>")
                . "</div>";
    }
}
