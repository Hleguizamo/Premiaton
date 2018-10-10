<?php

namespace Cpdg\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ProveedoresUsuariosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('telefono')
            ->add('contrasena', RepeatedType::class, 
            array('type' => PasswordType::class, 'invalid_message' => 'La contraseña no coincide.',
            'first_options'  => array('label' => 'Contraseña'),
            'second_options' => array('label' => 'Confirme la contraseña')))            
            ->add('email',EmailType::class, array(
                'label' => 'Correo elctrónico:',
            ))
            ->add('email',EmailType::class, array(
                'label' => 'Correo electrónico (Este será el usuario de ingreso):',
            ))
            //->add('superUsuario')
            //->add('public')
            //->add('idProveedor')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cpdg\UsuarioBundle\Entity\ProveedoresUsuarios'
        ));
    }
}
