# UPGRADE FROM 3.x to 4.0

Since Symfony doesn't support bundle inheritance anymore (https://symfony.com/doc/4.1/bundles/inheritance.html). It's necessary to copy templates inside this bundle into FOSUserBundle dir to override them.
```
$ ln -s vendor/imatic/user-bundle/Resources/views/ templates/bundles/FOSUserBundle
```