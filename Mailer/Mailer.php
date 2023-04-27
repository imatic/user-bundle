<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Mailer;

use Imatic\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class Mailer
{
    private \Symfony\Component\Mailer\MailerInterface $mailer;
    private UrlGeneratorInterface $router;
    private Environment $twig;
    private HtmlEmailBuilder $htmlEmailBuilder;
    private string $resettingFromEmail;
    private string $resettingFromSenderName;

    public function __construct(
        \Symfony\Component\Mailer\MailerInterface $mailer,
        UrlGeneratorInterface                     $router,
        Environment                               $twig,
        HtmlEmailBuilder                          $htmlEmailBuilder,
        string                                    $resettingFromEmail,
        string                                    $resettingFromSenderName
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->htmlEmailBuilder = $htmlEmailBuilder;
        $this->resettingFromEmail = $resettingFromEmail;
        $this->resettingFromSenderName = $resettingFromSenderName;
    }

    public function sendResettingEmailMessage(UserInterface $user): void
    {
        $template = '@ImaticUser/Resetting/email.txt.twig';
        $url = $this->router->generate('user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage($template, $context, $this->resettingFromEmail, (string) $user->getEmail(), $this->resettingFromSenderName);
    }

    private function sendMessage(string $templateName, array $context, string $fromEmail, string $toEmail, string $senderName): void
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($this->twig->getTemplateClass($templateName), $templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);
        $address = new \Symfony\Component\Mime\Address($fromEmail, $senderName);

        $message = new \Symfony\Component\Mime\Email();
        $message
            ->subject($subject)
            ->from($address)
            ->to($toEmail);

        if (!empty($htmlBody)) {
            $htmlBody = $this->htmlEmailBuilder->build($htmlBody);
            $message
                ->text($htmlBody, 'text/html')
                ->html($textBody, 'text/plain');
        } else {
            $message->text($textBody);
        }
        $this->mailer->send($message);
    }
}
