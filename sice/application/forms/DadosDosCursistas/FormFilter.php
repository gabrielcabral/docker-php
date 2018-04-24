<?php

class DadosDosCursistas_FormFilter extends Fnde_Form
{

    public function __construct()
    {
        $this->setAttribs(array(
            'id' => 'dadosdoscursistasform'
        ));

        $curso = $this->createElement('select', 'NU_SEQ_CURSO', array("label" => "Curso: "));
        $curso->setRequired(true);

        $uf = $this->createElement('select', 'UF_TURMA', array("label" => "UF: "));

        $ano = $this->createElement("select", "NU_ANO", array(
            "label" => "Ano: ",
            "style" => "width:200px;"
        ));
        $ano->setRequired(true);

        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();
        $perfilUsuario = $usuarioLogado->credentials;

        if(
            in_array(Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_ADM, $perfilUsuario) ||
            in_array(Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_EQUIPE, $perfilUsuario) ||
            in_array(Fnde_Sice_Model_PerfilUsuario::SICE_COORD_NACIONAL_GESTOR, $perfilUsuario)
        ){
            $uf->setRequired(false);
        }else{
            $uf->setRequired(true);
        }

        $municipio = $this->createElement('select', 'CO_MUNICIPIO', array("label" => "Município: "));
        $municipio->addMultiOption(null, 'Selecione');

        if(in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario)){
            $municipio->setRequired(true);
        }else{
            $municipio->setRequired(false);
        }

        $mesorregiao = $this->createElement('select', 'CO_MESORREGIAO', array("label" => "Mesorregião: "));
        $mesorregiao->addMultiOption(null, 'Selecione');

        //rede de ensino
        $rededeensino = $this->createElement('select', 'CO_REDE_ENSINO', array("label" => "Rede de Ensino: "));
        $rededeensino->setMultiOptions(array(
            '' => 'Selecione',
            '0' => 'FEDERAL',
            '1' => 'ESTADUAL',
            '2' => 'MUNICIPAL',
            '3' => 'PARTICULAR',
            '4' => 'INTERNACIONAL',
            '5' => 'NAO APLICADA',
            '6' => 'Migração SME',
            '8' => 'ESTADUAL E MUNICIPAL',
            '7' => 'DISTRITAL',
        ), "Selecione");

        $nomeEscola = $this->createElement('select', 'CO_ESCOLA', array('name' => 'CO_ESCOLA', 'label' => 'Nome da Escola:', "style" => "width:200px;"));
        $nomeEscola->addMultiOption(null, "Selecione");

        $cpf = $this->createElement('text', 'NU_CPF', array("label" => "CPF: ", "class" => "cpf"));

        $numeroturma = $this->createElement('text', 'NU_SEQ_TURMA', array("label" => "Número turma: ", "maxlength" => "7", "class" => "inteiro"));

        $situacaoCursista = $this->createElement('select', 'ST_CURSISTA', array("label" => "Situação do Cursista: "));
        $situacaoCursista->addMultiOptions(array(
            '' => 'Selecione',
            'APROVADO' => 'Aprovado',
            'APROVADO COM DESTAQUE' => 'Aprovado com destaque',
            'REPROVADO' => 'Reprovado',
            'DESISTENTE' => 'Desistente'
        ));

        $dtInicio = $this->createElement('text', 'DT_INICIO', array(
            "label" => "Turmas finalizadas entre  - Data início: ",
            "class" => "date dp-applied"
        ));

        $dtFim = $this->createElement('text', 'DT_FIM', array(
            "label" => "Turmas finalizadas entre  - Data fim: ",
            "class" => "date dp-applied"
        ));

        $this->addElements(array(
            $curso, $uf, $municipio,
            $mesorregiao, $rededeensino, $nomeEscola,
            $cpf, $numeroturma, $situacaoCursista,
            $ano, $dtInicio, $dtFim,
        ));

        $this->addDisplayGroup(array(
            'NU_SEQ_CURSO', 'UF_TURMA', 'CO_MUNICIPIO', 'CO_MESORREGIAO', 'CO_REDE_ENSINO',
            'CO_ESCOLA', 'NU_CPF', 'NU_SEQ_TURMA', 'ST_CURSISTA', 'NU_ANO', 'DT_INICIO', 'DT_FIM'
        ), 'dadosturma', array("legend" => "Filtro"));

        $btConsultar = $this->createElement('button', 'consultar', array(
            "label" => "Consultar",
            "value" => "Consultar",
            "class" => "btnConfirmar",
            "title" => "Consultar"
        ));

        $btExportar = $this->createElement('button', 'exportar', array(
            "label" => "Exportar",
            "value" => "Exportar",
            "class" => "btnGerarPlanilha",
            "title" => "Exportar"
        ));

        $this->addElements(array($btConsultar, $btExportar));

        $obDisplayGroup = $this->addDisplayGroup(
            array('consultar', 'exportar'),
            'botoes',
            array('class' => 'agrupador barraBtsAcoes')
        );

        parent::__construct();

        $obDisplayGroup->botoes->addDecorator('HtmlTag', array(
            'tag' => 'div',
            'id' => 'divBotoes',
            'class' => 'barraBtsAcoes'
        ));
        $obDisplayGroup->botoes->removeDecorator('fieldset');

        return $this;
    }

    public function setCurso($options, $value = null)
    {
        $element = $this->getElement('NU_SEQ_CURSO');
        $element->setMultiOptions($options);
        $element->setValue($value);
    }

    public function setUf($options, $value = null)
    {
        $element = $this->getElement('UF_TURMA');
        $element->addMultiOption(null, 'Selecione');
        $element->addMultiOptions($options);
        $element->setValue($value);
    }

    public function setMunicipio($options, $value = null)
    {
        $element = $this->getElement('CO_MUNICIPIO');
        $element->addMultiOption(null, 'Selecione');
        $element->addMultiOptions($options);
        $element->setValue($value);
    }

    public function setMesorregiao($options, $value = null)
    {
        $element = $this->getElement('CO_MESORREGIAO');
        $element->addMultiOption(null, 'Selecione');
        $element->addMultiOptions($options);
        $element->setValue($value);
    }

    public function setRedeEnsino($value = null)
    {
        $element = $this->getElement('CO_REDE_ENSINO');
        $element->setValue($value);
    }

    public function setNomeEscola($options, $value = null)
    {
        $element = $this->getElement('CO_ESCOLA');
        $element->addMultiOption(null, 'Selecione');

        if(count($options)){
            foreach($options as $row){
                $element->addMultiOption($row['CO_ESCOLA'], $row['NO_ESCOLA']);
            }
        }

        $element->setValue($value);
    }

    public function setCpf($value = null)
    {
        $element = $this->getElement('NU_CPF');
        $element->setValue($value);
    }

    public function setNumeroTurma($value = null)
    {
        $element = $this->getElement('NU_SEQ_TURMA');
        $element->setValue($value);
    }

    public function setSituacaoTurma($value = null)
    {
        $element = $this->getElement('ST_CURSISTA');
        $element->setValue($value);
    }

    public function setAno($options, $value = null)
    {
        $element = $this->getElement('NU_ANO');
        $element->addMultiOptions($options);
        $element->setValue($value);
    }

    public function setDtInicio($value = null)
    {
        $element = $this->getElement('DT_INICIO');
        $element->setValue($value);
    }

    public function setDtFim($value = null)
    {
        $element = $this->getElement('DT_FIM');
        $element->setValue($value);
    }
}
