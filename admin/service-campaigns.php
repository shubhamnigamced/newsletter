<?php 
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Ctct\ConstantContact;
use Ctct\Services;
use Ctct\Util\Config;
use Ctct\Services\EmailMarketingService;
use Ctct\Services\CampaignScheduleService;
use Ctct\Components\EmailMarketing\Campaign;
use Ctct\Components\EmailMarketing\MessageFooter;
use Ctct\Components\EmailMarketing\Schedule;
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
	$actcamp_key = get_option ( PLUGIN_CONSTANT . 'active_campaign_api_key' );
	$actcamp_url = get_option ( PLUGIN_CONSTANT . 'active_campaign_access_url' );
	define ( "API_URL", $actcamp_url );
	define ( "API_KEY", $actcamp_key );
	$ac = new ActiveCampaign ( API_URL, API_KEY );
	if(isset($_GET['campaign_id']))
	{
		include 'active-campaign-detail.php';
	}
	else 
	{
		if(isset($_POST['activecampaign_add_new_camapign']))
		{
			$campaign_name 			= $_POST['active_camp_name'];
			$campaign_type 			= 'text';
			$campaign_status 		= $_POST['active_camp_status'];
			$campaign_email_content = $_POST['active_camp_email_content'];
			$campaign_fname 		= $_POST['active_camp_fname'];
			$campaign_femail 		= $_POST['active_camp_femail'];
			$campaign_subject		= $_POST['active_camp_subject'];
			$campaign_sdate			= $_POST['active_camp_sdate'];
			$campaign_list			= $_POST['active_camp_list'];
			$campaignsend_date 		= date('Y-m-d H:i:s', strtotime($campaign_sdate));
			$campaign_format 		= 'mime';
			$campaign_charset 		= 'utf-8';
			
			$message = array(
					"format"        => "$campaign_format",
					"subject"       => "$campaign_subject",
					"fromemail"     => "$campaign_femail",
					"fromname"      => "$campaign_fname",
					"html"          => "<html><body>$campaign_email_content</body></html>",
					"p[{$campaign_list}]" => $campaign_list,
			);
	
			$message_add = $ac->api("message/add", $message);
			$message_id = (int)$message_add->id;
			$campaign = array(
						"type"             => "single",
						"name"             => "$campaign_name", // internal name (message subject above is what contacts see)
						"sdate"            => "$campaignsend_date",
						"status"           => $campaign_status,
						"public"           => 1,
						"tracklinks"       => "all",
						"trackreads"       => 1,
						"htmlunsub"        => 1,
					"p[{$campaign_list}]"  => $campaign_list,
						"m[{$message_id}]" => 100, // 100 percent of subscribers
			);
	
			$campaign_create = $ac->api("campaign/create", $campaign);
			if (!(int)$campaign_create->success) 
			{
				echo "<p style='color:red;'>Creating campaign failed. Error returned: " . $campaign_create->error . "</p>";
			}
			else 
			{
				// successful request
				$campaign_id = (int)$campaign_create->id;
				echo "<p style='color:green;'>Campaign successfully created!</p>";
			}	
		}
		
		// Retrieve Campaign
		$camp_lists = $ac->api("list/list_?ids=all");
		$campaign_lists = $ac->api("campaign/list_?ids=all");
		?>
		<h2><?php _e("Campaigns List",'wp-advanced-newsletter');?></h2>
		<table class="wp-list-table widefat fixed pages">
			<tr>
				<th class="manage-column" id="title" scope="col">
					<b><?php _e("Name",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="author" scope="col">
					<b><?php _e("Subscribers",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="date" scope="col">
					<b><?php _e("Status",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="date" scope="col">
					<b><?php _e("Schedule Time",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="date" scope="col">
					<b><?php _e("View/Send/Delete",'wp-advanced-newsletter');?></b>
				</th>
			</tr>
		<?php
		if(isset($campaign_lists) && !empty($campaign_lists))
		{
			foreach($campaign_lists as $campaign_list)
			{
				$campaign_detail = (array)$campaign_list;
				
				if(isset($campaign_detail['id']))
				{
					if($campaign_detail['status'] == 0)
					{
						$status = 'Draft';
					}	
					if($campaign_detail['status'] == 1)
					{
						$status = 'Scheduled';
					}
					if($campaign_detail['status'] == 2)
					{
						$status = 'Sending';
					}
					if($campaign_detail['status'] == 3)
					{
						$status = 'Paused';
					}
					if($campaign_detail['status'] == 4)
					{
						$status = 'Stopped';
					}
					if($campaign_detail['status'] == 5)
					{
						$status = 'Completed';
					}
					if($campaign_detail['status'] == 6)
					{
						$status = 'Disabled';
					}
					if($campaign_detail['status'] == 7)
					{
						$status = 'Pending Approval';
					}
					?>
					<tr>
						
						<td class="manage-column"><b><?php echo $campaign_detail['name'];?><b><p><b><?php _e("Created At",'wp-advanced-newsletter');?><b> : <?php echo $campaign_detail['cdate'];?></p></td>
						<td class="manage-column"><?php echo $campaign_detail['listslist'];?></td>
						<td class="manage-column"><?php echo $status;?></td>	
						<td class="manage-column"><?php echo $campaign_detail['sdate'];?></td>	
						<td class="manage-column">
							<a href="?page=advanced_newsletter_mail&tab=Campaigns&campaign_id=<?php echo $campaign_detail['id'];?>" class="button button-primary"><?php _e("View",'wp-advanced-newsletter');?></a>
							<input type="button" value="Send" data-id="<?php echo $campaign_detail['id'];?>" class="ced_actcamp_send button button-primary">
							<input type="button" value="Delete" data-id="<?php echo $campaign_detail['id'];?>" class="ced_actcamp_delete button button-primary">
							<img height="25px" src="<?php echo WANL_DIR_URL?>/assets/images/loading.gif" style="display:none;">
						</td>	
					</tr>	
					<?php 
				}
			}
		}
		else 
		{?>
			<tr><td colspan=3><b><?php _e("No Campaigns",'wp-advanced-newsletter');?></b></td></tr>
	<?php 
		}
	?>
	</table>
	<h2><?php _e("Add Campaign",'wp-advanced-newsletter');?></h2>
		<form action="" method="post" id="active_campaign">
			<table class="wp-list-table widefat fixed pages">
				<tr>
					<th><?php _e("Name",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="active_camp_name" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>
					<th><?php _e("Schedule Date",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="active_camp_sdate" id="active_camp_schedule_time" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>
					<th><?php _e("Status",'wp-advanced-newsletter');?>*</th>
					<td><input type="radio" name="active_camp_status" value="0">Draft<input type="radio" name="active_camp_status" checked value="1">Scheduled</td>
					<td></td>
				</tr>
				
				<tr>
					<th><?php _e("From Name",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="active_camp_fname" class="wan_required">
					<td></td>
				</tr>
				
				<tr>
					<th><?php _e("From Email",'wp-advanced-newsletter');?>*</th>
					<td><input type="email" name="active_camp_femail" class="wan_required">
					<td></td>
				</tr>
				
				<tr>
					<th><?php _e("Subject",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="active_camp_subject" class="wan_required">
					<td></td>
				</tr>
				<tr>	
					<th><?php _e("Email Content",'wp-advanced-newsletter');?>*</th>
					<td colspan="2">
					<?php 
							$content = "";
							$editor_id = 'active_camp_email_content';
							$settings = array( 
										'media_buttons'    => false,
										'drag_drop_upload' => true,
										'dfw'              => true, 
										'teeny'            => true,
										'editor_height'    => 200,
										'editor_class'	   => 'wan_required',
										'textarea_name'    => "active_camp_email_content"
										);
							wp_editor( $content, $editor_id, $settings );
					?>
					</td>
				</tr>	
				<tr>
					<th><?php _e("List",'wp-advanced-newsletter');?>*</th>
					<td>
						<?php 
							if(isset($camp_lists))
							{
								?>
								<select name="active_camp_list"  class="wan_required">
								<?php 
								foreach($camp_lists as $camp_list)
								{
									$camp_list = (array)$camp_list;
									if(isset($camp_list['id']))
									{
										?>
										<option value="<?php echo $camp_list['id']?>"><?php echo $camp_list['name']?></option>		
						  				<?php 
									}
								}	
						  		?>
						  		</select>
						  		<?php 
							}	
						?>
					</td>
					<td></td>
				</tr>
				
				<tr>
					<td><input type="submit" value="+ Add New Campaign" class="button button-primary" name="activecampaign_add_new_camapign"></td>
				</tr>
			</table>
		</form>		
		<?php 
	}
}

if($email_service == 'Constant Contact')
{
	require_once WANL_DIR.'email-service/constant_contact_api/src/Ctct/autoload.php';
	$cons_cont_api = get_option ( PLUGIN_CONSTANT . 'const_cont_api_key' );
	$cons_cont_access = get_option ( PLUGIN_CONSTANT . 'cons_cont_access_token' );
	
	if(isset($_GET['campaign_id']))
	{
		include 'constact-contact-campaign-detail.php';
	}
	else
	{
		try 
		{
			$cc = new ConstantContact($cons_cont_api);
			$constant_contact_lists = $cc->getLists($cons_cont_access);
			
			if(isset($_POST['constant_contact_add_new_camapign']) || isset($_POST['constant_contact_update_camapign']))
			{
				$opts = array();
				$message_footer = array();
				$_POST['constant_contact_camp_email_content'] = '<html><body>'.stripslashes($_POST['constant_contact_camp_email_content']).'</body></html>';
				$opts['name'] = $_POST['constant_contact_camp_title'];
				$opts['subject'] = $_POST['constant_contact_camp_subject'];
				$opts['from_name'] = $_POST['constant_contact_camp_name'];
				$opts['from_email'] = $_POST['constant_contact_camp_email'];
				$opts['reply_to_email'] = $_POST['constant_contact_camp_reply_email'];
				$opts['email_content'] = $_POST['constant_contact_camp_email_content'];
				$opts['text_content'] = $_POST['constant_contact_camp_text_content'];
				$opts['email_content_format'] = 'HTML';
				$opts['lists'] = $_POST['constant_contact_camp_list_id'];
				$message_footer['organization_name'] = $_POST['constant_contact_camp_organization_name'];
				$message_footer['address_line_1'] = $_POST['constant_contact_camp_address_line_1'];
				$message_footer['address_line_2'] = $_POST['constant_contact_camp_address_line_2'];
				$message_footer['address_line_3'] = $_POST['constant_contact_camp_address_line_3'];
				$message_footer['city'] = $_POST['constant_contact_camp_city'];
				$message_footer['state'] = $_POST['constant_contact_camp_state'];
				$message_footer['postal_code'] = $_POST['constant_contact_camp_postal_code'];
				$message_footer['country'] = $_POST['constant_contact_camp_country'];
				
				$campaign = new Campaign();
				$campaign->name = $opts['name'];
				$campaign->subject = $opts['subject'];
				$campaign->from_name = $opts['from_name'];
				$campaign->from_email = $opts['from_email'];
				$campaign->reply_to_email = $opts['reply_to_email'];
				$campaign->email_content = $opts['email_content'];
				$campaign->text_content = $opts['text_content'];
				$campaign->email_content_format = $opts['email_content_format'];
				
				if (isset($opts['lists'])) {
					if (count($opts['lists']) > 1) {
						foreach ($opts['lists'] as $list) {
							$campaign->addList($list);
						}
					} else {
						$campaign->addList($opts['lists'][0]);
					}
				}
				$messagefooter = new MessageFooter();
				$messagefooter->organization_name = $message_footer['organization_name'];
				$messagefooter->address_line_1 = $message_footer['address_line_1'];
				$messagefooter->address_line_2 = $message_footer['address_line_2'];
				$messagefooter->address_line_3 = $message_footer['address_line_3'];
				$messagefooter->city = $message_footer['city'];
				$messagefooter->state = $message_footer['state'];
				$messagefooter->international_state = $message_footer['international_state'];
				$messagefooter->postal_code = $message_footer['postal_code'];
				$messagefooter->country = $message_footer['country'];
				$campaign->message_footer = $messagefooter;
				
				if(!empty($_POST['constant_contact_camp_send_time']) && isset($_POST['constant_contact_camp_send_time']))
				{
					$send_date_format =  $_POST['constant_contact_camp_send_time'];
					$send_date = date('Y-m-d\TH:i:s\.000\Z', strtotime($send_date_format));
					$schedule = new Schedule();
					$schedule->scheduled_date = $send_date;
				}
				
				if(isset($_POST['constant_contact_add_new_camapign']))
				{
					$campaign_added = $cc->addEmailCampaign($cons_cont_access, $campaign);
					if(!empty($_POST['constant_contact_camp_send_time']) && isset($_POST['constant_contact_camp_send_time']))
					{
						$campaign_schedule = $cc->addEmailCampaignSchedule($cons_cont_access, $campaign_added->id, $schedule);
					}
					echo '<p><b style="color:green">Campaign Added</b></p>';
				}
				elseif(isset($_POST['constant_contact_update_camapign']))
				{
					if(!empty($_POST['constant_contact_camp_send_time']) && isset($_POST['constant_contact_camp_send_time']))
					{
						$campaign_schedules = $cc->getEmailCampaignSchedules($cons_cont_access,$_POST['constant_contact_camp_id']);
						if(isset($campaign_schedules) && !empty($campaign_schedules))
						{
							$campaign_schedule = $cc->getEmailCampaignSchedule($cons_cont_access,$_POST['constant_contact_camp_id'],$campaign_schedules[0]->id);
							$campaign_updated = $cc->updateEmailCampaignSchedule($cons_cont_access, $campaign, $campaign_schedule);
						}
						else 
						{
							$campaign_schedule = $cc->addEmailCampaignSchedule($cons_cont_access, $_POST['constant_contact_camp_id'], $schedule);
						}	
					}
					else
					{
						$campaign_updated = $cc->updateEmailCampaign($cons_cont_access, $campaign);
					}		
					echo '<p><b style="color:green">Campaign Update</b></p>';
				}
			}
		}
		catch (Exception $e)
		{
			$errors = $e->getErrors();
			foreach($errors as $error)
			{
				echo '<p><b style="color:red">'.$error['error_message'].'</b></p>';
			}
		}
		
		$cons_contact_campaigns = $cc->getEmailCampaigns($cons_cont_access);
		?>
		<h2><?php _e("Campaigns List",'wp-advanced-newsletter');?></h2>
		
		<table class="wp-list-table widefat fixed pages">
			<tr>
				
				<th class="manage-column" id="title" scope="col">
					<b><?php _e("Name",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="author" scope="col"><b><?php _e("Subscribers",'wp-advanced-newsletter');?></b></th>
				<th class="manage-column" id="date" scope="col">
					<b><?php _e("Status",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="date" scope="col">
					<b><?php _e("Schedule Time",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="date" scope="col">
					<b><?php _e("Delete",'wp-advanced-newsletter');?></b>
				</th>
				
			</tr>
			<?php 
			
			if(isset($cons_contact_campaigns->results) && !empty($cons_contact_campaigns->results))
			{
				$all_campaign = $cons_contact_campaigns->results;
				foreach($all_campaign as $campaign)
				{
					$campaign = (array)$campaign;
					$campaign_detail = $cc->getEmailCampaign($cons_cont_access, $campaign['id']);
					$campaign_detail = (array)$campaign_detail;
					
					if(isset($campaign_detail['sent_to_contact_lists'][0]))
					{
						$constant_contact_list_id = $campaign_detail['sent_to_contact_lists'][0]->id;
						$constant_contact_list_detail = $cc->getList($cons_cont_access,$constant_contact_list_id );
						$contact_count = $constant_contact_list_detail->contact_count;
					}	
					
					if(empty($contact_count))
					{
						$contact_count = 0;
					}
					?>
						<tr>
							<td class="manage-column"><b><?php echo $campaign_detail['name'];?><b><p><b><?php _e("Created At",'wp-advanced-newsletter');?><b> : <?php echo $campaign['modified_date'];?></p></td>
							<td class="manage-column"><?php echo $contact_count;?></td>
							<td class="manage-column"><?php echo $campaign_detail['status'];?></td>	
							<td class="manage-column"><?php echo $campaign_detail['next_run_date'];?></td>	
							<td class="manage-column">
								<a href="?page=advanced_newsletter_mail&tab=Campaigns&campaign_id=<?php echo $campaign['id'];?>" class="button button-primary"><?php _e("View",'wp-advanced-newsletter');?></a>
								<input type="button" value="Delete" data-id="<?php echo $campaign['id'];?>" class="ced_cons_con_delete button button-primary">
								<img height="25px" src="<?php echo WANL_DIR_URL?>/assets/images/loading.gif" style="display:none;">
							</td>	
						</tr>	
					<?php 
				}
			}
			else 
			{?>
			<tr><td colspan=3><b><?php _e("No Campaigns",'wp-advanced-newsletter');?></b></td></tr>
	
			<?php }	
			?>
		</table>
		<br/>
		<h2><?php _e("Add Campaign",'wp-advanced-newsletter');?></h2>
		<form action="" method="post" id="constant_contact">
			<table class="wp-list-table widefat fixed pages">
				<tr>
					<th><?php _e("Name",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_title" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("Subject",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_subject" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("From Name",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_name" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("From Email",'wp-advanced-newsletter');?>*</th>
					<td><input type="email" name="constant_contact_camp_email" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("Text Content",'wp-advanced-newsletter');?>*</th>
					<td colspan="2">
					<textarea name="constant_contact_camp_text_content" rows="8" cols="88" class="wan_required"></textarea>
					</td>
				</tr>
				<tr>	
					<th><?php _e("Email Content",'wp-advanced-newsletter');?>*</th>
					<td colspan="2">
					<?php 
							$content = "";
							$editor_id = 'constant_contact_camp_email_content';
							$settings = array( 
										'media_buttons'    => false,
										'drag_drop_upload' => true,
										'dfw'              => true, 
										'teeny'            => true,
										'editor_height'    => 200,
										'editor_class'	   => 'wan_required',
										'textarea_name'    => "constant_contact_camp_email_content"
										);
							wp_editor( $content, $editor_id, $settings );
					?>
					</td>
				</tr>	
				<tr>	
					<th><?php _e("Reply-To Email",'wp-advanced-newsletter');?>*</th>
					<td><input type="email" name="constant_contact_camp_reply_email" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("Lists to send to",'wp-advanced-newsletter');?>*</th>
					<td>
						<select multiple="multiple" name="constant_contact_camp_list_id[]" class="wan_required">
		                   <?php
		                   foreach ($constant_contact_lists as $list) {
		                      echo '<option value="' . $list->id . '" >' . $list->name . '</option><br />';
		                   }
		                   ?>
		                </select>
					</td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("Send Time",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_send_time" id="constant_contact_camp_send_time" class="wan_required"></td>
					<td></td>
				</tr>
				<tr>	
					<th><h3><?php _e("Address",'wp-advanced-newsletter');?></h3></th>
				</tr>
				
				<tr>	
					<th><?php _e("Organization Name",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_organization_name" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("Address Line 1",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_address_line_1" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("Address Line 2",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_address_line_2" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("Address Line 3",'wp-advanced-newsletter');?></th>
					<td><input type="text" name="constant_contact_camp_address_line_3"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("City",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_city" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("State",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_state" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("Postal Code",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="constant_contact_camp_postal_code" class="wan_required"></td>
					<td></td>
				</tr>
				<tr>
				<?php 
				$countries = array(
						'AF'=>'AFGHANISTAN',
						'AL'=>'ALBANIA',
						'DZ'=>'ALGERIA',
						'AS'=>'AMERICAN SAMOA',
						'AD'=>'ANDORRA',
						'AO'=>'ANGOLA',
						'AI'=>'ANGUILLA',
						'AQ'=>'ANTARCTICA',
						'AG'=>'ANTIGUA AND BARBUDA',
						'AR'=>'ARGENTINA',
						'AM'=>'ARMENIA',
						'AW'=>'ARUBA',
						'AU'=>'AUSTRALIA',
						'AT'=>'AUSTRIA',
						'AZ'=>'AZERBAIJAN',
						'BS'=>'BAHAMAS',
						'BH'=>'BAHRAIN',
						'BD'=>'BANGLADESH',
						'BB'=>'BARBADOS',
						'BY'=>'BELARUS',
						'BE'=>'BELGIUM',
						'BZ'=>'BELIZE',
						'BJ'=>'BENIN',
						'BM'=>'BERMUDA',
						'BT'=>'BHUTAN',
						'BO'=>'BOLIVIA',
						'BA'=>'BOSNIA AND HERZEGOVINA',
						'BW'=>'BOTSWANA',
						'BV'=>'BOUVET ISLAND',
						'BR'=>'BRAZIL',
						'IO'=>'BRITISH INDIAN OCEAN TERRITORY',
						'BN'=>'BRUNEI DARUSSALAM',
						'BG'=>'BULGARIA',
						'BF'=>'BURKINA FASO',
						'BI'=>'BURUNDI',
						'KH'=>'CAMBODIA',
						'CM'=>'CAMEROON',
						'CA'=>'CANADA',
						'CV'=>'CAPE VERDE',
						'KY'=>'CAYMAN ISLANDS',
						'CF'=>'CENTRAL AFRICAN REPUBLIC',
						'TD'=>'CHAD',
						'CL'=>'CHILE',
						'CN'=>'CHINA',
						'CX'=>'CHRISTMAS ISLAND',
						'CC'=>'COCOS (KEELING) ISLANDS',
						'CO'=>'COLOMBIA',
						'KM'=>'COMOROS',
						'CG'=>'CONGO',
						'CD'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
						'CK'=>'COOK ISLANDS',
						'CR'=>'COSTA RICA',
						'CI'=>'COTE D IVOIRE',
						'HR'=>'CROATIA',
						'CU'=>'CUBA',
						'CY'=>'CYPRUS',
						'CZ'=>'CZECH REPUBLIC',
						'DK'=>'DENMARK',
						'DJ'=>'DJIBOUTI',
						'DM'=>'DOMINICA',
						'DO'=>'DOMINICAN REPUBLIC',
						'TP'=>'EAST TIMOR',
						'EC'=>'ECUADOR',
						'EG'=>'EGYPT',
						'SV'=>'EL SALVADOR',
						'GQ'=>'EQUATORIAL GUINEA',
						'ER'=>'ERITREA',
						'EE'=>'ESTONIA',
						'ET'=>'ETHIOPIA',
						'FK'=>'FALKLAND ISLANDS (MALVINAS)',
						'FO'=>'FAROE ISLANDS',
						'FJ'=>'FIJI',
						'FI'=>'FINLAND',
						'FR'=>'FRANCE',
						'GF'=>'FRENCH GUIANA',
						'PF'=>'FRENCH POLYNESIA',
						'TF'=>'FRENCH SOUTHERN TERRITORIES',
						'GA'=>'GABON',
						'GM'=>'GAMBIA',
						'GE'=>'GEORGIA',
						'DE'=>'GERMANY',
						'GH'=>'GHANA',
						'GI'=>'GIBRALTAR',
						'GR'=>'GREECE',
						'GL'=>'GREENLAND',
						'GD'=>'GRENADA',
						'GP'=>'GUADELOUPE',
						'GU'=>'GUAM',
						'GT'=>'GUATEMALA',
						'GN'=>'GUINEA',
						'GW'=>'GUINEA-BISSAU',
						'GY'=>'GUYANA',
						'HT'=>'HAITI',
						'HM'=>'HEARD ISLAND AND MCDONALD ISLANDS',
						'VA'=>'HOLY SEE (VATICAN CITY STATE)',
						'HN'=>'HONDURAS',
						'HK'=>'HONG KONG',
						'HU'=>'HUNGARY',
						'IS'=>'ICELAND',
						'IN'=>'INDIA',
						'ID'=>'INDONESIA',
						'IR'=>'IRAN, ISLAMIC REPUBLIC OF',
						'IQ'=>'IRAQ',
						'IE'=>'IRELAND',
						'IL'=>'ISRAEL',
						'IT'=>'ITALY',
						'JM'=>'JAMAICA',
						'JP'=>'JAPAN',
						'JO'=>'JORDAN',
						'KZ'=>'KAZAKSTAN',
						'KE'=>'KENYA',
						'KI'=>'KIRIBATI',
						'KP'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF',
						'KR'=>'KOREA REPUBLIC OF',
						'KW'=>'KUWAIT',
						'KG'=>'KYRGYZSTAN',
						'LA'=>'LAO PEOPLES DEMOCRATIC REPUBLIC',
						'LV'=>'LATVIA',
						'LB'=>'LEBANON',
						'LS'=>'LESOTHO',
						'LR'=>'LIBERIA',
						'LY'=>'LIBYAN ARAB JAMAHIRIYA',
						'LI'=>'LIECHTENSTEIN',
						'LT'=>'LITHUANIA',
						'LU'=>'LUXEMBOURG',
						'MO'=>'MACAU',
						'MK'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
						'MG'=>'MADAGASCAR',
						'MW'=>'MALAWI',
						'MY'=>'MALAYSIA',
						'MV'=>'MALDIVES',
						'ML'=>'MALI',
						'MT'=>'MALTA',
						'MH'=>'MARSHALL ISLANDS',
						'MQ'=>'MARTINIQUE',
						'MR'=>'MAURITANIA',
						'MU'=>'MAURITIUS',
						'YT'=>'MAYOTTE',
						'MX'=>'MEXICO',
						'FM'=>'MICRONESIA, FEDERATED STATES OF',
						'MD'=>'MOLDOVA, REPUBLIC OF',
						'MC'=>'MONACO',
						'MN'=>'MONGOLIA',
						'MS'=>'MONTSERRAT',
						'MA'=>'MOROCCO',
						'MZ'=>'MOZAMBIQUE',
						'MM'=>'MYANMAR',
						'NA'=>'NAMIBIA',
						'NR'=>'NAURU',
						'NP'=>'NEPAL',
						'NL'=>'NETHERLANDS',
						'AN'=>'NETHERLANDS ANTILLES',
						'NC'=>'NEW CALEDONIA',
						'NZ'=>'NEW ZEALAND',
						'NI'=>'NICARAGUA',
						'NE'=>'NIGER',
						'NG'=>'NIGERIA',
						'NU'=>'NIUE',
						'NF'=>'NORFOLK ISLAND',
						'MP'=>'NORTHERN MARIANA ISLANDS',
						'NO'=>'NORWAY',
						'OM'=>'OMAN',
						'PK'=>'PAKISTAN',
						'PW'=>'PALAU',
						'PS'=>'PALESTINIAN TERRITORY, OCCUPIED',
						'PA'=>'PANAMA',
						'PG'=>'PAPUA NEW GUINEA',
						'PY'=>'PARAGUAY',
						'PE'=>'PERU',
						'PH'=>'PHILIPPINES',
						'PN'=>'PITCAIRN',
						'PL'=>'POLAND',
						'PT'=>'PORTUGAL',
						'PR'=>'PUERTO RICO',
						'QA'=>'QATAR',
						'RE'=>'REUNION',
						'RO'=>'ROMANIA',
						'RU'=>'RUSSIAN FEDERATION',
						'RW'=>'RWANDA',
						'SH'=>'SAINT HELENA',
						'KN'=>'SAINT KITTS AND NEVIS',
						'LC'=>'SAINT LUCIA',
						'PM'=>'SAINT PIERRE AND MIQUELON',
						'VC'=>'SAINT VINCENT AND THE GRENADINES',
						'WS'=>'SAMOA',
						'SM'=>'SAN MARINO',
						'ST'=>'SAO TOME AND PRINCIPE',
						'SA'=>'SAUDI ARABIA',
						'SN'=>'SENEGAL',
						'SC'=>'SEYCHELLES',
						'SL'=>'SIERRA LEONE',
						'SG'=>'SINGAPORE',
						'SK'=>'SLOVAKIA',
						'SI'=>'SLOVENIA',
						'SB'=>'SOLOMON ISLANDS',
						'SO'=>'SOMALIA',
						'ZA'=>'SOUTH AFRICA',
						'GS'=>'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
						'ES'=>'SPAIN',
						'LK'=>'SRI LANKA',
						'SD'=>'SUDAN',
						'SR'=>'SURINAME',
						'SJ'=>'SVALBARD AND JAN MAYEN',
						'SZ'=>'SWAZILAND',
						'SE'=>'SWEDEN',
						'CH'=>'SWITZERLAND',
						'SY'=>'SYRIAN ARAB REPUBLIC',
						'TW'=>'TAIWAN, PROVINCE OF CHINA',
						'TJ'=>'TAJIKISTAN',
						'TZ'=>'TANZANIA, UNITED REPUBLIC OF',
						'TH'=>'THAILAND',
						'TG'=>'TOGO',
						'TK'=>'TOKELAU',
						'TO'=>'TONGA',
						'TT'=>'TRINIDAD AND TOBAGO',
						'TN'=>'TUNISIA',
						'TR'=>'TURKEY',
						'TM'=>'TURKMENISTAN',
						'TC'=>'TURKS AND CAICOS ISLANDS',
						'TV'=>'TUVALU',
						'UG'=>'UGANDA',
						'UA'=>'UKRAINE',
						'AE'=>'UNITED ARAB EMIRATES',
						'GB'=>'UNITED KINGDOM',
						'US'=>'UNITED STATES',
						'UM'=>'UNITED STATES MINOR OUTLYING ISLANDS',
						'UY'=>'URUGUAY',
						'UZ'=>'UZBEKISTAN',
						'VU'=>'VANUATU',
						'VE'=>'VENEZUELA',
						'VN'=>'VIET NAM',
						'VG'=>'VIRGIN ISLANDS, BRITISH',
						'VI'=>'VIRGIN ISLANDS, U.S.',
						'WF'=>'WALLIS AND FUTUNA',
						'EH'=>'WESTERN SAHARA',
						'YE'=>'YEMEN',
						'YU'=>'YUGOSLAVIA',
						'ZM'=>'ZAMBIA',
						'ZW'=>'ZIMBABWE',
				);
				?>
					
					<th><?php _e("Country",'wp-advanced-newsletter');?>*</th>
					<td>
						<select name="constant_contact_camp_country" style="width: 100%;" class="wan_required">
						<?php 
						foreach($countries as $cid=>$country)
						{
							?>
							<option value="<?php echo $cid?>"><?php echo $country;?></option>
							<?php 
						}	
						?>
						</select>
					<td></td>
				</tr>	
				<tr>
					<td><input type="submit" value="+ Add New Campaign" class="button button-primary" name="constant_contact_add_new_camapign"></td>
				</tr>
			</table>
		</form>
		<?php 
	}
}

if($email_service == 'MailChimp')
{
	$apikey = get_option ( PLUGIN_CONSTANT . 'mailchimp_api_key' );
	require_once WANL_DIR.'email-service/mailchimp-api/src/Mailchimp/MAPI.class.php';
	$api = new MCAPI( $apikey );
	
	if(isset($_GET['campaign_id']))
	{
		include 'mailchimp-campaign-detail.php';
	}
	else
	{
		if(isset($_POST['mailchimp_add_new_camapign']) || isset($_POST['mailchimp_update_camapign']))
		{
			$type = $_POST['mailchimp_camp_type'];
			$opts['list_id'] = $_POST['mailchimp_list_id'];
			$opts['title'] = $_POST['mailchimp_camp_title'];
			$opts['subject'] = $_POST['mailchimp_camp_subject'];
			$opts['from_email'] = $_POST['mailchimp_camp_email'];
			$opts['from_name'] = $_POST['mailchimp_camp_name'];
			$opts['tracking']=array('opens' => true, 'html_clicks' => true, 'text_clicks' => false);
			$opts['authenticate'] = false;
			
			$contents = array(
					'html'=> stripcslashes($_POST['mail_chimp_email_content']),
					'text' => ' *|UNSUB|*'
			);
			
			if(isset($_POST['mailchimp_add_new_camapign']))
			{
				$api->campaignCreate($type, $opts, $contents);
			}
			if(isset($_POST['mailchimp_update_camapign']))
			{
				$campaign_id = $_POST['campaign_id'];
				$api->campaignUpdate($campaign_id, 'title', $opts['title']);
			}
		}
		
		$all_campaigns = $api->campaigns();
		$notification_details=get_option(PLUGIN_CONSTANT.'notification');
		$notification_details=json_decode($notification_details);
		
		$mailchimp_lists = $api->lists();
		?>
		<h2><?php _e("Campaigns List",'wp-advanced-newsletter');?></h2>
		
		<table class="wp-list-table widefat fixed pages">
			<tr>
				
				<th class="manage-column" id="title" scope="col">
					<b><?php _e("Name",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="author" scope="col"><b><?php _e("Subscribers",'wp-advanced-newsletter');?></b></th>
				<th class="manage-column" id="date" scope="col">
					<b><?php _e("Status",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="date" scope="col">
					<b><?php _e("Send Time",'wp-advanced-newsletter');?></b>
				</th>
				<th class="manage-column" id="date" scope="col">
					<b><?php _e("View/Send/Delete",'wp-advanced-newsletter');?></b>
				</th>
			</tr>
			<?php 
			if(isset($all_campaigns['total']))
			{
				if($all_campaigns['total'] > 0)
				{
					$all_campaign = $all_campaigns['data'];
					foreach($all_campaign as $campaign)
					{
						?>
				<tr>
					
					<td class="manage-column"><b><?php echo $campaign['title'];?><b><p><b><?php _e("Created At",'wp-advanced-newsletter');?><b> : <?php echo $campaign['create_time'];?></p></td>
					<td class="manage-column"><?php echo $campaign['emails_sent'];?></td>
					<td class="manage-column"><?php echo $campaign['status'];?></td>	
					<td class="manage-column"><?php echo $campaign['send_time'];?></td>	
					<td class="manage-column">
						<a href="?page=advanced_newsletter_mail&tab=Campaigns&campaign_id=<?php echo $campaign['id'];?>" class="button button-primary"><?php _e("View",'wp-advanced-newsletter');?></a>
						<input type="button" value="Send" data-id="<?php echo $campaign['id'];?>" class="mailchimp_send_mail button button-primary">
						<input type="button" value="Delete" data-id="<?php echo $campaign['id'];?>" class="ced_ad_new_delete button button-primary">
						<img height="25px" src="<?php echo WANL_DIR_URL?>/assets/images/loading.gif" style="display:none;">	
					</td>	
				</tr>	
						<?php 
					}
				}	
			}
			?>
		</table>
		<br/>
		
		<h2><?php _e("Add Campaign",'wp-advanced-newsletter');?></h2>
		
		<form action="" method="post" id="mailchimp">
			<table class="wp-list-table widefat fixed pages">
				<tr>
					<th><?php _e("Campaign Type",'wp-advanced-newsletter');?>*</th>
					<td>
						<select name="mailchimp_camp_type" class="wan_required">
							<option value="regular"><?php _e("Regular",'wp-advanced-newsletter');?></option>
							<option value="plaintext"><?php _e("Plain Text",'wp-advanced-newsletter');?></option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th><?php _e("Campaign Title",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="mailchimp_camp_title" class="wan_required"></td>
					<td></td>
				</tr>	
				<?php 
			if(isset($mailchimp_lists['total']))
			{
				if($mailchimp_lists['total'] > 0)
				{
					$mailchimp_list = $mailchimp_lists['data'];
					
						?>
				<tr>	
					<th><?php _e("Lists to send to",'wp-advanced-newsletter');?>*</th>
					<td>
						<select name="mailchimp_list_id" class="wan_required">
			               <?php
			               foreach($mailchimp_list as $list)
						   {	
								echo '<option value="' . $list['id'] . '" >' . $list['name'] . '</option><br />';
			               }
			               ?>
			           </select>
				   </td>
				   <td></td>
			   </tr>
			   <?php 
			  }
		  }	
		  ?>
				<tr>	
					<th><?php _e("Subject",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="mailchimp_camp_subject" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("From Name",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="mailchimp_camp_name" class="wan_required"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("From Email",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="mailchimp_camp_email" class="wan_required"></td>
					<td></td>
				</tr>
				
				<tr>	
					<th><?php _e("Email Content",'wp-advanced-newsletter');?>*</th>
					<td colspan="2">
					<?php 
							$content = "";
							$editor_id = 'mail_chimp_email_content';
							$settings = array( 
										'media_buttons'    => false,
										'drag_drop_upload' => true,
										'dfw'              => true, 
										'teeny'            => true,
										'editor_height'    => 200,
										'editor_class'	   => 'wan_required',
										'textarea_name'    => "mail_chimp_email_content"
										);
							wp_editor( $content, $editor_id, $settings );
					?>
					</td>
				</tr>		
				<tr>
					<td><input type="submit" value="+ Add New Campaign" class="button button-primary" name="mailchimp_add_new_camapign"></td>
				</tr>
			</table>
		</form>
		<?php 
		}
}
?>