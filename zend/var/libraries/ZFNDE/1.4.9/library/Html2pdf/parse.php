<?php
$config = array(
    'content'     => 'Sem conteúdo',
    'orientation' => 'P',
    'paper' => 'A4',
    'filename' => 'exemple.pdf',
    'output' => 'I'
);

$injects = array (
    '{{cliente.nome}}' => 'Walker de alencar Oliveira',
    '{{valor}}' => 'R$ 250,00'
);

if (isset($_POST['content']) && !is_null($_POST['content'])){
    $config['content'] = str_replace(array_keys($injects),array_values($injects), stripslashes($_POST['content']));
}
if (isset($_POST['orientation']) && !is_null($_POST['orientation'])){
    $config['orientation'] = $_POST['orientation'];
}
if (isset($_POST['paper']) && !is_null($_POST['paper'])){
    $config['paper'] = $_POST['paper'];
}
if (isset($_POST['filename']) && !is_null($_POST['filename'])){
    $config['filename'] = $_POST['filename'];
}
if (isset($_POST['output']) && !is_null($_POST['output'])){
    $config['output'] = $_POST['output'];
}
require_once(dirname(__FILE__).'/html2pdf.class.php');
try {
    $html2pdf = new HTML2PDF($config['orientation'],$config['paper'],'pt', false);
	$html2pdf->setTestIsImage(false);
    $html2pdf->WriteHTML($config['content'],false);
    $html2pdf->Output($config['filename'],$config['output']);
} catch(HTML2PDF_exception $e) {
    echo $e;
}

