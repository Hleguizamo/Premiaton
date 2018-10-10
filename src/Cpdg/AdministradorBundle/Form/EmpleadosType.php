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

class EmpleadosType extends AbstractType
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
            ->add('codigo', TextType::class, array(
                'label'    => 'Nit Empleado',
                'required' => false,
            ))
            ->add('nombre', TextType::class, array(
                'label'    => 'Nombre Completo',
                'required' => false,
            ))
            ->add('cedula', TextType::class, array(
                'label'    => 'Cédula',
                'required' => false,
            ))
            ->add('direccion', TextType::class, array(
                'label'    => 'Dirección',
                'required' => false,
            ))
            ->add('telefono', TextType::class, array(
                'label'    => 'Teléfono',
                'required' => false,
            ))
            ->add('ciudad', TextType::class, array(
                'label'    => 'Ciudad',
                'required' => false,
            ))
            ->add('departamento', TextType::class, array(
                'label'    => 'Departamento',
                'required' => false,
            ))
            ->add('public', CheckboxType::class, array(
                'label'    => '¿Activo?',
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
            'data_class' => 'Cpdg\AdministradorBundle\Entity\Empleados'
        ));
    }
}
