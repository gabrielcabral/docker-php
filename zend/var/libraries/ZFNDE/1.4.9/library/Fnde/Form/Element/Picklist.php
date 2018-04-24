<?php
/**
 * 
 * @author Theoziran Lima
 */
class Fnde_Form_Element_Picklist extends Zend_Form_Element_Select {

  public function init(){
      $this->setAttrib('class',$this->getAttrib('class') . ' picklist');
      $this->setAttrib('multiple','multiple');
      
      $this->addMultiOptions(array('PB' => 'Paraiba', 'PE' => 'Pernambuco'));
      
      $this->getView()->headScript()->appendFile('/arquitetura/theoziran/static/js/jquery/jquery.picklists.js')
                                ->appendFile('/arquitetura/theoziran/static/js/jquery/jquery.emulatedisabled.js');
                                 //->appendScript("$(document).ready(function(){\$('.picklist').pickList();});");
      
  }
  
  public function render(){
      $this->clearDecorators();
      $this->setDecorators(array('Picklist'));
      return parent::render();
  }
  
}