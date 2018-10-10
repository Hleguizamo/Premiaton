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
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EventosType extends AbstractType
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
            ->add('nombre', TextType::class, array(
                'required' => true,
                'label' => "Nombre del Evento: ",
            ))
            ->add('idCiudad', EntityType::class, array(
                'class' => 'CpdgAdministradorBundle:Ciudades',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nombre', 'ASC');
                },
                'choice_label' => function ($ciudades) {
                return $ciudades->getNombre();
                },
                'label' => "Ciudad: ",
            ))
            ->add('fechaInicio', DateType::class, array(
                'widget' => 'choice',
                'label' => 'Fecha Inicio:',
                'years' => range(2016,2026),
            ))
            ->add('horaInicio', TimeType::class, array(
                'widget' => 'choice',
                'label' => 'Hora Inicio:',
            ))

            ->add('fechaFin', DateType::class, array(
                'widget' => 'choice',
                'label' => 'Fecha Fin:',
                'years' => range(2016,2026),
            ))            
            ->add('horaFin', TimeType::class, array(
                'widget' => 'choice',
                'label' => 'Hora Fin:',
            ))
            ->add('periodicidad', IntegerType::class, array(
                'required' => true,
                'label' => "¿Cada cuantos minutos hay sorteo? - Periodicidad",
                'attr' => array('onchange' => 'validarn(this)'),
            ))
            ->add('numeroGanadores', IntegerType::class, array(
                'required' => true,
                'label' => "¿Cuantos ganadores hay por sorteo?",
                'attr' => array('onchange' => 'validarn(this)'),
            ))
            ->add('numeroMaximoGanadores', IntegerType::class, array(
                'required' => true,
                'label' => "Cantidad o número máxima de ganadores del evento",
                'attr' => array('onchange' => 'validarn(this)'),
            ))
            ->add('numeroProveedores', IntegerType::class, array(
                'required' => true,
                'label' => "Cantidad de proveedores participantes en el evento",
                'attr' => array('onchange' => 'validarn(this)'),
            ))
            ->add('imagen', FileType::class, array(
                'required' => false,
                'label'    => 'La imagen debe estar en formato JPG ó PNG y las medidas de la imágen deben ser: 1366px X 768px, el mínimo de tamaño permitido es 50 Kb y el máximo es de 4 Mb',
                'data' => '',
                'attr' => array('onchange' => 'validarformato(this)'),
            ))
            ->add('premio')
            ->add('valor', IntegerType::class, array(
                'label'    => 'Valor del Premio:',
                'required' => true,
                'attr' => array('onchange' => 'validarn(this)'),
            ))
            ->add('mostrarValor', ChoiceType::class, array(
                'choices' => array(
                    'Si' => '1',
                    'No' => '0'
                ),
                'label'    => '¿Mostrar el Valor?',
                'required'    => true,
                'empty_data'  => null
            )) 
            ->add('repiteProveedor', ChoiceType::class, array(
                'choices' => array(
                    'Si' => '1',
                    'No' => '0'
                ),
                'label'    => '¿Repite el proveedor como Ganador?',
                'required'    => true,
                'empty_data'  => null
            ))
            ->add('repiteAsociadoProveedor', ChoiceType::class, array(
                'choices' => array(
                    'Si' => '1',
                    'No' => '0'
                ),
                'label'    => '¿Repite el Asociado como Ganador con el mismo proveedor?',
                'required'    => true,
                'empty_data'  => null
            )) 
            ->add('estado', ChoiceType::class, array(
                'choices' => array(
                    'Activo' => '1',
                    'Inactivo' => '0',
                    'En Curso' => '9',
                    'En Pausa' => '5',
                ),
                'label'    => 'Estado:',
                'required'    => true,
                'empty_data'  => null
            ))    
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cpdg\AdministradorBundle\Entity\Eventos'
        ));
    }
}
