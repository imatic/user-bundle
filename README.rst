================
ImaticUserBundle
================

TODO: description

*****
Usage
*****

TODO: usage/setup


************
Sonata roles
************

Configuration
=============

config.yml

.. sourcecode:: yaml

    sonata_admin:
        # ...
        security:
            handler: sonata.admin.security.handler.role
        extensions:
            imatic_admin.extension.security_extension:
                admins:
                    - app_example.admin.foo
                    # more admins..

Admin and role translations
===========================

- Admin
   - "Plural" in "VENDOR+BUNDLE+Bundle+ENTITY+Admin"
   - example: "AppUserBundleUserAdmin"
- Admin role
   - "Plural" in "VENDOR+BUNDLE+Bundle+ENTITY"
   - example: "AppUserBundleUser"
