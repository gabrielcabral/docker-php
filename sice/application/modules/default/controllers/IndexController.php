<?php
class IndexController extends Fnde_Sice_Controller_Action {

	public function init() {
		$this->setTitle('Início');
		parent::init();

        //1159467
		//1467545 // 20667639268
//		Zend_Auth::getInstance()->getStorage()->write((object) array(
//			'nu_seq_usuario' => '1201194',//'1469396',
//            'username' => 'SICE_08899987874',
//            'nome' => 'ANA LUIZA LERMEN',
//            'email' => 'jose.camara@fnde.gov.br',
//            'ramal' => '5076',
//            'departamento' => 'DIRETORIA DE TECNOLOGIA',
//            'cpf' => '08899987874',//'31060170159',
//            'tipo' => 'I',
//			'credentials' => array(
//                //'sice_coord_nacional_gestor',
//				'sice_coord_nacional_adm',
//				//'sice_coord_nacional_equipe',
//                //'sice_coord_executivo_estadual',
//                //'sice_coord_estadual',
//                //'sice_articulador',
//                //'sice_tutor',
//                //'sice_cursista',
//			)
//		));

//        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
	}

	/**
	 * Login :SAMARAD
	Senha :34299213
	 */

	public function indexAction() {
		$config = Zend_Registry::get('config');

		$this->urlFilterNamespace = new Zend_Session_Namespace('searchParam');
		$this->urlFilterNamespace->unsetAll();
		$this->urlFilterNamespace->param = null;

		$this->setSubtitle('Informações');
		$this->view->app = $config['app'];

        /*
         * SGD 26371
         * TERMO DE COMPROMISSO
         * */
        $possuiTermo = Fnde_Sice_Business_TermoCompromisso::possuiTermo();
        if (!$possuiTermo) {
            //se não assinou redireciona para assinar termo
            $this->_redirect('/manutencao/termocompromisso/assinar');
        }
	}
}
