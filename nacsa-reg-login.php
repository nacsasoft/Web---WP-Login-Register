<?php
/*
Plugin Name: Nacsa Login-Registration fom
Plugin URI: 
Description: Egyszerű regisztráció és belépés valamint egyedi oldal létrehozása. Belépés után átirányítás a www.myblog/$username -oldalra.
Version: 1.0
Author: Nacsasoft
Author URI: https://nacsasoft.hu
*/

// regisztrációs form létrehozása
function nacsa_registration_form() {
 
	// csak azok látják akik még nincsenek belépve
	if(!is_user_logged_in()) {
 
		global $nacsa_load_css;
 
		// true ha a css már be van töltve
		$nacsa_load_css = true;
 
		// regisztráció engedélyezve van a wp-ben?
		$registration_enabled = get_option('users_can_register');
 
		// csak akkor látható a regisztrációs form ha engedélyezve van a regelés a wp-ben
		if($registration_enabled) {
			$output = nacsa_registration_form_fields();
		} else {
			$output = __('Regisztráció tiltva van a WordPress-ben!');
		}
		return $output;
	}
}
add_shortcode('register_form', 'nacsa_registration_form');


// belépés form
function nacsa_login_form() {
 
	if(!is_user_logged_in()) {
 
		global $nacsa_load_css;
 
		// true ha a css már be van töltve
		$nacsa_load_css = true;
 
		$output = nacsa_login_form_fields();
	} else {
		// itt megjeleníthetünk néhány bejelentkezett felhasználói információt....
		// $output = 'Információ a már bejelentkezett felhasználónak...';
	}
	return $output;
}
add_shortcode('login_form', 'nacsa_login_form');


// regisztrációs form felépítése HTML-ben
function nacsa_registration_form_fields() {
 
    // így nem kell bíbelődni az echo-kal, egy az egyben kinyomjuk
	ob_start(); ?>	
		<h3 class="nacsa_header"><?php _e('Regisztráció'); ?></h3>
 
		<?php 
		// az esetleges hibákat itt fogjuk megjeleníteni
		nacsa_show_error_messages(); ?>
 
		<form id="nacsa_registration_form" class="nacsa_form" action="" method="POST">
			<fieldset>
				<p>
					<label for="nacsa_user_Login"><?php _e('Név'); ?></label>
					<input name="nacsa_user_login" id="nacsa_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="nacsa_user_email"><?php _e('Email'); ?></label>
					<input name="nacsa_user_email" id="nacsa_user_email" class="required" type="email"/>
				</p>
                <p>
					<label for="nacsa_user_phone"><?php _e('Telefonszám'); ?></label>
					<input name="nacsa_user_phone" id="nacsa_user_phone" class="required" type="text"/>
				</p>
				<p>
					<label for="nacsa_user_webpage"><?php _e('Weboldal'); ?></label>
					<input name="nacsa_user_webpage" id="webpage" class="required" type="text"/>
				</p>
                <p>
					<label for="password"><?php _e('Jelszó'); ?></label>
					<input name="nacsa_user_pass" id="password" class="required" type="password"/>
				</p>
				<p>
					<label for="password_again"><?php _e('Jelszó újra'); ?></label>
					<input name="nacsa_user_pass_confirm" id="password_again" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="nacsa_register_nonce" value="<?php echo wp_create_nonce('nacsa-register-nonce'); ?>"/>
					<input type="submit" value="<?php _e('Regisztráció'); ?>"/>
				</p>
			</fieldset>
		</form>
	<?php
    // kiküldjük a full html tartalmat
	return ob_get_clean();
}


