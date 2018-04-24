<?php
/**
 * $Rev:: 13#$
 * $Date:: 2#$
 * $Author:: $
 *
 * @package ZFnde
 * @category View Helper
 * @name TabPane
 *
 * Helper para TabPane
 * @since v1.1.2
 * @author theoziran<theoziran.silva@fnde.gov.br>
 */
class Fnde_View_Helper_TabPane {

    private $title;
    private $content;
    private $isEnabled;

    /**
     * @param string $title
     * @param string $content
     * @param boolean $isEnabled
     */
    public function __construct($title = null, $content = null, $isEnabled = true) {
        $this->title = $title;
        $this->content = $content;
        $this->isEnabled = $isEnabled;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled) {
        $this->isEnabled = $enabled;
    }

    /**
     * @return boolean
     */
    public function isEnabled() {
        return $this->isEnabled;
    }

    /**
     * @param string $title
     * @param string $content
     * @param boolean $isEnabled
     * @return Fnde_View_Helper_TabPane 
     */
    public function TabPane($title, $content, $isEnabled = true) {
        return new Fnde_View_Helper_TabPane($title, $content, $isEnabled);
    }

    /**
     * @return string
     */
    public function __toString() {
        if ($this->isEnabled)
            return (string) $this->content;
    }

}