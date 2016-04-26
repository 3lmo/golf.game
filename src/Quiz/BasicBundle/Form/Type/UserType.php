<?php

namespace Quiz\BasicBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {     
        $builder->add('username', 'text', array(
        		'attr' => array('class' => 'form-control', 'placeholder' => "Username"),
        ));
        $builder->add('password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'Password fields must match!',
            'required' => true,
            'first_options' => array('attr' => array('placeholder' => "Password", 'class' => 'form-control')),
            'second_options' => array('attr' => array('placeholder' => "Repeat Password", 'class' => 'form-control'))
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Quiz\BasicBundle\Entity\User'
        ));
    }

    public function getName() {
        return 'user';
    }

}