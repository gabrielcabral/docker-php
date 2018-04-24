<?php
/**
 * Description of Schema
 *
 * @author walkero
 */
class Fnde_Corp_Business_Schema {

    /**
     *
     * @var Fnde_Corp_Model_Schema
     */
    public $modelSchema = null;

    static private $instance = null;

    private function __construct(){
        $this->modelSchema = new Fnde_Corp_Model_Schema();
    }

    static public function getInstance(){
        if ( is_null(self::$instance) ){
            self::$instance = new self();
        }
        return self::$instance;
    }

    static public function getList() {
        $obj = self::getInstance();
        $config = Zend_Registry::get('config');
        $stmt = $obj->modelSchema->listTables(strtoupper($config['resources']['db']['params']['username']));
        if (count($stmt) > 0) {
            return $stmt;
        }
    }

    /**
     *
     * @param array $listTables 
     */
    static public function generate(array $listTables) {
        $obj = self::getInstance();
        
        $config = Zend_Registry::get('config');

        $tool = new Fnde_Tool_Model();
        $tool->setApp($config['app']['name']);
        $tool->setPath( APPLICATION_ROOT );
        $tmpOut = null;
        foreach ($listTables as $table) {
            $tmpOut .= "Tabela: {$table}:" . PHP_EOL;
            try {
                $tool->setTableInfo($obj->modelSchema->infoTable($table));
                $tmpOut .= $tool->generate() . PHP_EOL;
            } catch(Exception $e){
                $tmpOut .= '><span style="color:#AA0000">Exception catch: ' . $e->getMessage() . '</span>'. PHP_EOL . PHP_EOL;
            }
        }
        return $tmpOut;
    }
}