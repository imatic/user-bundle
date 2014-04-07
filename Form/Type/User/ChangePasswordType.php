<?php

namespace Imatic\Bundle\UserBundle\Form\Type\User;

use FOS\UserBundle\Form\Type\ChangePasswordFormType as BaseChangePasswordFormType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordType extends BaseChangePasswordFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('submit', 'submit', [
            'label' => 'change_password.submit',
            'translation_domain' => 'FOSUserBundle',
            'attr' => ['class' => 'btn-primary']
        ]);
    }

    public function getName()
    {
        return 'imatic_user_change_password';
    }
}
