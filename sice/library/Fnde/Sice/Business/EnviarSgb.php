<?php

/**
 * Business do Atividade
 * 
 * @author diego.matos
 * @since 12/04/2012
 */
class Fnde_Sice_Business_EnviarSgb {

	private $_sistema = 'SICE';
	private $_login = 'SICE';
	private $_senha = 'SICE';
	private $_coPrograma = 'SIC';

	/**
	 * 
	 * Enviar para o webservice do SGB a gravação dos dados para pagamento
	 * 
	 * @param uarray $arDadosBolsa
	 * @return Ambigous <mixed, boolean, string, unknown, NULL>
	 */
	public function enviarSgbWs( $arDadosBolsa ) {
		$config = Zend_Registry::get('config');
		$wsdl = $config['webservices']['sgb']['uri'];

		

		$dados = array('sistema' => $this->_sistema, 'login' => $config['webservices']['sgb']['login'], 'senha' => $config['webservices']['sgb']['senha'],
				'dt_envio' => date('Y-m-d'),
				'pagamento' => array('co_programa' => $config['webservices']['sgb']['co_programa'], 'nu_cpf_bolsista' => $arDadosBolsa['NU_CPF'],
						'nu_mes_referencia' => $arDadosBolsa['MES_REFERENCIA'],
						'nu_ano_referencia' => $arDadosBolsa['ANO_REFERENCIA'],
						'nu_cnpj_entidade' => $arDadosBolsa['NU_CNPJ_ENTIDADE'],
						'vl_pagamento' => $arDadosBolsa['VL_BOLSA'], 'nu_parcela' => '1', 'co_funcao' => $arDadosBolsa['CO_FUNCAO'],
						'sg_uf_atuacao' => $arDadosBolsa['SG_UF_ATUACAO_PERFIL'],
						'co_municipio_atuacao' => $arDadosBolsa['CO_MUNICIPIO_PERFIL']));
                
                
		//realiza a chamada remota ao método...
		$soap = new Zend_Soap_Client($wsdl, array('encoding' => 'ISO-8859-1'));
		$soap->setSoapVersion(SOAP_1_1);
		$resposta = $soap->gravaDadosPagamento($dados);
                
		//$objXml = new Zend_Config_Xml($respXml);
		//$arr = $objXml->toArray();

		return utf8_decode($resposta);
	}

	/**
	 *
	 * Soliciar para o webservice do SGB informações sobre o bolsista cadastrado
	 *
	 * @param uarray $arDadosBolsa
	 * @return Ambigous <mixed, boolean, string, unknown, NULL>
	 */
	public function lerBolsistaSgbWs( $cpf ) {
		$config = Zend_Registry::get('config');
		$wsdl = $config['webservices']['sgb']['uri'];

		$dados = array('sistema' => $this->_sistema, 'login' => $config['webservices']['sgb']['login'], 'senha' => $config['webservices']['sgb']['senha'],
				'nu_cpf' => $cpf);

		//realiza a chamada remota ao método...
		$soap = new Zend_Soap_Client($wsdl);
		$soap->setSoapVersion(SOAP_1_1);
		$respXml = $soap->lerDadosBolsista($dados);

		$objXml = new Zend_Config_Xml($respXml);
		$arr = $objXml->toArray();

		return $arr;
	}

	/**
	 *
	 * Enviar para o webservice do SGB a gravação dos dados para pagamento
	 *
	 * @param uarray $arDadosBolsa
	 * @return Ambigous <mixed, boolean, string, unknown, NULL>
	 */
	public function salvarBolsistaWs( $cpf ) {
		$config = Zend_Registry::get('config');
		$wsdl = $config['webservices']['sgb']['uri'];

		$arDadosPessoais = $this->getDadosPessoaisBolsista($cpf);

		$telefones[0] = array("nu_ddd_pessoa" => $arDadosPessoais['DS_TELEFONE_USUARIO_DDD'],
				"nu_telefone_pessoa" => $arDadosPessoais['DS_TELEFONE_USUARIO'],
				"tp_telefone" => ( $arDadosPessoais['TP_ENDERECO'] == "P" ? "T" : $arDadosPessoais['TP_ENDERECO'] ));

		if ( $arDadosPessoais['DS_CELULAR_USUARIO_DDD'] ) {
			$telefones[1] = array("nu_ddd_pessoa" => $arDadosPessoais['DS_CELULAR_USUARIO_DDD'],
					"nu_telefone_pessoa" => $arDadosPessoais['DS_CELULAR_USUARIO'], "tp_telefone" => "C");
		}

		$dados = array('sistema' => $this->_sistema, 'login' => $config['webservices']['sgb']['login'], 'senha' => $config['webservices']['sgb']['senha'], 'acao' => 'I',
				//Inclusão
				'dt_envio' => date('Y-m-d'),
				'pessoa' => array('nu_cpf' => $arDadosPessoais['NU_CPF'],
						'no_pessoa' => $arDadosPessoais['NO_USUARIO'],
						'dt_nascimento' => $arDadosPessoais['DT_NASCIMENTO'], 'no_pai' => '',
						'no_mae' => $arDadosPessoais['NO_MAE'], 'sg_sexo' => $arDadosPessoais['CO_SEXO_USUARIO'],
						'co_municipio_ibge_nascimento' => $arDadosPessoais['CO_MUNICIPIO_NASCIMENTO'],
						'sg_uf_nascimento' => $arDadosPessoais['SG_UF_NASCIMENTO'],
						'co_estado_civil' => $arDadosPessoais['CO_ESTADO_CIVIL'], 'co_nacionalidade' => '10',
						//Brasileiro
						'co_situacao_pessoa' => '1', //Ativo
						'no_conjuge' => '', 'ds_endereco_web' => '',
						'co_agencia_sugerida' => $arDadosPessoais['CO_AGENCIA_PAGAMENTO'], 'formacoes' => array(),
						'experiencias' => array(),
						'documentos' => array(
								array("uf_documento" => $arDadosPessoais['SG_UF_EMISSAO_DOC'],
										"co_tipo_documento" => "2",
										//Identidade
										"nu_documento" => $arDadosPessoais['NU_IDENTIDADE'],
										"dt_expedicao" => $arDadosPessoais['DT_EMISSAO_DOCUMENTACAO'],
										"no_orgao_expedidor" => $arDadosPessoais['CO_ORGAO_EMISSOR'])),
						'enderecos' => array(
								array("co_municipio_ibge" => $arDadosPessoais['CO_MUNICIPIO_ENDERECO'],
										"sg_uf" => $arDadosPessoais['CO_UF_ENDERECO'],
										"ds_endereco" => utf8_decode($arDadosPessoais['DS_ENDERECO']),
										"ds_endereco_complemento" => $arDadosPessoais['DS_COMPLEMENTO_ENDERECO'],
										"nu_endereco" => "0", "nu_cep" => $arDadosPessoais['NU_CEP'],
										"no_bairro" => $arDadosPessoais['DS_BAIRRO_ENDERECO'],
										"tp_endereco" => $arDadosPessoais['TP_ENDERECO'])), 'telefones' => $telefones,
						'emails' => array(array("ds_email" => $arDadosPessoais['DS_EMAIL_USUARIO'])),
						'vinculacoes' => array()));

		//realiza a chamada remota ao método...
		$soap = new Zend_Soap_Client($wsdl, array('encoding' => 'ISO-8859-1'));
		$soap->setSoapVersion(SOAP_1_1);
		$resp = $soap->gravarDadosBolsista($dados);

		// 		$objXml = new Zend_Config_Xml($resp);
		// 		$arr = $objXml->toArray();

		return $resp;
	}

