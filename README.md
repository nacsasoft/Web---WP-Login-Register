# Web---WP-Login-Register
## Egyedi regisztráció és beléptetés WordPress-hez.
<u>**Plugin működése:**</u>

Következő rövidkódok (shortcode) használhatók:

**[register_form]**

-   regisztrációs form megjelenítése
    -   mezők:
        -   Név
        -   Email
        -   Telefonszám
        -   Weboldal
        -   Jelszó
        -   Jelszó mégegyszer
    -   sikeres regisztráció után:
        -   új oldal létrehozása a domain.hu/username -névvel
        -   új profil oldal létrehozása a fenti adatokkal
        -   új oldal tartalma:
            -   Üdvözlő szöveg
            -   Link: Saját profil (domain.hu/profil-username)
            -   Link: Főoldal (domain.hu)
        -   átirányítás az új oldalra

    
**[login_form]**

-   belépés form megjelenítése
    -   Ha az admin lép be akkor Őt a WP admin felületére irányítja át
    -   Ha nem az admin lép be akkor átirányítás a saját oldalra (domain.hu/username) ami tartalmazza a következőket:
        -   Üdvözlő szöveg
        -   Link: Saját profil (domain.hu/profil-username)
        -   Link: Főoldal (domain.hu)