// belépés form összerakása
function nacsa_login_form_fields() {
 
	ob_start(); ?>
		<h3 class="nacsa_header"><?php _e('Belépés'); ?></h3>
 
		<?php
		// az esetleges hibákat itt fogjuk megjeleníteni
		nacsa_show_error_messages(); ?>
 
		<form id="nacsa_login_form"  class="nacsa_form"action="" method="POST">
			<fieldset>
				<p>
					<label for="nacsa_user_Login">Név</label>
					<input name="nacsa_user_login" id="nacsa_user_login" class="required" type="text"/>
				</p>
				<p>
					<label for="nacsa_user_pass">Jelszó</label>
					<input name="nacsa_user_pass" id="nacsa_user_pass" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="nacsa_login_nonce" value="<?php echo wp_create_nonce('nacsa-login-nonce'); ?>"/>
					<input id="nacsa_login_submit" type="submit" value="Belépés"/>
				</p>
			</fieldset>
		</form>
	<?php
    // kiküldjük a full html tartalmat
	return ob_get_clean();
}


// belépéskor megadott adatok ellenörzése 
function nacsa_login_member() {
 
	if(isset($_POST['nacsa_user_login']) && wp_verify_nonce($_POST['nacsa_login_nonce'], 'nacsa-login-nonce')) {
 
        // lekérjük a felhasználó egyedi azonosítóját (ha már beregelt!)
		$user = get_userdatabylogin($_POST['nacsa_user_login']);
 
		if(!$user) {
			// ha nincs ilyen felhasználó akkor jelezni kell
			nacsa_errors()->add('empty_username', __('Nincs ilyen felhasználó!'));
		}
 
		if(!isset($_POST['nacsa_user_pass']) || $_POST['nacsa_user_pass'] == '') {
			// ha nincs jelszó megadva
			nacsa_errors()->add('empty_password', __('Kérem adja meg jelszavát a belépéshez!'));
		}
 
		// le kell ellenőrizni hogy a megadott jelszó egyezik-e a beírt felhasználóhoz tartozóval
		if(!wp_check_password($_POST['nacsa_user_pass'], $user->user_pass, $user->ID)) {
			// a jelszó nem egyezik a regeléskor megadottal
			nacsa_errors()->add('empty_password', __('Érvénytelen jelszó!'));
		}
 
		// visszatérünk a hibákkal (ha vannak)
		$errors = nacsa_errors()->get_error_messages();
 
		/* 
        *   Ha nincs hiba akkor mehet a belépés.
        *   Itt kell ellenőrizni hogy van-e már a felhasználónak saját oldala.
        *   Ha van akkor átirányítás oda, ha nincs akkor létre kell hozni és utána 
        *   átirányítani oda!
        */ 
		if(empty($errors)) {
 
			wp_setcookie($_POST['nacsa_user_login'], $_POST['nacsa_user_pass'], true);
			wp_set_current_user($user->ID, $_POST['nacsa_user_login']);	
			do_action('wp_login', $_POST['nacsa_user_login']);
 
			wp_redirect(home_url()); exit;
		}
	}
}
add_action('init', 'nacsa_login_member');


