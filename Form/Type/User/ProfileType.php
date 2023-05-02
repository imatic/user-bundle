<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildUserForm($builder, $options);

        $constraintsOptions = [
            'message' => 'imatic_user.current_password.invalid',
        ];

        if (!empty($options['validation_groups'])) {
            $constraintsOptions['groups'] = [\reset($options['validation_groups'])];
        }

        $builder->add('current_password', PasswordType::class, [
            'translation_domain' => 'ImaticUserBundle',
            'mapped' => false,
            'constraints' => [
                new NotBlank(),
                new UserPassword($constraintsOptions),
            ],
            'attr' => [
                'autocomplete' => 'current-password',
            ],
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'profile.edit.submit',
            'translation_domain' => 'ImaticUserBundle',
            'attr' => ['class' => 'btn-primary'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_token_id' => 'profile',
            'validation_groups' => ['Profile', 'Default'],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'user_profile';
    }

    protected function buildUserForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null, ['translation_domain' => 'ImaticUserBundle'])
            ->add('email', EmailType::class, ['translation_domain' => 'ImaticUserBundle']);
    }
}
