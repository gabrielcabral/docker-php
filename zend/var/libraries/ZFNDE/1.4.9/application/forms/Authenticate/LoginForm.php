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
class Authenticate_LoginForm extends Fnde_Form {
    const MIN = 2;
    const MAX = 30;

    public function init() {
        $this->addElements(array(
            $this->createElement('Text', 'username')
                 ->setLabel('Usuário')
                 ->setRequired(true)
                 ->addValidator('NotEmpty', true, array('messages' => 'Por favor, informe o usuário'))
                 ->addValidator('StringLength', true, array('min' => self::MIN, 'max' => self::MAX)),
            $this->createElement('Password', 'password')
                 ->setLabel('Senha')
                 ->setRequired(true)
                 ->addValidator('NotEmpty', true, array('messages' => 'Por favor, informe a senha'))
                 ->addValidator('StringLength', true, array('min' => self::MIN, 'max' => self::MAX)),
            $this->createElement('Button', 'Entrar')
                 ->setValue('Entrar')
                 ->setAttrib('class', 'btnLogin')
                 ->setAttrib('type', 'submit'),
        ));
        parent::init();
    }

}