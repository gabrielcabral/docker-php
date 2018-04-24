<?php
/**
 * Arquivo de classe de modelo do tipo: database
 * 
 * Gerado automaticamente pelo gerador: ZFnde Model.
 *
 * $Rev::                      $
 * $Date::                     $
 * $Author::                   $
 * 
 * @package Sice
 * @category Model
 * @name Usuario
 */

/**
 * Classe de Modelo: Fnde_Sice_Model_Usuario
 * @uses Fnde_Sice_Model_Database_Usuario
 * @version $Id$
 */
class Fnde_Sice_Model_Usuario extends Fnde_Sice_Model_Database_Usuario {

    public function getUserByIdSEGWEB($id){
        $row = $this->fetchRow(
            Fnde_Sice_Model_Usuario::select()->where('NU_SEQ_USUARIO_SEGWEB = ?', $id)
        );

        return $row ? $row->toArray() : array();
    }
}
