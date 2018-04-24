<?php
/**
 * @category   Fnde
 * @package    View
 * @subpackage Helper_Date
 */

class Fnde_View_Helper_Date
{
    /**
     * @param  string $date
     * @param  string $format
     * @param  boolean $returnNow
     * @param  boolean $month Numeração equivalente ao mês desejado.
     * @return string
     */
    function date($date = null, $format = 'd/m/Y', $returnNow = false, $month = false)
    {
    	if ($month) {
    		return $this->writeMonth($date);
    	}

        if ($date != null) {
            $date = $this->fromString( $date );
            return date($format, $date);
        }
        if ($returnNow) {
            return date($format, time());
        }
        return '';
    }

    /**
     * @param  string $date
     * @return integer
     */
    function fromString($date)
    {
        if (is_integer($date) || is_numeric($date)) {
            return intval($date);
        }
        return strtotime($date);
    }

    /**
     * @param  int $month
     * @return string
     */
    function writeMonth($month)
    {
    	$unabbreviated = array('01' => 'Janeiro',
    	                       '02' => 'Fevereiro',
                               '03' => 'Mar&ccedil;o',
                               '04' => 'Abril',
                               '05' => 'Maio',
                               '06' => 'Junho',
                               '07' => 'Julho',
                               '08' => 'Agosto',
                               '09' => 'Setembro',
                               '10' => 'Outubro',
    	                       '11' => 'Novembro',
                               '12' => 'Dezembro');
        return $unabbreviated[$month];
    }
}