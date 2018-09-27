<?php

       
class Atlas_Form_Ticket extends Zend_Form
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
 
        $this->addElement('text', 'subject', array(
            'class'     => 'form-element',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')        ));

        $this->addElement('textarea', 'detail', array(
            'class'     => 'form-element',
            'required'   => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 250))
                ),
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));

        $this->addElement('text', 'requestedDate', array(
            'class'     => 'form-element',
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));
 
        $this->addElement('text', 'processedBy', array(
            'class'     => 'form-element',
            'required'   => false,
            'filters'    => array('StringTrim', 'StripTags'),
            'Decorators'    => array('ViewHelper')
        ));
 
       
        $element = new Zend_Form_Element_Select('status', array(
            'class'     => 'form-element',
            'required'   => true,
            'multiOptions' => array(
                '0' => 'Pending',
                '1' => 'In Progress',
                '2' => 'Completed'
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