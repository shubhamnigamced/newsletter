<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$campaign_id = $_GET['campaign_id'];
$campaign_details = $api->campaigns(array('campaign_id'=>$campaign_id));
$campaign_detail = $campaign_details['data'][0];
$mailchimp_lists = $api->lists();
?>
<h2><?php _e("Update Campaign",'wp-advanced-newsletter');?></h2>
		
		<form action="?page=advanced_newsletter_mail&tab=Campaigns" method="post" id="mailchimp">
			<table class="wp-list-table widefat fixed pages" style="width: 98%;">
				<tr>
					<th><?php _e("Campaign Type",'wp-advanced-newsletter');?>*</th>
					<td>
						<input name="campaign_id" type="hidden" value="<?php echo $campaign_id?>">
						<select name="mailchimp_camp_type" class="wan_required">
							<option value="regular"><?php _e("Regular",'wp-advanced-newsletter');?></option>
							<option value="plaintext"><?php _e("Plain Text",'wp-advanced-newsletter');?></option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th><?php _e("Campaign Title",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="mailchimp_camp_title" class="wan_required" value="<?php echo $campaign_detail['title'];?>"></td>
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
						   		$checked = '';
						   		if($list['id'] == $campaign_detail['list_id'])
						   		{
						   			$checked = 'checked="checked"';
						   		}	
						   		?>
						   		<option value="<?php echo $list['id']?>" <?php echo $checked?>><?php echo $list['name']?></option>
						   		<?php 
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
					<td><input type="text" name="mailchimp_camp_subject" class="wan_required" value="<?php echo $campaign_detail['subject'];?>"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("From Name",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="mailchimp_camp_name" class="wan_required" value="<?php echo $campaign_detail['from_name'];?>"></td>
					<td></td>
				</tr>	
				<tr>	
					<th><?php _e("From Email",'wp-advanced-newsletter');?>*</th>
					<td><input type="text" name="mailchimp_camp_email" class="wan_required" value="<?php echo $campaign_detail['from_email'];?>"></td>
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
					<td><input type="submit" value="Update Campaign" class="button button-primary" name="mailchimp_update_camapign"></td>
				</tr>
			</table>
		</form>