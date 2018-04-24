<?php
class ErrorController extends Fnde_Sice_Controller_Action {
	public function errorAction() {
		$errors = $this->_getParam('error_handler');
		$this->setTitle('Erro');

		switch ( $errors->type ) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
			// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR, 'Controller/Action não encontrada!');
				$this->setSubtitle('Controller/Action');
				break;
			default:
			// application error
				$this->getResponse()->setHttpResponseCode(500);
				$this->addInstantMessage(Fnde_Message::MSG_ERROR,
						'Ocorreu um erro inesperado na aplicação! Tente novamente mais tarde.');
				$this->setSubtitle('Application');

				$this->sendExceptionByMail($errors->exception);
				break;
		}

		// conditionally display exceptions
		if ( $this->getInvokeArg('displayExceptions') == true ) {
			$this->view->exception = $errors->exception;
		}

		$this->view->request = $errors->request;
	}
	/**
	 * Obtem o objeto de log.
	 * @return Zend_Mail
	 */
	private function sendExceptionByMail( Exception $exception ) {
		throw $exception;/*
						 $options    = Zend_Registry::get('config');
						 $serverName = gethostbyaddr($_SERVER['SERVER_ADDR']);
						 
						 $listMail   = (isset($options['app']['exceptions']['log']['mail'])? $options['app']['exceptions']['log']['mail'] : array());
						 $appName    = (isset($options['app']['name'])? $options['app']['name'] : 'Nome não identificado');
						 
						 
						 $mail = new Zend_Mail();
						 $mail->setFrom("Erro no servidor: {$serverName}");
						 $mail->setSubject("Falha na Aplicacao: {$appName} em {$serverName}");
						 if ( APPLICATION_ENV !== 'desenv' ){
						 $listMail[] = 'CGETI_CDEV_arquitetura_PHP@fnde.gov.br';
						 }
						 foreach($listMail as $email){
						 $mail->addTo($email);
						 }
						 
						 $mail->setBodyHtml(
						 "[{$exception->getCode()}] - {$exception->getMessage()} in {$exception->getFile()}".
						 " on line: {$exception->getLine()}.<br />".
						 "<h3>Informações sobre a Exception:</h3>
						 <p>
						 <b>Mensagem:</b> [{$exception->getCode()}] - {$exception->getMessage()}<br/>
						 <b>Arquivo:</b> {$exception->getFile()}<br/>
						 <b>Linha:</b> {$exception->getLine()}
						 </p>
						 
						 <h3>Segue no arquivo em anexo com o GetTrace da Exception:</h3>
						 <pre>{$exception->getTraceAsString()}</pre>
						 "
						 );
						 if( count($listMail) > 0 ){
						 $mail->send();
						 }
						 
						 //Limpa Variáveis.
						 unset($options);
						 unset($serverName);
						 unset($appName);
						 unset($listMail);
						 unset($email);
						 unset($mail);*/

	}
}
