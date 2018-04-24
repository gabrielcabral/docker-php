<?php

/**
 * Business do Atividade
 *
 * @author diego.matos
 * @since 12/04/2012
 */
class Fnde_Sice_Business_Componentes
{
    /**
     * Retorna Array de dados da tabela informada
     *
     * @author Orion Teles de Mesquita <orion.mesquita@fnde.gov.br>
     * @since 08/04/2010
     */
    //Perfis dos usuários no SEGWEB
    const COORDENADOR_NACIONAL_ADMINISTRADOR = "sice_coord_nacional_adm";
    const COORDENADOR_NACIONAL_EQUIPE = "sice_coord_nacional_equipe";
    const COORDENADOR_NACIONAL_GESTOR = "sice_coord_nacional_gestor";
    const COORDENADOR_EXECUTIVO_ESTADUAL = "sice_coord_executivo_estadual";
    const COORDENADOR_ESTADUAL = "sice_coord_estadual";
    const ARTICULADOR = "sice_articulador";
    const TUTOR = "sice_tutor";
    const CURSISTA = "sice_cursista";
    const MSG_NAO_PERMITIR_ACESSO = "Para acesso a esta funcionalidade é necessário estar cadastrado no SICE. Entre em contado com seu superior!";

    //Alterar valor de moeda valido para Americano
    //Quando estiver no servidor dever estar DE = "," PARA = "."
    const REPLACE_DE = ",";
    const REPLACE_PARA = ".";

    //Maiusculo e minusculo das iniciais do mês da data
    const MINUSCULO = 0;
    const INI_MAIUSCULO = 1;
    const MAIUSCULO = 2;

    /**
     * Função para retornar os valores de uma tabela para preenchimento de um combo.
     * @param string $stTable
     * @param array $arCampos
     * @param array $arExtra
     */
    public static function getAllByTable($stTable, $arCampos, $arExtra = array())
    {
        $stWhere = isset($arExtra['stWhere']) ? $arExtra['stWhere'] : null;
        $stOrdem = isset($arExtra['stOrdem']) ? $arExtra['stOrdem'] : $arCampos[1];

        $stModel = "Fnde_Sice_Model_{$stTable}";
        $obProjeto = new $stModel;
        $obResult = $obProjeto->fetchAll($stWhere, $stOrdem)->toArray();

        $arResultados = array();

        foreach ($obResult as $arDados) {
            $arResultados[$arDados[$arCampos[0]]] = $arDados[$arCampos[1]];
        }
        return $arResultados;
    }

    /**
     *
     * @param unknown_type $form
     * @param unknown_type $arOptions
     * @param unknown_type $arOptionsForm
     * @param unknown_type $valueSelected
     * @param unknown_type $boSelecione
     */
    public static function selectGenericForm($form, $arOptions = array(), $arOptionsForm = array(),
                                             $valueSelected = '', $boSelecione = true)
    {

        $select = new Zend_Form_Element_Select($arOptionsForm['name'], $arOptionsForm);
        $select->setLabel($arOptionsForm['label'])->setRequired($arOptionsForm['required']);
        if ($boSelecione) {
            $select->addMultiOption("", "Selecione");
        }

        if (!empty($arOptions)) {
            foreach ($arOptions as $value => $descricao) {
                $select->addMultiOption($value, $descricao);
            }
        }

        //monta combo com valor selecionado
        $select->setValue($valueSelected);

        $select->setRegisterInArrayValidator(false);

        //Adicionado Componentes no formulário
        $form->addElement($select);
    }

    /**
     * Obtem um array de dados
     *
     * @author diego.matos
     * @since 10/04/2012
     */
    public function getArDados($id, $boArray = true, $obModelo)
    {

        $obModelo->getAdapter()->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");

        $aDados = $obModelo->find($id)->current();
        if ($aDados) {
            return $boArray ? $aDados->toArray() : $aDados;
        }
        return $boArray ? $obModelo->createRow()->toArray() : $obModelo->createRow();
    }

    /**
     * Obtem o código do perfil do usuário pelo CPF.
     *
     * @param string cpf
     * @author poliane.silva
     * @since 21/05/2012
     */
    public function getPerfilUsuarioByCpf($cpf)
    {

        $query = "SELECT NU_SEQ_TIPO_PERFIL FROM SICE_FNDE.s_usuario WHERE nu_cpf = '$cpf'";

        $obModelo = new Fnde_Sice_Model_Usuario();
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetch();
        return $result['NU_SEQ_TIPO_PERFIL'];
    }

