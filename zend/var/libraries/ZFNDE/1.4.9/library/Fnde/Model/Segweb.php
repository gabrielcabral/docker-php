
<?php

/**
 * Arquivo de classe Segweb FNDE.
 *
 * $Rev::                      $
 * $Date::                     $
 * $Author::                   $
 *
 * @package Fnde
 * @category Model
 * @name Segweb
 * @author Alberto Guimaraes Viana <alberto.viana@fnde.gov.br>
 */

/**
 * Classe que consome o Webservice do Segweb
 *
 * @version $Id$
 */
class Fnde_Model_Segweb extends Fnde_Model_Service_Rest
{
    const XML_AUTENTICAR = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
          <params>
            <sg_aplicacao>%s</sg_aplicacao>
            <login>%s</login>
            <senha>%s</senha>
          </params>
      </body>
    </request>';
    const XML_INFO = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
        <params>
          <login>%s</login>
          %s
        </params>
      </body>
    </request>';
    const XML_USUARIO_APLICACAO = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
        <params>
          <sg_aplicacao>%s</sg_aplicacao>
        </params>
      </body>
    </request>';
    const XML_USUARIO_INSERT_INTERNO = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
        <usuario>
          <login>%s</login>
          <email>%s</email>
          <cpf>%s</cpf>
        </usuario>
        <usuariointerno>
          <idCadastro>%s</idCadastro>
        </usuariointerno>
      </body>
    </request>';
    const XML_USUARIO_INSERT_EXTERNO = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
        <usuario>
          <login>%s</login>
          <email>%s</email>
          <cpf>%s</cpf>
        </usuario>
        <usuarioexterno>
          <nome>%s</nome>
          <identidade>%s</identidade>
        </usuarioexterno>
      </body>
    </request>';
    const XML_USUARIO_INSERT_DIFERENCIADO = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
        <usuario>
          <login>%s</login>
          <email>%s</email>
        </usuario>
        <usuariodiferenciado>
          <caixapostal>%s</caixapostal>
          <cpf>%s</cpf>
          <orgao>%s</orgao>
          <descricaoorgao>%s</descricaoorgao>
          <dddfax>%s</dddfax>
          <dddtelefone>%s</dddtelefone>
          <numerofax>%s</numerofax>
          <numerotelefone>%s</numerotelefone>
          <nomebairro>%s</nomebairro>
          <cep>%s</cep>
          <cepcaixapostal>%s</cepcaixapostal>
          <municipio>%s</municipio>
          <nome>%s</nome>
          <endereco>%s</endereco>
          <complementoendereco>%s</complementoendereco>
          <numeroendereco>%s</numeroendereco>
        </usuariodiferenciado>
      </body>
    </request>';
    const XML_USUARIO_UPDATE = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
        <usuario>
          <idusuario>%s</idusuario>
          <statusativo>%s</statusativo>
          <dataexpiracao>%s</dataexpiracao>
          <datavalidade>%s</datavalidade>
          <alterarsenha>%s</alterarsenha>
          <email>%s</email>
        </usuario>
        <usuariodiferenciado>
          <dddtelefone>%s</dddtelefone>
          <numerotelefone>%s</numerotelefone>
          <nomebairro>%s</nomebairro>
          <cep>%s</cep>
          <endereco>%s</endereco>
          <complementoendereco>%s</complementoendereco>
        </usuariodiferenciado>
      </body>
    </request>';
    const XML_USUARIO_UPDATE_SENHA = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
          <idusuario>%s</idusuario>
      </body>
    </request>';
    const XML_USUARIO_GRUPO = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
        <idusuario>%s</idusuario>
        <grupos>
          <idgrupo>%s</idgrupo>
        </grupos>
      </body>
    </request>';
    const XML_MANTER_USUARIO_GRUPO = '<?xml version="1.0" encoding="iso-8859-1"?>
    <request>
      %s
      <body>
        <loginusuario>%s</loginusuario>
        <grupos>
          <nogrupo>%s</nogrupo>
        </grupos>
      </body>
    </request>';
    /**
     * Autentica o usuario e retorna os perfis
     * @param string $user
     * @param string $password
     * @return array
     */
    public function authenticate($user, $password)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/autenticar');
        $this->xml(sprintf(self::XML_AUTENTICAR, $this->getHeader(), $this->getApp(), $user, $password));
        $response = $this->post();

