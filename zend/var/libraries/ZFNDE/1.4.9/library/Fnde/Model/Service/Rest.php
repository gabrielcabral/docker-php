<?php

/**
 * Arquivo de classe de Servico do padrao de consumo de webservices do FNDE.
 *
 * $Rev::                      $
 * $Date::                     $
 * $Author::                   $
 *
 * @package Fnde
 * @category Model
 * @name Rest
 * @author Alberto Guimaraes Viana <alberto.viana@fnde.gov.br>
 */

/**
 * Classe abstrata de Consumo de Webservices
 *
 * @version $Id$
 */
abstract class Fnde_Model_Service_Rest extends Zend_Rest_Client {

    protected $_config = null;
    protected $_app = null;
    protected $_version = null;
    protected $_url = null;
    protected $_function = null;

    const HEADER = '
      <header>
        <app>%s</app>
        <version>%s</version>
        <created>%s</created>
      </header>';

    /**
     * Constructor
     */
    public function __construct() {
        $this->_config = Zend_Registry::get('config');
        $this->setApp($this->_config['app']['name']);
        $this->setVersion($this->_config['app']['version']);
    }

    /**
     * Retorna a aplicacao
     * @return string
     */
    protected function getApp() {
        return $this->_app;
    }

    /**
     * Seta a aplicacao
     * @param string $_app
     */
    protected function setApp($_app) {
        $this->_app = $_app;
    }

    /**
     * Retorna a versao
     * @return string
     */
    protected function getVersion() {
        return $this->_version;
    }

    /**
     * Seta a versao
     * @param string $_version
     */
    private function setVersion($_version) {
        $this->_version = $_version;
    }

    /**
     * Retorna a url
     * @return string
     */
    protected function getUrl() {
        return $this->_url;
    }

    /**
     * Seta a url
     * @param string $_url
     */
    protected function setUrl($_url) {
        $this->_url = $_url;
    }

    /**
     * Retorna a funcao
     * @return string
     */
    protected function getFunction() {
        return $this->_function;
    }

    /**
     * Seta a funcao
     * @param string $_function
     */
    protected function setFunction($_function) {
        if (is_null($this->getUrl())) {
            throw new Fnde_Model_Service_Exception('URL deve ser informada.', '0');
        }
        $this->_function = $_function;
        $this->setUri($this->getUrl() . $this->getFunction());
    }

    /**
     * Retorna um datetime
     * @return string
     */
    private function getDate() {
        return date('Y-m-d\TH:i:s');
    }

    /**
     * Retorna o header 
     * @return string
     */
    protected function getHeader() {
        return sprintf(
            self::HEADER,
            $this->getApp(),
            $this->getVersion(),
            $this->getDate()
        );
    }

    /**
     * Converte xml para array
     *
     * @param string $xml String XML
     * @return array
     */
    protected static function xmlToArray($xml) {

        $children = array();
        $return = false;

        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }

        if ($xml instanceof SimpleXMLElement) {
            $children = $xml->children();
            $return = null;
        }

        $first = true;

        if (!empty($children)) {
            foreach ($children as $element => $value) {
                if ($value instanceof SimpleXMLElement) {
                    $values = (array) $value->children();

                    if (count($values) > 0) {
                        if (isset($return[$element])) {
                            if ($first) {
                                $old_value = $return[$element];
                                $return[$element] = array();
                                $return[$element][] = $old_value;
                            }
                            $return[$element][] = self::xmlToArray($value);
                            $first = false;
                        } else {
                            $return[$element] = self::xmlToArray($value);
                        }
                    } else {
                        if (!isset($return[$element])) {
                            $return[$element] = (string) utf8_decode($value);
                        } else {
                            if (!is_array($return[$element])) {
                                $return[$element] = array($return[$element], (string) utf8_decode($value));
                            } else {
                                $return[$element][] = (string) utf8_decode($value);
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }

}