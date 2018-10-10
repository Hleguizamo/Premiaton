<?php

namespace Cpdg\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ActualizacionPersonaNaturalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class, array(
                'label' => 'Nombres:',
            ))
            ->add('documentoTipo', ChoiceType::class, array(
                'choices' => array(
                    'CC' => 'CC',
                    'CE' => 'CE'
                ),
                'label'    => 'Tipo de documento:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))
            ->add('documentoNumero')

            ->add('documentoFecha', DateType::class, array(
                'widget' => 'choice',
                'label' => 'Fecha de expedición del documento:',
                'years' => range(1900,2020),
            ))
            ->add('documentoCiudad', EntityType::class, array(
                'class' => 'CpdgUsuarioBundle:Ciudades',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nombre', 'ASC');
                },
                'choice_label' => function ($ciudades) {
                return $ciudades->getNombre()." (".$ciudades->getIdDepartamento()->getNombre().")";
                },
                'label' => "Ciudad de expedición de Documento: ",
                'placeholder' => '',
            ))
            ->add('sexo', ChoiceType::class, array(
                'choices' => array(
                    'Masculino' => 'Masculino',
                    'Femenino' => 'Femenino'
                ),
                'label'    => 'Sexo:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))
            ->add('nacionalidad',TextType::class, array(
                'label' => 'Nacionalidad:',
            ))
            ->add('estadoCivil', ChoiceType::class, array(
                'choices' => array(
                    'Soltero' => 'Soltero',
                    'Casado' => 'Casado',
                    'Unión libre' => 'Unión libre',
                    'Viudo' => 'Viudo',
                    'Divorciado' => 'Divorciado',
                    'Separado' => 'Separado'
                ),
                'label'    => 'Estado civil:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))
            ->add('profesion',TextType::class, array(
                'label' => 'Profesión, Ocupación u Oficio:',
            ))
            ->add('domicilioDireccion',TextType::class, array(
                'label' => 'Dirección del domicilio:',
            ))
            ->add('domicilioCiudad', EntityType::class, array(
                'class' => 'CpdgUsuarioBundle:Ciudades',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nombre', 'ASC');
                },
                'choice_label' => function ($ciudades) {
                return $ciudades->getNombre()." (".$ciudades->getIdDepartamento()->getNombre().")";
                },
                'label' => "Ciudad del domicilio: ",
                'placeholder' => '',
            ))
            ->add('domicilioBarrio',TextType::class, array(
                'label' => 'Barrio del domicilio:',
            ))
            ->add('domicilioEstrato', ChoiceType::class, array(
                'choices' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6'
                ),
                'label'    => 'Estrato:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))
            ->add('domicilioTelefono')
            ->add('celular')
            ->add('email',EmailType::class, array(
                'label' => 'Correo elctrónico:',
            ))
            ->add('administraRecursosPublicos', ChoiceType::class, array(
                'choices' => array(
                    'Si' => '1',
                    'No' => '0'
                ),
                'label'    => 'Administra Recursos Publicos:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))
            ->add('ejercePoderPublico', ChoiceType::class, array(
                'choices' => array(
                    'Si' => '1',
                    'No' => '0'
                ),
                'label'    => 'Ejerce Poder Publico:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))
            ->add('reconocimientoPublico', ChoiceType::class, array(
                'choices' => array(
                    'Si' => '1',
                    'No' => '0'
                ),
                'label'    => 'Reconocimiento Publico:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))
            ->add('ingresosMensuales')
            ->add('ingresosMensualesOtros')
            ->add('ingresosMensualesOtrosConcepto')
            ->add('egresosMensuales')
            ->add('totalActivos')
            ->add('totalPasivos')
            ->add('operacionesMonedaExtranjera', ChoiceType::class, array(
                'choices' => array(
                    'Si' => '1',
                    'No' => '0'
                ),
                'label'    => 'Realiza Operaciones con Moneda Extranjera:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))
            ->add('operacionesMonedaExtranjeraCuales',TextType::class, array(
                'required' => false,
            ))
            ->add('operacionesMonedaExtranjeraPais', EntityType::class, array(
                'class' => 'CpdgUsuarioBundle:Paises',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nombre', 'ASC');
                },
                'choice_label' => function ($paises) {
                return $paises->getNombre();
                },
                'label' => "Pais: ",
                'placeholder' => '',
                'required'    => false,
            ))
            ->add('operacionesMonedaExtranjeraMoneda',TextType::class, array(
                'required' => false,
            ))
            ->add('adjunto1', FileType::class, array(
                'label' => 'Documento de Identificación (Formato PDF):',
            ))
            ->add('adjunto2', FileType::class, array(
                'label' => 'RUT (Formato PDF):',
            ))
            ->add('adjunto3', FileType::class, array(
                'label' => 'Decalración de Renta (Formato PDF):',
            ))  
        ;
         
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cpdg\UsuarioBundle\Entity\ActualizacionPersonaNatural'
        ));
    }
}
