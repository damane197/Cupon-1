<?php

/*
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * Este archivo pertenece a la aplicación de prueba Cupon.
 * El código fuente de la aplicación incluye un archivo llamado LICENSE
 * con toda la información sobre el copyright y la licencia.
 */

namespace AppBundle\Form\Extranet;

use AppBundle\Listener\OfertaTypeListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para crear y manipular entidades de tipo Oferta.
 * Como se utiliza en la extranet, algunas propiedades de la entidad
 * no se incluyen en el formulario.
 */
class OfertaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('condiciones')
            ->add('foto', 'Symfony\Component\Form\Extension\Core\Type\FileType', array('required' => false))
            ->add('precio', 'Symfony\Component\Form\Extension\Core\Type\MoneyType')
            ->add('descuento', 'Symfony\Component\Form\Extension\Core\Type\MoneyType')
            ->add('umbral')
            ->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => 'Guardar cambios',
                'attr' => array('class' => 'boton'),
            ))
        ;

        // El formulario es diferente según se utilice en la acción 'new' o en la acción 'edit'
        // Para determinar en qué acción estamos, se comprueba si el atributo `id` del objeto
        // es null, en cuyo caso estamos en la acción 'new'
        //
        // La acción `new` muestra un checkbox que no corresponde a ninguna propiedad de la entidad
        // del modelo. Se añade dinámicamente y se indica que no es parte del modelo (con la propiedad
        // `property_path`).
        if (null == $options['data']->getId()) {
            $builder->add('acepto', 'checkbox', array('mapped' => false, 'required' => false));

            // registrar el listener que validará el campo 'acepto' añadido anteriormente
            $listener = new OfertaTypeListener();
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($listener, 'preSubmit'));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Oferta',
        ));
    }

    public function getBlockPrefix()
    {
        return 'oferta_tienda';
    }
}