<?php

namespace LP\PartnerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhoneCallType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateCall', 'date', array(
                'required' => false,
                'widget' =>'single_text',
                'format' =>'dd/MM/yyyy'))
            ->add('noteCall')
           // ->add('member')
           // ->add('user')
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LP\PartnerBundle\Entity\PhoneCall'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lp_partnerbundle_phonecall';
    }
}
