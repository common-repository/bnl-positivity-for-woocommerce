<?php /*
@package   bnl-positivity-for-woocommerce
@author    Archa Multimedia Studios <info@archaweb.com>
@link      http://www.archaweb.com/prodotto/bnl-positivity-per-woocommerce/
@copyright 2013

@wordpress-plugin
	Plugin Name: BNL Positivity for WooCommerce
Plugin URI: http://www.archaweb.com/prodotto/bnl-positivity-per-woocommerce/
Description: Aggiunge la voce BNL Positivity fra i metodi di pagamento nella sezione Cassa di WooCommerce
Version: 3.1
Author: Archa Multimedia Studios
Author URI: http://www.archaweb.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: bnl-positivity-for-woocommerce
Domain Path: /languages

Versions compatibility:
Plugin:	Woocommerce:
3.1		3.1.0 (and backward to 2.1.9)
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('BNLP_RCHA_WOOCOMMERCE_MAX_VERSION', '3.1.0');

/*
STRING TRANSLATION
*/
$wpml_LANGUAGE = '';
if (defined('ICL_LANGUAGE_CODE')) {
	$wpml_LANGUAGE = ICL_LANGUAGE_CODE;
} else if (isset($_GET['lang']) && !empty($_GET['lang']) && strlen($_GET['lang'])==2) {
	$wpml_LANGUAGE = $_GET['lang'];
} else {
	$wpml_LANGUAGE = substr(get_locale(), 0, 2);
}

if (empty($wpml_LANGUAGE)) {
	$wpml_LANGUAGE = apply_filters( 'wpml_current_language', NULL );
}

switch ($wpml_LANGUAGE) {
	case '':
		define('BNLP_RCHA_LANGUAGE', 'IT');
		break;
	case 'it':
		define('BNLP_RCHA_LANGUAGE', 'IT');
		break;
	case 'de':
		define('BNLP_RCHA_LANGUAGE', 'DE');
		break;
	case 'en':
		define('BNLP_RCHA_LANGUAGE', 'EN');
		break;
	default:
		define('BNLP_RCHA_LANGUAGE', 'EN');
}


// Function that outputs the contents of the dashboard widget
function bnlp_rcha_dashboard_widget_function( $post, $callback_args ) {
	echo( '<center>' . __( '<div style="padding: 15px; border: 1px solid transparent; border-radius: 4px; color: #8a6d3b; background-color: #fcf8e3; border-color: #faebcc; max-width: 95%;">Thank you for installing<br />Plugin BNL Positivity for WooCommerce.<br /><br />Get your license and link it with few clicks<br />at your BNL Positivity account<br /><br /><a class="button" href="http://www.archaweb.com/prodotto/bnl-positivity-per-woocommerce/" target="_blank">Buy now!</a></div>', 'bnl-positivity-for-woocommerce' ) . '<img width="98%" height="98%" src="'.plugin_dir_url(__FILE__).'mockupbnl-300x300.jpg" /></center>' );
}

// Function used in the action hook
function bnlp_rcha_add_dashboard_widgets() {
	wp_add_dashboard_widget('dashboard_widget', __('Plugin BNL Positivity for WooCommerce', 'bnl-positivity-for-woocommerce'), 'bnlp_rcha_dashboard_widget_function');
}


function bnlp_rcha_woocommerce_not_running_error()
{
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
?>
	<div class="error">
		<p><?php _e( '<b>Warning:</b> The <b>BNL Positivity</b> plugin request WooCommerce to be activated for running', 'bnl-positivity-for-woocommerce' ); ?></p>
	</div>
<?php
	}
	return;
}	


function bnlp_rcha_wpbo_get_woo_version_number()
{
	// If get_plugins() isn't available, require it
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	// Create the plugins folder and file variables
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';

	// If the plugin version number is set, return it
	if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return $plugin_folder[$plugin_file]['Version'];
	} else {
		// Otherwise return null
		return NULL;
	}
}


