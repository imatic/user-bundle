<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Mailer;

use Imatic\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class Mailer
{
    private \Swift_Mailer $mailer;
    private UrlGeneratorInterface $router;
    private Environment $twig;
    private HtmlEmailBuilder $htmlEmailBuilder;
    private array $resettingFromEmail;

    public function __construct(
        \Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        Environment $twig,
        HtmlEmailBuilder $htmlEmailBuilder,
        array $resettingFromEmail
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->htmlEmailBuilder = $htmlEmailBuilder;
        $this->resettingFromEmail = $resettingFromEmail;
    }

    public function sendResettingEmailMessage(UserInterface $user): void
    {
        $template = '@ImaticUser/Resetting/email.txt.twig';
        $url = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage($template, $context, $this->resettingFromEmail, (string) $user->getEmail());
    }

    private function sendMessage(string $templateName, array $context, $fromEmail, $toEmail): void
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($this->twig->getTemplateClass($templateName), $templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $htmlBody = $this->htmlEmailBuilder->build($htmlBody);
            $message
                ->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }
}
