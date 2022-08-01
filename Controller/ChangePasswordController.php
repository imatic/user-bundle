<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Controller;

use Imatic\Bundle\UserBundle\Form\Type\User\ChangePasswordType;
use Imatic\Bundle\UserBundle\Manager\UserManager;
use Imatic\Bundle\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="fos_user_")
 */
class ChangePasswordController extends AbstractController
{
    /**
     * @Route("/change-password", methods={"GET", "POST"}, name="change_password")
     */
    public function changePasswordAction(Request $request, UserManager $userManager)
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return $this->createAccessDeniedException();
        }

        $form = $this->createForm(ChangePasswordType::class, $user, ['data_class' => $user::class]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->updateUser($user);

            return new RedirectResponse($this->generateUrl('fos_user_profile_show'));
        }

        return $this->render('@ImaticUser/ChangePassword/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
