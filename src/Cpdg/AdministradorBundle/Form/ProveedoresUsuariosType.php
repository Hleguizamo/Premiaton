<?php

namespace Cpdg\AdministradorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProveedoresUsuariosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idProveedor', EntityType::class, array(
                'class' => 'CpdgAdministradorBundle:Proveedores',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nombre', 'ASC');
                },
                'choice_label' => function ($centros) {
                return $centros->getNombre();
                },
                'label' => "Proveedor: ",
            ))
            ->add('nombre')
            ->add('telefono')
            ->add('contrasena', RepeatedType::class, 
            array('type' => PasswordType::class, 'invalid_message' => 'La contrasena no coincide.',
            'first_options'  => array('label' => 'Contrasena'),
            'second_options' => array('label' => 'Confirme la contrasena')))            
            ->add('email',EmailType::class, array(
                'label' => 'Correo electronico (Este sera el usuario de ingreso):',
            ))
            ->add('superUsuario', CheckboxType::class, array(
                'label'    => 'Este usuario puede crear otros usuarios?',
                'required' => false,
            ))
            ->add('public', CheckboxType::class, array(
                'label'    => 'Acceso Permitido?',
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
            'data_class' => 'Cpdg\AdministradorBundle\Entity\ProveedoresUsuarios'
        ));
    }
}
