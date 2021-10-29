<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Controller;

use Imatic\Bundle\UserBundle\Form\Type\User\ResettingFormType;
use Imatic\Bundle\UserBundle\Mailer\Mailer;
use Imatic\Bundle\UserBundle\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/resetting", name="fos_user_resetting_")
 */
class ResettingController extends AbstractController
{
    private int $retryTtl = 3600;
    private int $tokenTtl = 7200;

    /**
     * @Route("/request", methods={"GET"}, name="request")
     */
    public function requestAction()
    {
        return $this->render('@ImaticUser/Resetting/request.html.twig');
    }

    private function generateToken(): string
    {
        return \rtrim(\strtr(\base64_encode(\random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @Route("/send-email", methods={"POST"}, name="send_email")
     */
    public function sendEmailAction(Request $request, UserManager $userManager, Mailer $mailer)
    {
        $username = $request->request->get('username');

        $user = $userManager->findUserByUsernameOrEmail($username);

        if (null !== $user && !$user->isPasswordRequestNonExpired($this->retryTtl)) {
            if (!$user->isAccountNonLocked()) {
                new RedirectResponse($this->generateUrl('fos_user_resetting_request'));
            }

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->generateToken());
            }

            $mailer->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $userManager->updateUser($user);
        }

        return new RedirectResponse($this->generateUrl('fos_user_resetting_check_email', ['username' => $username]));
    }

    /**
     * @Route("/check-email", methods={"GET"}, name="check_email")
     */
    public function checkEmailAction(Request $request)
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            return new RedirectResponse($this->generateUrl('fos_user_resetting_request'));
        }

        return $this->render('@ImaticUser/Resetting/check_email.html.twig', [
            'tokenLifetime' => \ceil($this->retryTtl / 3600),
        ]);
    }

    /**
     * @Route("/reset/{token}", methods={"GET", "POST"}, name="reset")
     */
    public function resetAction(Request $request, $token, UserManager $userManager)
    {
        $user = $userManager->findUserByConfirmationToken($token);
        if (null === $user) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }

        if (!$user->isPasswordRequestNonExpired($this->tokenTtl)) {
            return new RedirectResponse($this->generateUrl('fos_user_resetting_request'));
        }

        $form = $this->createForm(ResettingFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setEnabled(true);
            $userManager->updateUser($user);

            return new RedirectResponse($this->generateUrl('fos_user_profile_show'));
        }

        return $this->render('@ImaticUser/Resetting/reset.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }
}
