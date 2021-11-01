<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Mailer;

interface HtmlEmailBuilder
{
    public function build(string $html): string;
}
