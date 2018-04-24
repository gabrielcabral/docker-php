<?php

/**
 * Form de Filtro Curso
 *
 * @author vinicius.cancado
 * @since 18/04/2012
 */
class ParametroCertificado_FormFilter extends Fnde_Form
{
    /**
     * Construtor do formulário de pesquisa da tela de Curso.
     *
     * @return object - Objeto de formulário
     *
     * @author vinicius.cancado
     * @since 18/04/2012
     */
    public function __construct()
    {

        $this->setAttrib("class", "labelLongo");

        $ano = $this->createElement("select", "ANO", array("label" => "Ano: ", "style" => "width:200px;"))
            ->addMultiOption(null, "Selecione");
        $anoAtual = date('Y');
        for ($i = 2010; $i <= $anoAtual; $i++) {
            $ano->addMultiOption($i, $i);
        }

        $noDirigente = $this->createElement('text', 'NO_SECRETARIO', array("label" => "Nome do Dirigente: "));

        $dsCargo = $this->createElement('text', 'NO_CARGO', array("label" => "Cargo: ", 'maxlength' => '100'));

        $dsLocal = $this->createElement('text', 'NO_LOCAL_ATUACAO', array("label" => "Local de Atuação: ", 'maxlength' => '100'));

        $btConfirmar = $this->createElement('submit', 'confirmar',
            array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar")
        );

        $btCancelar = $this->createElement('button', 'cancelar',
            array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar")
        );

        $this->addElements(array($ano, $noDirigente, $dsCargo, $dsCargo, $dsLocal, $btConfirmar, $btCancelar));

        $this->addDisplayGroup(
            array('ANO', 'NO_SECRETARIO', 'NO_CARGO', 'NO_LOCAL_ATUACAO'), 'dadoscurso',
            array("legend" => "Dados"));

        $obDisplayGroup = $this->addDisplayGroup(array('GerarCertificadoTeste', 'confirmar', 'cancelar'), 'botoes',
            array('class' => 'agrupador barraBtsAcoes'));

        parent::__construct();

        $obDisplayGroup->botoes->addDecorator('HtmlTag',
            array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
        $obDisplayGroup->botoes->removeDecorator('fieldset');
    }
}
