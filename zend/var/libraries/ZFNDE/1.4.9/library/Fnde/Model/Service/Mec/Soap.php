<?php

/**
 * Arquivo de classe de Servico do padrao de consumo de webservices do FNDE.
 *
 * $Rev:: 351                  $
 * $Date:: 2012-07-19 10:19:35#$
 * $Author:: 88602010125       $
 *
 * @package Fnde
 * @category Model
 * @name Soap
 * @author Daniel Wilson de Alvarenga <daniel.alvarenga@fnde.gov.br>
 */

/**
 * Classe abstrata de Consumo de Webservices
 *
 * @version $Id: Soap.php 351 2012-07-19 13:19:35Z 88602010125 $
 */
abstract class Fnde_Model_Service_Mec_Soap extends Zend_Soap_Client {

    protected $_config = null;
    protected $_url = null;
    protected $_function = null;

    /**
     * Constructor
     */
    public function __construct() {
        $this->_config = Zend_Registry::get('config');
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
     * Convert xml to array
     *
     * @param string $xml String XML
     * @return array
     */
    public static function xmlToArray($xml)
    {
        $children = array();
        $return   = false;

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
                    $values = (array)$value->children();

                    if (count($values) > 0) {
                        if (isset($return[$element])) {
                        	if ($first) {
                                $old_value          = $return[$element];
                        		$return[$element]   = array();
                        		$return[$element][] = $old_value;
                        	}
                            $return[$element][] = self::xmlToArray($value);
                            $first = false;
                        } else {
                            $return[$element] = self::xmlToArray($value);
                        }
                    } else {
                        if (!isset($return[$element])) {
                            $return[$element] = (string)$value;
                        } else {
                            if (!is_array($return[$element])) {
                                $return[$element] = array($return[$element], (string)$value);
                            } else {
                                $return[$element][] = (string)$value;
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }
    
}