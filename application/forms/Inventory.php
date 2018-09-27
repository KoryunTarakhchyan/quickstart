<?php

       
class Atlas_Form_Inventory extends Zend_Form
{
    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
     	/* HIDDEN FORM FIELDS ****************************/
        $field = new Zend_Form_Element_Hidden("id");
        $field->setDecorators(array('ViewHelper'));
        $this->addElement($field);
        
        $field = new Zend_Form_Element_Hidden("createdDate");
        $field->setDecorators(array('ViewHelper'));
        $this->addElement($field);
        
 
        $this->addElement('text', 'name', array(
            'class'     => 'form-element',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));
 
        $this->addElement('hidden', 'id');
 
        $this->addElement('text', 'model', array(
            'class'     => 'form-element',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));
 
        $this->addElement('text', 'core', array(
            'class'     => 'form-element',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));

 
        $this->addElement('text', 'memory', array(
            'class'     => 'form-element',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));

 
        $this->addElement('text', 'os', array(
            'class'     => 'form-element',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));

 
        $this->addElement('text', 'tag', array(
            'class'     => 'form-element',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));

 
         $tag = new Zend_Form_Element_Select('location ', array(
            'class'     => 'form-element',
            'required'   => true,
            'multiOptions' => array(
                '0' => 'Robertson',
                '1' => 'Shoemaker'
            ),
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));
         
        $this->addElement($tag);
 
        $this->addElement('text', 'support_date', array(
            'required'   => false,
        )); 
       
        $element = new Zend_Form_Element_Select('type', array(
            'label'      => 'Type:',
            'class'     => 'form-element',
            'required'   => true,
            'multiOptions' => array(
                '0' => 'Desktop',
                '1' => 'Laptop',
                '2' => 'Server'
            ),
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));         
        
        $this->addElement($element);
 
        /* SUBMIT BUTTON *****************************************/
    	$submit = new Zend_Form_Element_Submit('submit', 'Submit');
	    $submit->setAttrib('class', 'form-element')
			->setDecorators(array('ViewHelper'));
             $this->addElement($submit);
 
        // And finally add some CSRF protection
//        $this->addElement('hash', 'csrf', array(
//            'ignore' => true,
//        ));
    }
}