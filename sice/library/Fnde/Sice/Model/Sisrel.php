<?php

/**
 * Classe criada pelo Gerador ZendRaulzito.
 * Arquivo de classe de modelo do tipo: database
 */

/**
 * Classe Fnde_Spae_Model_Sisrel, responsável
 * pela camada de modelo.
 *
 * @package
 * @category Model
 * @name Fnde_Spae_Model_Sisrel
 *
 */
class Fnde_Sice_Model_Sisrel {

	public function getTicket( $cfgAmbiente, $perfil ) {
		try {
			$dados['system'] = $cfgAmbiente['system'];
			$dados['senha'] = $cfgAmbiente['senha'];
			$dados['perfil'] = $perfil['perfil'];
			$dados['co_usuario'] = $perfil['co_usuario'];
			$dados['data_expire'] = $perfil['data_expire'];

			$urlTicket = $urlPath . "?getTicket=$infoTicket";
			$ticket = self::carregaUrlExterna($urlTicket);
			$url = $urlPath . "?c=ajax&m=carregaConsulta&useTicket=$ticket";
			self::carregaUrlExterna($url);

			//$infoTicket = json_encode( $dados );

			$infoTicket = urlencode(
					base64_encode(
							self::rc4("!sisrel@fnde!",
									$dados['system'] . "&" . $dados['perfil'] . "&" . $dados['senha'] . "&"
											. date('Y-m-d'))));
			//$urlTicket = $cfgAmbiente[ 'urlPath' ] . "?c=ajax&m=getTicket&getTicket=$infoTicket";
			$urlTicket = $cfgAmbiente['urlPath'] . "?getTicket=$infoTicket";
			$ticket = self::carregaUrlExterna($urlTicket, 1);
			return $ticket;
		} catch ( Exception $e ) {
			throw new Exception($e->getMessage(), $e->getCode());
		}

	}

	public function getList( $cfgAmbiente, $ticket = "" ) {
		try {
			$url = $cfgAmbiente['urlPath'] . "?module=consulta&controller=arvore-consulta&action=list";
			$dadosJson = self::carregaUrlExterna($url, 1);
			return $dadosJson;
		} catch ( Exception $e ) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	public static function carregaUrlExterna( $url, $return = false ) {
		
		$cookie = "perfil_cookie=" . $_COOKIE['perfil_cookie'] . ";co_usuario=" . $_COOKIE["co_usuario"] . ";http_x_forwarded_host=" . $_COOKIE["http_x_forwarded_host"];
		//$cookeie = "perfil_cookie=ADMINISTRADOR;co_usuario=203005;http_x_forwarded_host=sice.fnde.gov.br";
		if ( ( strstr($url, ".gov.br") || ( strstr($url, ".org.br") ) ) ) {
			$session = curl_init();
			curl_setopt($session, CURLOPT_URL, $url);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_COOKIE, $cookie);
			$dados = curl_exec($session);
			curl_close($session);
			$retorno = $dados;
		} else {
			$retorno = "";
		}

		if ( $return ) {
			return $retorno;
		} else {
			echo $retorno;
		}
	}

	public static function rc4( $keyStr, $dataStr ) {
		// convert input string(s) to array(s)
		$key = array();
		$data = array();
		for ( $i = 0; $i < strlen($keyStr); $i++ ) {
			$key[] = ord($keyStr{$i});
		}
		for ( $i = 0; $i < strlen($dataStr); $i++ ) {
			$data[] = ord($dataStr{$i});
		}
		// prepare key
		$state = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25,
				26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51,
				52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77,
				78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102,
				103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122,
				123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142,
				143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162,
				163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182,
				183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198, 199, 200, 201, 202,
				203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 217, 218, 219, 220, 221, 222,
				223, 224, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239, 240, 241, 242,
				243, 244, 245, 246, 247, 248, 249, 250, 251, 252, 253, 254, 255);
		// removed the following two lines as the array() version above is *significantly* faster [23% on PHP 4.3.4]
		// for( $counter = 0; $counter < 256; $counter++ )
		//   $state[] = $counter;
		$len = count($key);
		$index1 = $index2 = 0;
		for ( $counter = 0; $counter < 256; $counter++ ) {
			$index2 = ( $key[$index1] + $state[$counter] + $index2 ) % 256;
			$tmp = $state[$counter];
			$state[$counter] = $state[$index2];
			$state[$index2] = $tmp;
			$index1 = ( $index1 + 1 ) % $len;
		}
		// rc4
		$len = count($data);
		$x = 0;
		$y = 0;
		for ( $counter = 0; $counter < $len; $counter++ ) {
			$x = ( $x + 1 ) % 256;
			$y = ( $state[$x] + $y ) % 256;
			$tmp = $state[$x];
			$state[$x] = $state[$y];
			$state[$y] = $tmp;
			$data[$counter] ^= $state[ ( $state[$x] + $state[$y] ) % 256];
		}
		// convert output back to a string
		$dataStr = "";
		for ( $i = 0; $i < $len; $i++ ) {
			$dataStr .= chr($data[$i]);
		}
		return $dataStr;
	}
}

