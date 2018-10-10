<?php

namespace Cpdg\AdministradorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AdministradorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idCentro', EntityType::class, array(
                'class' => 'CpdgAdministradorBundle:Centros',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.id', 'ASC');
                },
                'choice_label' => function ($centros) {
                return $centros->getCentro();
                },
                'label' => "Centro: ",
            ))
            ->add('usuario')
            ->add('nombre',TextType::class, array(
                'label' => 'Contacto:',
            ))
            ->add('contrasena', RepeatedType::class, 
            array('type' => PasswordType::class, 'invalid_message' => 'La contraseña no coincide.',
            'first_options'  => array('label' => 'Contraseña'),
            'second_options' => array('label' => 'Confirme la contraseña')))            
            ->add('email',EmailType::class, array(
                'label' => 'Correo elctrónico:',
            ))
            ->add('email',EmailType::class, array(
                'label' => 'Correo electrónico:',
            ))
            ->add('telefono')
            ->add('SuperAdministrador', CheckboxType::class, array(
                'label'    => '¿Super Administrador?',
                'required' => false,
            ))

            ->add('menuEventos', CheckboxType::class, array(
                'label'    => '¿Menu Eventos?',
                'required' => false,
            ))
            ->add('menuProveedores', CheckboxType::class, array(
                'label'    => '¿Menu Proveedores?',
                'required' => false,
            ))
            ->add('menuAsociados', CheckboxType::class, array(
                'label'    => '¿Menu Asociados?',
                'required' => false,
            ))
            ->add('menuAdministradores', CheckboxType::class, array(
                'label'    => '¿Menu Administradores?',
                'required' => false,
            ))
            ->add('menuEventosinternos', CheckboxType::class, array(
                'label'    => '¿Menu Eventos Internos?',
                'required' => false,
            ))
            ->add('menuRegistro', CheckboxType::class, array(
                'label'    => '¿Menu Registro?',
                'required' => false,
            ))
            ->add('public', CheckboxType::class, array(
                'label'    => '¿Acceso Permitido?',
                'required' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cpdg\AdministradorBundle\Entity\Administrador'
        ));
    }
}
