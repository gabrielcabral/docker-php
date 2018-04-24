<?php
class Fnde_Tool_Template
{
    private $filename = null;
    private $template = null;
    private $replacer = array();
    private $message = null;

    /**
     * @param string $fromTemplate local path do arquivo de template
     * @param string $toFilename   local path do arquivo que será gerado
     * @param array $arrReplacer   deve conter Key: Search content, Value: Replace content
     */
    public function __construct($fromTemplate = null, $toFilename = null, array $arrReplacer = array()){
        if (!is_null($fromTemplate)){
            $this->setTemplate($fromTemplate);
        }
        if (!is_null($toFilename)){
            $this->setFilename($toFilename);
        }
        if (count($arrReplacer) > 0){
            $this->setReplacer($arrReplacer);
        }
    }

    private function setMessage($message){
        $this->message = $message;
    }

    /**
     * Após o uso do $this->generate(), haverá uma mensagem.
     *
     * @return string
     */
    public function getMessage(){
        return $this->message;
    }

    /**
     * Define o arquivo de template que será utilizado.
     * 
     * @param string $template local path do arquivo de template
     */
    public function setTemplate($template) {
        $this->template = $template;
    }

    /**
     * Define o arquivo que será gerado.
     *
     * @param string $filename local path do arquivo que será gerado
     */
    public function setFilename($filename) {
        $this->filename = $filename;
    }

    /**
     * Define o array contendo Key: Search content, Value: Replace content
     *
     * @param array $replacer deve conter Key: Search content, Value: Replace content
     */
    public function setReplacer(array $replacer) {
        $this->replacer = $replacer;
    }

    private function checkDependencies(){
        if (is_null($this->template)){
            throw new Exception('Template não definido!',E_WARNING);
        }
        if (!file_exists($this->template)){
            throw new Exception("Template '{$this->template}' não foi encontrado!",E_WARNING);
        }
        if (is_null($this->filename)){
            throw new Exception('Arquivo a ser gerado não definido!',E_WARNING);
        }
        if (count($this->replacer) == 0){
            throw new Exception('Replacer não definido!',E_WARNING);
        }
    }

    private function _generate($overwrite = false){
        $this->checkDependencies();
        $tmpFileExist = file_exists($this->filename);
        if ( $tmpFileExist && !$overwrite){
           $this->setMessage("<span style=\"color:#AA0000\">Ignorado</span> ({$this->filename})");
           return false;
        }

        if ( !file_exists(dirname($this->filename)) ){
            mkdir(dirname($this->filename),0777,true);
        }
        $retPut = file_put_contents($this->filename,
            str_replace(array_keys($this->replacer), array_values($this->replacer), file_get_contents($this->template)));

        if ($retPut){
            if ($tmpFileExist){
                $this->setMessage("<span style=\"color:#0000AA\">Sobrescrito</span> ({$this->filename})");
            } else {
                $this->setMessage("<span style=\"color:#00AA00\">Gerado</span> ({$this->filename})");
            }
            return true;
        } else {
            $this->setMessage('Não foi possível gerar o arquivo.');
            return false;
        }
    }

    /**
     * Generate file.
     * 
     * @return bool 
     */
    public function generate($overwrite = false){
        return $this->_generate($overwrite);
    }

}