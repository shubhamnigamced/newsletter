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

if($email_service == 'MailChimp')
{
	$apikey = get_option ( PLUGIN_CONSTANT . 'mailchimp_api_key' );
	require_once WANL_DIR.'email-service/mailchimp-api/src/Mailchimp/MAPI.class.php';
	$api = new MCAPI( $apikey );
	$mailchimp_lists = $api->lists();
	?>
	<h2><?php _e("Mailchimp Lists",'wp-advanced-newsletter');?></h2>
	<table style="width:98%;" id="straymanage" class="widefat">
	<tr>
	<th><?php _e("S. No.",'wp-advanced-newsletter');?></th>
	<th><?php _e("Name",'wp-advanced-newsletter');?></th>
	<th><?php _e("No of Subscriber",'wp-advanced-newsletter');?></th>
	<th><?php _e("Created Date",'wp-advanced-newsletter');?></th>
	</tr>
	<?php
	if(isset($mailchimp_lists['total']))
	{
		if($mailchimp_lists['total'] > 0)
		{
			$i = 0;
			$mailchimp_list = $mailchimp_lists['data'];
			foreach($mailchimp_list as $list)
			{
				$i++;
				?>
				<tr>
					<td><?php echo $i;?></td>
		   			<td><?php echo $list['name']?></td>
		   			<td><?php echo $list['stats']['member_count']?></td>
		   			<td><?php echo $list['date_created']?></td>
		   			<!-- <td><input type="button" value="Delete" data-id="<?php //echo $val->id;?>" class="active_camp_list_del button button-primary"></td> -->
				</tr>
				<?php 
			}	
		}
	}
	?>
	</table>
	<?php 
}

if($email_service == 'Constant Contact')
{
	require_once WANL_DIR.'email-service/constant_contact_api/src/Ctct/autoload.php';
	$cons_cont_api = get_option ( PLUGIN_CONSTANT . 'const_cont_api_key' );
	$cons_cont_access = get_option ( PLUGIN_CONSTANT . 'cons_cont_access_token' );
	$cc = new ConstantContact($cons_cont_api);
	
	if(isset($_POST['cons_con_add_new_list']))
	{
		$list_name  = $_POST['cons_con_name'];
		$cl = new ContactList($cons_cont_api);
		$cl->name = $list_name;
		$cl->status = "ACTIVE";
		$cc->addList($cons_cont_access, $cl);
	}
	$constant_contact_lists = $cc->getLists($cons_cont_access);
	?>
	<h2><?php _e("Constant Contact Lists",'wp-advanced-newsletter');?></h2>
	<table style="width:98%;" id="straymanage" class="widefat">
	<tr>
	<th><?php _e("S. No.",'wp-advanced-newsletter');?></th>
	<th><?php _e("Name",'wp-advanced-newsletter');?></th>
	<th><?php _e("No of Subscriber",'wp-advanced-newsletter');?></th>
	<th><?php _e("Created Date",'wp-advanced-newsletter');?></th>
	</tr>
	<?php
	if(isset($constant_contact_lists))
	{
		$i = 0;
		foreach($constant_contact_lists as $key=>$val)
		{
			if(isset($val->id))
			{
				$i++;
				?>
					<tr>
						<td><?php echo $i;?></td>
			   			<td><?php echo $val->name?></td>
			   			<td><?php echo $val->contact_count?></td>
			   			<td><?php echo $val->created_date?></td>
			   			<!-- <td><input type="button" value="Delete" data-id="<?php //echo $val->id;?>" class="active_camp_list_del button button-primary"></td> -->
					</tr>
					<?php 
				}	
			}
		}
		?>
	</table>
	
	<h2><?php _e("Add List",'wp-advanced-newsletter');?></h2>
	<form action="" method="post">
		<table class="wp-list-table widefat fixed pages" style="width: 98%;">
			<tr>
				<th><?php _e("List Name",'wp-advanced-newsletter');?></th>
				<td><input type="text" name="cons_con_name"></td>
				<td></td>
			</tr>
			
			<tr>
				<td><input type="submit" value="+ Add New List" class="button button-primary" name="cons_con_add_new_list"></td>
			</tr>
		</table>
	</form>		
	<?php 
	
}

