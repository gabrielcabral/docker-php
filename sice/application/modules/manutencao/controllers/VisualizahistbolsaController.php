<?php

/**
 * Controller do Visualizar historico bolsista
 * 
 * @author fabiana.rose
 * @since  29/08/2012
 */

class Manutencao_VisualizaHistBolsaController extends Fnde_Sice_Controller_Action {

	/**
	 * Retorna o formulario de cadastro
	 * 
	 * @access public
	 * 
	 * @author fabiana.rose
	 * @since 29/08/2012
	 */

	public function getForm( $arDados = array(), $arExtra = array() ) {

		//Recupera os parametros
		$params = $this->_getAllParams();

		if ( $params['NU_SEQ_USUARIO'] ) {
			$arDados['NU_SEQ_USUARIO'] = $params['NU_SEQ_USUARIO'];
		}

		$form = new VisualizaHistBolsa_Form($arDados, $arExtra);
		$form->setDecorators(array('FormElements', 'Form'));


		$htmlDadosBolsista = $form->getElement("htmlDadosBolsista");
		$strDadosBolsista = $this->retornaHtmlDadosBolsista($params['NU_SEQ_USUARIO']);
		$htmlDadosBolsista->setValue($strDadosBolsista);

		return $form;
	}

	/**
	 * Método para escrever o html da tabela de bolsistas.
	 * @param String $idUsuario
	 */
	public function retornaHtmlDadosBolsista( $idUsuario ) {
		$obBusiness = new Fnde_Sice_Business_VisualizaHistBolsa();
		$arDadosBolsista = $obBusiness->getDadosBolsista($idUsuario);

		$html = "<div class='listagem' style='display:inline;'>";
		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";
		$html .= "<caption>Dados do Bolsista</caption>";
		$html .= "</table>";

		$html .= "<table class='borders' cellspacing='0' cellpadding='0' width='100%'>";

		$html .= "<tr class='alt'>";
		$html .= "<td style='background-color:#E6EFF4; width:25%;'>";
		$html .= "Cód.Bolsista";
		$html .= "</td>";
		$html .= "<td style='width:25%;'>";
		$html .= "<b>{$arDadosBolsista['NU_SEQ_USUARIO']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4; width:25%;'>";
		$html .= "UF";
		$html .= "</td>";
		$html .= "<td style='width:25%;'>";
		$html .= "<b>{$arDadosBolsista['SG_UF_ATUACAO_PERFIL']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4;'>";
		$html .= "Nome";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arDadosBolsista['NOME']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4;'>";
		$html .= "Mesorregião";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arDadosBolsista['NO_MESO_REGIAO']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$cpfFormatado = Fnde_Sice_Business_Componentes::formataCpf($arDadosBolsista['NU_CPF']);

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4;'>";
		$html .= "CPF";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$cpfFormatado}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Município";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arDadosBolsista['NO_MUNICIPIO']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Perfil";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arDadosBolsista['PERFIL']}</b>";
		$html .= "</td>";

		$html .= "<td style='background-color:#E6EFF4'>";
		$html .= "Qtd. turmas finalizadas";
		$html .= "</td>";
		$html .= "<td>";
		$html .= "<b>{$arDadosBolsista['QTD_TURMAS_BOLSA']}</b>";
		$html .= "</td>";
		$html .= "</tr>";

		$html .= "</table>";

		$html .= "</div>";

		return $html;

	}

	/**
	 * Renderiza a view para exibir a tela.
	 */ 
	public function formAction() {
		$this->setTitle('Usuário');
		$this->setSubtitle('Visualizar histórico bolsista');

		$menu = array($this->getUrl('manutencao', 'usuario', 'list', ' ') => 'filtrar');
		$this->setActionMenu($menu);

		$arParam = $this->_getAllParams();

		$form = $this->getForm($arParam);
		$form->populate($arParam);

		$this->view->form = $form;

        $query = "
SELECT 
  b.nu_seq_periodo_vinculacao,
  b.nu_seq_bolsa,
  b.nu_seq_usuario,
  TO_CHAR(pv.dt_inicial,'DD/MM/YYYY') AS dt_inicial,
  TO_CHAR(pv.dt_final,'DD/MM/YYYY')   AS dt_final,
  exec.no_usuario                     AS executor,
  ava.no_usuario                      AS avaliador,
  b.st_aptidao,
  hb.st_bolsa,
  b.ds_observacao_inaptidao,
  hb.ds_observacao,
  jc.ds_justif_cancelamento,
  jd.ds_justif_dev_bolsa,
  ji.ds_justif_inaptidao,

  vbp.vl_bolsa,

  TO_CHAR(hb.dt_historico,'DD/MM/YYYY HH:MM:SS') AS dt_historico_br,
  hb.dt_historico

FROM sice_fnde.s_historico_bolsa hb
JOIN sice_fnde.s_bolsa b ON hb.nu_seq_bolsa = b.nu_seq_bolsa
JOIN sice_fnde.s_periodo_vinculacao pv ON pv.nu_seq_periodo_vinculacao = b.nu_seq_periodo_vinculacao

INNER JOIN sice_fnde.s_configuracao con ON pv.dt_inicial >= con.dt_ini_vigencia AND pv.dt_inicial <= nvl(con.dt_termino_vigencia, sysdate + 1)
AND pv.dt_final >= con.dt_ini_vigencia AND pv.dt_final <= nvl(con.dt_termino_vigencia, sysdate + 1)

JOIN sice_fnde.s_vincula_conf_perfil vcp ON vcp.nu_seq_configuracao = con.nu_seq_configuracao AND vcp.nu_seq_tipo_perfil = pv.nu_seq_tipo_perfil
JOIN SICE_FNDE.s_valor_bolsa_perfil vbp ON vbp.nu_seq_vinc_conf_perf = vcp.nu_seq_vinc_conf_perf
 /* AND vbp.qt_turma =
  CASE
    WHEN (vcp.nu_seq_tipo_perfil <> 6) THEN 1
    ELSE
      (SELECT
        CASE
          WHEN COUNT(nu_seq_turma) > 2 THEN 4
          ELSE COUNT(nu_seq_turma)
        END AS qtd
      FROM sice_fnde.s_turma ti
      WHERE ti.st_turma                = 11
      AND ti.nu_seq_usuario_tutor      = b.nu_seq_usuario
      AND ti.nu_seq_periodo_vinculacao = b.nu_seq_periodo_vinculacao
      )
  END*/

JOIN sice_fnde.s_usuario EXEC ON exec.nu_seq_usuario = hb.nu_seq_usuario
JOIN sice_fnde.s_usuario ava ON ava.nu_seq_usuario = b.nu_seq_usuario_avaliador
JOIN sice_fnde.s_usuario bol ON bol.nu_seq_usuario = b.nu_seq_usuario
LEFT JOIN sice_fnde.s_justif_cancelamento jc ON jc.nu_seq_justif_cancelamento = hb.nu_seq_justif_cancelamento
LEFT JOIN sice_fnde.s_justif_dev_bolsa jd ON jd.nu_seq_justif_dev_bolsa = hb.nu_seq_justif_dev_bolsa
LEFT JOIN sice_fnde.s_justif_inaptidao_bolsista ji ON ji.nu_seq_justif_inaptidao = hb.nu_seq_justif_inaptidao

WHERE b.nu_seq_usuario = '{$arParam['NU_SEQ_USUARIO']}'
GROUP BY b.nu_seq_periodo_vinculacao,
  b.nu_seq_bolsa,
  b.nu_seq_usuario,
  dt_inicial,
  dt_final,
  exec.no_usuario,
  ava.no_usuario,
  b.st_aptidao,
  hb.st_bolsa,
  b.ds_observacao_inaptidao,
  hb.ds_observacao,
  jc.ds_justif_cancelamento,
  jd.ds_justif_dev_bolsa,
  ji.ds_justif_inaptidao,
  vbp.vl_bolsa,
  hb.dt_historico,
  hb.dt_historico
ORDER BY hb.dt_historico ASC";

        $adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $historico = $adapter->query($query)->fetchAll();

        $st_bolsa = array(
            1 => 'Devolvida',
            2 => 'Homologada',
            3 => 'Encaminhada p/ SGB',
            4 => 'Cancelada',
            5 => 'Avaliada',
            6 => 'Não avaliada',
            7 => 'Falha no envio',
            8 => 'Solicitada homologação',
            9 => 'Pendências'
        );

        $report = array();

        foreach($historico as $row){

            $descricao = array_filter(array(
                $st_bolsa[$row['ST_BOLSA']],
                $row['DS_OBSERVACAO_INAPTIDAO'],
                $row['DS_OBSERVACAO'],
                $row['DS_JUSTIF_CANCELAMENTO'],
                $row['DS_JUSTIF_DEV_BOLSA'],
                $row['DS_JUSTIF_INAPTIDAO']
            ));

            $periodo = array(
                'dataInicial' => $row['DT_INICIAL'],
                'dataFinal' => $row['DT_FINAL']
            );

            $bolsa = array(
                'id' => $row['NU_SEQ_BOLSA'],
                'valor' => 'R$ ' . number_format($row['VL_BOLSA'],2,',','.'),
                'data' => $row['DT_HISTORICO_BR'],
                'executor' => $row['EXECUTOR'],
                'avaliador' => $row['AVALIADOR'],
                'descricao' => implode(' / ',$descricao),
                'aptidao' => $row['ST_APTIDAO']
            );

            $report[$row['NU_SEQ_PERIODO_VINCULACAO']]['periodo'] = $periodo;
            $report[$row['NU_SEQ_PERIODO_VINCULACAO']]['bolsas'][] = $bolsa;
        }

        $this->view->report = $report;

		// Consulta Situação Bolsista SGB
		if($historico){
			$obBusiness = new Fnde_Sice_Business_VisualizaHistBolsa();
			$rsDadosBolsista = $obBusiness->getDadosBolsista($arParam['NU_SEQ_USUARIO']);

			$config = Zend_Registry::get('config');

			$dados = array(
				'sistema' => 'SICE',
				'login' => $config['webservices']['sgb']['login'],
				'senha' => $config['webservices']['sgb']['senha'],
				'nu_cpf' => $rsDadosBolsista['NU_CPF']
			);


			$urlSOAP = $config['webservices']['sgb']['uri'];
			// para testar no ambiente local basta utilizar um dos links a
			// seguir ou personalizar conforme necessidade
//			$urlSOAP = "http://sgbdev.fnde.gov.br/leidison/FNDE_934_REQ000000004604_29954/sistema/ws?wsdl";
//			$urlSOAP = "http://sgbhmg.fnde.gov.br/sistema/ws?wsdl";

			$soap = new Zend_Soap_Client($urlSOAP, array('encoding' => 'ISO-8859-1'));

			list($usec, $sec) = explode(" ", microtime());
			$script_start = (float) $sec + (float) $usec;


			try {
				$soap->setSoapVersion(SOAP_1_1);

				$respXml = $soap->lerSituacaoDoBolsista($dados);

				$objXml = new Zend_Config_Xml($respXml);

				$this->view->arrWsSGBSituacaoDoBolsista = $objXml->toArray();

			}catch ( SoapFault $exp ) { //catching exception and print out.

				list($usec, $sec) = explode(" ", microtime());
				$script_end = (float) $sec + (float) $usec;
				$elapsed_time = round($script_end - $script_start, 5);
				echo "<br> A chamada do webservice foi interrompida após $elapsed_time segundos.";

				echo "<br> O tempo suportado pelo servidor é de " . ini_get('default_socket_timeout') . ' segundos';

				echo "<br><br>Exceção específica de SoapFault [{$exp->faultcode}]: {$exp->faultstring}";

//				echo "<br><br>====== REQUEST HEADERS ===== <br>";
//				var_dump($soap->getLastRequestHeaders());
//				echo "<br><br>========= REQUEST ========== <br>";
//				var_dump($soap->getLastRequest());
//				echo "<br><br>========= RESPONSE ========= <br>";
//				var_dump($soap->getLastResponse());
				echo "<br><br><br>";
			}catch (Exception $exp){

				list($usec, $sec) = explode(" ", microtime());
				$script_end = (float) $sec + (float) $usec;
				$elapsed_time = round($script_end - $script_start, 5);

				echo "<br> A chamada do webservice foi interrompida após $elapsed_time segundos.";

				echo "<br> O tempo suportado pelo servidor é de " . ini_get('default_socket_timeout') . ' segundos';

				echo '<br> Resultando no erro básico: ' . $exp->getMessage();

//				echo "<br><br>====== REQUEST HEADERS ===== <br>";
//				var_dump($soap->getLastRequestHeaders());
//				echo "<br><br>========= REQUEST ========== <br>";
//				var_dump($soap->getLastRequest());
//				echo "<br><br>========= RESPONSE ========= <br>";
//				var_dump($soap->getLastResponse());
				echo "<br><br><br>";
			}
		}

		return $this->render('form');
	}
}
