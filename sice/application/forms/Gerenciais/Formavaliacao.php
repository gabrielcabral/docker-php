<?php

/**
 * Form de Filtro Gerenciais
 *
 */
class Gerenciais_Formavaliacao extends Fnde_Form
{
    public function __construct()
    {
        //form
        $this->setAttribs(array('id' => 'form', 'name' => 'form'))
            ->setAction($this->getView()->baseUrl() . '/index.php/relatorios/gerenciais/avaliacaoinstitucional');

        //id relatorio
        $id_relatorio = $this->createElement('hidden', 'id_relatorio');

        $curso = $this->createElement('select', 'NU_SEQ_CURSO', array("label" => "Curso: "));
        $curso->addMultiOption(null, "Selecione");

        $numeroturma = $this->createElement('text', 'NU_SEQ_TURMA', array("label" => "Número turma: ", "maxlength" => "7", "class" => "inteiro"));

        $dtInicio = $this->createElement('text', 'DT_INICIO', array(
            "label" => "Turmas finalizadas<br>Data início: ",
            "class" => "date dp-applied"
        ));

        $dtFim = $this->createElement('text', 'DT_FIM', array(
            "label" => "Data fim: ",
            "class" => "date dp-applied"
        ));

        //filtro de ano
        $nu_ano = $this->createElement('select', 'NU_ANO', array("label" => "Ano: "));

        $municipio = $this->createElement('select', 'CO_MUNICIPIO', array("label" => "Município: "));
        $municipio->addMultiOption(null, 'Selecione');

        $mesorregiao = $this->createElement('select', 'CO_MESORREGIAO', array("label" => "Mesorregião: "));
        $mesorregiao->addMultiOption(null, 'Selecione');

        //usuario logado
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();

        $obBusinessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
        if(in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $usuarioLogado->credentials)
            || in_array(Fnde_Sice_Business_Componentes::TUTOR, $usuarioLogado->credentials)){

            $businessUsuario = new Fnde_Sice_Business_Usuario();
            $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);
            $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);

            $municipioTutor = $obBusinessMesoregiao->getMunicipioById($arUsuario["CO_MUNICIPIO_PERFIL"]);
            $municipio->addMultiOption($municipioTutor[0]["CO_MUNICIPIO_IBGE"], $municipioTutor[0]["NO_MUNICIPIO"]);
            $municipio->setValue($municipioTutor[0]["CO_MUNICIPIO_IBGE"]);

            $dadosMesoregiao = $obBusinessMesoregiao->getMesoRegiaoById($arUsuario["CO_MESORREGIAO"]);
            $mesorregiao->addMultiOption($dadosMesoregiao["CO_MESO_REGIAO"], $dadosMesoregiao["NO_MESO_REGIAO"]);
            $mesorregiao->setValue($dadosMesoregiao["CO_MESO_REGIAO"]);
        }

        $rededeensino = $this->createElement('select', 'CO_REDE_ENSINO', array("label" => "Rede de Ensino: "));
        $redeEnsino = $obBusinessMesoregiao->getRedeDeEnsino();
        $rededeensino->addMultiOption ('', 'Selecione');
        foreach ( $redeEnsino as $row ) {
            $rededeensino->addMultiOption ($row["CO_ESFERA_ADM"], $row["NO_ESFERA_ADM"]);
        }

        $this->addElements(array($id_relatorio, $curso, $numeroturma, $dtInicio, $dtFim, $nu_ano , $municipio, $mesorregiao, $rededeensino));

        //botao submit
        $btConsultar = $this->createElement('submit', 'consultar', array("label" => "Consultar", "value" => "Consultar", "class" => "btnConsultar", "title" => "Consultar"));
        $btExportar = $this->createElement('submit', 'exportar', array("label" => "Exportar", "value" => "Exportar", "class" => "btnExportar", "title" => "Exportar"));

        $this->addElements(array($btConsultar, $btExportar));
    }

    public function display($arrDisplay)
    {
        //construir formulário
        parent::__construct();

        //merge com filtro basico (NU_ANO)
        $arrDisplay = array_merge(array('NU_SEQ_CURSO', 'NU_SEQ_TURMA', 'NU_ANO', 'DT_INICIO', 'DT_FIM', 'filtroUF', 'CO_MESORREGIAO', 'CO_MUNICIPIO', 'CO_REDE_ENSINO'),$arrDisplay);

        //display filtros
        $obDisplayGroupFiltros = $this->addDisplayGroup($arrDisplay, 'filtro', array("legend" => "Filtro"));

        //display botoes
        $obDisplayGroupBotoes = $this->addDisplayGroup(array('exportar','consultar'), 'botoes', array('class' => 'agrupador barraBtsAcoes'));

        $obDisplayGroupBotoes->botoes->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
        $obDisplayGroupBotoes->botoes->removeDecorator('fieldset');
    }

    public function setCurso($options, $value = null)
    {
        $element = $this->getElement('NU_SEQ_CURSO');
        $element->setMultiOptions($options);
        $element->setValue($value);
    }

    public function setIdRelatorio($id)
    {
        $element = $this->getElement('id_relatorio');
        $element->setValue($id);
    }

    public function setAno($options)
    {
        $element = $this->getElement('NU_ANO');
        $element->setMultiOptions($options);
    }

    public function addFiltroUF($content, $rsUf = null)
    {
        $id = 'filtroUF';
        $filtro = new Html($id);
        $filtro->setLabel('UF:')->setValue($this->retornaHtmlUf($content, $rsUf));

        $this->addElement($filtro);

        return $id;
    }
    
    public function retornaHtmlUf($arDados, $rsUf = null){
        $cont = 0;

        $disabled = (count($arDados) == 1) ? 'disabled = \'disabled\'' : "";

        $html = "
                <table>
                    <tr>
                        <td>
                            <input type='checkbox' id='filter_todos' name='filter_todos' value='TODOS' $disabled/> Todos
                        </td>
                    </tr>
                    <tr>
                        <td width='60px'>
                        ";

        foreach ($arDados as $chave => $valor) {

            $checked = (in_array($chave, $rsUf) || count($arDados) == 1) ? "checked" : "";

            $cont++;

            $html .= "<input type='checkbox' class='UFS' name='SG_UF[]' value='$chave' $disabled $checked/> $valor<br/>";

            if ($cont == 9) {
                $html .= "</td><td width='60px'>";
                $cont = 0;
            }
        }

        $html .= "
                        </td>
                    </tr>
                </table>
        ";

        return $html;
    }
}