if($email_service == 'Active Campaign')
{
	$i = 0;
	$actcamp_key = get_option ( PLUGIN_CONSTANT . 'active_campaign_api_key' );
	$actcamp_url = get_option ( PLUGIN_CONSTANT . 'active_campaign_access_url' );
	define ( "API_URL", $actcamp_url );
	define ( "API_KEY", $actcamp_key );
	$ac = new ActiveCampaign ( API_URL, API_KEY );
	
	if(isset($_POST['activecampaign_add_new_list']))
	{
		$list = array(
				"name"           => $_POST['active_camp_name'],
				"sender_name"    => $_POST['active_camp_sender_name'],
				"sender_addr1"   => $_POST['active_camp_address'],
				"sender_city"    => $_POST['active_camp_city'],
				"sender_zip"     => $_POST['active_camp_zip'],
				"sender_country" => $_POST['active_camp_country'],
		);
		
		$list_add = $ac->api("list/add", $list);
	}
	
	$camp_lists = $ac->api("list/list_?ids=all");
	
	?>
	<h2><?php _e("Active Campaign Lists",'wp-advanced-newsletter');?></h2>
	<table style="width:98%;" id="straymanage" class="widefat">
	    <tr>
			<th><?php _e("S. No.",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("Name",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("No of Subscriber",'wp-advanced-newsletter');?></th>
		   	<th><?php _e("Created Date",'wp-advanced-newsletter');?></th>
		</tr>
	<?php 
	if(isset($camp_lists))
	{
		$i = 0;
		foreach($camp_lists as $key=>$val)
		{
			if(isset($val->id))
			{
				$i++;
				?>
				<tr>
					<td><?php echo $i;?></td>
		   			<td><?php echo $val->name?></td>
		   			<td><?php echo $val->subscriber_count?></td>
		   			<td><?php echo $val->cdate?></td>
		   			<!-- <td><input type="button" value="Delete" data-id="<?php //echo $val->id;?>" class="active_camp_list_del button button-primary"></td> -->
				</tr>
				<?php 
			}	
		}
	}
	?>
	</table>
	<h2><?php _e("Add List",'wp-advanced-newsletter');?></h2>
	<form action="" method="post">
		<table class="wp-list-table widefat fixed pages" style="width: 98%;">
			<tr>
				<th><?php _e("List Name",'wp-advanced-newsletter');?></th>
				<td><input type="text" name="active_camp_name"></td>
				<td></td>
			</tr>
			<tr>
				<th><?php _e("Sender Name",'wp-advanced-newsletter');?></th>
				<td><input type="text" name="active_camp_sender_name"></td>
				<td></td>
			</tr>
			<tr>
				<th><?php _e("Sender Address",'wp-advanced-newsletter');?></th>
				<td><input type="text" name="active_camp_address"></td>
				<td></td>
			</tr>
			<tr>
				<th><?php _e("Sender City",'wp-advanced-newsletter');?></th>
				<td><input type="text" name="active_camp_city"></td>
				<td></td>
			</tr>
			<tr>
				<th><?php _e("Zip Code",'wp-advanced-newsletter');?></th>
				<td><input type="text" name="active_camp_zip"></td>
				<td></td>
			</tr>
			<tr>
				<th><?php _e("Country",'wp-advanced-newsletter');?></th>
				<td><input type="text" name="active_camp_country"></td>
				<td></td>
			</tr>
			<tr>
				<td><input type="submit" value="+ Add New List" class="button button-primary" name="activecampaign_add_new_list"></td>
			</tr>
		</table>
	</form>			
<?php 		
}
?>