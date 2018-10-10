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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProveedoresType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array(
                'required' => true,
                'label' => "Nombre del Proveedor: ",
            ))
            ->add('nit', TextType::class, array(
                'required' => true,
                'label' => "Nit del Proveedor: ",
            ))
            ->add('representante', TextType::class, array(
                'required' => true,
                'label' => "Representante del Proveedor: ",
            ))
            ->add('telefono', TextType::class, array(
                'required' => true,
                'label' => "Teléfono del Proveedor: ",
            ))
            ->add('email', EmailType::class, array(
                'required' => true,
                'label' => "Email del Proveedor: ",
            ))
            ->add('imagen', FileType::class, array(
                'required' => false,
                'label'    => 'La imagen debe estar en formato JPG o PNG, el minimo de tamano permitido es 20 Kb y el maximo es de 2 Mb',
                'data' => '',
                'attr' => array('onchange' => 'validarformatoImagen(this)'),
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cpdg\AdministradorBundle\Entity\Proveedores'
        ));
    }
}
