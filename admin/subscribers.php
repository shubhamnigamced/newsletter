<?php 
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;
?>
<h3>
	<?php echo __("Select an Email Subscription Service","wp-advanced-newsletter");?>
</h3>	
<select class="advancednewsletter-email-service-option" onchange="service_value(this.value)" id="advancednewsletter-email-service-option" name="advancednewsletter-email-service-option">
	<option selected="" value="noservice" data-val=""><?php echo __("No Service","wp-advanced-newsletter");?></option>
	<option value="activecampaign" data-val="Active Campaign" <?php if (get_option(PLUGIN_CONSTANT.'email_subscriber') == "Active Campaign"){?> selected <?php }?>><?php echo __("Active Campaign","wp-advanced-newsletter");?></option>
	<option value="constantcontact" data-val="Constant Contact" <?php if (get_option(PLUGIN_CONSTANT.'email_subscriber') == "Constant Contact"){?> selected <?php }?>><?php echo __("Constant Contact","wp-advanced-newsletter");?></option>
	<option value="mailchimp" data-val="MailChimp" <?php if (get_option(PLUGIN_CONSTANT.'email_subscriber') == "MailChimp"){?> selected <?php }?>><?php echo __("MailChimp","wp-advanced-newsletter");?></option>
</select>
<?php

$email_service = get_option(PLUGIN_CONSTANT.'email_subscriber');

if($email_service == 'Active Campaign')
{
	$i = 0;
	$actcamp_key = get_option ( PLUGIN_CONSTANT . 'active_campaign_api_key' );
	$actcamp_url = get_option ( PLUGIN_CONSTANT . 'active_campaign_access_url' );
	define ( "API_URL", $actcamp_url );
	define ( "API_KEY", $actcamp_key );
	$ac = new ActiveCampaign ( API_URL, API_KEY );
	$camp_users = $ac->api("contact/list_?ids=all");
	?>
	<table style="width:98%;" id="straymanage" class="widefat">
	    <tr>
			<th><?php _e("Sno",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("Email address",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("Lists",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("Subscription From",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("UnSubscribe",'wp-advanced-newsletter');?></th>
		</tr>
	<?php 
	if(isset($camp_users))
	{
		foreach($camp_users as $key=>$val)
		{
			$lists = $val->lists;
			if(isset($val->id))
			{
				$i++;
				?>
				<tr>
					<td><?php echo $i;?></td>
		   			<td><?php echo $val->email?></td>
		   			<td><?php 
		   			if(isset($lists))
		   			{
		   				foreach($lists as $list)
		   				{
		   				?>
							<span><?php echo $list->listname;?>&nbsp;&nbsp;</span>
						<?php 
		   				}	
		   			}	
		   			?>
		   			</td>
		   			<td><?php echo $val->cdate;?></td>
		   			<td>
		   				<input type="button" value="Unsubscribe" data-id="<?php echo $val->id;?>" class="active_camp_unsubscribe button button-primary">
		   				<img height="25px" src="<?php echo WANL_DIR_URL?>/assets/images/loading.gif" style="display:none;">	
		   			</td>
				</tr>
				<?php 
			}	
		}
	}	
}


if($email_service == 'Constant Contact')
{
	require_once WANL_DIR.'email-service/constant_contact_api/src/Ctct/autoload.php';
	$cons_cont_api = get_option ( PLUGIN_CONSTANT . 'const_cont_api_key' );
	$cons_cont_access = get_option ( PLUGIN_CONSTANT . 'cons_cont_access_token' );
	$cc = new ConstantContact ( $cons_cont_api );
	$cons_contact_lists = $cc->getContacts($cons_cont_access);
	?>
		<table style="width:98%;" id="straymanage" class="widefat">
		    <tr>
				<th><?php _e("Sno",'wp-advanced-newsletter');?></th>
			   	<th><?php _e("Email address",'wp-advanced-newsletter');?></th>
			   	<th><?php _e("Subscription From",'wp-advanced-newsletter');?></th>
			   	<th><?php _e("UnSubscribe",'wp-advanced-newsletter');?></th>
			</tr>
			<?php 
	
	if(isset($cons_contact_lists->results))
	{
		$i = 1;
		$contact_lists = $cons_contact_lists->results;
		foreach($contact_lists as $key=>$val)
		{
			if(!empty($val->lists))
			{	
				?>
				<tr>
					<td><?php echo $i;?></td>
		   			<td><?php echo $val->email_addresses[0]->email_address?></td>
		   			<td><?php echo $val->created_date;?></td>
		   			<td>
		   				<input type="button" value="Unsubscribe" data-id="<?php echo $val->id;?>" class="constant_contact_unsubscribe button button-primary">
		   				<img height="25px" src="<?php echo WANL_DIR_URL?>/assets/images/loading.gif" style="display:none;">	
		   			</td>
				</tr>
				<?php 
				$i++;
			}
		}
	}
	?>
		</table>
	<?php 
	
}


if($email_service == 'MailChimp')
{
	$apikey = get_option ( PLUGIN_CONSTANT . 'mailchimp_api_key' );
	require_once WANL_DIR.'email-service/mailchimp-api/src/Mailchimp/MAPI.class.php';
	$api = new MCAPI( $apikey );
	$listId = get_option ( PLUGIN_CONSTANT . 'mailchimp_list_id' );
	$mailchimp_subscriber = $api->listMembers($listId);

?>
	<table style="width:98%;" id="straymanage" class="widefat">
	    <tr>
			<th><?php _e("Sno",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("Email address",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("Subscription From",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("UnSubscribe",'wp-advanced-newsletter');?></th>
		</tr>
		<?php 
		if(isset($mailchimp_subscriber))
		{
			$mailchimp_subscribers = $mailchimp_subscriber['data'];
			$i = 1;
			foreach($mailchimp_subscribers as $key=>$val)
			{
				?>
				<tr>
					<td><?php echo $i;?></td>
		   			<td><?php echo $val['email'];?></td>
		   			<td><?php echo $val['timestamp'];?></td>
		   			<td>
		   				<input type="button" value="Unsubscribe" data-email="<?php echo $val['email'];?>"  data-id="<?php echo $listId;?>" class="mail_chimp_unsubscribe button button-primary">
		   				<img height="25px" src="<?php echo WANL_DIR_URL?>/assets/images/loading.gif" style="display:none;">		
		   			</td>
				</tr>
				<?php 
				$i++;
			}
		}
		?>
	</table>
<?php 
}
?>	