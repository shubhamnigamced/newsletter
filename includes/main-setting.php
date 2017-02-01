<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * template for adding menus to admin side
 * like menu page 
 * setting and tools page
 */
add_menu_page(__('setting_newsletter','wp-advanced-newsletter'), __('Advanced Newsletter','wp-advanced-newsletter'), 'manage_options', 'advanced_newsletter_mail', 'advanced_newsletter_menu','',7, 'dashicons-admin-media');

/**
 * @name advanced_newsletter_menu()
 * @author CedCommerce<>
 * @link http://cedcommerce.com/
 */

function advanced_newsletter_menu()
{
	if(empty($_GET['tab']))
	{
		$_GET['tab']='subscription-setting';
	}
 	?>
 	<div class="wrap1">
 		<h2 class="nav-tab-wrapper1">
		 	<a class="nav-tab<?php if(isset($_GET['tab'])&&$_GET['tab']=='subscription-setting'){?> nav-tab-active <?php ;} ?>" href="<?php echo admin_url() ?>/admin.php?page=advanced_newsletter_mail&tab=subscription-setting"><?php _e("Subscription Setting",'wp-advanced-newsletter');?></a>
	 		<a class="nav-tab<?php if(isset($_GET['tab'])&&$_GET['tab']=='notification-setting'){?> nav-tab-active <?php ;} ?>" href="<?php echo admin_url() ?>/admin.php?page=advanced_newsletter_mail&tab=notification-setting"><?php _e("Notification Setting",'wp-advanced-newsletter');?></a>
	 		<a class="nav-tab<?php if(isset($_GET['tab'])&&$_GET['tab']=='service-campaigns'){?> nav-tab-active <?php ;} ?>" href="<?php echo admin_url() ?>/admin.php?page=advanced_newsletter_mail&tab=service-campaigns"><?php _e("Service Campaigns",'wp-advanced-newsletter');?></a>
	 		<a class="nav-tab<?php if(isset($_GET['tab'])&&$_GET['tab']=='service-lists'){?> nav-tab-active <?php ;} ?>" href="<?php echo admin_url() ?>/admin.php?page=advanced_newsletter_mail&tab=service-lists"><?php _e("Service Lists",'wp-advanced-newsletter');?></a>
			<a class="nav-tab<?php if(isset($_GET['tab'])&&$_GET['tab']=='subscribers'){?> nav-tab-active <?php ;} ?>" href="<?php echo admin_url() ?>/admin.php?page=advanced_newsletter_mail&tab=subscribers"><?php _e("Subscribers",'wp-advanced-newsletter');?></a>
	 	</h2>
	</div>
	<p>
	<?php
	if(isset($_GET['tab'])&&$_GET['tab']=='subscribers')
	{
		include  WANL_DIR.'/admin/subscribers.php';
	}
	if(isset($_GET['tab'])&&$_GET['tab']=='service-lists')
	{
		include  WANL_DIR.'/admin/service-lists.php';
	}
	
	if(isset($_GET['tab'])&&$_GET['tab']=='notification-setting')
	{
		include  WANL_DIR.'/admin/notification.php';
	}
	
	if(isset($_GET['tab'])&&$_GET['tab']=='service-campaigns')
	{
		include  WANL_DIR.'/admin/service-campaigns.php';
	}
	if(isset($_GET['tab'])&&$_GET['tab']=='Form_Settings')
	{
		include  WANL_DIR.'/admin/form_setting.php';
	}
	if(isset($_GET['tab'])&&$_GET['tab']=='subscription-setting')
	{
		include  WANL_DIR.'/admin/subscription-setting.php';
	}
 }
 ?>