<?php

/**
 * Classe criada pelo Gerador ZendRaulzito.
 */

/**
 * Classe Fnde_Spae_Business_Sisrel, responsável
 * pela validação de regras negociais.
 *
 * @author name
 */
class Fnde_Sice_Business_Sisrel {

	/**
	 * @var modelSisrel
	 */
	private $_modelSisrel = null;

	/**
	 * Construtor da classe.
	 */
	public function __construct() {
		$this->_modelSisrel = new Fnde_Sice_Model_Sisrel();
	}

	/**
	 * Função para obter o perfil para login no SEGWEB
	 * 
	 * @throws Exception
	 */
	public function getPerfil() {
		try {
			$usuarioLogado = Zend_Auth::getInstance()->getIdentity();
			
			$nuSeqUsuario = $usuarioLogado->nu_seq_usuario;
			
			$dados['perfil'] = "ADMINISTRADOR"; //retornar perfil logado no SEGWEG
			$dados['co_usuario'] = $nuSeqUsuario;
			$dados['criaRel'] = 1; // habilita o botão para criação de relatorios personalizados
			$dados['data_expire'] = date("Y-m-d h:i:s");
			return $dados;
		} catch ( Exception $e ) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Função para obter a configuração do ambiente.
	 * 
	 * @throws Exception
	 */
	public function getConfigAmbiente() {
		try {
			// 			if ( strstr( $_SERVER[ 'HTTP_HOST' ], 'dev' ) ) {
			// 				$urlPath = "http://sisreldev.fnde.gov.br";
			// 				$password = "SISRELSISUGP";
			// 				$system = "devfndegovbrsisugp";
			// 			} elseif ( strstr( $_SERVER[ 'HTTP_HOST' ], 'hmg' ) ) {
			// 				//$urlPath = "http://sisrelhmg.fnde.gov.br/novo/public/";
			// 				$urlPath = "http://sisrelhmg.fnde.gov.br";
			// 				$password = "SISRELSISUGP";
			// 				$system = "hmgfndegovbrsisugp";
			// 			} else {
			$urlPath = "http://sisrelv2.fnde.gov.br/";
			//}
			$dados['system'] = $system;
			$dados['urlPath'] = $urlPath;
			return $dados;
		} catch ( Exception $e ) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Função para obter o ticket do sisrel.
	 * 
	 * @param array $cfgAmbiente
	 * @param array $perfil
	 * @throws Exception
	 */
	public function getTicket( $cfgAmbiente, $perfil ) {
		try {
			return $this->_modelSisrel->getTicket($cfgAmbiente, $perfil);
		} catch ( Exception $e ) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Função para obter os relatórios do sisrel.
	 * @param array $cfgAmbiente
	 * @param array $ticket
	 * @throws Exception
	 */
	public function getRelatorios( $cfgAmbiente, $ticket = "" ) {
		try {
			return $this->_modelSisrel->getList($cfgAmbiente, $ticket);
		} catch ( Exception $e ) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}
}

