<?php

/**
 * Controller do Turma
 * 
 * @author diego.matos
 * @since 25/04/2012
 */

class Financeiro_AceiteController extends Fnde_Sice_Controller_Action {

	protected $_stSistema = 'sice';

	/**
	 * Monta o formulário e renderiza na view
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 22/06/2012
	 */
	public function formAction() {
		if ( !Fnde_Sice_Business_Componentes::permitirAcesso() ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, Fnde_Sice_Business_Componentes::MSG_NAO_PERMITIR_ACESSO);
			$this->_redirect('index');
		}

		$this->setTitle('Bolsas');
		$this->setSubtitle('Termo de Aceite');

		//Recupera o objeto de formulário para validação
		$form = $this->getForm();
		$this->view->form = $form;
		$this->render('form');
	}

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		$form = new Aceite_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/financeiro/bolsa/list')->setMethod('post');

		$htmlAceite = $form->getElement("htmlAceite");
		$str = $this->retornaHTMLAceite();
		$htmlAceite->setValue($str);

		return $form;
	}

	/**
	 * Método para escrever HTML com o texto do Termo de Aceite.
	 * 
	 * @author diego.matos
	 * @since 25/04/2012
	 */
	private function retornaHTMLAceite() {
		$html = "<div class='listagem datatable'>";
		$html .= "<table style='border-color:black'>";
		$html .= "<tr>";
		$html .= "<td style='background-color:#DCDCDC'>";
		$html .= "<div style=\"text-align:justify;\">";
                
                $html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Você está entrando em uma funcionalidade que faz parte do processo de pagamento de bolsas pelo Governo Federal.  
                    O pagamento de bolsas, amparado pela Lei nº 11.273, de 6 de fevereiro de 2006, requer que você analise se o(s) agente(s) 
                    executores do Curso de Formação para Conselheiros Escolares fizeram jus ao recebimento da bolsa.  
                    Neste processo, o Articulador deve verificar se o Tutor efetivamente desenvolveu atividades de formação com 
                    os conselheiros escolares da turma finalizada.  A ocorrência de fraude deve ser comunicada imediatamente ao 
                    Fundo Nacional de Desenvolvimento da Educação e ao Programa Nacional de Fortalecimento dos Conselhos Escolares 
                    para que estes tomem as providências legais cabíveis.";
                
		$html .= "</div>";
		$html .= "</td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .= "</div>";
		return $html;
	}
}
