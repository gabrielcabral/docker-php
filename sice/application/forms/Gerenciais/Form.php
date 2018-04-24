<?php

/**
 * Form de Filtro Gerenciais
 *
 */
class Gerenciais_Form extends Fnde_Form
{
    public function __construct()
    {
        //form
        $this->setAttribs(array('id' => 'form', 'name' => 'form', 'target' => '_blank'))
            ->setAction($this->getView()->baseUrl() . '/index.php/relatorios/gerenciais/excel');

        //id relatorio
        $id_relatorio = $this->createElement('hidden', 'id_relatorio');
        $this->addElement($id_relatorio);

        //filtro de ano
        $nu_ano = $this->createElement('select', 'NU_ANO', array("label" => "Ano: "));
        $nu_ano->setRequired(true);
        $this->addElement($nu_ano);

        //botao submit
        $btExportar = $this->createElement('submit', 'exportar', array("label" => "Exportar", "value" => "Exportar", "class" => "btnExportar", "title" => "Exportar"));
        $this->addElement($btExportar);
    }

    public function display($arrDisplay)
    {
        //construir formulário
        parent::__construct();

        //merge com filtro basico (NU_ANO)
        $arrDisplay = array_merge(array('NU_ANO'),$arrDisplay);

        //display filtros
        $obDisplayGroupFiltros = $this->addDisplayGroup($arrDisplay, 'filtro', array("legend" => "Filtro"));

        //display botoes
        $obDisplayGroupBotoes = $this->addDisplayGroup(array('exportar'), 'botoes', array('class' => 'agrupador barraBtsAcoes'));

        $obDisplayGroupBotoes->botoes->addDecorator('HtmlTag', array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
        $obDisplayGroupBotoes->botoes->removeDecorator('fieldset');
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

    public function addFiltroUF($content)
    {
        $id = 'filtroUF';
        $filtro = new Html($id);
        $filtro->setLabel('UF:')->setRequired(true)->setValue($this->retornaHtmlUf($content));

        $this->addElement($filtro);

        return $id;
    }
    
    public function retornaHtmlUf($arDados){
        $cont = 0;

        $html = "
                <table>
                    <tr>
                        <td>
                            <input type='checkbox' id='filter_todos' name='filter_todos' value='TODOS' /> Todos
                        </td>
                    </tr>
                    <tr>
                        <td width='60px'>
                        ";

        foreach ($arDados as $chave => $valor) {
            $cont++;

            $html .= "<input type='checkbox' name='SG_UF[]' value='$chave' /> $valor<br/>";

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
