<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Mailer;

class NoopHtmlEmailBuilder implements HtmlEmailBuilder
{
    public function build(string $html): string
    {
        return $html;
    }
}
