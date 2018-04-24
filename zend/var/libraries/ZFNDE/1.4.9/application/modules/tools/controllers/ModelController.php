<?php
class Tools_ModelController extends Fnde_Controller_Action {

    private $_sessionName = 'ToolsModel';

    public function init() {
        $this->setTitle('Ferramentas');
        $this->addActionMenu($this->view->Url(array('module' => 'tools', 'controller'=>'model')), 'Gerador de Model');
        $this->addActionMenu($this->view->Url(array('module' => 'tools', 'controller'=>'model', 'action'=>'sql')), 'Consulta Sql');
    }

    public function indexAction() {
        $this->_forward('list');
    }

    public function listAction() {
        $this->setSubtitle('Gerador de Model: Configuração');
        $form = new Tools_Model_ListForm(Fnde_Corp_Business_Schema::getList());
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (!$form->isValid($formData)) {
                $this->addInstantMessage(Fnde_Message::MSG_ERROR, self::MSG_INVALID);
            }
        }
        $this->addInstantMessage(Fnde_Message::MSG_INFO, 'Selecione uma ou mais entidades.');
        $form->setAction($this->getUrl('tools', 'model', 'generate'))
             ->setMethod('post');
        $this->view->form = $form;
    }

    public function generateAction() {
        $this->setSubtitle('Gerador de Model: Resultado');
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (!isset($formData['tables']) or count($formData['tables']) === 0 ) {
                $this->addMessage(Fnde_Message::MSG_ALERT, 'Selecione pelo menos 1(uma) entidade.');
                $this->_forward('list');
            } else {
                $this->view->content = nl2br(Fnde_Corp_Business_Schema::generate($formData['tables']));
            }
        } else {
            $this->addMessage(Fnde_Message::MSG_ALERT, 'Selecione pelo menos 1(uma) entidade.','/tools/model');
        }
    }

    public function sqlAction(){
        $this->setSubtitle('Consultas SQL');
        $form = new Tools_Model_SqlForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ( $form->isValid($formData) ){
                try {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $stmt = $db->fetchAll($formData['sqlcode']);
                    $this->view->data = $stmt;
                    $this->addInstantMessage(Fnde_Message::MSG_SUCCESS, 'Consulta realizada com sucesso.');
                } catch (Exception $e) {
                    $this->addInstantMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
                }
            } else {
                $this->addInstantMessage(Fnde_Message::MSG_ALERT, self::MSG_INVALID);
            }
        }
        $this->addInstantMessage(Fnde_Message::MSG_INFO, 'Área disponível apenas em ambiente de desenvolvimento.');
        $form->setAction($this->getUrl('tools', 'model', 'sql'))
             ->setMethod('post');
        $this->view->form = $form;
    }

//    public function generateTable(array $data){
//        $str = "<div class=\"listagem\">
//        <table>
//            <caption>Resultou em " .count($data). " linhas</caption>";
//        $str .= '<thead><tr><th>' .implode('</th><th>', array_keys($data[0])) . '</th></tr></thead>';
//        $str .= '<tbody>';
//        foreach($data as $row){
//            $str .= '<tr><td>' .implode('</td><td>', $row) . '</td></tr>';
//        }
//        $str .= '</tbody></table></div>';
//        return $str;
//    }
}