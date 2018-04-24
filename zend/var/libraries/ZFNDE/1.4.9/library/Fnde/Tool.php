<?php
class Fnde_Tool
{
    protected $path = null;
    protected $pathTemplate = null;

    public function  __construct() {
        $this->pathTemplate = ZF_FNDE_APPLICATION . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR;
    }

    public function setPath($path) {
        if (file_exists($path)){
            $this->path = $path;
        } else {
            throw new Exception("Path definido '{$path}', não existe!", E_ERROR);
        }
    }

    /**
     *
     * @param string $value
     * @return string
     */
    final public function stringToUpperCamelCase($value){
        $value = strtolower($value);
        if (strpos($value,'_') === false){
            return ucfirst($value);
        }
        return implode('', array_map('ucfirst',  explode('_',$value)));
    }

    final public function stringOfArray(array $pValue, $identation = 2){
        //string de Fim de Linha e Identação;
        $strEOL = PHP_EOL . str_repeat(' ', $identation * 4);
        $tmpOut = "array(" . $strEOL;
        foreach($pValue as $key => $value){
            if (is_string($key)){
                $tmpOut .= "'{$key}' => ";
            }
            if (is_array($value)){
                $tmpOut .= $this->stringOfArray($value, $identation + 1);
            } else if (is_bool($value)){
                $tmpOut .= ($value?'true':'false');
            } else if (is_string($value)) {
                $value = trim($value);
                $value = (( $value[0]=="'" && $value[strlen($value)-1] == "'" )? "{$value}" : "'{$value}'");
                $tmpOut .= $value;
            } else if (is_null($value)) {
                $tmpOut .= 'null';
            } else {
                $tmpOut .= $value;
            }
            $tmpOut .= ',' . $strEOL;
        }
        $tmpOut .= ')';
        return $tmpOut;
    }
}