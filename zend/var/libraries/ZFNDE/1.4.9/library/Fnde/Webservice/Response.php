<?php

/**
 * Arquivo de classe de Resposta do padrao de webservices do FNDE.
 *
 * Último Commit: r$Rev$ por $Author$ em $Date$.
 *
 * @package Fnde
 * @category Webservice
 * @name Response
 * @author Alberto Guimaraes Viana <alberto.viana@fnde.gov.br>
 * @author Walker de Alencar Oliveira <walker.oliveira@fnde.gov.br>
 */

/**
 * Classe de Resposta para Webservices
 *
 * @version $Id$
 */
class Fnde_Webservice_Response {
    const DATA_STATUS_EMPTY = 'Status não foi definido';

    private $_validType = array('xml');

    /**
     * Depende das configurações do application.ini
     *
     * @return array
     */
    protected function getHeader() {
        $config = Zend_Registry::get('config');
        return array(
            'app' => $config['app']['name'],
            'version' => $config['app']['version'],
            'created' => date('Y-m-d\TH:i:s')
        );
    }

    /**
     * Gera uma saída XML baseada em um Array
     *
     * Depende das configurações do application.ini
     *
     * @param array $data
     * @return string XML
     */
    final private function toXML(array $data) {
        $config = Zend_Registry::get('config');
        $doc = new Fnde_DOMDocument('1.0', $config['webservices']['encoding']);
        if( $config[ 'webservices' ][ 'encoding' ] == 'UTF-8' ) {
            array_walk_recursive( $data, array($this,'encodeUtf8'));
        }
        $doc->arrayToXML($data);
        return html_entity_decode($doc->saveXML(),null,$config['webservices']['encoding']);
    }

    /**
     * Gera a saida do Webservice
     *
     * @param array $content
     * @param string $type
     * @return string
     */
    final protected function treatOutput(array $data, $type = 'xml') {
        $config = Zend_Registry::get('config');
        if (!array_key_exists('status', $data)) {
            throw new exception(self::DATA_STATUS_EMPTY, E_USER_ERROR);
        }
        if (!in_array($type, $this->_validType)) {
            $type = 'xml';
        }
        /**
         * @todo Ajustes do Status.
         */
        $arrBase = array(
            'response' => array(
                'header' => $this->getHeader(),
                'status' => $data['status']
            )
        );
        if (array_key_exists('content', $data)) {
            $arrBase['response']['body'] = $data['content'];
        }

        switch ($type) {
            case 'xml':
                $out = $this->toXML($arrBase);
                break;
        }
        return html_entity_decode($out,null,$config['webservices']['encoding']);
    }

    /**
     * Gera a estrutura de erro para a saida do Webservice
     *
     * @param string $message
     * @param string $code
     * @param array $listError
     * @return array
     */
    final protected function treatError($message, $code, array $listError) {
        return array(
            'status' => array(
                'result' => false,
                'message' => array(
                    'code' => str_pad($code,4,"0", STR_PAD_LEFT),
                    'text' => $message
                ),
                'error' => array(
                    'message' => $listError
                )
            )
        );
    }

    /**
     * Metodo que gera a saída do Webservice
     *
     * @param array $response
     * @return string
     */
    final public function build($response) {
        if (isset($response['status']) && !$response['status']) {
            unset($response['status']);
            $out = $this->treatOutput(
                    $this->treatError(
                        'Operação falhou!', '1',
                        $response
                    )
            );
        } else {
            $out = $this->treatOutput($response);
        }
        return $out;
    }
    
    /**
     * para ser usado com array_walk_recursive
     */
    final private function encodeUtf8(&$value,$key){
        if ( mb_check_encoding( $value, 'UTF-8' ) === false ) {
            $value = utf8_encode( $value );
        }
    }
}