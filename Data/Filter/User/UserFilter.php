<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Data\Filter\User;

use Imatic\Bundle\DataBundle\Data\Query\DisplayCriteria\Filter;
use Imatic\Bundle\DataBundle\Data\Query\DisplayCriteria\Filter as FilterRule;

class UserFilter extends Filter
{
    protected function configure(): void
    {
        $this->setTranslationDomain('ImaticUserBundleUser');

        $this
            ->add(new FilterRule\TextRule('username'));
    }
}
