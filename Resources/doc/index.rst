Instalace
=========
Pomocí composer:

    composer require imatic/user-bundle 2.3.*


Konfigurace
===========
Přehled konfigurace je dostupní cez:

    app/console config:dump-reference imatic_user


Povinně je potřebné nastavit entity pro užívatela a skupinu:

    # app/config.yml
    imatic_user:
        entities:
            user: App\Bundle\CoreBundle\Entity\User
            group: App\Bundle\CoreBundle\Entity\Group


V nastavení ``security.yml`` používat mezi ``providers``:

* ``imatic_user.user_provider.username``,
* ``imatic_user.user_provider.email_username``

