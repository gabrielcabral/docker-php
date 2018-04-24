<?php

class ParametroCertificado_Form extends Fnde_Form
{

    public function __construct()
    {

        $this->setAttrib("class", "labelLongo");

        $id = $this->createElement('hidden', 'NU_SEQ_PARAM_CERT');

        $dtInicio = $this->createElement('text', 'DT_INICIO', array("label" => "Data Início: ", 'class' => 'date dp-applied'))
            ->setRequired(true);

        $dtFim = $this->createElement('text', 'DT_FIM', array("label" => "Data Fim: ", 'class' => 'date dp-applied'))
            ->setRequired(false);

        $noDirigente = $this->createElement('text', 'NO_SECRETARIO', array("label" => "Nome do Dirigente: ", 'maxlength' => '100'))
            ->setRequired(true);

        $dsCargo = $this->createElement('text', 'NO_CARGO', array("label" => "Cargo: ", 'maxlength' => '100'))
            ->setRequired(true);

        $dsLocal = $this->createElement('text', 'NO_LOCAL_ATUACAO', array("label" => "Local de Atuação: ", 'maxlength' => '100'))
            ->setRequired(true);

        $dsLogo = $this->createElement('file', 'NU_SEQ_LOGOMARCA_CASTOR', array("label" => "Logo: "))
            ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
            ->setRequired(true);

        $btConfirmar = $this->createElement('button', 'confirmar',
            array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar")
        );

        $btnGerarCertificadoTeste = $this->createElement('button', 'Gerar Certificado Teste',
            array("label" => "Gerar Certificado Teste", "class" => "btnConfirmar", "title" => "Gerar Certificado Teste")
        )->setValue('Gerar Certificado Teste');

        $btCancelar = $this->createElement('button', 'cancelar',
            array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar")
        );

        $this->addElements(array($id, $dtInicio, $dtFim, $noDirigente, $dsCargo, $dsLocal, $dsLogo, $btConfirmar, $btnGerarCertificadoTeste, $btCancelar));

        $this->addDisplayGroup(
            array('DT_INICIO', 'DT_FIM', 'NO_SECRETARIO', 'NO_CARGO', 'NO_LOCAL_ATUACAO', 'NU_SEQ_LOGOMARCA_CASTOR'), 'dadoscurso',
            array("legend" => "Dados"));

        $obDisplayGroup = $this->addDisplayGroup(array('GerarCertificadoTeste', 'confirmar', 'cancelar'), 'botoes',
            array('class' => 'agrupador barraBtsAcoes'));

        parent::__construct();

        $obDisplayGroup->botoes->addDecorator('HtmlTag',
            array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
        $obDisplayGroup->botoes->removeDecorator('fieldset');

    }

    public function isValid($data)
    {
        if(!isset($data['validacao_simples']) || $data['validacao_simples'] == false) {
            list($dayI, $monthI, $yearI) = sscanf($data['DT_INICIO'], '%02d/%02d/%04d');
            $dayI = str_pad($dayI, 2, '0', STR_PAD_LEFT);
            $monthI = str_pad($monthI, 2, '0', STR_PAD_LEFT);
            $data['DT_INICIO'] = "$yearI-$monthI-$dayI";

            if (!empty($data['DT_FIM'])) {
                list($dayF, $monthF, $yearF) = sscanf($data['DT_FIM'], '%02d/%02d/%04d');
                $dayF = str_pad($dayF, 2, '0', STR_PAD_LEFT);
                $monthF = str_pad($monthF, 2, '0', STR_PAD_LEFT);
                $data['DT_FIM'] = "$yearF-$monthF-$dayF";

                $this->DT_FIM->addValidator(new Fnde_Sice_Validate_GreaterOrIqualThan($data['DT_INICIO']), true);
                $this->DT_INICIO->addValidator(new Fnde_Sice_Validate_LessOrIqualThan($data['DT_FIM']), true);
            }

            $valid = parent::isValid($data);

            if ($valid) {
                $model = new Fnde_Sice_Business_ParametroCertificado();
                $conflito = $model->conflitoData($data['DT_INICIO'], $data['DT_FIM'], $data['NU_SEQ_PARAM_CERT']);

                if ($conflito) {
                    // adiciona erro nas datas
                    $msgConflitoData = 'O registro não pode ser salvo, pois geraria um conflito de datas.';

                    $this->DT_FIM->addError($msgConflitoData);
                    $this->DT_INICIO->addError($msgConflitoData);

                    $valid = false;
                }
            }
            return $valid;
        }
        return parent::isValid($data);
    }

    public function populate($data){

        parent::populate($data);
    }


}
