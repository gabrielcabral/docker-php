<?php

/**
 * Arquivo de classe de Negocio do padrao de webservices do FNDE.
 *
 * Último Commit: r$Rev$ por $Author$ em $Date$.
 * 
 * @package Fnde
 * @category Webservice
 * @name Business
 * @author Alberto Guimaraes Viana <alberto.viana@fnde.gov.br>
 * @author Walker de Alencar Oliveira <walker.oliveira@fnde.gov.br>
 */

/**
 * Classe abstrata de Business para Webservices
 * 
 * @version $Id$
 */
abstract class Fnde_Webservice_Business {
    const DATA_MESSAGE_EMPTY = 'Mensagem não foi definida';
    const DATA_MESSAGE_ERROR = 'Os parâmetros atribuídos na Mensagem estão inválidos';

    private $content = null;
    private $message = null;

    /**
     * Retorna o valor da variavel content
     * @return array
     */
    private function getContent() {
        return $this->content;
    }

    /**
     * Seta um content
     * @param array $content
     * @final
     */
    final protected function setContent(array $content) {
        $this->content = $content;
    }

    /**
     * Retorna o valor da variavel message
     * @return array
     */
    private function getMessage() {
        return $this->message;
    }

    /**
     * Seta uma mensagem
     * @param string $message
     * @param string $code
     * @final
     */
    final protected function setMessage($message, $code) {
        if( empty ($message) || $code == '' ) {
            throw new Exception(self::DATA_MESSAGE_ERROR, E_WARNING);
        }
        $this->message = array(
            'code' => $code,
            'text' => $message
        );
    }

    /**
     * Constroi a estrutura da resposta
     * @return array
     * @final
     */
    final protected function buildResponse() {
        if (is_null($this->message)) {
            throw new Exception(self::DATA_MESSAGE_EMPTY, E_WARNING);
        }

        $response = array();
        if (!is_null($this->content)) {
            $response['content'] = $this->getContent();
        }

        $response['status'] = array(
            'result' => true,
            'message' => $this->getMessage()
        );
        return $response;
    }

    /**
     * Valida o xml de acordo com o schema xsd
     * 
     * @param string $strXML
     * @param string $fileXSD
     * @return array|bool
     * @final
     */
    final protected function validateSchema($strXML, $fileXSD) {
        return Fnde_Util::validateSchema($strXML, $fileXSD);
    }

}