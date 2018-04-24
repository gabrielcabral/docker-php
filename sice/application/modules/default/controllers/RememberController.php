<?php
class RememberController extends Fnde_Sice_Controller_Action {
    public function indexAction(){
        $this->setTitle("Lembrar Senha");
        $this->setSubtitle("Digite seu CPF para receber sua senha por e-mail");

        if($this->getRequest()->isPost()){
            $cpf = substr(preg_replace('/\D/', '', $this->getRequest()->getParam("cpf")),0,11);
            $adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
            $res = $adapter->select()->from("SICE_FNDE.S_USUARIO AS U","*")->where($adapter->quoteInto("NU_CPF = ?",$cpf))->query()->fetch();
            if(!$res){
                $this->addInstantMessage(Fnde_Message::MSG_ERROR,"CPF n„o encontrado!");
            }else{
		if(!empty($res['NU_SEQ_USUARIO_SEGWEB'])){

	                //ajustando email no segweb
                        $config = Zend_Registry::get('config');
                        $urlSegWeb = $config['webservices']['segweb']['uri'] . 'usuario/Edit';
                        //$urlSegWeb = 'http://www.fnde.gov.br/webservices/segweb/index.php/usuario/Edit';
                        $xml = '<request>
                                      <header>
                                        <app>SICE</app>
                                        <version>1.0</version>
                                        <created>' . date("Y-m-d\TH:i:s") . '</created>
                                      </header>
                                      <body>
                                        <usuario>
                                          <idusuario>' . $res['NU_SEQ_USUARIO_SEGWEB'] . '</idusuario>
                                          <alterarsenha>N</alterarsenha>
                                          <email>' . $res['DS_EMAIL_USUARIO'] . '</email>
                                        </usuario>
                                      </body>
                                </request>';

                        $postdata = http_build_query(
                            array(
                                'xml' => $xml
                            )
                        );

                        $curl = curl_init($urlSegWeb);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl,CURLOPT_POSTFIELDS, $postdata);
                        $result = curl_exec($curl);
                        curl_close($curl);


                        $obj = simplexml_load_string($result);
                        $ok = (int)$obj->status->result;

                        if ($obj && $ok == 1) {
                            $segweb = new Fnde_Model_Segweb();
                            $segweb->setNovaSenha($res['NU_SEQ_USUARIO_SEGWEB']);
                        } else {
                            die(json_encode(array("status" => "error", "message" => "Erro ao acessar o SEGWEB! Tente novamente mais tarde.")));
                        }
                        //fim ajustando email
            $this->addInstantMessage(Fnde_Message::MSG_SUCCESS,"Senha enviada para o e-mail: <b>".$res['DS_EMAIL_USUARIO']."</b> !");
		}else{
	        $this->addInstantMessage(Fnde_Message::MSG_ERROR,"Erro! Usu√rio n√o liberado!");
		}
            }
        }

    }
} 