	/**
	 * Recupera os dados pessoais adaptados às necessidades do SGB
	 * @param String $cpf
	 */
	public function getDadosPessoaisBolsista( $cpf ) {
		$businessUsuario = new Fnde_Sice_Business_Usuario();
		$businessComponete = new Fnde_Sice_Business_Componentes();

		$arDadosUsuario = $businessUsuario->getUsuarioByCpf($cpf);

		/* 								 *
		 * Estado civil SICE			 * Estado civil SGB				
		 * 1	Casado                   * 1	Casado(a)
		 * 2	Divorciado               * 2	Desquitado(a)
		 * 3	Solteiro                 * 3	Divorciado(a)
		 * 4	Viúvo                    * 4	Viúvo(a)
		 *                               * 5	Separado(a) judicialmente
		 * 6	Solteiro(a)
		 * 7	União Estável
		 */

		$arEstCivSice = array('1', '2', '3', '4');
		$arEstCivSgb = array('1', '3', '6', '4');
		$arDadosUsuario['CO_ESTADO_CIVIL'] = str_replace($arEstCivSice, $arEstCivSgb,
				$arDadosUsuario['CO_ESTADO_CIVIL']);

		/* 								 *
		 * Sexo SICE	 				 * Sexo SGB
		 * 1	Masculino                * M	Masculino
		 * 2	Feminino	             * F	Feminino
		 *                               */

		$arSexCiSice = array('1', '2');
		$arSexCiSgb = array('M', 'F');
		$arDadosUsuario['CO_SEXO_USUARIO'] = str_replace($arSexCiSice, $arSexCiSgb, $arDadosUsuario['CO_SEXO_USUARIO']);

		/*
		 * Formatando data de nascimento para YYYY-MM-DD
		 */
		$arDadosUsuario['DT_NASCIMENTO'] = $businessComponete->dataBRToEUA($arDadosUsuario['DT_NASCIMENTO']);

		/*
		 * Formatando data de emissão do documanto para YYYY-MM-DD
		 */
		$arDadosUsuario['DT_EMISSAO_DOCUMENTACAO'] = $businessComponete->dataBRToEUA(
				$arDadosUsuario['DT_EMISSAO_DOCUMENTACAO']);

		/*
		 * Não permitir que complemento seja um valor null quando não retorna valor
		 */
		$arDadosUsuario['DS_COMPLEMENTO_ENDERECO'] = $arDadosUsuario['DS_COMPLEMENTO_ENDERECO'] ? $arDadosUsuario['DS_COMPLEMENTO_ENDERECO']
				: "";

		/*
		 * Formatando Telefone
		 */
		$telefone = $arDadosUsuario['DS_TELEFONE_USUARIO'];
		$arDadosUsuario['DS_TELEFONE_USUARIO_DDD'] = substr($telefone, 0, 2);
		$arDadosUsuario['DS_TELEFONE_USUARIO'] = substr($telefone, 2, strlen($telefone));

		/*
		 * Formatando Celular
		 */
		$celular = $arDadosUsuario['DS_CELULAR_USUARIO'];
		if ( $celular ) {
			$arDadosUsuario['DS_CELULAR_USUARIO_DDD'] = substr($celular, 0, 2);
			$arDadosUsuario['DS_CELULAR_USUARIO'] = substr($celular, 2, strlen($celular));
		} else {
			$arDadosUsuario['DS_CELULAR_USUARIO_DDD'] = "";
			$arDadosUsuario['DS_CELULAR_USUARIO'] = "";
		}

		return $arDadosUsuario;
	}
}
