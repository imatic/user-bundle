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

Sonata model and admin roles
============================

Configuration
-------------

.. sourcecode:: yaml

    # app/config/config.yml

    sonata_admin:
        # ...
        security:
            handler: sonata.admin.security.handler.role
        extensions:
            imatic_admin.extension.security_extension:
                admins:
                    - app_example.admin.foo
                    # more admins..

Translation
-----------

- Admin
   - "Plural" in "VENDOR+BUNDLE+Bundle+ENTITY+Admin"
   - example: "AppUserBundleUserAdmin"
- Admin role
   - "Plural" in "VENDOR+BUNDLE+Bundle+ENTITY"
   - example: "AppUserBundleUser"



*************
Installation
*************

1. Setup dependencies
=====================

This bundle has the following dependencies whose installation is not covered by this manual.

 - SonataAdminBundle
 - ImaticViewBundle

2. Generate AppUserBundle
=========================

 - generate AppUserBundle
 - create "src/App/Bundle/UserBundle/Resources/config/config.yml" with the following contents:

.. sourcecode:: yaml

    parameters:
        imatic_user.entity.user.class:      'App\Bundle\UserBundle\Entity\User'
        imatic_user.entity.group.class:     'App\Bundle\UserBundle\Entity\Group'

3. Download ImaticUserBundle using compoer
==========================================

.. sourcecode:: yaml

    "require": {
        # ...
        "imatic/user-bundle": "dev-master"
    }

4. Enable the bundle
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

5. Configure the bundles
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
                Imatic\Bundle\UserBundle\Model\UserInterface: App\Bundle\UserBundle\Entity\User
                Imatic\Bundle\UserBundle\Model\GroupInterface: App\Bundle\UserBundle\Entity\Group

    # Imatic user
    imatic_user:
        entities:
            user: App\Bundle\UserBundle\Entity\User
            group: App\Bundle\UserBundle\Entity\Group
        security:
            role:
                model:
                    namespaces:
                        includes: ~
                        excludes: ~
                hierarchy: ~
                sonata: ~

6. Configure the security
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
                    csrf_provider: form.csrf_provider
                logout:       true
                anonymous:    true
                switch_user:  true

        access_control:
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/, role: IS_AUTHENTICATED_FULLY }

7. Configure the routing
========================

.. sourcecode:: yaml

    # app/config/routing.yml

    imatic_user:
        resource: "@ImaticUserBundle/Resources/config/routing.yml"
