<?php
abstract class Fnde_Model_Xml_Abstract
{
    /**
     * @var SimpleXMLElement
     */
    protected $xml = null;
    protected $filename = null;

    /**
     * Load file error string.
     *
     * Is null if there was no error while file loading
     *
     * @var string
     */
    protected $_loadFileErrorStr = null;

    protected function checkFilename($pFilename){
        if (is_null($pFilename)){
            throw new Fnde_Model_Exception('Nome de Arquivo não informado!');
        }
        if (!file_exists($pFilename)){
            throw new Fnde_Model_Exception("Arquivo {$pFilename} não existe!");
        }
    }

    /**
     * Handle any errors from simplexml_load_file
     *
     * @param integer $errno
     * @param string $errstr
     * @param string $errfile
     * @param integer $errline
     */
    public function _loadFileErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($this->_loadFileErrorStr === null) {
            $this->_loadFileErrorStr = $errstr;
        } else {
            $this->_loadFileErrorStr .= (PHP_EOL . $errstr);
        }
    }

    public function __construct($xml = null){

        set_error_handler(array($this, '_loadFileErrorHandler'));
        if (is_null($xml)){
            if (dirname($this->filename) == '.'){
                $this->filename =  APPLICATION_DATA . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . $this->filename;
            }
            $this->checkFilename($this->filename);
            $this->xml = simplexml_load_file($this->filename);
        } else {
            if (strstr($xml, '<?xml')) {
                $this->xml = simplexml_load_string($xml);
            } else {
                $this->setFilename($xml);
                $this->xml = simplexml_load_file($this->filename);
            }
        }
        restore_error_handler();

        // Check if there was a error while loading file
        if ($this->_loadFileErrorStr !== null) {
            throw new Fnde_Model_Exception($this->_loadFileErrorStr);
        }
    }

    public function setFilename($pFilename){
        $this->checkFilename($pFilename);
        $this->filename = $pFilename;
    }

    public function save(){
        return $this->xml->asXML($this->filename);
    }

    /**
     * Returns a string or an associative and possibly multidimensional array from
     * a SimpleXMLElement.
     *
     * @param  SimpleXMLElement $xmlObject Convert a SimpleXMLElement into an array
     * @return array|string
     */
    protected function _toArray()
    {
        return Fnde_Util::XmlToArray($this->xml);
    }


}