<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$anl_notification=array();
if(isset($_POST['advanced_newsletter_btn_notification_submit']))
{
	include_once WANL_DIR.'/html/template_html.php';
	require_once WANL_DIR.'email-service/mailchimp-api/src/Mailchimp/MAPI.class.php';
	$anl_notification['subscriber_success_message']=sanitize_text_field($_POST['subscriber_success_txt_message']);
	$anl_notification['subscriber_failure_message']=sanitize_text_field($_POST['subscriber_failure_txt_message']);
	$anl_notification['subscriber_success_mail']=sanitize_text_field($_POST['subscriber_success_txt_mail']);
	$anl_notification['subscriber_success_radio']=sanitize_text_field($_POST['subscriber_success_radio']);
	$anl_notification['subscriber_success_admin_mail']=sanitize_text_field($_POST['subscriber_success_admin_txt_mail']);
	$anl_notification['newsletter_templates']=sanitize_text_field($_POST['newsletter_templates_select']);
	$anl_notification=json_encode($anl_notification);
	update_option(PLUGIN_CONSTANT.'notification',$anl_notification);
}

$notification_details=get_option(PLUGIN_CONSTANT.'notification');
$notification_details=json_decode($notification_details, true);

?>

<form enctype="multipart/form-data" action="" id="mainform" method="post">
	<table class="form-table">
		<tbody>
			<tr>
				<td>
					<label for="Subscriber Success Message" class="anl_help_lable">
					<?php _e("Subscriber Success Message",'wp-advanced-newsletter');?>
					</label>
				</td>
				<td>
					<span class="dashicons dashicons-editor-help">
						<span class="help_anl"><?php _e("Sucess message to subscriber ",'wp-advanced-newsletter');?></span>
					</span>
				</td>
				<td>
					<textarea  id="subscriber_success_txt_message" name="subscriber_success_txt_message"  cols="40" rows="4"><?php echo $notification_details['subscriber_success_message'];?></textarea>
				</td>
			</tr>
			
			<tr>
				<td>
					<label for="Subscriber Success Message" class="anl_help_lable">
					<?php _e("Subscriber Failure Message",'wp-advanced-newsletter');?>
					</label>
				</td>
				<td>
					<span class="dashicons dashicons-editor-help">
						<span class="help_anl"><?php _e("Failure message to subscriber ",'wp-advanced-newsletter');?></span>
					</span>
				</td>
				<td>
					<textarea  id="subscriber_success_txt_message" name="subscriber_failure_txt_message"  cols="40" rows="4"><?php echo $notification_details['subscriber_failure_message'];?></textarea>
				</td>
			</tr>
			
			<tr>
				<td>
				<label for="Subscriber Success Mail" class="anl_help_lable">
					<?php _e('Subscriber Success Mail','wp-advanced-newsletter');?>
					</label>
				</td>
				<td>
					<span class="dashicons dashicons-editor-help">
						<span class="help_anl"><?php _e("Success mail to subscriber",'wp-advanced-newsletter');?></span>
					</span>
				</td>
				<td>
					<textarea  style="" id="subscriber_success_txt_mail" name="subscriber_success_txt_mail" cols="40" rows="4"><?php echo $notification_details['subscriber_success_mail']; ?></textarea>
				</td>
			</tr>
			
			
			<tr>
				<td>
					<b><label><?php _e("Admin Notification",'wp-advanced-newsletter');?></label></b>
				</td>
			</tr>
			<tr>
				<td>
					<label for="Subscriber Success Admin Mail" class="anl_help_lable">
						<?php _e("Subscriber Success Admin Mail",'wp-advanced-newsletter');?>
					</label>
				</td>
				<td>
					<span class="dashicons dashicons-editor-help">
						<span class="help_anl"><?php _e("Success mail to admin",'wp-advanced-newsletter');?></span>
					</span>
				</td>
				<td>
					<textarea id="subscriber_success_admin_txt_mail" name="subscriber_success_admin_txt_mail" cols="40" rows="4"><?php echo $notification_details['subscriber_success_admin_mail'];?></textarea>
				</td>
			</tr>
			
			<tr>
				<td>
					<label for="submit">
						<input type="submit" class="button button-primary button-large" value="Save" id="advanced_newsletter_btn_notification_submit" name="advanced_newsletter_btn_notification_submit">
					</label><br>
				</td>
			</tr>
			
		</tbody>
	</table>
</form>
<?php 