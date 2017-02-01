<?php 
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$mailchimp_checked = "checked='checked'";
$cons_con_checked = "checked='checked'";
$act_camp_checked = "checked='checked'";

if(isset($_POST['adn_email_subscripiton_submit']))
{
	unset($_POST['adn_email_subscripiton_submit']);
	$enable_subscription = json_encode($_POST);
	update_option('adn_email_sub_enable', $enable_subscription);
}

$enable_subscription_enc = get_option('adn_email_sub_enable', $enable_subscription);
if(isset($enable_subscription_enc))
{
	$enable_subscription = json_decode($enable_subscription_enc, true);
}
if(!isset($enable_subscription['adn_email_subscripiton_mailchimp'])) 
{
	$enable_subscription['adn_email_subscripiton_mailchimp'] = '';
	$mailchimp_checked = "";
}
if(!isset($enable_subscription['adn_email_subscripiton_cons_con']))
{	
	$enable_subscription['adn_email_subscripiton_cons_con'] = '';
	$cons_con_checked = "";
}
if(!isset($enable_subscription['adn_email_subscripiton_act_camp']))
{	
	$enable_subscription['adn_email_subscripiton_act_camp'] = '';
	$act_camp_checked = "";
}	

?>
<div class="clear"></div>
<div id="overlay-loading" class="hide_section">
	<svg width="110px" height="110px" xmlns="http://cedcommerce.com" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-ring-alt">
		<rect x="0" y="0" width="100" height="100" fill="none" class="bk"/>
		<circle cx="50" cy="50" r="40" stroke="#85a69e" fill="none" stroke-width="10" stroke-linecap="round"/>
		<circle cx="50" cy="50" r="40" stroke="#5cffd6" fill="none" stroke-width="6" stroke-linecap="round">
			<animate attributeName="stroke-dashoffset" dur="2s" repeatCount="indefinite" from="0" to="502"/>
			<animate attributeName="stroke-dasharray" dur="2s" repeatCount="indefinite" values="150.6 100.4;1 250;150.6 100.4"/>
		</circle>
	</svg>
