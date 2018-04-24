<?php

class Fnde_Sice_Business_Municipio {

    public static function getMunicipioIbge ( $co_municipio_fnde ) {
		$query = "select sg_uf, co_municipio_ibge, co_mesoregiao_ibge 
				from corp_fnde.s_municipio 
				where co_municipio_fnde = {$co_municipio_fnde}";

		$obModelo = new Fnde_Sice_Model_Municipio();
		$stm = $obModelo->getAdapter()->query($query);
		$result = $stm->fetch();

		return $result;
	}

    public function getDadosMunicipio($co_municipio_fnde){
        $query = "SELECT m.SG_UF,
                      m.CO_MUNICIPIO_FNDE,
                      m.CO_MUNICIPIO_IBGE,
                      mr.CO_MESO_REGIAO
                    FROM CORP_FNDE.S_MUNICIPIO m
                    JOIN CTE_FNDE.T_MESO_REGIAO mr
                    ON mr.CO_MUNICIPIO_IBGE  = m.CO_MUNICIPIO_IBGE
                    WHERE m.CO_MUNICIPIO_FNDE = :co_municipio_fnde";

        $obModelo = new Fnde_Sice_Model_Municipio();
        $stm = $obModelo->getAdapter()->query($query, array('co_municipio_fnde'=>$co_municipio_fnde));
        $result = $stm->fetch();

        return $result;
    }

    public function getDadosMunicipioFndeByCodIbge($co_municipio_ibge){
        $query = "select sg_uf, co_municipio_fnde
				from corp_fnde.s_municipio
				where co_municipio_ibge = {$co_municipio_ibge}";

        $obModelo = new Fnde_Sice_Model_Municipio();
        $stm = $obModelo->getAdapter()->query($query);
        $result = $stm->fetch();

        return $result;
    }

}
