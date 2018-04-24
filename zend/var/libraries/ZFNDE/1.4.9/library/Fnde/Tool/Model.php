<?php

class Fnde_Tool_Model extends Fnde_Tool {

    private $tableInfo = null;
    private $app = null;

    public function setTableInfo(array $tableInfo) {
        $this->tableInfo = new stdClass();

        //retira prefixo da tabela e ajusta no padrão UpperCamelCase
        $this->tableInfo->tableName = Fnde_Tool::stringToUpperCamelCase(
                substr($tableInfo['name'], strpos($tableInfo['name'], '_') + 1)
        );
        $this->tableInfo->schema = $tableInfo['schema'];
        $this->tableInfo->name = $tableInfo['name'];
        $this->tableInfo->primary = $this->stringOfArray($tableInfo['primary']);
        $this->tableInfo->cols = $this->stringOfArray($tableInfo['cols']);
        $this->tableInfo->metadata = $this->stringOfArray($tableInfo['metadata']);
        $this->tableInfo->sequence = $tableInfo['sequence'];
    }

    public function setApp($sgApp) {
        $this->app = ucfirst(strtolower($sgApp));
    }

    public function generate() {
        $basePath = $this->path . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR;

        $listReplace = array(
            '{_app_}' => $this->app,
            '{_table_}' => $this->tableInfo->tableName,
            '{_schema_}' => $this->tableInfo->schema,
            '{_name_}' => $this->tableInfo->name,
            '{_primary_}' => $this->tableInfo->primary,
            '{_cols_}' => $this->tableInfo->cols,
            '{_metadata_}' => $this->tableInfo->metadata,
            '{_sequence_}' => $this->tableInfo->sequence
        );

        $template = new Fnde_Tool_Template();
        $tmpOut = '>Gerando Abstract Model...';
        $template->setTemplate($this->pathTemplate . 'Model_Database.skl');
        $template->setFilename($basePath . str_replace('_', DIRECTORY_SEPARATOR,  "Fnde_{$this->app}_Model_Database_" . $this->tableInfo->tableName . '.php'));
        $template->setReplacer($listReplace);
        if ($template->generate(true)){
            $tmpOut .= $template->getMessage() . ' - OK.' . PHP_EOL;
        } else {
            $tmpOut .= $template->getMessage() . ' - Falhou.' . PHP_EOL;
        }
        $tmpOut .= '>Gerando Model...';
        $template->setTemplate($this->pathTemplate . 'Model.skl');
        $template->setFilename($basePath . str_replace('_', DIRECTORY_SEPARATOR, "Fnde_{$this->app}_Model_". $this->tableInfo->tableName . '.php'));
        $template->setReplacer($listReplace);
        if ($template->generate()){
            $tmpOut .= $template->getMessage() . ' - OK.' . PHP_EOL;
        } else {
            $tmpOut .= $template->getMessage() . ' - Falhou.' . PHP_EOL;
        }

        return $tmpOut;
    }
}