<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of Fnde_Form_Login
 *
 * @author Leandro
 */
class Fnde_Form_Login extends Fnde_Form {
    public function init() {
		
		$max = '30';
		$min = '2';
	
        $this->addElements( array(
	        $this->createElement('Text','username')
	             ->setLabel('Usuário')
	             ->setRequired(true)
				 ->setAttrib('maxlength',$max)
	             ->addValidator('NotEmpty', true, array('messages'=>'Por favor, informe o usuário'))
                 ->addValidator('StringLength', true, array('min'=> $min ,'max'=> $max)),
	        $this->createElement('Password','password')
	             ->setLabel('Senha')
	             ->setRequired(true)
	             ->addValidator('NotEmpty', true,array('messages'=>'Por favor, informe a senha'))
                 ->addValidator('StringLength', true, array('min'=> $min,'max'=> $max)),
            $this->createElement('Button','Entrar')
                 ->setValue('Entrar')
                 ->setAttrib('class', 'btnLogin')
                 ->setAttrib('type', 'submit'),
        ));
        parent::init();
    }
}