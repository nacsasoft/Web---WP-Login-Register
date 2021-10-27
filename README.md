# Web---WP-Login-Register
<h1>Egyedi regisztráció és beléptetés WordPress-hez.</h1>
<h3><b><u>Plugin működése:</u></b></h3>
<p>Következő rövidkódok (shortcode) használhatók:<br></p>
<p><b>[register_form]</b></p>
    <ul>
        <li>regisztrációs form megjelenítése</li>
        <li>mezők:</li>
            <ul>
                <li>Név</li>
                <li>Email</li>
                <li>Telefonszám</li>
                <li>Weboldal</li>
                <li>Jelszó</li>
                <li>Jelszó mégegyszer</li>
            </ul>
        <li>sikeres regisztráció után:</li>
            <ul>
                <li>új oldal létrehozása a domain.hu/felhasznalo -névvel</li>
                <li>új oldal tartalma:</li>
                    <ul>
                        <li>Saját profil (domain.hu/profil-username)</li>
                        <li>Főoldal (domain.hu)</li>
                    </ul>
                <li>átirányítás az új oldalra</li>
            </ul>
    </ul>
<br>    
<p><b>[login_form]</b></p>
    <ul>
        <li>belépés form megjelenítése</li>
        <li>Ha az admin lép be akkor Őt a WP admin felületére irányítja át</li>
        <li>Ha nem az admin lép be akkor átirányítás a saját oldalra (domain.hu/username) ami tartalmazza a következő linkeket:</li>
            <ul>
                <li>Saját profil (domain.hu/profil-username)</li>
                <li>Főoldal (domain.hu)</li>
            </ul>
    </ul>

