<?php
/**
 * Plugin Name: WP Advanced Newsletter
 * Plugin URI: https://cedcommerce.com/
 * Description: WP Advanced Newsletter plugin allow users to subscribe newsletter to receive new updates using email subscribers Mailchimp, Constant Contact, Active campaign and Campaign Monitor
 * Version: 1.0.5
 * Author:  CedCommerce <plugins@cedcommerce.com>
 * Author URI: https://cedcommerce.com/
 * Requires at least: 4.0
 * Tested up to: 4.5.2
 * Text Domain: wp-advanced-newsletter
 * Domain Path: /language/
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Exceptions\CtctException;

require dirname(__FILE__).'/email-service/mailchimp-api/src/Mailchimp.php';
require dirname(__FILE__).'/email-service/activecampaign/includes/ActiveCampaign.class.php';


define('WANL_DIR',plugin_dir_path(__FILE__));
define('WANL_DIR_URL',plugin_dir_url(__FILE__));
define('PLUGIN_CONSTANT', 'anl_');
function wanl() {
	include 'includes/wpanl-class.php';
	

}
add_action( 'init','wanl');


add_action( 'admin_enqueue_scripts', 'ced_advance_newsletter_preview_popup' );

function ced_advance_newsletter_preview_popup()
{
	wp_enqueue_script ( 'jquery.colorbox', WANL_DIR_URL . 'colorbox/jquery.colorbox.js', array('jquery') );
	wp_enqueue_style ( 'colorbox-css', WANL_DIR_URL . 'colorbox/colorbox.css' );
}
?>