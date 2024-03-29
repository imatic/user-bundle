<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => ['translation_domain' => 'ImaticUserBundle'],
                'first_options' => ['label' => 'form.new_password'],
                'second_options' => ['label' => 'form.new_password_confirmation'],
                'invalid_message' => 'imatic_user.password.mismatch',
            ])
            ->add('email')
            ->add('enabled')
            ->add('groups')
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ImaticUserBundleUser',
            'data_class' => $this->userClass,
            'validation_groups' => function (FormInterface $form) {
                $user = $form->getData();
                if ($user->getId()) {
                    return ['Profile'];
                }
                return ['Profile', 'ChangePassword'];
            },
            'empty_data' => function () {
                return new $this->userClass();
            },
        ]);
    }
}