    /**
     * Obtém o código do perfil do usuário pela string do perfil
     * @param STRING $perfil
     */
    public function getCodPerfilByTipoPerfil($perfil)
    {
        switch ($perfil) {
            case 'sice_coordenador_nacional_administrador':
                return 1;
                break;
            case 'sice_coordenador_nacional_equipe':
                return 2;
                break;
            case 'sice_coordenador_nacional_gestor':
                return 3;
                break;
            case 'sice_coordenador_estadual':
                return 4;
                break;
            case 'sice_articulador':
                return 5;
                break;
            case 'sice_tutor':
                return 6;
                break;
            case 'sice_cursista':
                return 7;
                break;
        }
    }

    /**
     * Monta o menu de contexto das telas de manter usuários
     * @param array $perfil
     * @param string $urlFiltrar
     * @param string $urlCadastrar
     */
    public static function montaMenuContextoUsuario($perfilUsuario, $urlFiltrar, $urlCadastrar)
    {
        $menu = null;
        if (!in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR, $perfilUsuario)
            && !in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario)
            && !in_array(Fnde_Sice_Business_Componentes::ARTICULADOR, $perfilUsuario)
            && !in_array(Fnde_Sice_Business_Componentes::CURSISTA, $perfilUsuario)
        ) {
            $menu = array($urlFiltrar => 'filtrar', $urlCadastrar => 'cadastrar');
        } else {
            $menu = array($urlFiltrar => 'filtrar');
        }
        return $menu;
    }

    /**
     * Monta o menu de contexto das telas de manter turma
     * @param array $perfil
     * @param string $urlFiltrar
     * @param string $urlCadastrar
     */
    public static function montaMenuContextoTurma($perfilUsuario, $urlFiltrar, $urlCadastrar)
    {
        $menu = null;
        if (
            in_array(Fnde_Sice_Business_Componentes::TUTOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE, $perfilUsuario) ||
            in_array(Fnde_Sice_Business_Componentes::COORDENADOR_EXECUTIVO_ESTADUAL, $perfilUsuario)
        ) {
            $menu = array($urlFiltrar => 'filtrar', $urlCadastrar => 'cadastrar');
        } else {
            $menu = array($urlFiltrar => 'filtrar');
        }
        return $menu;
    }

    /**
     * Gera uma string com os campos com problemas em um formulário.
     * @param form $form
     */
    public static function listarCamposComErros($form)
    {
        $camposMensagem = "";
        $cont = 1;
        foreach ($form->getElements() as $elemento) {
            if ($elemento->getMessages()) {
                $camposMensagem .= $cont . ". " . str_replace(":", "", $elemento->getLabel()) . "<br/>";
                $cont++;
            }
        }
        return $camposMensagem;
    }


    /**
     * Escreve o código html do grid de Critérios sugeridos para cálculo da nota.
     */
    public static function retornaHtmlCriteriosSugeridos()
    {
        $html = "<br /><br /><div>";

        $html .= "<h4>Critérios sugeridos para cálculo da nota</h4>";
        $html .= "	<br />";
        $html .= "<ol>";
        $html .= "	<li>Participação nos encontros presenciais";
        $html .= "		<ol type='a'>";
        $html .= "			<li>Dedicação e interesse pelo curso</li>";
        $html .= "			<li>Freqüência e participação nas atividades propostas pelo tutor</li>";
        $html .= "			<li>Independência e autonomia</li>";
        $html .= "			<li>Habilidade na Interação com colegas e tutor</li>";
        $html .= "			<li>Perspicácia, criatividade e capacidade de assimilação de conteúdo</li>";
        $html .= "		</ol>";
        $html .= "	</li>";
        $html .= "	<br />";
        $html .= "	<li>Participação nos encontros presenciais - avaliação de cada cursista pelo tutor";
        $html .= "		<ol type='a'>";
        $html .= "			<li>Dedicação e interesse pelo curso</li>";
        $html .= "			<li>Frequência e participação nas atividades propostas pelo tutor</li>";
        $html .= "			<li>Habilidade para o estudo com independência e autonomia</li>";
        $html .= "			<li>Habilidade para interagir com colegas/tutor</li>";
        $html .= "			<li>Perspicácia, criatividade e capacidade de assimilação do conteúdo</li>";
        $html .= "		</ol>";
        $html .= "	</li>";
        $html .= "	<br />";
        $html .= "	<li>Desenvolvimento dos estudos e das atividades à distância, realizados pelo cursista na plataforma moodle";
        $html .= "		<ol type='a'>";
        $html .= "			<li>Coerência na apresentação dos textos</li>";
        $html .= "			<li>Conclusão das questões</li>";
        $html .= "			<li>Desempenho nas questões assertivas</li>";
        $html .= "			<li>Periodicidade no acesso online</li>";
        $html .= "			<li>Participação nos fóruns</li>";
        $html .= "		</ol>";
        $html .= "	</li>";
        $html .= "	<br />";
        $html .= "	<li>Atividade final";
        $html .= "		<ol type='a'>";
        $html .= "			<li>Clareza e objetividade no trabalho realizado</li>";
        $html .= "			<li>Identifica problemas e apresenta possíveis soluções</li>";
        $html .= "			<li>Pontualidade na entrega dos trabalhos</li>";
        $html .= "			<li>Criatividade</li>";
        $html .= "		</ol>";
        $html .= "	</li>";
        $html .= "</ol>";

        $html .= "</div><br />";

        return $html;
    }

    /**
     * Valida pedido de autorização para abrir uma nova turma
     * @param unknown_type $arParam
     * @throws Exception
     */
    public function validaAutorizacao($arParam)
    {
        $obQtTurma = new Fnde_Sice_Business_QuantidadeTurma();
        $arQtTurmas = $obQtTurma->getQuantidadeTurmas($arParam['NU_SEQ_TURMA']);

        if ($arParam['NU_ALUNOS_MATRICULADOS'] < $arParam['NU_MIN_ALUNOS']) {

            throw new Exception("Quantidade mínima de cursista para a turma não foi atingida.");

        } else if ($arQtTurmas && $arQtTurmas['QTD_SOLICITADO'] >= $arQtTurmas['QTD_CONFIGURACAO']) {

            throw new Exception(
                "A quantidade máxima de turmas para esta mesorregião já foi atingida. Favor contactar a Coordenação Estadual.");

        }
        return true;
    }

    /**
     * Valida se o CPF está cadastrado no SICE para permitir o acesso
     */
    public static function permitirAcesso()
    {
        $businessUsuario = new Fnde_Sice_Business_Usuario();
        $usuarioLogado = Zend_Auth::getInstance()->getIdentity();

        //$perfilUsuario = $usuarioLogado->credentials;
        $cpfUsuarioLogado = preg_replace("/[^0-9]/", "", $usuarioLogado->cpf);

        if ($cpfUsuarioLogado) {
            $arUsuario = $businessUsuario->getUsuarioByCpf($cpfUsuarioLogado);
            if ($arUsuario) {
                return true;
            }
        }
        return false;
    }

    /**
     * Validação do arquivo de configuração XML
     * @param  $caminhoArquivo
     * @param  $nomeArquivo
     * @throws Exception
     */
    public static function validaXmlConfiguracao($caminhoArquivo, $nomeArquivo)
    {
        $bc = new Fnde_Sice_Business_Componentes();
        $arquivoXsd = $caminhoArquivo . "/ArquivoConfiguracao.xsd";
        $arquivoXml = $caminhoArquivo . "/" . $nomeArquivo;

        //verificar erros o corridos durante a verificação do XML
        //libxml_use_internal_errors(true);

        $xml = new DOMDocument();
        $xml->load($arquivoXml);

        if (!$xml->schemaValidate($arquivoXsd)) {
            throw new Exception($bc->getMsgXmlConfArquivoInvalido($nomeArquivo));
        }

        //Exibir os erro ocorridos
        //$errors = libxml_get_errors();
        //var_dump($errors);
        //die();

    }

    /**
     * Validação do campos vazios do XML
     * @param  $xml
     * @param  $nomeArquivo
     * @throws Exception
     */
    public static function validaXmlCampoVazio($valor, $campo)
    {
        $bc = new Fnde_Sice_Business_Componentes();
        $sValor = ( string )$valor;

        if (!isset($sValor) || trim($sValor) == "") {
            throw new Exception($bc->getMsgXmlConfCampoInvalido($campo));
        }

    }

    /**
     * Mensagem de arquivo inválido da confiuração XML
     * @param  $nomeArquivo
     */
    public function getMsgXmlConfArquivoInvalido($nomeArquivo)
    {
        return "O arquivo de configuração com formato $nomeArquivo, está com erro e/ou corrompido.";
    }

    /**
     * Mensagem de campos vazios da configuração XML
     * @param  $campo
     */
    public function getMsgXmlConfCampoInvalido($campo)
    {
        return "O valor do campo $campo não encontrado no arquivo importado e/ou fora do padrão estabelecido.";
    }

    /**
     * Ao chamar a opção em que exibe a tela de gerar arquivo moodle verifica a se a turma selecionad é ativa
     * @param  $arParam
     * @throws Exception
     */
    public function validarGerarArquivoMoodle($arParam)
    {
        $obTurma = new Fnde_Sice_Business_Turma();
        $arTurma = $obTurma->getTurmaPorId($arParam['NU_SEQ_TURMA']);

        if ($arTurma['ST_TURMA'] != 4) {
            throw new Exception(
                "A turma selecionada com a situação {$arTurma['DS_ST_TURMA']} não pode ser executada a ação Gerar arquivo moodle.");
        }
    }

    /**
     * Função para verificar a quantidade de configurações ativas no sistema.
     */
    public static function validaConfigAtiva()
    {
        //verifica a quantidade de configurações ativas no sistema
        $query = "SELECT COUNT(NU_SEQ_CONFIGURACAO) AS QTD FROM SICE_FNDE.S_CONFIGURACAO WHERE ST_CONFIGURACAO = 'A'";

        $obModelo = new Fnde_Sice_Model_Configuracao();
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetch();
        return $result['QTD'];
    }

    /**
     * Formata o CPF informado.
     * @param string $cpf CPF não formatado.
     * @return string CPF formatado.
     */
    public static function formataCpf($cpf)
    {

        $parteUm = substr($cpf, 0, 3);
        $parteDois = substr($cpf, 3, 3);
        $parteTres = substr($cpf, 6, 3);
        $parteQuatro = substr($cpf, 9, 2);

        return "$parteUm.$parteDois.$parteTres-$parteQuatro";
    }

    /**
     * Função para obter a descrição da situação da bolsa pelo código
     *
     * @param int $cod
     */
    public static function nomeSituacaoBolsa($cod)
    {
        $situacaoBusiness = new Fnde_Sice_Business_SituacaoBolsa();
        $arSituacao = $situacaoBusiness->getSituacaoBolsaById($cod);
        return $arSituacao['DS_SITUACAO_BOLSA'];
    }

    /**
     * Converte uma data no formato brasileiro para americano (de dd/mm/aaaa para aaaa-mm-dd)
     * @param unknown_type $databr
     * @return string
     */
    public static function dataBRToEUA($databr)
    {
        if (!empty($databr)) {
            $pDt = explode('/', $databr);
            $dataSql = substr($pDt[2], 0, 4) . '-' . $pDt[1] . '-' . $pDt[0];
            return $dataSql;
        }
    }

    /**
     * Converte uma data no formato americano para brasileiro (de aaaa-mm-dd para dd/mm/aaaa)
     * @param string $datasql
     * @return string
     */
    public static function dataEUAToBR($datasql)
    {
        if (!empty($datasql)) {
            $pDt = explode('-', $datasql);
            $dataBr = $pDt[2] . '/' . $pDt[1] . '/' . $pDt[0];
            return $dataBr;
        }
    }

    /**
     *
     * @param array $arDados
     */
    public static function retornaHtmlPdf($arDados, $coordEstadual)
    {

        //Montagem do cabeçalho e rodapé
        $html = "<page backtop='25mm' backbottom='20mm' backleft='10mm' backright='20mm'>
					<page_header>
						<div align='center'><img src='img/pdf_sgb_brasao.jpg' height='89' width='92'></div>
					</page_header>
					<page_footer>
						<div align='center' height='89'><img src='img/pdf_sgb_educacao.jpg' height='42' width='220'></div>
					</page_footer>";

        //Texto do documento
        $html .= "<p style='text-align: justify'>
					A Coordenação Estadual do Programa Nacional de Fortalecimento dos Conselhos Escolares do(de) " . $coordEstadual['NO_UF']
            . ",
					por meio do(a) Coordenador(a) Executivo(a) Estadual " . $coordEstadual['NO_USUARIO']
            . ",
					titular do CPF {$coordEstadual['NU_CPF']},
					certifica a efetiva ocorrência do Curso de Formação para Conselheiros Escolares,
					realizado pelos(as) Tutores(as) e supervisionado pelos(as) Articuladores(as) identificados neste documento,
					solicita à Coordenação Nacional do Programa Nacional de Fortalecimento dos Conselhos Escolares a
					homologação das bolsas - de acordo com a lei federal nº 11.273/2006 - 
					uma vez que os respectivos agentes fizeram jus ao seu recebimento.
				</p>";

        //Cabeçalho da tabela
        $html .= "<p style='text-align:justify; font-size:12px;' >
					<table border='0.5' cellpadding='0' cellspacing='0' width='100'>
						<tr>
							<td align='center'  >
								<b>Perfil <br /> avaliado</b>
							</td>
							<td align='center'  >
								<b>Nome <br /> avaliado</b>
							</td>
							<td align='center' >
								<b>CPF <br /> avaliado</b>
							</td>
							<td align='center'  >
								<b>Perfil<br /> avaliador</b>
							</td>
							<td align='center'  >
								<b>Nome <br />avaliador</b>
							</td>
							<td align='center' >
								<b>CPF <br />avaliador</b>
							</td>
							<td align='center' >
								<b>Período de <br />vinculação</b>
							</td>
						</tr>";
        //Dados da tabela
        $qtdBolsas = count($arDados);

        for ($i = 0; $i < $qtdBolsas; $i++) {
            $html .= "		<tr>
				<td width='80'>
					{$arDados[$i]['DS_TIPO_PERFIL']}
				</td>
				<td width='110'>" . $arDados[$i]['NO_USUARIO']
                . "</td>
				<td >
					{$arDados[$i]['NU_CPF']}
				</td>
				<td width='80'>
					{$arDados[$i]['DS_TIPO_PERFIL_AVALIADOR']}
				</td>
				<td width='110' >" . $arDados[$i]['NO_USUARIO_AVALIADOR']
                . "</td>
				<td >
					{$arDados[$i]['NU_CPF_AVALIADOR']}
				</td>
				<td >
					{$arDados[$i]['MES_REFERENCIA']}/{$arDados[$i]['ANO_REFERENCIA']}
				</td>
				</tr>";
        }

        //Finalização da pagina do documento
        $html .= "</table>
			</p>
		</page>";

        return utf8_encode($html);
    }

    /**
     * Função para gerar o arquivo PDF com o relatório do envio de bolsas ao SGB.
     * @param array $arBolsas
     * @param array $coordEstadual
     */
    public static function gerarPdfSgb($arBolsas, $coordEstadual)
    {

        $content = Fnde_Sice_Business_Componentes::retornaHtmlPdf($arBolsas, $coordEstadual);

        require_once ZF_FNDE_ROOT . '/library/Html2pdf/html2pdf.class.php';

        try {

            $html2pdf = new HTML2PDF('P', 'A4', 'pt', true);

            $html2pdf->writeHTML($content);

            //Seta os Headers para abrir o pdf no browser
            header('Content-Type: application/pdf');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            ini_set('zlib.output_compression', '0');

            echo $html2pdf->Output("", true);
            die();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

    /**
     * Função para gerar o arquivo PDF
     */
    public static function gerarPdf($content)
    {
        require_once ZF_FNDE_ROOT . '/library/Html2pdf/html2pdf.class.php';

        try {
            $marges = array(15, 15, 15, 15);
            $html2pdf = new HTML2PDF('P', 'A4', 'pt', true, 'UTF-8', $marges);

            $html2pdf->writeHTML(utf8_encode($content));

            //Seta os Headers para abrir o pdf no browser
            header('Content-Type: application/pdf');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            ini_set('zlib.output_compression', '0');

            echo $html2pdf->Output("", true);
            die();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

    /**
     * Função para escrever o HTML necessário para a criação do arquivo PDF do Certificado de conclusão do curso.
     *
     * @param array $arDados
     */
    public static function retornaHtmlCertificadoPdf($arDados)
    {
        $dtLemaCertificado = preg_replace("/(\\d{2})\\/(\\d{2})\\/(\\d{4})$/", "$3$2$1", $arDados['DT_FIM']);
        $imgLemaCertificado = null;

        if (isset($arDados['LOGO_GOVERNO'])) {
            $imgLemaCertificado = "certificado_frente.gif";
        } else if ($dtLemaCertificado > 20160512) {
            $imgLemaCertificado = "certificado_frente_003.gif";
        } else if ($dtLemaCertificado >= 20150101 && $dtLemaCertificado <= 20160511) {
            $imgLemaCertificado = "certificado_frente_002.gif";
        } else {
            $imgLemaCertificado = "certificado_frente_001.gif";
        }

        //PÁGINA 1
        $html = "<page backtop='30mm' backbottom='7mm' backleft='130mm' backright='5mm' backimg='img/{$imgLemaCertificado}' backimgx='11' backimgy='8' backimgw='1100' >";
        //Texto do documento
        //font-family:Century Gothic
        $html .= "<p style='text-align: center; font-size:22px; color:#AA3333; line-height:145%;'>
					Certificamos que <br>
					<b>{$arDados['NO_USUARIO']}</b>, <br>
					concluiu o Curso de Formação de Conselheiros
					Escolares, <b>{$arDados['DS_NOME_MODULO']}</b>,
					do Programa Nacional de Fortalecimento dos
					Conselhos Escolares, na cidade de(o)
					<b>{$arDados['NO_MUNICIPIO']}/{$arDados['SG_UF']}</b>, no período de
					<b>{$arDados['DT_INICIO']}</b> à <b>{$arDados['DT_FIM']}</b>,
					com carga horária de <b>{$arDados['VL_CARGA_HORARIA']}</b> horas.
					<br>
					<br>
					Brasília/DF, "
            . Fnde_Sice_Business_Componentes::dataPorExtensoCertificado(date("d/m/Y"))
            . ".
					<br>
					<br>
					<span style='font-size:15px;'>{$arDados['COD_IDENTIFICADOR']}</span>
					<br>
                                        <span style='line-height:23px;'>
                                            <strong>{$arDados['NO_SECRETARIO']}</strong><br>
                                            {$arDados['NO_CARGO']}<br>
                                            {$arDados['NO_LOCAL_ATUACAO']}
                                        </span>
				</p>
				";
        if (isset($arDados['LOGO_GOVERNO'])) {
            $html .= "<div style='position:absolute; float:left; width:100%; text-align: center; bottom: 0'>

					<img  src='{$arDados['LOGO_GOVERNO']}'>

				</div>";
        }
        //Finalização da pagina do documento
        $html .= "</page>";
        return $html;

    }

    public static function retornapagina2($arDados)
    {
        //PÁGINA 2
        //$html .= "<page backtop='30mm' backbottom='7mm' backleft='80mm' backright='30mm' backimg='img/certificado_verso.gif' backimgx='11' backimgy='8' backimgw='1100'>";
        //Texto do documento
        $html = "<table border='1' >
			<tr>
			    <td width='170px'> </td>
				<td height='480px' width='500px'>
				    <p style='text-align: left;'>
						<b>Programa Nacional de Fortalecimento dos Conselhos Escolares</b><br/>
						<br/>
						Curso: <b>{$arDados['DS_NOME_CURSO']}</b><br/>
						<br/>
						Módulo: <b>{$arDados['DS_NOME_MODULO']}</b><br/>
						Carga Horária: <b>{$arDados['VL_CARGA_HORARIA']} horas</b><br/>
						Nome do Tutor: <b>{$arDados['NO_USUARIO_TUTOR']}</b><br/>
						Conteúdo Programático:<br/>
						<div style='width:100%; margin-left:40px'>
						{$arDados['DS_CONTEUDO_PROGRAMATICO']}
						</div>
						<br/>
						<br/>
						Resolução/CD/FNDE nº 55, de 27 de dezembro de 2012
					</p>
				</td>
				
			</tr>
			<tr>
				<td colspan='2'>Para verificar a autenticidade deste documento, consulte em https://www.fnde.gov.br/autenticidade/index.php/, digitando o código de assinatura apresentado neste certificado.</td>
			</tr>	
	    </table>
		";
        //Finalização da pagina do documento com rodapé
        //	$html .= "</page>";

        return $html;
    }


    /**
     * Função para gravar o arquivo PDF certificado de conclusão do curso.
     *
     * @param array $arDados
     * @param int $idUsuarioLogado
     * @throws HTML2PDF_exception
     */
    public static function salvarPdfCertificado($arDados, $idUsuarioLogado)
    {


        $content = Fnde_Sice_Business_Componentes::retornaHtmlCertificadoPdf($arDados);
        $content2 = Fnde_Sice_Business_Componentes::retornapagina2($arDados);
        $pagina = "<page backtop='30mm' backbottom='7mm' backleft='20mm' backright='0mm' backimg='img/certificado_verso.gif' backimgx='11' backimgy='8' backimgw='1100'></page>";

        require_once "Fnde/Sice/Html2pdf/Html2pdf.php";

        try {
            $html2pdf = new Fnde_Sice_Html2pdf_Html2pdf('L', 'A4', 'pt', true);
            $html2pdf->writeHTML(utf8_encode($content));
            $html2pdf->WriteHTML(utf8_encode($pagina));
            $html2pdf->rotateText(180, 150, 111, utf8_encode($content2));

            $fileName = 'tmp/Certificado_' . $arDados['NU_SEQ_USUARIO'] . '.pdf';
            $html2pdf->Output($fileName, 'F');

            return $fileName;
        } catch (HTML2PDF_exception $e) {
            throw $e;
        }
    }

    /**
     * Função para criar o arquivo PDF do Certificado de Conclusão do Curso.
     *
     * @param array $arDados
     * @param string $fileName
     */
    public static function gerarPdfCertificado($arDados, $fileName)
    {

        $content = Fnde_Sice_Business_Componentes::retornaHtmlCertificadoPdf($arDados);
        $content2 = Fnde_Sice_Business_Componentes::retornapagina2($arDados);
        $pagina = "<page backtop='30mm' backbottom='7mm' backleft='10mm' backright='0mm' backimg='img/certificado_verso.gif' backimgx='11' backimgy='8' backimgw='1100'></page>";

        require_once "Fnde/Sice/Html2pdf/Html2pdf.php";

        try {
            $html2pdf = new Fnde_Sice_Html2pdf_Html2pdf('L', 'A4', 'pt', true);
            $html2pdf->writeHTML(utf8_encode($content));
            $html2pdf->WriteHTML(utf8_encode($pagina));
            $html2pdf->rotateText(180, 150, 111, utf8_encode($content2));
            $html2pdf->Output($fileName, 'F');
            //$html2pdf->Output($fileName, 'I'); Para testes

            $usuarioLogado = (array)Zend_Auth::getInstance()->getIdentity();

            $castor = new Fnde_Model_Castor();
            $codCertificado = $castor->write($usuarioLogado['nu_seq_usuario'], APPLICATION_ROOT . '/public/' . $fileName, $fileName, 'pdf');
            $businessCertificado = new Fnde_Sice_Business_EmitirCertificado();
            $businessCertificado->setNumeroCertificado($arDados['NU_SEQ_USUARIO'], $arDados['NU_SEQ_TURMA'], $codCertificado);

        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

    /**
     *
     * Enviar para a webservice do SGB a gravação dos dados para pagamento
     *
     * @param uarray $arDadosBolsa
     * @return Ambigous <mixed, boolean, string, unknown, NULL>
     */
    public function enviarSgbWs($arDadosBolsa)
    {

        require_once(__DIR__ . '/../Model/lib/lib/nusoap/class.nusoap_base.php');
        require_once(__DIR__ . '/../Model/lib/lib/nusoap/class.xmlschema.php');
        require_once(__DIR__ . '/../Model/lib/lib/nusoap/class.soap_parser.php');
        require_once(__DIR__ . '/../Model/lib/lib/nusoap/class.soap_transport_http.php');
        require_once(__DIR__ . '/../Model/lib/lib/nusoap/class.wsdl.php');
        require_once(__DIR__ . '/../Model/lib/lib/nusoap/class.soapclient.php');

        $config = Zend_Registry::get('config');
        $wsdl = $config['webservices']['sgb']['uri'];

        $client = new nusoap_client($wsdl, 'wsdl');
        $dados = array(
            'sgb' => array('sistema' => 'SICE', 'login' => 'SICE', 'senha' => 'SICE', 'dt_envio' => date('Y-m-d'),
                'pagamento' => array('co_programa' => 'SIC', 'nu_cpf_bolsista' => $arDadosBolsa['NU_CPF'],
                    'nu_mes_referencia' => $arDadosBolsa['MES_REFERENCIA'],
                    'nu_ano_referencia' => $arDadosBolsa['ANO_REFERENCIA'],
                    'nu_cnpj_entidade' => $arDadosBolsa['NU_CNPJ_ENTIDADE'],
                    'vl_pagamento' => $arDadosBolsa['VL_BOLSA'], 'nu_parcela' => '1', 'co_funcao' => '10',
                    'sg_uf_atuacao' => $arDadosBolsa['SG_UF_ATUACAO_PERFIL'],
                    'co_municipio_atuacao' => $arDadosBolsa['CO_MUNICIPIO_PERFIL'])));

        //realiza a chamada remota ao método...
        $resposta = $client->call('gravaDadosPagamento', $dados);

        return $resposta;
    }

    /**
     * Converte a data em formato separado por barras(/) em uma string com numero do dia mês por estenso e ano
     * @param date $data (dd/mm/yyyy)
     */
    public static function dataPorExtensoCertificado($data, $maiusulo = 0)
    {
        $d = explode("/", $data);

        $de = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $para = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro',
            'Outubro', 'Novembro', 'Dezembro');

        $d[1] = str_replace($de, $para, $d[1]);

        $retorno = $d[0] . " de " . $d[1] . " de " . $d[2];

        if ($maiusulo == 0) {
            $retorno = strtolower($retorno);
        } else if ($maiusulo == 2) {
            $retorno = strtoupper($retorno);
        }

        return $retorno;

    }

    /**
     * Função para enviar e-mail
     *
     * @param  $bodyText - Texto do e-mail
     * @param  $addTo - e-mail do destinatário
     * @param  $nameTo - nome do destinatário
     * @param  $subject - Assunto do e-mail
     */
    public static function sendMail($bodyText, $addTo, $nameTo, $subject)
    {

        $config = Zend_Registry::get('config');
        $mail = new Zend_Mail();
        $mail->setBodyHtml($bodyText);
        $mail->setFrom($config['app']['mailer']['from_mail'], $config['app']['mailer']['from_name']);
        $mail->addTo($addTo, $nameTo);
        $mail->setSubject($subject);
        $mail->send();
    }

    /**
     * Função para verificar se um campo está vazio
     * @author diego.matos
     * @since 07/11/2012
     * @param var $campo
     */
    public static function isEmpty($campo)
    {
        return $campo == null || $campo == '';
    }

    /**
     * Retira as mesnagens padrões do validate de e-mail
     * @author poliane.silva
     * @since 13/11/2012
     * @return array
     */
    public static function limpaMensagensEmailValidate()
    {
        return array("emailAddressInvalid" => "", "emailAddressInvalidFormat" => "",
            "emailAddressInvalidHostname" => "", "emailAddressInvalidMxRecord" => "",
            "emailAddressInvalidSegment" => "", "emailAddressDotAtom" => "", "emailAddressQuotedString" => "",
            "emailAddressInvalidLocalPart" => "", "emailAddressLengthExceeded" => "");
    }

    /**
     * Retira as mesnagens padrões do validate de e-mail na parte de host name
     * @author poliane.silva
     * @since 13/11/2012
     * @return array
     */
    public static function limpaMensagensEmailValidateHostName()
    {
        return array("hostnameInvalid" => "", "hostnameIpAddressNotAllowed" => "", "hostnameUnknownTld" => "",
            "hostnameDashCharacter" => "", "hostnameInvalidHostnameSchema" => "",
            "hostnameUndecipherableTld" => "", "hostnameInvalidHostname" => "", "hostnameInvalidLocalName" => "",
            "hostnameLocalNameNotAllowed" => "", "hostnameCannotDecodePunycode" => "");
    }

    public static function generateZip($files, $download = false)
    {
        try {

            if (is_array($files)) {
                $zip = new ZipArchive;
                $filename = md5(date('m-d-Y H:i:s')) . '.zip';
                $file = APPLICATION_ROOT . '/public/' . $filename;
                //echo $file; die;
                //$file 		= tempnam(sys_get_temp_dir(), 'sice_' . date('m-d-Y') . '_');
                //$file_m		= $zip->open($file, ZipArchive::OVERWRITE);

                if ($zip->open($file) !== TRUE) {
                    $zip->open($file, ZipArchive::CREATE);
                }

                foreach ($files as $f) {
                    $content = self::getFileContent($f["nu_seq_arquivo"]);
                    $zip->addFromString($f["no_arquivo"], $content);
                }

                $zip->close();

                if ($download) {
                    self::downloadZip($file, $filename);
                }

                return $filename;
            }

        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        return true;
    }

    public static function downloadZip($file, $filename)
    {
        if (file_exists($file)) {
            $temp = tempnam('/tmp', 'logo-certificado-');
            $temp .= $filename;
            copy($file, $temp);
            unlink($file);

            $file = $temp;

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            //header("Content-type: application/octet-stream");
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($file));
            ob_end_flush();
            readfile($file);
        } else {
            throw new Exception('Arquivo não encontrado: ' . $filename);
        }
    }

    public static function getFileContent($nu_seq_arquivo)
    {
        try {
            $options = Zend_Registry::get('config');
            $castor = new Fnde_Model_Castor();
            $file = $castor->view($nu_seq_arquivo, $options['app']['name']);

            return $file->getBody();


        } catch (Exception $e) {
            echo "Erro: [{$e->getCode()}] - {$e->getMessage()}";
        }
    }
}
