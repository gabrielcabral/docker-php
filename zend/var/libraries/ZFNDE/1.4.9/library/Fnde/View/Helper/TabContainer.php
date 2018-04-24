<?php
/**
 * $Rev:: 14#$
 * $Date:: 2#$
 * $Author:: $
 *
 * @package ZFnde
 * @category View Helper
 * @name TabContainer
 *
 * Helper para TabContainer
 * @since v1.1.2
 * @author theoziran<theoziran.silva@fnde.gov.br>
 */
class Fnde_View_Helper_TabContainer {

    /**
     * Armazena as abas que deve ser adicionadas
     * @var array
     */
    private $_tabs;
    /**
     * Classes que são usadas no estilo dos containers
     * @var array
     */
    private $_confs = array(
        'container_tag' => 'div',
        'container_class' => 'tab',
        'container_menu_class' => 'navigation',
        'cotainer_tab_class' => 'tabContainer'
    );

    public function __construct() {
        $this->_tabs = array();
    }

    /**
     * Adiciona um TabPane para o TabContainer
     * @param string $id
     * @param Fnde_View_Helper_TabPane $tab
     * @return Fnde_View_Helper_TabContainer
     */
    public function addTab($id, Fnde_View_Helper_TabPane $tab) {
        $this->_tabs[$id] = $tab;
        return $this;
    }

    /**
     * Remove um TabPane para o TabContainer
     * @param string $id
     * @return Fnde_View_Helper_TabContainer 
     */
    public function removeTab($id) {
        unset($this->_tabs[$id]);
        return $this;
    }

    /**
     * @return Fnde_View_Helper_TabContainer 
     */
    public function TabContainer() {
        return $this;
    }

    /**
     * Rederiza o html caso imprima o objeto
     * @return string
     */
    public function __toString() {
        $html = '<' . $this->_confs['container_tag'] . ' class="' . $this->_confs['container_class'] . '">';
        if (count($this->_tabs))
            $html .= '<ul class="' . $this->_confs['container_menu_class'] . '">';
        $isFirst = true;
        foreach ($this->_tabs as $id => $tab) {
            $className = ($isFirst) ? 'active' : '';
            if (!$tab->isEnabled()) {
                $className .= ' disabled';
                $id = 'disable';
            }
            $html .= '<li class="' . $className . '"><a href="#' . $id . '">' . $tab->getTitle() . '</a></li>';
            $isFirst = false;
        }
        if (count($this->_tabs))
            $html .= '</ul>';
        $isFirst = true;
        foreach ($this->_tabs as $id => $tab) {
            $display = 'none';
            $html .= '<div id="' . $id . '" class="' . $this->_confs['cotainer_tab_class'] . '" style="display:' . $display . ';">';
            $html .= $tab->getContent();
            $html .= '</div>';
            $isFirst = false;
        }
        $html .= '</' . $this->_confs['container_tag'] . '>';
        return $html;
    }

}