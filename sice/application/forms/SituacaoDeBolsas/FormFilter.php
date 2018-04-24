<?php

class SituacaoDeBolsas_FormFilter extends Fnde_Form
{
    /*
     * Ano Ref. 	Mês Ref. 	Região 	UF 	Mesorregião 	Município 	Nome do Curso 	Perfil 	CPF 	Nome
     */
    public function __construct()
    {
        $this->setAttribs(array(
            'id' => 'situacaodebolsasform'
        ));

        $curso = $this->createElement('select', 'NU_SEQ_TIPO_CURSO', array("label" => "Nome do Curso: "));

        $regiao = $this->createElement('select', 'SG_REGIAO', array("label" => "Região: "));

        $uf = $this->createElement('select', 'UF_TURMA', array("label" => "UF: "));

        $anoReferencia = $this->createElement("text", "NU_ANO", array(
            "label" => "Ano Ref.: ",
            "style" => "width:200px;"
        ));
        $anoReferencia->setRequired(true);

        $mesReferencia = $this->createElement("text", "NU_MES", array(
            "label" => "Mês Ref.: ",
            "style" => "width:200px;"
        ));

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

        $perfil = $this->createElement('select', 'NU_SEQ_TIPO_PERFIL', array("label" => "Perfil: ", "style" => "width:200px;"));
        $perfil->addMultiOption(null, 'Selecione');

        $cpf = $this->createElement('text', 'NU_CPF', array("label" => "CPF: ", "class" => "cpf"));

        $nomeCursista = $this->createElement('text', 'NO_CURSISTA', array("label" => "Nome: "));

        $this->addElements(array(
            $anoReferencia, $mesReferencia, $mesorregiao,
            $municipio, $curso, $regiao, $perfil,
            $cpf, $nomeCursista, $uf
        ));

        $this->addDisplayGroup(array(
            'NU_ANO', 'NU_MES', 'SG_REGIAO', 'UF_TURMA', 'CO_MESORREGIAO', 'CO_MUNICIPIO',
            'NU_SEQ_TIPO_CURSO', 'NU_SEQ_TIPO_PERFIL', 'NU_CPF', 'NO_CURSISTA'
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

    public function setCpf($value = null)
    {
        $element = $this->getElement('NU_CPF');
        $element->setValue($value);
    }

    public function setAnoReferencia($value = null)
    {
        $element = $this->getElement('NU_ANO');
        $element->setValue($value);
    }

    public function setMesReferencia($value = null)
    {
        $element = $this->getElement('NU_MES');
        $element->setValue($value);
    }

    public function setNomeCursista($value = null)
    {
        $element = $this->getElement('NO_CURSISTA');
        $element->setValue($value);
    }

    public function setRegiao( $results, $regiaoAtual )
    {
        $regiao = $this->getElement('SG_REGIAO');
        $regiao->setMultiOptions(array(null => "Selecione"));

        for ( $i = 0; $i < count($results); $i++ ) {
            $regiao->addMultiOption($results[$i]['SG_REGIAO'], $results[$i]['NO_REGIAO']);
        }

        $regiao->setValue($regiaoAtual);
    }

    public function setTipoCurso( $rsTipoCurso ) {
        $this->getElement('NU_SEQ_TIPO_CURSO')->addMultiOptions($rsTipoCurso);
    }

    public function setPerfil( $arPerfil ) {
        $this->getElement('NU_SEQ_TIPO_PERFIL')->addMultiOptions($arPerfil);
    }
}
