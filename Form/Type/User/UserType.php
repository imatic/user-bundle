<?php

namespace Imatic\Bundle\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @var string
     */
    private $userClass;

    public function __construct($userClass)
    {
        $this->userClass = $userClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('plainPassword', 'password', ['label' => 'Password', 'required' => false])
            ->add('email')
            ->add('enabled')
            ->add('groups')
            ->add('save', 'submit', ['attr' => ['class' => 'btn-primary']]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'ImaticUserBundleUser',
            'data_class' => $this->userClass,
            'validation_groups' => 'Profile',
            'empty_data' => function () {
                    return new $this->userClass;
                }
        ));
    }

    public function getName()
    {
        return 'imatic_user_user';
    }
}
