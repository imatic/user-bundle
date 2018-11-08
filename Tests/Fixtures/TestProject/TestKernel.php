<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject;

use Imatic\Bundle\TestingBundle\Test\TestKernel as BaseTestKernel;

class TestKernel extends BaseTestKernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $parentBundles = parent::registerBundles();

        $bundles = [
            new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new \Imatic\Bundle\ViewBundle\ImaticViewBundle(),

            new \Imatic\Bundle\UserBundle\ImaticUserBundle(),
            new \Imatic\Bundle\UserBundle\Tests\Fixtures\TestProject\ImaticUserBundle\AppImaticUserBundle(),
        ];

        return \array_merge($parentBundles, $bundles);
    }
}
