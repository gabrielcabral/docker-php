<?php
/**
 * $Rev:: 155                  $
 * $Date:: 2010-11-12 16:43:24#$
 * $Author:: TheoziranSilva    $
 *
 * @package ZFnde
 * @category View Helper
 * @name Link
 *
 * Helper para criação de Links
 * @since v1.1.2
 * @author theoziran<theoziran.silva@fnde.gov.br>
 */
class Fnde_View_Helper_Link extends Zend_View_Helper_HtmlElement {

    /**
     *
     * @param string $title
     * @param string $url
     * @param array $attribs
     * @param string $escapeTitle
     * @return string 
     */
    public function link($title, $url = null, array $attribs = array(), $escapeTitle = true) {
        $title = ($escapeTitle) ? htmlentities($title) : $title;
        $url = $url ? $url : $title;
        $attribs['href'] = $url;
        if (is_null($attribs['title']))
            $attribs['title'] = $title;
        $html = '<a href="' . $attribs['href'] . '" ';
        unset($attribs['href']);
        $html .= $this->_htmlAttribs($attribs) . '>' . $title . '</a>';
        return $html;
    }

}