</div>
<ul id="service_list">
	<li class="advancenewsletter_email_service">
		<div class="advancednewsletter-popup-options mailchimp">
			<form action="" method="post">
		
			<table class="advancednewsletter-email-options form-table">
				<tbody>
					
					<tr>
						<td>
							<h2><?php echo __("Mailchimp Setting:","wp-advanced-newsletter");?></h2>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __("Enable:","wp-advanced-newsletter");?>
						</td>
						<td>
							<input type="checkbox" value="Mailchimp" <?php echo $mailchimp_checked?> name="adn_email_subscripiton_mailchimp"><?php _e("Mailchimp",'wp-advanced-newsletter');?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __("Mailchimp API Key:","wp-advanced-newsletter");?>
						</td>
						<td>
							<input  placeholder="<?php echo __("Enter Api Key here ...","wp-advanced-newsletter");?>"  type="text" class="advancednewsletter-popup-mailchimpapikey email_mailchimp regular-text" value="<?php echo get_option(PLUGIN_CONSTANT.'mailchimp_api_key');?>" id="advancednewsletter-popup-mailchimpapikey">
						</td>
						<td>	
							<input type="button" value="Submit" class="mailchimp_api_key submit_change button button-primary" id="mailchimp_api_key">
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __("List :","wp-advanced-newsletter");?>
						</td>
						<td>
							<select id="mailchimp_data_val">
							 	<option value="select">
							 		<?php echo __("Select","wp-advanced-newsletter");?>
							 	</option>
							 	<?php $list_id_append=get_option(PLUGIN_CONSTANT.'mailchimp_list_id_append');
							 	if (isset($list_id_append))
							 	{
							 		foreach($list_id_append as $key=>$val)
							 		{?>
							 			<option value="<?php echo $val[0];?>" <?php if ($val[0] == get_option(PLUGIN_CONSTANT.'mailchimp_list_id')){?>selected<?php }?>><?php echo $val[1];?></option>
							  <?php }
							 	} ?>
							</select>
						</td>
						<td>
							<input type="button" value="Update" id="mailchimp_api_list" class="button button-primary">
						</td> 
					</tr>
								
					<tr>
						<td>
							<h2><?php echo __("Constant contact Setting:","wp-advanced-newsletter");?></h2>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __("Enable:","wp-advanced-newsletter");?>
						</td>
						<td>
								<input type="checkbox" value="Constant Contact" <?php echo $cons_con_checked?> name="adn_email_subscripiton_cons_con"><?php _e("Constant Contact",'wp-advanced-newsletter');?>
					</td>
					</tr>
					<tr>
						<td>
							<?php echo __("Constant contact API Key:","wp-advanced-newsletter");?>
						</td>
						<td>
							<input type="text" placeholder="<?php echo __("Enter Api Key here ...","wp-advanced-newsletter");?>" class="advancednewsletter-popup-constantcontact email_constantcontact regular-text" value="<?php echo get_option(PLUGIN_CONSTANT.'const_cont_api_key');?>" id="advancednewsletter-popup-constantcontact">
						</td>
						<td>
						</td>
					</tr>	
					<tr>
						<td>
							<?php echo __("Constant contact access token:","wp-advanced-newsletter");?>
						</td>
						<td>
							<input  placeholder="<?php echo __("Enter Access Token here ...","wp-advanced-newsletter");?>" type="text" class="advancednewsletter-constantcontact_token regular-text" value="<?php echo get_option(PLUGIN_CONSTANT.'cons_cont_access_token');?>" id="advancednewsletter-constantcontact_token">
						</td>
						<td>
							<input type="button" value="Submit" class="button button-primary advancednewsletter-constantcontacttoken_save submit_change" id="advancednewsletter-constantcontacttoken_save">
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __("List :","wp-advanced-newsletter");?>
						</td>
						<td>
							<select id="constantcontact_data_val">
							 	<option value="select">
							 		<?php echo __("Select","wp-advanced-newsletter");?>
							 	</option>
						 		<?php $list_id_append=get_option(PLUGIN_CONSTANT.'const_cont_list_id_append');
								 	if (isset($list_id_append))
								 	{
								 		foreach($list_id_append as $key=>$val)
								 		{?>
								 			<option value="<?php echo $val[0];?>" <?php if ($val[0] == get_option(PLUGIN_CONSTANT.'const_cont_list_id')){?>selected<?php }?>><?php echo $val[1];?></option>
								  <?php }
								 	} ?>
							</select>
						</td>
						<td>
							<input type="button" value="Update" id="constantcontact_api_list" class="button button-primary">
						</td> 
					</tr>							
					<tr>
						<td>
							<h2><?php echo __("Active campaign Setting:","wp-advanced-newsletter");?></h2>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __("Enable:","wp-advanced-newsletter");?>
						</td>
						<td>
							<input type="checkbox" value="Active Campaign" <?php echo $act_camp_checked?> name="adn_email_subscripiton_act_camp"><?php _e("Active Campaign",'wp-advanced-newsletter');?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __("Active campaign API Url :","wp-advanced-newsletter");?>
						</td>
						<td>
							<input type="text" placeholder="<?php echo __("Enter Api Url here ...","wp-advanced-newsletter");?>"  class="advancednewsletter-popup-activecampaign email_activecampaign regular-text" value="<?php echo get_option(PLUGIN_CONSTANT.'active_campaign_access_url');?>" id="advancednewsletter-popup-activecampaign">
						</td>
					</tr>	
					<tr>
						<td>
							<?php echo __("Active campaign access Key:","wp-advanced-newsletter");?>
						</td>
						<td>
							<input type="text" placeholder="<?php echo __("Enter Access Key here ...","wp-advanced-newsletter");?>" class="advancednewsletter-activecampaign_accesskey regular-text" value="<?php echo get_option(PLUGIN_CONSTANT.'active_campaign_api_key');?>" id="advancednewsletter-activecampaign_accesskey">
						</td>
						<td>
							<input type="button" value="Submit" class="button button-primary advancednewsletter-activecampaigntoken_save submit_change" id="advancednewsletter-activecampaigntoken_save">
						</td>
					</tr>
					<tr>
						<td>
							<?php echo __("List :","wp-advanced-newsletter");?>
						</td>
						<td>
							<select id="activecampaign_val">
							 	<option value="select"><?php echo __("Select","wp-advanced-newsletter");?></option>
							 	<?php $list_id_append=get_option(PLUGIN_CONSTANT.'active_campaign_list_id_append');
							 	if (isset($list_id_append))
							 	{
							 		foreach($list_id_append as $key=>$val)
							 		{?>
							 			<option value="<?php echo $val[0];?>" <?php if ($val[0] == get_option(PLUGIN_CONSTANT.'activecampaign_list_id')){?>selected<?php }?>><?php echo $val[1];?></option>
							  <?php }
							 	} ?>
							</select>
						</td>
						<td>
							<input type="button" value="Update" id="activecampaign_api_list" class="button button-primary">
						</td> 
					</tr>
					<tr>
						<td>
							<input type="submit" value="Save" name="adn_email_subscripiton_submit" class="button button-primary">
						</td>
					</tr>		
				</tbody>
			</table>	
		</div>	
		</form>
	</li>
</ul>
<div id="overlay-loading" class="hide_section">
	<svg width="110px" height="110px" xmlns="http://cedcommerce.com" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-ring-alt">
		<rect x="0" y="0" width="100" height="100" fill="none" class="bk"/>
		<circle cx="50" cy="50" r="40" stroke="#85a69e" fill="none" stroke-width="10" stroke-linecap="round"/>
		<circle cx="50" cy="50" r="40" stroke="#5cffd6" fill="none" stroke-width="6" stroke-linecap="round">
			<animate attributeName="stroke-dashoffset" dur="2s" repeatCount="indefinite" from="0" to="502"/>
			<animate attributeName="stroke-dasharray" dur="2s" repeatCount="indefinite" values="150.6 100.4;1 250;150.6 100.4"/>
		</circle>
	</svg>
