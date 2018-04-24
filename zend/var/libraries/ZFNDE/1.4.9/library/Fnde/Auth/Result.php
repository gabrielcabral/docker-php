<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of Fnde_Auth_Credential
 *
 * @author Leandro
 */
class Fnde_Auth_Result extends Zend_Auth_Result {

  /**
   * Falha para quando o perfil não tiver permissão de acesso a aplicação
   */
  const FAILURE_PERFIL_SEM_ACESSO_APLICACAO = -5;

  public function  __sleep() {
    return array('_code','_identity','_messages');
  }
}
?>