        if ((int) $response->status->result == '0') {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        if (!is_array($result['perfil'])) {
            $result['perfil'] = array($result['perfil']);
        }
        return array('result' => '1', 'perfil' => $result['perfil']);
    }

    /**
     * Retorna as informações de um usuario
     * @param string $user
     * @param boolean $inativo busca informacao de um usuario que está desativado no segweb
     * @return array
     */
    public function info($user, $inativo = false)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/info');

        $tmp = '';
        if ($inativo === true) {
            $tmp = sprintf('<inativo>%s</inativo>', (bool) $inativo);
        }

        $this->xml(sprintf(self::XML_INFO, $this->getHeader(), $user, $tmp));
        $response = $this->post();
        
        if ((int) $response->status->result == '0') {
            $result = self::xmlToArray($response->status->error);
            return $result['message'];
        }
        $result = self::xmlToArray($response->body);
        return $result;
    }

    /**
     * Criptografa uma senha
     * @param string $password
     * @return array|string
     */
    public function crypt($password)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/criptografar');
        $this->senha($password);
        $response = $this->post();

        if ((int) $response->status->result == '0') {
            $result = self::xmlToArray($response->status->error);
            return $result['message'];
        }
        return (string) $response->body->senha;
    }

    /**
     * Retorna os usuarios ativos e os perfis ativos por aplicacao
     *
     * @param string $sgAplicacao
     * @return array
     */
    public function getUserByApplication($sgAplicacao)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/aplicacao');
        $this->xml(sprintf(self::XML_USUARIO_APLICACAO, $this->getHeader(), $sgAplicacao));
        $response = $this->post();
        
        if ((int) $response->status->result == '0') {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        return array('result' => '1', 'user' => $result['row']);
    }
    /**
     * Insere novo usuário Interno
     *
     * @param string $dsLogin
     * @param string $dsEmail
     * @param string $nuCpfUsuario
     * @param string $idCadastro
     * @return array
     */
    public function setUserByAddInterno($dsLogin, $dsEmail, $nuCpfUsuario, $idCadastro)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/AddInterno');
        $this->xml(sprintf(self::XML_USUARIO_INSERT_INTERNO, $this->getHeader(), $dsLogin, $dsEmail, $nuCpfUsuario, $idCadastro));
        $response = $this->post();
        
        if ((int) $response->status->result === 0) {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        return array('result' => '1', 'user' => $result);

    }
    /**
     * Insere novo usuário Externo
     *
     * @param string $dsLogin
     * @param string $dsEmail
     * @param string $nuCpfUsuario
     * @param string $noUsuario
     * @param string $nuSeqEntidade
     * @return array
     */
    public function setUserByAddExterno($dsLogin, $dsEmail, $nuCpfUsuario, $noUsuario, $nuSeqEntidade)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/AddExterno');
        $this->xml(sprintf(self::XML_USUARIO_INSERT_EXTERNO, $this->getHeader(), $dsLogin, $dsEmail, $nuCpfUsuario, $noUsuario, $nuSeqEntidade));
        $response = $this->post();
        
        if ((int) $response->status->result === 0) {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        return array('result' => '1', 'user' => $result);

    }
    /**
     * Insere novo usuário Diferenciado
     *
     * @param string $nucaixapostal
     * @param string $nucpf
     * @param string $orgao
     * @param string $nudddfax
     * @param string $nudddtel
     * @param string $nufax
     * @param string $nutelefone
     * @param string $nobairro
     * @param string $nucep
     * @param string $nucepcaixapostal
     * @param string $comunicipiofnde
     * @param string $nousuario
     * @param string $dsendereco
     * @param string $dscomplemento
     * @param string $nuendereco
     * @return array
     */
    public function setUserByAddDiferenciado($dsLogin, $dsEmail, $nucaixapostal, $nucpf, $orgao, $descricaoorgao, $nudddfax, $nudddtel, $nufax, $nutelefone, $nobairro, $nucep, $nucepcaixapostal, $comunicipiofnde, $nousuario, $dsendereco, $dscomplemento, $nuendereco)
    {
	ob_start();
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/AddDiferenciado');
        $this->xml(sprintf(self::XML_USUARIO_INSERT_DIFERENCIADO, $this->getHeader(), $dsLogin, $dsEmail, $nucaixapostal, $nucpf, $orgao, $descricaoorgao, $nudddfax, $nudddtel, $nufax, $nutelefone, $nobairro, $nucep, $nucepcaixapostal, $comunicipiofnde, $nousuario, $dsendereco, $dscomplemento, $nuendereco));
        $response = $this->post();
	var_dump($response);
	
	$debug = ob_get_clean();
	file_put_contents("/tmp/model_segweb.log",$debug);
        
        if ((int) $response->status->result === 0) {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        return array('result' => '1', 'user' => $result);
    }
    /**
     * Update Usuário
     *
     * @param string $nusequsuario
     * @param string $stativo
     * @param date $dtexpiracaosenha
     * @param date $dtvalidadesenha
     * @param string $alterarsenha
     * @param array $usuarioDiferenciado
     * @return array
     */
    public function setUserUpdate($nusequsuario, $stativo, $dtexpiracaosenha, $dtvalidadesenha, $alterarsenha, $usuarioDiferenciado = array())
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/Edit');
        
        $this->xml(sprintf(
                self::XML_USUARIO_UPDATE, 
                $this->getHeader(), 
                $nusequsuario, 
                $stativo, 
                $dtexpiracaosenha, 
                $dtvalidadesenha, 
                $alterarsenha,
                isset($usuarioDiferenciado['email']) ? $usuarioDiferenciado['email'] : '',
                isset($usuarioDiferenciado['dddtelefone']) ? $usuarioDiferenciado['dddtelefone'] : '',
                isset($usuarioDiferenciado['numerotelefone']) ? $usuarioDiferenciado['numerotelefone'] : '',
                isset($usuarioDiferenciado['nomebairro']) ? $usuarioDiferenciado['nomebairro'] : '',
                isset($usuarioDiferenciado['cep']) ? $usuarioDiferenciado['cep'] : '',
                isset($usuarioDiferenciado['endereco']) ? $usuarioDiferenciado['endereco'] : '',
                isset($usuarioDiferenciado['complementoendereco']) ? $usuarioDiferenciado['complementoendereco'] : ''
                )
            );
        
        $response = $this->post();
        
        if ((int) $response->status->result === 0) {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        return array('result' => '1', 'user' => $result);
    }
   /**
     * Update senha usuário
     *
     * @param string $nusequsuario
     * @return array
     */
    public function setNovaSenha($nusequsuario)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/RenewPassword');
        $this->xml(sprintf(self::XML_USUARIO_UPDATE_SENHA, $this->getHeader(), $nusequsuario));
        $response = $this->post();
        
        if ((int) $response->status->result === 0) {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        return array('result' => '1', 'user' => $result);
    }
   /**
     * Manter Grupo
     *
     * @param string $idusuario
     * @param string $idgrupo
     * @return array
     */
    public function setManterGrupos($idusuario, $idgrupo)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/ManterGrupos');
        $this->xml(sprintf(self::XML_USUARIO_GRUPO, $this->getHeader(), $idusuario, $idgrupo));
        $response = $this->post();
        
        if ((int) $response->status->result === 0) {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        return array('result' => '1', 'user' => $result);
    }
   /**
     * Vincular Grupos
     *
     * @param string $loginUsuario
     * @param string $noGrupo
     * @return array
     */
    public function setVincularGrupos($loginUsuario, $noGrupo)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/VincularGrupos');
        $this->xml(sprintf(self::XML_MANTER_USUARIO_GRUPO, $this->getHeader(), $loginUsuario, $noGrupo));
        $response = $this->post();
        
        if ((int) $response->status->result === 0) {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        return array('result' => '1', 'user' => $result);
    }
   /**
     * Desvincular Grupos
     *
     * @param string $loginUsuario
     * @param string $noGrupo
     * @return array
     */
    public function setDesvincularGrupos($loginUsuario, $noGrupo)
    {
        $this->setUrl($this->_config['webservices']['segweb']['uri']);
        $this->setFunction('usuario/DesvincularGrupos');
        $this->xml(sprintf(self::XML_MANTER_USUARIO_GRUPO, $this->getHeader(), $loginUsuario, $noGrupo));
        $response = $this->post();
        
        if ((int) $response->status->result === 0) {
            $result = self::xmlToArray($response->status->error);
            return array('result' => '0', 'message' => $result['message']);
        }
        $result = self::xmlToArray($response->body);
        return array('result' => '1', 'user' => $result);
    }

    public function getUserById($idUsuario){
	return json_decode(file_get_contents($this->_config['webservices']['segweb']['uri'] . 'usuario/getUserById?id='.$idUsuario));
    }
}
