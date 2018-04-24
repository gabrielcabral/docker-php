<?php

/**
 * Classe padrão de Ações do Controller
 */
abstract class Fnde_Controller_Action extends Zend_Controller_Action {
    const MSG_INVALID = 'O formulário possui informações incompletas ou inválidas.';

    /**
     * Método Construtor.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs
     * @final
     */
    final public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);
        $this->view->message = array();

        // @todo Tem um problema com Zend_Controller_Action::_forward(). MAS quase ninguem utiliza forward.
        $this->_helper->_flashMessenger->setNamespace(Fnde_Message::MSG_ERROR);
        $this->view->message[Fnde_Message::MSG_ERROR] = $this->_helper->_flashMessenger->getMessages();

        $this->_helper->_flashMessenger->setNamespace(Fnde_Message::MSG_ALERT);
        $this->view->message[Fnde_Message::MSG_ALERT] = $this->_helper->_flashMessenger->getMessages();

        $this->_helper->_flashMessenger->setNamespace(Fnde_Message::MSG_SUCCESS);
        $this->view->message[Fnde_Message::MSG_SUCCESS] = $this->_helper->_flashMessenger->getMessages();

        $this->_helper->_flashMessenger->setNamespace(Fnde_Message::MSG_INFO);
        $this->view->message[Fnde_Message::MSG_INFO] = $this->_helper->_flashMessenger->getMessages();
    }

    /**
     * Título do Caso de uso.
     * @access public
     * @param string $value
     */
    final public function setTitle($value) {
        $this->view->title = $value;
    }

    /**
     * Subtítulo do contexto no caso de uso.
     * 
     * @access public
     * @param string $value
     */
    final public function setSubtitle($value) {
        $this->view->subtitle = $value;
    }

    /**
     * Adiciona Mensagem que será exibida no proximo contexto, recomendada para uso com redirecionamento.
     *
     * @access protected
     * @param string $msg | ['title' : 'Title', 'msg' : 'Msg']
     * @param redirect String
     */
    final protected function addMessage($namespace, $msg, $redirect = null) {
        $this->_helper->_flashMessenger->setNamespace($namespace);
        $this->_helper->_flashMessenger->addMessage($msg);

        if ($redirect) {
            $this->_redirect($redirect);
        }
    }

    /**
     * Adiciona Mensagem Instantânea no mesmo contexto, sem redirecionamento.
     *
     * @access protected
     * @param string $namespace
     * @param string $msg | ['title' : 'Title', 'msg' : 'Msg']
     */
    final protected function addInstantMessage($namespace, $msg) {
        $this->view->message[$namespace][0] = $msg;
    }

    /**
     * Obtem a URL usando o ViewHelper: url.
     *
     * @access protected
     * @param string $module
     * @param string $controller
     * @param string $action
     */
    final protected function getUrl($module = null, $controller = null, $action = null, $clear = false) {
        $arrUrl = array();
        if (!is_null($module)) {
            $arrUrl['module'] = $module;
        }
        if (!is_null($controller)) {
            $arrUrl['controller'] = $controller;
        }
        if (!is_null($action)) {
            $arrUrl['action'] = $action;
        }
        return $this->view->url($arrUrl, null, $clear);
    }

    /**
     * Testa se a url já existe no menu
     * @param string $url
     * @return boolean
     */
    protected function actionMenuExist($url) {
        $actionMenu = $this->getActionMenu();
        foreach ($actionMenu as $item)
            if ($item['url'] == $url)
                return true;
        return false;
    }

    /**
     * Apenas para inicilizar o atributo na view
     * @return array
     */
    protected function getActionMenu() {
        if (is_null($this->view->actionMenu) && !is_array($this->view->actionMenu)) {
            $this->view->actionMenu = array();
        }
        return $this->view->actionMenu;
    }

    /**
     * Adiciona um action menu
     * @param string $url
     * @param string $label
     */
    final protected function addActionMenu($url, $label) {
        $actionMenu = $this->getActionMenu();
        if (!$this->actionMenuExist($url)) {
            $this->view->actionMenu[] = array("url" => $url, "label" => $label);
        }
    }

    /**
     * Adicionar menu de ações na tela, a chave deve ser a URL e o valor o rótulo do menu
     * @param array $menu 
     */
    final protected function setActionMenu(array $menu) {
        $urls = array_keys($menu);
        $menu[] = array();
        foreach ($urls as $url) {
            $_menu[] = array('url' => $url, 'label' => $menu[$url]);
        }
        $this->view->actionMenu = $_menu;
    }

    /**
     * Remove todos os itens do menu de ações 
     * @return void
     */
    final protected function clearActionMenu() {
        $this->view->actionMenu = array();
    }

}