<?php

namespace LP\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserFullType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',   'text')
            ->add('useremail',  'text')
            ->add('password',   'text')
            ->add('roles',      'choice', array(
                    'choices'   => array(
                        'ROLE_USER'  => 'Activated', 
                        'ROLE_ADMIN' => 'Admin', 
                        'ROLE_NONE'  => 'Disactivated'), 
                        'expanded'   => true,
                        'multiple'   => true,
                        'required'   => true))
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LP\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lp_userbundle_user';
    }
}