function bnlp_rcha_init_bnl_gateway_class()
{
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	if (function_exists('load_plugin_textdomain')) {
		load_plugin_textdomain( 'bnl-positivity-for-woocommerce', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
	}

	add_filter( 'woocommerce_payment_gateways', 'bnlp_rcha_add_BNL_gateway' );

	function bnlp_rcha_add_BNL_gateway( $methods )
	{
		$methods[] = 'WC_Gateway_BNL';
		return $methods;
	}


	class WC_Gateway_BNL extends WC_Payment_Gateway
	{
		function __construct()
		{
			$this->id = 'bnl';
			$this->icon = plugin_dir_url(__FILE__) . 'logo_bnl_positivity.png';

			if (bnlp_rcha_wpbo_get_woo_version_number() > BNLP_RCHA_WOOCOMMERCE_MAX_VERSION) {
?>
				<div class="error">
					<p><?php printf(__('<b>Warning:</b> The plugin <b>BNL Positivity</b> is tested up to version %s. In case the new version of WooCommerce had introduced significant changes, there could be anomalies.', 'bnl-positivity-for-woocommerce'), BNLP_WOOCOMMERCE_MAX_VERSION); ?></p>
				</div>
<?php
			}

			$this->method_title = __('BNL Positivity', 'bnl-positivity-for-woocommerce');
			$this->method_description = __('Allows payments via the gateway of BNL Positivity circuit<div class="update-nag">Thank you for installing<br />Plugin BNL Positivity for WooCommerce.<br /><br />Get your license and link it with few clicks<br />at your BNL Positivity account<br /><br /><a class="button" href="http://www.archaweb.com/prodotto/bnl-positivity-per-woocommerce/" target="_blank">Buy now!</a></div><br /><div class="update-nag" style="border-left-color: red;">Warning! The following form has a demonstration purpose</div>', 'bnl-positivity-for-woocommerce') . '<br /><img style="border-width: 2px; border-color: #ffeeee; border-style: solid;" src="'.plugin_dir_url(__FILE__).'form.'.BNLP_RCHA_LANGUAGE.'.png" />';
		}
	} // End gateway class

}


function bnlp_rcha_license_menu()
{
	add_submenu_page('woocommerce', __('BNL Positivity Plugin License Activation Menu', 'bnl-positivity-for-woocommerce'), __('BNL Positivity Plugin License', 'bnl-positivity-for-woocommerce'), 'manage_options', __FILE__, 'bnlp_rcha_license_management_page');
}

function bnlp_rcha_license_management_page()
{
	echo '<div class="wrap">';
	echo '<h2>'.__('Plugin BNL Positivity for WooCommerce', 'bnl-positivity-for-woocommerce').'</h2>';
?>
	<p><?php _e('Please enter the license key for this product to activate it. You were given a license key when you purchased this item.', 'bnl-positivity-for-woocommerce'); ?></p>
	<form action="" method="post">
		<table class="form-table">
			<tr>
				<th style="width:100px;"><label for="bnlp_license_key"><?php _e('License Key', 'bnl-positivity-for-woocommerce'); ?></label></th>
				<td ><input class="regular-text" type="text" id="bnlp_license_key" name="bnlp_license_key" value="" > <input type="submit" name="activate_license" value="<?php _e('Activate', 'bnl-positivity-for-woocommerce'); ?>" class="button-primary" /></td>
			</tr>
		</table>
		<?php _e('<div class="update-nag">Thank you for installing<br />Plugin BNL Positivity for WooCommerce.<br /><br />Get your license and link it with few clicks<br />at your BNL Positivity account<br /><br /><a class="button" href="http://www.archaweb.com/prodotto/bnl-positivity-per-woocommerce/" target="_blank">Buy now!</a></div>', 'bnl-positivity-for-woocommerce'); ?>
	</form>
<?php
	echo '</div>';
}


add_action('admin_notices', 'bnlp_rcha_woocommerce_not_running_error');
add_action('plugins_loaded', 'bnlp_rcha_init_bnl_gateway_class');
add_action('admin_menu', 'bnlp_rcha_license_menu');

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'bnlp_rcha_add_dashboard_widgets' );

?>