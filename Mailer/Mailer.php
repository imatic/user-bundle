<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Mailer;

use Imatic\Bundle\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Mailer
{
    private \Swift_Mailer $mailer;
    private UrlGeneratorInterface $router;
    private EngineInterface $templating;
    private array $resettingFromEmail;

    public function __construct(
        \Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        EngineInterface $templating,
        array $resettingFromEmail
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->resettingFromEmail = $resettingFromEmail;
    }

    public function sendResettingEmailMessage(UserInterface $user): void
    {
        $template = '@ImaticUser/Resetting/email.txt.twig';
        $url = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($template, [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);
        $this->sendEmailMessage($rendered, $this->resettingFromEmail, (string) $user->getEmail());
    }

    /**
     * @param string       $renderedTemplate
     * @param array|string $fromEmail
     * @param array|string $toEmail
     */
    protected function sendEmailMessage(string $renderedTemplate, $fromEmail, $toEmail): void
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = \explode("\n", \trim($renderedTemplate));
        $subject = \array_shift($renderedLines);
        $body = \implode("\n", $renderedLines);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $this->mailer->send($message);
    }
}
