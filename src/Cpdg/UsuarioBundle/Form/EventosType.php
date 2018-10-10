<?php

namespace Cpdg\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('fechaCreacion', 'date')
            ->add('fechaInicio', 'date')
            ->add('fechaFin', 'date')
            ->add('horaInicio', 'time')
            ->add('horaFin', 'time')
            ->add('periodicidad')
            ->add('numeroGanadores')
            ->add('numeroProveedores')
            ->add('imagen')
            ->add('premio')
            ->add('valor')
            ->add('mostrarValor')
            ->add('estado')
            ->add('idAdministrador')
            ->add('idCiudad')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cpdg\UsuarioBundle\Entity\Eventos'
        ));
    }
}
