UPGRADE Z VERZE 2.2
===================

Entity
------

Tahle verze může vyžadovat ručně vytvořenou migraci pro tabulky s uživateli a skupinami.
Entity pro uživatele a s kupiny je třeba extendovat z ``Imatic\Bundle\UserBundle\Model\User``
a ``Imatic\Bundle\UserBundle\Model\Group``. Zde je už nastaveno také mapování mezi ``User``
a ``Group`` a musí být odebráno z aplikačních entít. Dále je nutno v ``app/config.yml`` nastavit:

    doctrine:
        orm:
            resolve_target_entities:
                Imatic\Bundle\UserBundle\Model\UserInterface: App\Bundle\UserBundle\Entity\User # resp. aplikační třída pro uživatele
                Imatic\Bundle\UserBundle\Model\GroupInterface: App\Bundle\UserBundle\Entity\Group # resp. aplikační třída pro skupinu


