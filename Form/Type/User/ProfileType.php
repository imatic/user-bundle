<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Form\Type\User;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('submit', SubmitType::class, [
            'label' => 'profile.edit.submit',
            'translation_domain' => 'FOSUserBundle',
            'attr' => ['class' => 'btn-primary'],
        ]);
    }

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildUserForm($builder, $options);
    }

    public function getParent()
    {
        return BaseProfileFormType::class;
    }
}