// új felhasználó regisztrálása, a megadott adatok ellenörzése majd belépés
function nacsa_add_new_member() {
    if (isset( $_POST["nacsa_user_login"] ) && wp_verify_nonce($_POST['nacsa_register_nonce'], 'nacsa-register-nonce')) {
      $user_login		= $_POST["nacsa_user_login"];	
      $user_email		= $_POST["nacsa_user_email"];
      $user_phone 	    = $_POST["nacsa_user_phone"];
      $user_webpage	 	= $_POST["nacsa_user_webpage"];
      $user_pass		= $_POST["nacsa_user_pass"];
      $pass_confirm 	= $_POST["nacsa_user_pass_confirm"];

      // kell a felhasználónév ellenörzéshez
      require_once(ABSPATH . WPINC . '/registration.php');

      if(username_exists($user_login)) {
          // ez a felhasználónév már regelve van
          nacsa_errors()->add('username_unavailable', __('Ez a felhasználónév már használatban van!'));
      }
      if(!validate_username($user_login)) {
          // érvénytelen felhasználónév
          nacsa_errors()->add('username_invalid', __('A megadott felhasználónév érvénytelen!'));
      }
      if($user_login == '') {
          // felhasználónév mező üres....
          nacsa_errors()->add('username_empty', __('Kérem adja meg felhasználónevét!'));
      }
      if(!is_email($user_email)) {
          //érvénytelen email cím
          nacsa_errors()->add('email_invalid', __('Érvénytelen email cím!'));
      }
      if(email_exists($user_email)) {
          //ezen az email címen már regisztráltak
          nacsa_errors()->add('email_used', __('Ezzel az email címmel már regisztráltak!'));
      }
      if($user_pass == '') {
          // jelszó nem lehet üres...
          nacsa_errors()->add('password_empty', __('Kérem adjon meg egy jelszót!'));
      }
      if($user_pass != $pass_confirm) {
          // jelszavak nem egyeznek
          nacsa_errors()->add('password_mismatch', __('A megadott jelszavak nem egyeznek!'));
      }

      // begyüjtjük a hibákat (ha vannak)
      $errors = nacsa_errors()->get_error_messages();

      // ha nincs hiba, létre lehet hozni a felhasználót
      if(empty($errors)) {

          $new_user_id = wp_insert_user(array(
                  'user_login'		=> $user_login,
                  'user_pass'	 	=> $user_pass,
                  'user_email'		=> $user_email,
                  'user_url'		=> $user_webpage,
                  //'user_phone'		=> $user_phone,
                  'user_registered'	=> date('Y-m-d H:i:s'),
                  'role'			=> 'subscriber'
              )
          );
          if($new_user_id) {
              // telefonszámot nem lehet a users táblában rögzíteni , ezért kell a 
              // usermeta -táblát használni erre a célra!
              add_user_meta( $new_user_id, 'user_phone', $user_phone );

              // admin értesítése az új regisztrációról
              wp_new_user_notification($new_user_id);

              // az új felhasználó beléptetése
              wp_setcookie($user_login, $user_pass, true);
              wp_set_current_user($new_user_id, $user_login);	
              do_action('wp_login', $user_login);

              // belépés után átirányítás....
              // saját oldalára kellene itt is sztem...
              // oldal létrehozása a felhasználónévvel
                $my_post = array(
                    'post_title'    => wp_strip_all_tags( $user_login ),
                    'post_content'  => "<h1>$user_login oldala</h1>",
                    'post_status'   => 'publish',
                    'post_author'   => 1,
                    'post_type'     => 'page'
                );
                // oldal elhelyezése az adatbázisban
                wp_insert_post( $my_post );

                // átirányítás az új oldalra
                wp_redirect(home_url("/$user_login")); exit;
          }

      }

  }
}
add_action('init', 'nacsa_add_new_member');


// hibák követése
function nacsa_errors(){
    static $wp_error;
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}


// formokról érkező esetleges hibaüzenetek megjelenítése
function nacsa_show_error_messages() {
	if($codes = nacsa_errors()->get_error_codes()) {
		echo '<div class="nacsa_errors">';
		    // végig kell menni az összes hibaüzeneten és ki kell listázni ha több van
		   foreach($codes as $code){
		        $message = nacsa_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Hiba') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}


// formok stílusának beregisztrálása (forms.css)
function nacsa_register_css() {
	wp_register_style('nacsa-form-css', plugin_dir_url( __FILE__ ) . '/css/forms.css');
}
add_action('init', 'nacsa_register_css');


// stílus fájlunk betöltése amikor szükséges!
// csak akkor töltjük be ha jelen van a SHORTCODE !!
function nacsa_print_css() {
	global $nacsa_load_css;
 
	// a globális változónk akkor TRUE ha a shortcode használatban van a lapon vagy bejegyzésben
	if ( ! $nacsa_load_css )
		return; // shortcode nincs jelen így nincs szükség a stílusfájlra sem...
 
	wp_print_styles('nacsa-form-css');
}
add_action('wp_footer', 'nacsa_print_css');



// extra phone mezőt fel kell venni mert bekérjük regisztráláskor
/*
function my_show_extra_profile_fields( $user ) { ?>
    <h3>Extra profile information</h3>
        <table class="form-table">
            <tr>
                <th><label for="phone">Telefonszám</label></th>
                <td>
                <input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" class="regular-text" /><br />
                    <span class="description">Kérem adja meg telefonszámát.</span>
                </td>
            </tr>
        </table>
    <?php }
add_action( 'show_user_profile', 'my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'my_show_extra_profile_fields' );
*/


