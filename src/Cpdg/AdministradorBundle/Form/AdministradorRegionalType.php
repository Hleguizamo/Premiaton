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

class AdministradorRegionalType extends AbstractType
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
            ->add('nombre')
            ->add('contrasena', RepeatedType::class, 
            array('type' => PasswordType::class, 'invalid_message' => 'La contraseña no coincide.',
            'first_options'  => array('label' => 'Contraseña'),
            'second_options' => array('label' => 'Confirme la contraseña')))            
            ->add('email',EmailType::class, array(
                'label' => 'Correo electrónico:',
            ))
            ->add('email',EmailType::class, array(
                'label' => 'Correo electrónico:',
            ))
            ->add('telefono')
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
