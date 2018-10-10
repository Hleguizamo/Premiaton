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

class ActualizacionPersonaJuridicaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('razonSocial')
            ->add('nit')
            ->add('tipoPersonaJuridica', ChoiceType::class, array(
                'choices' => array(
                    'Empresa Unipersonal' => 'Empresa Unipersonal',
                    'Sociedad comercial LTDA' => 'Sociedad comercial LTDA',
                    'Sociedad comercial SA' => 'Sociedad comercial SA',
                    'Sociedad comercial SCS' => 'Sociedad comercial SCS',
                    'Sociedad comercial SCA' => 'Sociedad comercial SCA',
                    'Sociedad comercial SAS' => 'Sociedad comercial SAS',
                    'Fundación'=>'Fundación',
                    'Corporación civil'=>'Corporación civil',
                    'Cooperativa'=>'Cooperativa',
                    'Fondo de empleados'=>'Fondo de empleados',
                    'Otro'=>'Otro'
                ),
                'label'    => 'Tipo de persona Jurídica:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))
            ->add('direccionJudicial')
            ->add('ciudad', EntityType::class, array(
                'class' => 'CpdgUsuarioBundle:Ciudades',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nombre', 'ASC');
                },
                'choice_label' => function ($ciudades) {
                return $ciudades->getNombre()." (".$ciudades->getIdDepartamento()->getNombre().")";
                },
                'label' => "Ciudad: ",
                'placeholder' => '',
            ))            
            ->add('telefono')
            ->add('email',EmailType::class, array(
                'label' => 'Correo elctrónico:',
            ))
            ->add('representanteLegalNombre')
            ->add('representanteLegalDocumento')
            ->add('representanteLegalTelefono')
            ->add('representanteLegalCiudad', EntityType::class, array(
                'class' => 'CpdgUsuarioBundle:Ciudades',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nombre', 'ASC');
                },
                'choice_label' => function ($ciudades) {
                return $ciudades->getNombre()." (".$ciudades->getIdDepartamento()->getNombre().")";
                },
                'label' => "Ciudad de domicilio: ",
                'placeholder' => '',
            ))
            ->add('representanteSuplenteNombre')
            ->add('representanteSuplenteDocumento')
            ->add('representanteSuplenteTelefono')

            ->add('representanteSuplenteCiudad', EntityType::class, array(
                'class' => 'CpdgUsuarioBundle:Ciudades',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.nombre', 'ASC');
                },
                'choice_label' => function ($ciudades) {
                return $ciudades->getNombre()." (".$ciudades->getIdDepartamento()->getNombre().")";
                },
                'label' => "Ciudad de domicilio del suplente: ",
                'placeholder' => '',
            ))
            ->add('tipoEmpresa', ChoiceType::class, array(
                'choices' => array(
                    'Pública'=>'Pública',
                    'Privada'=>'Privada',
                    'Mixta'=>'Mixta'
                ),
                'label'    => 'Tipo de persona Jurídica:',
                'required'    => true,
                'placeholder' => '',
                'empty_data'  => null
            ))

            ->add('ciiu')
            ->add('accionistasIngresos')
            ->add('accionistasOtrosIngresos')
            ->add('accionistasIngresosConcepto')
            ->add('accionistasEgresos')
            ->add('accionistasActivos')
            ->add('accionistasPasivos')            
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
                'label' => 'Que operaciones realiza:',
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
                'label' => 'En que monedas las realiza:',
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
            'data_class' => 'Cpdg\UsuarioBundle\Entity\ActualizacionPersonaJuridica'
        ));
    }
}
