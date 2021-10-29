<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("", name="fos_user_security_")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", methods={"GET", "POST"}, name="login")
     */
    public function loginAction(Request $request, CsrfTokenManagerInterface $tokenManager = null)
    {
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null;
        }

        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $tokenManager
            ? $tokenManager->getToken('authenticate')->getValue()
            : null;

        return $this->render('@ImaticUser/Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ]);
    }

    /**
     * @Route("/login_check", methods={"POST"}, name="check")
     */
    public function checkAction(): void
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    /**
     * @Route("/logout", methods={"GET", "POST"}, name="logout")
     */
    public function logoutAction(): void
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
