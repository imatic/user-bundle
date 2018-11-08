<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Form\Type\User;

use FOS\UserBundle\Form\Type\ChangePasswordFormType as BaseChangePasswordFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordType extends BaseChangePasswordFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('submit', SubmitType::class, [
            'label' => 'change_password.submit',
            'translation_domain' => 'FOSUserBundle',
            'attr' => ['class' => 'btn-primary'],
        ]);
    }
}
