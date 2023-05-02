# UPGRADE FROM 5.x to 6.0

- Route names: removed prefix `fos_` in `fos_user_*` routes.
- Service names changed from 
    - `imatic_user.*` to `Imatic\Bundle\*`
- Form types block prefix removed `fos_` prefix.
- Translation domain name `FOSUserBundle` -> `ImaticUserBundle`.
- Twig template blocks removed `fos_` prefix.
- PHP8 updates
    - Added types.
    - Added constructor property promotion.
    - Updated annotations to PHP attributes.
