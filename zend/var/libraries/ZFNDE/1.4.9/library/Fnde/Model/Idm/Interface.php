<?php
/**
 * Interface do modelo do IDM
 * @author theoziran
 */
interface Fnde_Model_Idm_Interface {
    public function getAllEntities();
    public function getEntity($application = null);
    public function getRole($type, $idEntity = null, $application = null);
}