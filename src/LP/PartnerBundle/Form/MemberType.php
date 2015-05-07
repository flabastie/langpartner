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
            ->add('name',           'text')
            ->add('firstName',      'text')
            ->add('dateBirth',      'date', array(
                        'required'  => true,
                        'widget'    =>'single_text',
                        'format'    => 'dd/MM/yyyy'))    
            ->add('telephone',      'text')
            ->add('telephoneBis',   'text')
            ->add('email',          'text')
            ->add('category',       'choice', array(
                    'choices'   => array(
                        'en'        => 'English', 
                        'fr'        => 'French'),
                        'required'  => true))
            ->add('membership',     'choice', array(
                    'choices'   => array(
                        'no'        => 'No', 
                        'yes'       => 'Yes', 
                        'pending'   => 'Pending'),
                        'required'  => true))
            ->add('status',         'choice', array(
                    'choices'   => array(
                        'Available'     => 'Available', 
                        'Ended'         => 'Ended', 
                        'New'           => 'New', 
                        'Not available' => 'Not available'),
                        'required'      => true))
            ->add('englishLevel',   'choice', array(
                    'choices'   => array(
                        'Beginner'          => 'Beginner', 
                        'Pre intermediate'  => 'Pre intermediate', 
                        'Intermediate'      => 'Intermediate', 
                        'Advanced'          => 'Advanced', 
                        'Mother tongue'     => 'Mother tongue'),
                        'required'          => true))
            ->add('frenchLevel',    'choice', array(
                    'choices'   => array(
                        'Beginner'          => 'Beginner', 
                        'Pre intermediate'  => 'Pre intermediate', 
                        'Intermediate'      => 'Intermediate', 
                        'Advanced'          => 'Advanced', 
                        'Mother tongue'     => 'Mother tongue'),
                        'required'  => true))
            ->add('profession',     'text')
            ->add('objective',      'textarea')
            ->add('dateStart',      'date', array(
                        'required' => true,
                        'widget' =>'single_text',
                        'format' =>'dd/MM/yyyy'))  
            ->add('dateEnd',        'date', array(
                        'required' => true,
                        'widget' =>'single_text',
                        'format' =>'dd/MM/yyyy'))  
            ->add('interests',    'choice', array(
                    'choices'   => array(
                        'travel'     => 'Travel', 
                        'cooking'    => 'Cooking', 
                        'cinema'     => 'Cinema', 
                        'music'      => 'Music', 
                        'sport'      => 'Sport', 
                        'reading'    => 'Reading', 
                        'literature' => 'Literature', 
                        'animals'    => 'Animals', 
                        'art'        => 'Art', 
                        'economics'  => 'Economics', 
                        'politics'   => 'Politics', 
                        'meeting'    => 'Meeting', 
                        'outing'     => 'Outing',
                        'interest14' => 'Interest 14',
                        'interest15' => 'Interest 15'),
                        'expanded' => true,
                        'multiple' => true))
            ->add('save',           'submit')
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
