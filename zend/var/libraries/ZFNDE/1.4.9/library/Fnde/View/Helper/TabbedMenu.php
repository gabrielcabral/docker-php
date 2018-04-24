<?php

/**
 * @author walkero
 */
class Fnde_View_Helper_TabbedMenu extends Zend_View_Helper_Navigation_Menu {

    public function tabbedMenu(Zend_Navigation_Container $container = null) {
        if (null !== $container) {
            $this->setContainer($container);
        }
        return $this;
    }

    /**
     * Renders a normal menu (called from {@link renderMenu()})
     *
     * @param  Zend_Navigation_Container $container   container to render
     * @param  string                    $ulClass     CSS class for first UL
     * @param  string                    $indent      initial indentation
     * @param  int|null                  $minDepth    minimum depth
     * @param  int|null                  $maxDepth    maximum depth
     * @param  bool                      $onlyActive  render only active branch?
     * @return string
     */
    protected function _renderMenu(Zend_Navigation_Container $container, $ulClass, $indent, $minDepth, $maxDepth, $onlyActive) {
        if (!strlen($indent)) {
            $indent = str_repeat(' ', 8);
            $html .= parent::_renderMenu(
                            $container,
                            $ulClass,
                            $indent,
                            0,
                            0,
                            $onlyActive
            );
        }

        $nivel = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);
        $nivel->setMaxDepth(0);

        foreach ($nivel as $page) {
            
            if (( $page->hasPages()) && ( '#' == substr($page->getHref(), 0, 1) )) {
                $pageId = str_replace('#', '', $page->getHref());

                $html .= PHP_EOL . $indent . "<div id=\"{$pageId}\">" . PHP_EOL;
                $subNivel = $container->findBy('uri', '#' . $pageId);
                $html .= parent::_renderMenu(
                                $subNivel,
                                $ulClass,
                                $indent . str_repeat(' ', 4),
                                0,
                                0,
                                $onlyActive
                );
                $html .= self::_renderMenu(
                                $subNivel,
                                $ulClass,
                                $indent . str_repeat(' ', 4),
                                0,
                                0,
                                $onlyActive
                );
                $html .= PHP_EOL . $indent . "</div>";
            }
        }
        return $html;
    }

}