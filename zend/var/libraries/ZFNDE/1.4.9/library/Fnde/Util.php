<?php

class Fnde_Util
{

    static public function debug($var, $collapsed = true)
    {
        require_once(dirname(dirname(__FILE__)) .'/dBug.php');
        ob_start();
        $dbug = new dBug($var, null, $collapsed);
        $tmpStr = ob_get_contents();
        ob_end_clean();
        return $tmpStr;
    }
    
    /**
     * Returns a string or an associative and possibly multidimensional array from
     * a SimpleXMLElement.
     *
     * @param  SimpleXMLElement $xmlObject Convert a SimpleXMLElement into an array
     * @return array|string
     */
    static public function XmlToArray(SimpleXMLElement $xmlObject)
    {
        $tmpArray = array();

        // Search for parent node values
        if (count($xmlObject->attributes()) > 0) {
            foreach ($xmlObject->attributes() as $key => $value) {
                $value = (string) $value;

                if (array_key_exists($key, $tmpArray)) {
                    if (!is_array($tmpArray[$key])) {
                        $tmpArray[$key] = array($tmpArray[$key]);
                    }

                    $tmpArray[$key][] = $value;
                } else {
                    $tmpArray[$key] = $value;
                }
            }
        }

        // Search for children
        if (count($xmlObject->children()) > 0) {
            foreach ($xmlObject->children() as $key => $value) {
                if (count($value->children()) > 0) {
                    $value = self::XmlToArray($value);
                } else if (count($value->attributes()) > 0) {
                    $attributes = $value->attributes();
                    if (isset($attributes['value'])) {
                        $value = (string) $attributes['value'];
                    } else {
                        $value = self::XmlToArray($value);
                    }
                } else {
                    $value = (string) $value;
                }

                if (array_key_exists($key, $tmpArray)) {
                    if (!is_array($tmpArray[$key]) || !array_key_exists(0, $tmpArray[$key])) {
                        $tmpArray[$key] = array($tmpArray[$key]);
                    }

                    $tmpArray[$key][] = $value;
                } else {
                    $tmpArray[$key] = $value;
                }
            }
        } else if (count($tmpArray) === 0) {
            // Object has no children nor attributes it's a string
            $tmpArray = (string) $xmlObject;
        }

        return $tmpArray;
    }

    static public function yUmlException(Exception $e, array $options = array()){
        $err = $e->getTrace();
        return "<h2>Exception: {$e->getMessage()}</h2>" . self::yUml($err, $options);
    }

    static public function yUml(array $value, array $options = array()){
        $opt = '';
        $opts = array('dir'=>'td', 'scale'=>'70');

        if (isset($options['dir'])){
            $opts['dir'] = $options['dir'];
        }
        if (isset($options['scale'])){
            $opts['scale'] = $options['scale'];
        }
        if (count($opts)){
            foreach($opts as $k => $v){
                $opt .= (empty($opt)?'':';') . "{$k}:{$v}";
            }
            $opt .= '/';
        }
        $imgUrl = "http://yuml.me/diagram/class/";
        $ymlParam = '';
        $value = array_reverse(array_reverse($value),true);
        $obj = array();
        foreach($value as $k => $v){
            $strArg = '';
            if (count($v['args'])){
                foreach($v['args'] as $arg){
                    $strArg .= (empty($strArg)?'':'.');
                    switch (gettype($arg)){
                        case "string":
                            $strArg .= "'{$arg}'";
                            break;
                        case "object":
                            $strArg .= "object(".get_class($arg).")";
                            break;
                        case "boolean":
                            $strArg .= ($arg?'true':'false');
                            break;
                        case "integer":
                        case "double":
                            $strArg .= $arg;
                            break;
                        default:
                            $strArg .= gettype($arg);
                            break;
                    }
                }
            }
            $obj[$k] = "[" . ( isset($v['class'])?$v['class'].$v['type']:'' ) . "{$v['function']}({$strArg})" . ( $k == max(array_keys($value)) ? '{bg:red}':'' ) . "]";
            if (count($obj) > 1){
                $ymlParam .= $obj[$k+1] . "<-$obj[$k]" . ( $k > 0 ? ', ': '');
            }
        }
        if (count($obj) === 1){
            $ymlParam .= "$obj[0]";
        }
        $ymlParam = (isset($options['note'])?"[note:" . str_replace('"', "'", $options['note']) . "{bg:cornsilk}], ":'') . urlencode($ymlParam);
        return '<img src="' . $imgUrl . $ymlParam . '" />';
    }

    static public function validateSchema($strXML, $fileXSD) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'iso-8859-1');
        $dom->loadXML(stripcslashes($strXML));

        if (!$dom->schemaValidate($fileXSD)) {
            $errors = libxml_get_errors();
            $arrMessage = array();
            foreach ($errors as $error) {
                $arrMessage[] = array('code' => $error->code,
                    'text' => $error->message);
            }
            $arrMessage['status'] = 0;
            unset($dom);
            return $arrMessage;
        }
        unset($dom);
        return true;
    }

    static public function stringToHex($value){
        return implode('',array_map('dechex',array_map('ord', str_split($value))));
    }
    
    static public function hexToString($value){
        return implode('',array_map('chr',array_map('hexdec', str_split($value,2))));
    }
}