<?php
header('Content-Type: text/xml; charset=iso-8859-1');
$xml = '<?xml version="1.0" encoding="iso-8859-1"?>
    <response>
    <header>
        <app>(dev) WS-SEGWEB</app>
        <version>0.12</version>
        <created>2012-04-19T14:46:29</created>
    </header>';
$perfis = explode(',', $_GET['perfil']);
if ( isset($_GET['result']) && $_GET['result'] == 'OK' && $_GET['op'] == 'usuario/autenticar' ) {
	$xml .= '<status>
                <result>1</result>
            <message>
                    <code>OK</code>
                    <text>Consulta efetuada com sucesso.</text>
            </message>
            </status><body>';
	foreach ( $perfis as $perfil ) {
		$xml .= '<perfil>' . $perfil . '</perfil>';
	}
	$xml .= '</body>';
} elseif ( $_GET['result'] == 'FAIL' && $_GET['op'] == 'usuario/autenticar' ) {
	$xml .= '<status>
                <result>0</result>
                <message>
                    <code>E-001</code>
                    <text>Operação falhou!</text>
                </message>
                <error>
                    <message>
                        <code>1</code>
                        <text>Erro login e senha ou não possui perfil</text>
                    </message>
                </error>
            </status> ';
} elseif ( $_GET['op'] == 'usuario/info' ) {
	$xml .= '<status>
                <result>1</result>
            <message>
                    <code>OK</code>
                    <text>Consulta efetuada com sucesso.</text>
            </message>
            </status><body>';
	$xml .= '<nu_seq_usuario>5555</nu_seq_usuario>
    <nome>NOME DO DESENVOLVEDOR</nome>
    <email>desenvolvedor@fnde.gov.br</email>
    <ramal></ramal>
    <departamento>ARQUITETURA</departamento>
    <cpf>05847416695</cpf></body>';

}
$xml .= '</response>';

die($xml);
