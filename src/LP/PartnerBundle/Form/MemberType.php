<?php

namespace LP\PartnerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MemberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('firstName', 'text')
            ->add('dateBirth', 'date', array(
                'required' => true,
                'widget' =>'single_text',
                'format' => 'dd/MM/yyyy'))    
            ->add('telephone', 'text')
            ->add('telephoneBis', 'text')
            ->add('email', 'text')
            ->add('category', 'choice', array(
                'choices'   => array('en' => 'English', 'fr' => 'French'),
                'required'  => true))
            ->add('membership', 'choice', array(
                'choices'   => array('no' => 'no', 'yes' => 'yes', 'pending' => 'pending'),
                'required'  => true))
            ->add('status', 'choice', array(
                'choices'   => array('available' => 'available', 'ended' => 'ended', 'new' => 'new', 'not available' => 'not available'),
                'required'  => true))
            ->add('englishLevel', 'choice', array(
                'choices'   => array('debutant' => 'Débutant', 'faux_debutant' => 'Faux Débutant', 'intermediaire' => 'Intermédiaire', 'avance' => 'Avancé', 'langue_maternelle' => 'Langue Maternelle'),
                'required'  => true))
            ->add('frenchLevel', 'choice', array(
                'choices'   => array('debutant' => 'Débutant', 'faux_debutant' => 'Faux Débutant', 'intermediaire' => 'Intermédiaire', 'avance' => 'Avancé', 'langue_maternelle' => 'Langue Maternelle'),
                'required'  => true))
            ->add('profession', 'text')
            ->add('objective', 'textarea')
            ->add('dateStart', 'date', array(
                'required' => true,
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy'))  
            ->add('dateEnd', 'date', array(
                'required' => true,
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy'))  
            ->add('interests', 'entity', array(
                'class'    => 'LPPartnerBundle:Interest',
                'property' => 'name',
                'expanded' => true,
                'multiple' => true))
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LP\PartnerBundle\Entity\Member'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lp_partnerbundle_member';
    }
}
