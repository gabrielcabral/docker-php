<?php

require_once ZF_FNDE_ROOT . '/library/Html2pdf/html2pdf.class.php';

class Fnde_Sice_Html2pdf_Html2pdf extends HTML2PDF{
	
	public function rotateText($angulo, $x, $y, $texto){
		$this->pdf->Rotate($angulo, $x, $y);
		$this->pdf->writeHTML($texto);
		$this->pdf->Rotate(0);
	}
	
}