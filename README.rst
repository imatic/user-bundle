================
ImaticUserBundle
================

*****
Roles
*****

Global roles
============

Configuration
-------------

.. sourcecode:: yaml

    # app/config/security.yml

    security:
        # ...

        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

Translation
-----------

Global roles are translated using the "roles" domain.

Model roles
===========

Configuration
-------------

.. sourcecode:: yaml

    # app/config/config.yml

    # Imatic user
    imatic_user:
        # ...
        security:
            role:
                model:
                    namespaces:
                        includes:
                            - App
                        excludes:
                            - AppExampleBundle\Entity\Example   # exclude single entity
                            - AppFooBundle\Entity               # exclude all entities


*************
Installation
*************

1. Generate AppUserBundle
=========================

Generate and enable a local bundle called AppUserBundle.

This bundle will contain user-related entities and fixtures.


2. Download ImaticUserBundle using composer
===========================================

.. sourcecode:: yaml

    "require": {
        # ...
        "imatic/user-bundle": "^3.0"
    }

3. Enable the bundle
====================

.. sourcecode:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new FOS\UserBundle\FOSUserBundle(),
            new Imatic\Bundle\UserBundle\ImaticUserBundle(),
        );
    }

4. Configure the bundles
========================

.. sourcecode:: yaml

    # app/config/config.yml

    imports:
        # ...
        - { resource: "@ImaticUserBundle/Resources/config/config.yml" }
        - { resource: "@AppUserBundle/Resources/config/config.yml" }

    doctrine:
        # ...
        orm:
            #...
            resolve_target_entities:
                # UserBundle
                Imatic\Bundle\UserBundle\Model\UserInterface: ApUserBundle\Entity\User
                Imatic\Bundle\UserBundle\Model\GroupInterface: AppUserBundle\Entity\Group

    # Imatic user
    imatic_user:
        entities:
            user: AppUserBundle\Entity\User
            group: AppUserBundle\Entity\Group
        security:
            role:
                model:
                    namespaces:
                        includes: ~
                        excludes: ~
                hierarchy: ~

5. Configure the security
=========================

.. sourcecode:: yaml

    # app/config/security.yml

    security:
        encoders:
            Symfony\Component\Security\Core\User\UserInterface: sha512

        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

        providers:
            imatic_user_provider:
                id: imatic_user.user_provider.username

        firewalls:
            dev:
                pattern:  ^/(_(profiler|wdt)|css|images|js)/
                security: false

            main:
                pattern: ^/
                form_login:
                    provider: imatic_user_provider
                    csrf_token_generator: security.csrf.token_manager
                logout:       true
                anonymous:    true
                switch_user:  true

        access_control:
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/, role: IS_AUTHENTICATED_FULLY }

6. Configure the routing
========================

.. sourcecode:: yaml

    # app/config/routing.yml

    imatic_user:
        resource: "@ImaticUserBundle/Resources/config/routing.yml"
