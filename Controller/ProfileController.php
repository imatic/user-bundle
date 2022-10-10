<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Controller;

use Imatic\Bundle\UserBundle\Form\Type\User\ProfileType;
use Imatic\Bundle\UserBundle\Manager\UserManager;
use Imatic\Bundle\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="user_profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="show")
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('@ImaticUser/Profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit", methods={"GET", "POST"}, name="edit")
     */
    public function editAction(Request $request, UserManager $userManager)
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            $this->createAccessDeniedException();
        }

        $form = $this->createForm(ProfileType::class, $user, ['data_class' => $user::class]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->updateUser($user);

            return new RedirectResponse($this->generateUrl('user_profile_show'));
        }

        return $this->render('@ImaticUser/Profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
