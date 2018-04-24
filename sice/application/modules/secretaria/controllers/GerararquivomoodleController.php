<?php

/**
 * Controller do SolicitarAutorizacao
 *
 * @author rafael.paiva
 * @since 08/05/2012
 */

class Secretaria_GerarArquivoMoodleController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 *
	 * @access public
	 *
	 * @author rafael.paiva
	 * @since 08/05/2012
	 */
	public function getForm( $arDados = array(), $arExtra = array() ) {

		//Recupera os parametros
		$params = $this->_getAllParams();

		$objTurma = new Fnde_Sice_Business_Turma();
		$infoTurma = $objTurma->pesquisaTurma($params, true);
		$infoTurma = $infoTurma[0];
		$infoComplementarTurma = $objTurma->pesquisarDadosComplementaresTurma(
				array('NU_SEQ_CURSO' => $infoTurma['NU_SEQ_CURSO'],
						'NU_SEQ_CONFIGURACAO' => $infoTurma['NU_SEQ_CONFIGURACAO']));
		$quantCursistas = $objTurma->pesquisarVinculosPorTurma($params['NU_SEQ_TURMA']);

		$form = new GerarArquivoMoodle_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));
		$form->setAction($this->view->baseUrl() . '/index.php/secretaria/gerararquivomoodle/gerar-arquivo')->setMethod(
				'post')->setAttrib('id', 'form');

		$html = $form->getElement("htmlTurma");
		$str = $this->view->retornarHtmlTabela($infoTurma, $infoComplementarTurma,
				$quantCursistas['QUANT_CURSISTAS']);
		$html->setValue($str);

		$htmlAlunosMatriculados = $form->getElement("htmlAlunosMatriculados");
		$strAlunosMatriculados = $this->retornaHtmlAlunosMatriculados($params['NU_SEQ_TURMA']);
		$htmlAlunosMatriculados->setValue($strAlunosMatriculados);

		$nuMinAlunos = $form->getElement("NU_MIN_ALUNOS");
		$nuMinAlunos->setValue($infoComplementarTurma['NU_MIN_ALUNOS']);

		$nuAlunosMatriculados = $form->getElement("NU_ALUNOS_MATRICULADOS");
		$nuAlunosMatriculados->setValue($quantCursistas['QUANT_CURSISTAS']);

		return $form;
	}

	/**
	 * Função que constrói o HTML da tabela de cursistas matriculados.
	 * @param int $codTurma
	 */
	public function retornaHtmlAlunosMatriculados( $codTurma ) {

		$obBusiness = new Fnde_Sice_Business_HistoricoTurma();
		$arAlunosMatriculados = $obBusiness->getAlunosMatriculadosPorTurma($codTurma);

		$html .= "<div class='listagem datatable'>";
		$html .= "<table>";
		$html .= "<thead><tr><th style='text-align: center'>Contagem</th><th style='text-align: center'>Matrícula</th>";
		$html .= "<th style='text-align: center'>Nome</th><th style='text-align: center'>CPF</th></tr></thead>";
		$html .= "<tbody>";

		$count = 0;

		foreach ( $arAlunosMatriculados as $aluno ) {
			$html .= "<tr><td style='text-align: center'>" . ++$count . "</td><td style='text-align: center'>"
					. $aluno['NU_MATRICULA'];
			$html .= "</td><td>" . $aluno['NO_USUARIO'] . "</td><td style='text-align: center'>"
					. Fnde_Sice_Business_Componentes::formataCpf($aluno['NU_CPF']) . "</td></tr>";
		}

		$html .= "</tbody>";
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	/**
	 * Carrega tela.
	 */
	public function carregarTurmaAction() {
		$this->setTitle('Matricular');
		$this->setSubtitle('Gerar arquivo moodle');

		//monta menu de contexto
		$menu = array($this->getUrl('secretaria', 'turma', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);
		$oBisComponetes = new Fnde_Sice_Business_Componentes();

		try {

			$arParam = $this->_getAllParams();

			$form = $this->getForm($arParam);
			$form->populate($arParam);

			$oBisComponetes->validarGerarArquivoMoodle($arParam);

			$this->view->form = $form;

			$msgOrientacao = "Para notificar o cursita é necessário, para evitar transtornos, gerar o arquivo do moodle, importar o";
			$msgOrientacao .= " arquivo gerado no moodle, certificar que está tudo certo e só depois notificar os cursitas.";
			$this->addInstantMessage(Fnde_Message::MSG_INFO, $msgOrientacao);

			return $this->render('form');
		} catch ( Exception $e ) {
			$this->addMessage(Fnde_Message::MSG_ERROR, $e->getMessage());
			$this->_redirect("/secretaria/turma/list");
		}
	}

	/**
	 * Função para carregar as informações necessárias ao arquivo. 
	 */
	public function gerarArquivoAction()
	{
		$this->_helper->layout()->disableLayout();
		$arParam = $this->_getAllParams();
		$session = new Zend_Session_Namespace('secretaria.moodle.msg');

		try {
			// nome do arquivo
			$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
			$filename = $usuarioLogado->cpf;
			$filename .= "_" . date("YmdHis") . ".csv";

			$csv = $this->gerarCSV($arParam['NU_SEQ_TURMA']);

			// Enviar e-mail aos responsáveis
			$msg = "O arquivo foi gerado e encaminhado, automaticamente, para a Equipe de Suporte. ";
			$msg .= "Para seu controle salve o arquivo em seu computador.";

			$config = Zend_Registry::get('config');
			$mail = new Zend_Mail();
			$mail->setBodyHtml($msg);
			$mail->setFrom($config['app']['mailer']['from_mail'], $config['app']['mailer']['from_name']);
			$mail->addTo("suporteconselheiro@gmail.com");
			$mail->addTo("conselhoescolar@mec.gov.br");
			$mail->setSubject($msg);

			$at = new Zend_Mime_Part($csv);
			$at->type = 'text/csv';
			$at->disposition = Zend_Mime::DISPOSITION_INLINE;
			$at->encoding = Zend_Mime::ENCODING_BASE64;
			$at->filename = $filename;

			$mail->addAttachment($at);

			$mail->send();

			$session->donwloadArquivo = array('msg' => $msg, 'tipo' => Fnde_Message::MSG_SUCCESS);

			// Dumpar o CSV
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header('Content-Description: File Transfer');
			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=" . $filename);
			header("Expires: 0");
			header("Pragma: public");

			echo $csv;
			exit;
		} catch (Exception $e) {
			$session->donwloadArquivo = array('msg' =>  $e->getMessage(), 'tipo' => Fnde_Message::MSG_ERROR);
		}
	}

	/**
	 * Função para gerar um arquivo CSV a partir dos dados da pesquisa.
	 */
	public function gerarCSV( $nu_seq_turma ) {
		$separador = ";";
		$csv = implode($separador, array('username', 'password', 'firstname', 'lastname', 'email', 'city'));
		$csv .= "\r\n";

		// modal turma
		$obTurma = new Fnde_Sice_Business_Turma();
		$result = $obTurma->obterDadosArquivoMoodle($nu_seq_turma);

		// gerando conteudo
		if ($result) {
			foreach ( $result as $item ) {
				$linha = array();

				// username/password
				if($item['DS_TIPO_PERFIL_SEGWEB'] == "sice_cursista") {
					$username = 'm' . $item['NU_CPF'];
				}else{
					$username = $item['NU_CPF'];
				}

				$username = $this->removerAcento($username);

				$linha[] = $username;
				$linha[] = $username;

				// firstname
				$linha[] = $this->removerAcento($item['NO_USUARIO']);

				// lastname
				if($item['DS_TIPO_PERFIL_SEGWEB'] == "sice_tutor") {
					$lastname = sprintf("Tutor - %s", $item['UF_TURMA']);
				} else if($item['DS_TIPO_PERFIL_SEGWEB'] == "sice_articulador"){
					$lastname = sprintf("Articulador - %s", $item['UF_TURMA']);
				} else{
					$lastname = sprintf("Turma %s - %s", $item['NU_SEQ_TURMA'], $item['UF_TURMA']);
				}

				$linha[] = $lastname;

				// email
				$linha[] = $item['DS_EMAIL_USUARIO'];

				// city
				$linha[] = $this->removerAcento($item['NO_MUNICIPIO']);

				$csv .= implode($separador, $linha) . "\r\n";
			}
		}

		return $csv;
	}

	private function removerAcento($str){
		return strtr($str, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC");
	}

	/**
	 * Prepara os parametros para enviar o email.
	 */
	public function enviarEmailAction() {
		$arParam = $this->_getAllParams();

		$obTurma = new Fnde_Sice_Business_Turma();
		$result = $obTurma->obterDadosArquivoMoodle($arParam['NU_SEQ_TURMA']);

		foreach ( $result as $item ) {
			if($item['DS_TIPO_PERFIL_SEGWEB'] == "sice_cursista") {
				$username = 'm' . $item['NU_CPF'];
			}else{
				$username = $item['NU_CPF'];
			}

			$bodyText = $this->bodyText($username, $username);
			$addTo = $item['DS_EMAIL_USUARIO'];
			$nameTo = $item['NO_USUARIO'];
			$subject = "Senha de Acesso ao Sistema de Informação do Programa Nacional de Fortalecimento dos Conselhos Escolares";
			Fnde_Sice_Business_Componentes::sendMail($bodyText, $addTo, $nameTo, $subject);
		}

		$this->addMessage(Fnde_Message::MSG_SUCCESS, "Email enviado com sucesso.");
		$this->_redirect("/secretaria/turma/list");
	}

	/**
	 * Recupera o corpo do email.
	 * @param string $login
	 * @param string $senha
	 */
	public function bodyText( $login, $senha ) {

		$text = "Você está recebendo seu login e senha para acessar a plataforma MOODLE.
Lembre-se: todo login e senha são de uso pessoal e intransferível, esta é a chave do sistema de segurança de sua comunicação.
<br><br>
O nome de usuário ou login e a senha são gerados automaticamente, podendo ser alterada pelo usuário.
<br><br>
Login: $login
Senha: $senha
Link: http://cursos.mec.gov.br/
<br><br>	
<br>
Obs.: Seu acesso será liberado em até 1 dia útil. ";

		return $text;
	}
}
