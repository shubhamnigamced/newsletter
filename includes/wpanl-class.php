<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Ctct\ConstantContact;
use Ctct\Services;
use Ctct\Util\Config;
use Ctct\Services\EmailMarketingService;
use Ctct\Services\CampaignScheduleService;
use Ctct\Components\EmailMarketing\Campaign;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\EmailMarketing\MessageFooter;
use Ctct\Components\EmailMarketing\Schedule;
use Ctct\Exceptions\CtctException;

/**
 * Checks if Product_Shortlist class has been defined
 *
 * @author CedCommerce <http://cedcommerce.com>
 */
if( ! class_exists( 'wpanl' ) ) 
{

	/**
	 * Creates the class named wpanl
	 *
	 * @category class
	 * @author CedCommerce
	 */
	class wpanl {
	
			/**
			 * Initializes the settings over here
			 *
			 * @category function
			 * @author CedCommerce <http://cedcommerce.com>
			 * @access public
			 */
			public function __construct() { 
				// Enqueue scripts and Css in backend Front end
				
				add_action ( 'admin_init', array ($this,'admin_panel_scripts') );
				add_action ( 'wp_enqueue_scripts', array ($this,'register_script') );
				add_action ( 'wp_enqueue_scripts', array ($this,'enqueue_script') );
				add_action( 'admin_menu', array( $this, 'advanced_newsletter_fun' ) );
				add_action( 'admin_enqueue_scripts', array($this,'advanced_newsletter_css'));
				
				// Email subcription hooks and filter
				
				add_action ( 'wp_ajax_ced_email_service', array ($this,	'prefix_ajax_ced_email_service'	) );
				
				//Mailchimp hooks and filter
				
				add_action ( 'wp_ajax_mailchimp_get_api', array ($this,'prefix_ajax_mailchimp_get_api') );
				add_action ( 'wp_ajax_mailchimp_add_data', array ($this,'prefix_ajax_mailchimp_add_data') );
				add_action ( 'wp_ajax_mailchimp_send_mail', array($this,'ced_mailchimp_send_mail' ));
				add_action ( 'wp_ajax_nopriv_mailchimp_send_mail', array($this,'ced_mailchimp_send_mail'));
				add_action ( 'wp_ajax_mailchimp_delete_campaign', array ($this,'prefix_ajax_mailchimp_delete_campaign') );
				add_action ( 'wp_ajax_nopriv_mailchimp_delete_campaign', array ($this,'prefix_ajax_mailchimp_delete_campaign') );
				add_action ( 'wp_ajax_mailchimp_unsubscribe_mail', array($this,'ced_mailchimp_unsubscribe_mail' ));
				add_action ( 'wp_ajax_nopriv_mailchimp_unsubscribe_mail', array($this,'ced_mailchimp_unsubscribe_mail'));
				
				
				//Constact Contact hooks and filter
				
				add_action ( 'wp_ajax_constant_contact_api_func', array ($this,'prefix_ajax_constant_contact_api_func') );
				add_action ( 'wp_ajax_const_cont_update_list', array ($this,'prefix_ajax_const_cont_update_list') );
				add_action ( 'wp_ajax_constant_contact_unsubscribe_mail', array($this,'ced_constant_contact_unsubscribe_mail' ));
				add_action ( 'wp_ajax_nopriv_constant_contact_unsubscribe_mail', array($this,'ced_constant_contact_unsubscribe_mail'));
				add_action ( 'wp_ajax_constant_contact_delete_campaign', array ($this,'prefix_ajax_constant_contact_delete_campaign') );
				add_action ( 'wp_ajax_nopriv_constant_contact_delete_campaign', array ($this,'prefix_ajax_constant_contact_delete_campaign') );
					
				
				// Active Campaign hooks and filter
				
				add_action ( 'wp_ajax_active_campaign_func', array ($this,'prefix_ajax_active_campaign_func') );
				add_action ( 'wp_ajax_activecampaign_update_list', array ($this,'prefix_ajax_activecampaign_update_list') );
				add_action ( 'wp_ajax_active_camp_delete_campaign', array ($this,'prefix_ajax_active_camp_delete_campaign') );
				add_action ( 'wp_ajax_nopriv_active_camp_delete_campaign', array ($this,'prefix_ajax_active_camp_delete_campaign') );
				add_action ( 'wp_ajax_active_camp_unsubscribe_mail', array ($this,'prefix_ajax_active_camp_unsubscribe_mail') );
				add_action ( 'wp_ajax_nopriv_active_camp_unsubscribe_mail', array ($this,'prefix_ajax_active_camp_unsubscribe_mail') );
				add_action ( 'wp_ajax_activecamp_send_mail', array ($this,'prefix_ajax_activecamp_send_mail') );
				add_action ( 'wp_ajax_nopriv_activecamp_send_mail', array ($this,'prefix_ajax_activecamp_send_mail') );
				
					//Form Section hooks filter
				
				add_action ( 'wp_ajax_newsletter_popup_content', array ($this,'prefix_ajax_newsletter_popup_content') );
				add_action ( 'wp_ajax_nopriv_newsletter_popup_content', array ($this,'prefix_ajax_newsletter_popup_content') );
				add_action ( 'wp_ajax_add_new_subscriber', array ($this,'prefix_ajax_add_new_subscriber') );
			}
			
			//Frontend Newsletter Html
			
			function prefix_ajax_newsletter_popup_content() 
			{
				if (is_admin ()) {
					if (isset ( $_COOKIE ['advanced_newsletter_cookie'] )) {
					} else {
						$html = '';
						$html .= '<form id="advanced_newsletter_signup" name="advanced_newsletter_signup">';
						$html.=  '<div class="subscribe_div">';
						$html .= '<div class="advanced_newsletter_title">';
						if(get_option(PLUGIN_CONSTANT.'advanced_newsletter_wp_color_val')['wp_popup_icon_image'] != ""){
							$html.= '<img src="'.get_option(PLUGIN_CONSTANT.'advanced_newsletter_wp_color_val')['wp_popup_icon_image'].'">';
						}
						else {
							$html.= '<img src="' . WANL_DIR_URL . 'assets/images/mail.png">';
						}
						$html .= '<span>'.__("Subscribers",'wp-advanced-newsletter').'<span> </div>';
						$html .= '<div class="advanced_newsletter_sub">'.$popup_settings ['subtitle']. '</div>';
						$html .=  '</div>';
						$html .= '<div id="user_email_address">';
						$html .=  '<div class="wp_news_input">';
						$html .= '<input type="email" name="advanced_newsletter_email" id="advanced_newsletter_email" placeholder="' . $popup_settings ['email'] . '"  />';
						$html .=  '</div>';
						$html .=  '<div class="wp_news_btn">';
						$html .= '<input type="button" class="advanced_newsletter_sumit_data" name="subscribe_newsletter_form" value="SUBMIT"' . $popup_settings ['submit'] . '">';
						$html .= '</div>';
						$html .= '<div id="load_img">';
						$html .= '<img src="' . WANL_DIR_URL . 'assets/images/ajax-loader.gif" id="load" class="hide_section">';
						$html .= '</div>';
						$html .=  '</div>';
						$html .= '<div class="mail_error">';
						$html .= '</div>';
						$html .= '<div class="mail_error_success">';
						$html .= '</div>';
						$html .= '</form>';
						
						$css = "#user_email_address .wp_news_input > input {
									    margin: 15px 0;
									    width: 100%;
									    padding: 10px 5px;
									}
									#cboxLoadedContent > form {
									    padding: 15px;
									}
									#load_img{
										display:none;
									}
									.wp_news_btn input {
									    background: #1E8CBE none repeat scroll 0 0;
									    border: medium none;
									    color: #fff;
									    padding: 7px 20px;
									}
									.advanced_newsletter_title img,
									.advanced_newsletter_title span {
									    display: inline-block;
									    font-size: 18px;
									    vertical-align: middle;
									}
									.advanced_newsletter_title > span {
									    margin-left: 15px;
									    margin-top: 15px;
									}";
						
						$html .= '<style type="text/css">'.$css.'</style>';
						echo $html . '---' . "1";
					}
				}
				exit ();
			}
			
		
		/**
		 ****************************************************************
	 	 *                 MAILCHIMP FUNCTION                           *                    
	  	 ****************************************************************
	  	 **/
		function ced_mailchimp_unsubscribe_mail()
		{
			$id = $_POST['id'];
			$email_address = $_POST['email'];
			$apikey = get_option ( PLUGIN_CONSTANT . 'mailchimp_api_key' );
			require_once WANL_DIR.'email-service/mailchimp-api/src/Mailchimp/MAPI.class.php';
			$api = new MCAPI( $apikey );
			if($api->listUnsubscribe($id, $email_address))
			{
				echo 'success';
			}
			else
			{
				echo 'failure';
			}
			die;
		}	
		
		/**
		 * function to send mail to subscribers
		 * @name newsletter_send_now
		 * @author Cedcommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_mailchimp_send_mail()
		{
			$message = array();
			$campaign_id = $_POST['id'];
			require_once WANL_DIR.'email-service/mailchimp-api/src/Mailchimp/MAPI.class.php';
			$apikey = get_option ( PLUGIN_CONSTANT . 'mailchimp_api_key' );
			$api = new MCAPI($apikey);
			$retval = $api->campaignSendNow($campaign_id);
			if ($api->errorCode){
				$message['message'] = $api->errorMessage;
				$message['status'] = false;
			} else {
				$message['message'] = "Campaign Sent!\n";
				$message['status'] = true;
			}
				
			echo json_encode($message);
			die;
		}
		
		/**
		 * This function is for update mailchimp api key and generate list
		 *
		 * @name prefix_ajax_mailchimp_get_api()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function prefix_ajax_mailchimp_get_api() {
				
			$mailchimp_api_key = sanitize_text_field ( trim ( $_POST ['mailchimp_api_key'] ) );
			$mailchimp_api = get_option ( PLUGIN_CONSTANT . 'mailchimp_api_key' );
			update_option ( PLUGIN_CONSTANT . 'mailchimp_api_key', $mailchimp_api_key );
				
			$Mchimp = new Mailchimp ( $mailchimp_api_key );
			$M_Lists = new Mailchimp_Lists ( $Mchimp );
			$subscriber_list = $M_Lists->getList();
			$sub_arr = $subscriber_list ['data'];
			$arr = array ();
			$html = "";
			foreach ( $sub_arr as $key => $val ) {
				$arr [$key] [] = $val ['id'];
				$arr [$key] [] = $val ['name'];
				update_option ( PLUGIN_CONSTANT . 'mailchimp_list_id_append', $arr );
				$html .= "<option value='" . $val ['id'] . "'>" . $val ['name'] . "</option>";
			}
			echo $html;
			exit ();
		}
		
		/**
		 * This function is for update mailchimp list id
		 *
		 * @name prefix_ajax_mailchimp_add_data()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function prefix_ajax_mailchimp_add_data() {
			$list_id = sanitize_text_field ( trim ( $_POST ['mailchimp_list_id'] ) );
			if ($list_id == "select") {
				echo 0;
			} else {
				update_option ( PLUGIN_CONSTANT . 'mailchimp_list_id', $list_id );
				echo 1;
			}
			exit ();
		}
		
			
		/**
		 ****************************************************************
		 *                  CONSTANT CONTACT FUNCTION                   *
		 ****************************************************************
		 **/
			
		function prefix_ajax_constant_contact_delete_campaign()
		{
			$id = $_POST['id'];
			require_once WANL_DIR.'email-service/constant_contact_api/src/Ctct/autoload.php';
			$cons_cont_api = get_option ( PLUGIN_CONSTANT . 'const_cont_api_key' );
			$cons_cont_access = get_option ( PLUGIN_CONSTANT . 'cons_cont_access_token' );
			$cc = new EmailMarketingService ( $cons_cont_api );
			if($cc->deleteCampaign($cons_cont_access, $id))
			{
				echo 'success';
			}
			else
			{
				echo 'failure';
			}
			die;
		}
			
		function ced_constant_contact_unsubscribe_mail()
		{
			$id = $_POST['id'];
			require_once WANL_DIR.'email-service/constant_contact_api/src/Ctct/autoload.php';
			$cons_cont_api = get_option ( PLUGIN_CONSTANT . 'const_cont_api_key' );
			$cons_cont_access = get_option ( PLUGIN_CONSTANT . 'cons_cont_access_token' );
			$cc = new ConstantContact ( $cons_cont_api );
			$remove_contact_lists = $cc->deleteContact($cons_cont_access, $id);
			if($remove_contact_lists)
			{
				echo 'success';
			}
			else
			{
				echo 'failure';
			}
			die;
		}	
	
		/**
		 * This function is for update constant contact api key ,access token and generate list
		 *
		 * @name prefix_ajax_constant_contact_api_func()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function prefix_ajax_constant_contact_api_func() 
		{
			require_once WANL_DIR.'email-service/constant_contact_api/src/Ctct/autoload.php';
			$cons_cont_api_key = sanitize_text_field ( trim ( $_POST ['const_cont_api_key'] ) );
			$cons_cont_access_token = sanitize_text_field ( trim ( $_POST ['const_cont_access_token'] ) );
			$cons_cont_api = get_option ( PLUGIN_CONSTANT . 'const_cont_api_key' );
			$cons_cont_access = get_option ( PLUGIN_CONSTANT . 'cons_cont_access_token' );
			update_option ( PLUGIN_CONSTANT . 'const_cont_api_key', $cons_cont_api_key );
			update_option ( PLUGIN_CONSTANT . 'cons_cont_access_token', $cons_cont_access_token );
			define ( "APIKEY", $cons_cont_api_key );
			define ( "ACCESS_TOKEN", $cons_cont_access_token );
			$cc = new ConstantContact ( APIKEY );
			$lists = $cc->getLists ( ACCESS_TOKEN );
				
			$arr = array ();
			$html_new = '';
			if (! empty ( $lists )) {
				foreach ( $lists as $key => $list ) {
					$arr [$key] [] = $list->id;
					$arr [$key] [] = $list->name;
					update_option ( PLUGIN_CONSTANT . 'const_cont_list_id_append', $arr );
					$html_new .= "<option value='" . $list->id . "'>" . $list->name . "</option>";
				}
			}
			echo $html_new;
			exit ();
		}
		
		/**
		 * This function is for update constant contact list id
		 *
		 * @name prefix_ajax_const_cont_update_list()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function prefix_ajax_const_cont_update_list()
		{
			$list_id = sanitize_text_field ( trim ( $_POST ['const_cont_list_id'] ) );
			if ($list_id == "select") {
				echo 0;
			} else {
				update_option ( PLUGIN_CONSTANT . 'const_cont_list_id', $list_id );
				echo 1;
			}
			exit ();
		}
		
		/**
		 ****************************************************************
		 *                 ACTIVE CAMPAIGN FUNCTION                     *
		 ****************************************************************
		 **/
	
		function prefix_ajax_activecamp_send_mail()
		{
			$campaign_id = $_POST['id'];
			$actcamp_key = get_option ( PLUGIN_CONSTANT . 'active_campaign_api_key' );
			$actcamp_url = get_option ( PLUGIN_CONSTANT . 'active_campaign_access_url' );
			define ( "API_URL", $actcamp_url );
			define ( "API_KEY", $actcamp_key );
			$ac = new ActiveCampaign ( API_URL, API_KEY );
			$camp_details = $ac->api("campaign/list_?ids=$campaign_id");
			if(isset($camp_details) && $camp_details->result_code == 1)
			{
				foreach($camp_details as $camp_detail)
				{
					if(isset($camp_detail->id))
					{
						$campaign_detail = $camp_detail;
					}
				}
				$campaign_msg_id = $campaign_detail->messageslist;
				$campaign_list_id = $campaign_detail->listslist;
				$camp_details = $ac->api("campaign/send?campaignid=$campaign_id&messageid=$campaign_msg_id&type=mime&action=send&email=mohdjafar@cedcoss.com");
				if($camp_details->success == 1)
				{
					echo 'success';
				}
			}
			die;	
		}
			
		function prefix_ajax_active_camp_unsubscribe_mail()
		{
			$id = $_POST['id'];
			$actcamp_key = get_option ( PLUGIN_CONSTANT . 'active_campaign_api_key' );
			$actcamp_url = get_option ( PLUGIN_CONSTANT . 'active_campaign_access_url' );
			define ( "API_URL", $actcamp_url );
			define ( "API_KEY", $actcamp_key );
			$ac = new ActiveCampaign ( API_URL, API_KEY );
			$deleteResponse = $ac->api("contact/delete?id=$id");
			if($deleteResponse->success == 1)
			{
				echo 'success';
			}
			die;
		}
			
			
		function prefix_ajax_active_camp_delete_campaign()
		{
			$id = $_POST['id'];
			$actcamp_key = get_option ( PLUGIN_CONSTANT . 'active_campaign_api_key' );
			$actcamp_url = get_option ( PLUGIN_CONSTANT . 'active_campaign_access_url' );
			define ( "API_URL", $actcamp_url );
			define ( "API_KEY", $actcamp_key );
			$ac = new ActiveCampaign ( API_URL, API_KEY );
			$deleteResponse = $ac->api("campaign/delete?id=$id");
			if($deleteResponse->success == 1)
			{
				echo 'success';
			}
			die;
		}
		
	
		/**
		 * This function is for update active campaign api key ,access url and generate list
		 *
		 * @name prefix_ajax_active_campaign_func()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function prefix_ajax_active_campaign_func()
		{
			$active_campaign_api_key = sanitize_text_field ( trim ( $_POST ['active_campaign_api_key'] ) );
			$active_campaign_access_url = sanitize_text_field ( trim ( $_POST ['active_campaign_access_url'] ) );
			$active_campaign_api = get_option ( PLUGIN_CONSTANT . 'active_campaign_api_key' );
			update_option ( PLUGIN_CONSTANT . 'active_campaign_api_key', $active_campaign_api_key );
			update_option ( PLUGIN_CONSTANT . 'active_campaign_access_url', $active_campaign_access_url );
			define ( "API_URL", $active_campaign_access_url );
			define ( "API_KEY", $active_campaign_api_key );
			$ac = new ActiveCampaign ( API_URL, API_KEY );
			$args = array (
					'api_key' => API_KEY,
					'api_action' => 'list_list',
					'api_output' => 'json',
					'ids' => 'all',
					'full' => 0
			);
			$data_field = '';
			foreach ( $args as $key => $value ) {
				$data_field .= $key . '=' . urlencode ( $value ) . '&';
			}
			$data_field = rtrim ( $data_field, '&' );
			$apiurl = API_URL . '/admin/api.php?' . $data_field;
			$objFetchSite = _wp_http_get_object ();
			$result_val = $objFetchSite->get ( $apiurl );
			if (isset ( $result_val ['body'] )) {
				$output_data = json_decode ( $result_val ['body'], true );
				if ($result_val ['response'] ['code'] == 200) {
					$arr = array ();
					$html_ac = '';
					foreach ( $output_data as $key => $val ) {
						if (is_array ( $val ) && isset ( $val ['id'] ) && isset ( $val ['name'] )) {
							$arr [$key] [] = $val ['id'];
							$arr [$key] [] = $val ['name'];
							update_option ( PLUGIN_CONSTANT . 'active_campaign_list_id_append', $arr );
							$html_ac .= "<option value='" . $val ['id'] . "'>" . $val ['name'] . "</option>";
						}
					}
					echo $html_ac;
				} else {
					echo 0;
				}
			}
			exit ();
		}
		
		
		/**
		 * This function is for update active campaign list id
		 *
		 * @name prefix_ajax_activecampaign_update_list()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function prefix_ajax_activecampaign_update_list()
		{
			$list_id = sanitize_text_field ( trim ( $_POST ['activecampaign_list_id'] ) );
			if ($list_id == "select") {
				echo 0;
			} else {
				update_option ( PLUGIN_CONSTANT . 'activecampaign_list_id', $list_id );
				echo 1;
			}
			exit ();
		}
			
		/**
		 * Main Settings Page
		 */
		
		public function advanced_newsletter_fun()
		{ 
			include_once WANL_DIR.'/includes/main-setting.php';
		}
			
		/**
		 * CSS
		 */
		public function advanced_newsletter_css()
		{
			wp_enqueue_style( 'wpa_newsletter_css', WANL_DIR_URL.'assets/css/wanl.css');
			wp_enqueue_style( 'wpa_newsletter_fulldatetime_css', WANL_DIR_URL.'assets/css/jquery.datetimepicker.min.css');
			wp_enqueue_script( 'wpa_newsletter_fulldatetime_js', WANL_DIR_URL.'assets/js/jquery.datetimepicker.full.min.js',array('jquery') );
		}
			
		/**
		 * This function is for update value of email subscriber
		 *
		 * @name prefix_ajax_email_service()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function prefix_ajax_ced_email_service() 
		{
			$email_service_name = sanitize_text_field ( trim ( $_POST ['email_service_name'] ) );
			update_option ( PLUGIN_CONSTANT . 'email_subscriber', $email_service_name );
			echo "success";
			exit ();
		}
			
		/**
		 * This function is for add contact in email subscriber
		 *
		 * @name prefix_ajax_add_new_subscriber()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function prefix_ajax_add_new_subscriber() 
		{
			$mail_send = false;
			
			$email=sanitize_text_field ( trim ( $_POST ['email'] ) );
			
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$response['status'] = false;
				$response['message'] = "Invalid Email !!";
				echo json_encode($response);
				die;
			}
			$enable_subscription_enc = get_option('adn_email_sub_enable', $enable_subscription);
			if(isset($enable_subscription_enc))
			{
				$enable_subscription = json_decode($enable_subscription_enc, true);
			}
			
			if(isset($enable_subscription['adn_email_subscripiton_mailchimp']))
			{
				$api_key = get_option ( PLUGIN_CONSTANT . 'mailchimp_api_key' );
				if ($api_key) 
				{
					$list_id = get_option ( PLUGIN_CONSTANT . 'mailchimp_list_id' );
					if ($list_id) 
					{
						require_once WANL_DIR.'email-service/mailchimp-api/src/Mailchimp.php';
						$Mchimp = new Mailchimp ( $api_key );
						$M_Lists = new Mailchimp_Lists ( $Mchimp );
						$pattern = "/^[A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z.]{2,4}$/";
						if (preg_match ( $pattern, $email )) 
						{
							$subscriber = $Mchimp->lists->members ( $list_id,$status='subscribed');
							try
							{
								if(is_array($subscriber['data']))
								{
									foreach ($subscriber['data'] as $user_key=>$user_val)
									{
										if (is_array($user_val))
										{
											$subscribed_users[]=$user_val['email'];
										}
									}
								}
								if(is_array($subscribed_users))
								{
									if (!in_array($email, $subscribed_users))
									{
										$subscriber = $Mchimp->lists->subscribe ( $list_id, array (
												"email" => sanitize_text_field ( trim ( $_POST ['email'] ) )
										) );
									}
								}
								else 
								{
									$subscriber = $Mchimp->lists->subscribe ( $list_id, array (
											"email" => sanitize_text_field ( trim ( $_POST ['email'] ) )
									) );
								}
							}
							catch(Exception $errors)
							{
								
							}
							if (! empty ( $subscriber ['leid'] )) 
							{
								$mail_send = true;
							} 
						}
					} 
				}
			}
			if(isset($enable_subscription['adn_email_subscripiton_cons_con']))
			{
				require_once WANL_DIR.'email-service/constant_contact_api/src/Ctct/autoload.php';
				$api_key = trim ( get_option ( PLUGIN_CONSTANT . 'const_cont_api_key' ) );
				$list_id = trim ( get_option ( PLUGIN_CONSTANT . 'const_cont_list_id' ) );
				$access_token = trim ( get_option ( PLUGIN_CONSTANT . 'cons_cont_access_token' ) );
				$email = sanitize_text_field ( trim ( $_POST ['email'] ) );
				define ( "APIKEY", $api_key );
				define ( "ACCESS_TOKEN", $access_token );
				if ($api_key) 
				{
					if ($list_id) 
					{
						$cc = new ConstantContact ( APIKEY );
						$response = $cc->getContacts ( ACCESS_TOKEN, array (
								"email" => sanitize_text_field ( trim ( $_POST ['email'] ) )
						) );
						$opt_action = array (
								'action_by' => 'ACTION_BY_VISITOR'
						);
						
						if (empty ( $response->results )) 
						{
							$contact = new Contact();
							$pattern = "/^[A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z.]{2,4}$/";
							if (preg_match ( $pattern, $email )) 
							{
								$contact->addEmail ( sanitize_text_field ( trim ( $_POST ['email'] ) ) );
								$contact->addList ( $list_id );
								$returnContact = $cc->addContact ( ACCESS_TOKEN, $contact, $opt_action );
								if ($returnContact == null) 
								{
									$mail_send = true;
								}
							} 
						} 
						else 
						{
							$contact = $response->results [0];
							if ($contact instanceof Contact) 
							{
								$contact->addList ( $list_id );
								$returnContact = $cc->updateContact ( ACCESS_TOKEN, $contact, $opt_action );
								$mail_send = true;
							} 
						}
					} 
				} 
			}
			if(isset($enable_subscription['adn_email_subscripiton_act_camp']))
			{
				$api_key_val = trim ( get_option ( PLUGIN_CONSTANT . 'active_campaign_api_key' ) );
				$api_url_val = trim ( get_option ( PLUGIN_CONSTANT . 'active_campaign_access_url' ) );
				$list_id_val = trim ( get_option ( PLUGIN_CONSTANT . 'activecampaign_list_id' ) );
				
				if ($api_key_val) 
				{
					if ($list_id_val) 
					{
						$ac = new ActiveCampaign ( $api_url_val, $api_key_val );
						$email = sanitize_text_field ( trim ( $_POST ['email'] ) );
						$list_id = $list_id_val;
						$post_data['email'] = $email;
						$post_data['p['.$list_id.']'] = $list_id;
						$contact_add = $ac->api("contact/add", $post_data);
						$mail_send = true;
					}
				} 
			}
			
			$notification_details=get_option(PLUGIN_CONSTANT.'notification');
			$notification_details=json_decode($notification_details, true);
			
			if($mail_send)
			{
				$anl_mail_settings = get_option('adn_mail_setting');
				$anl_mail_setting = json_decode($anl_mail_settings, true);
				
				$to = $email;
				$subject = "Subscribed Successfully";
				$body = $notification_details['subscriber_success_mail'];
				wp_mail( $to, $subject, $body );
				
				$to = $anl_mail_setting['sender_mailid'];
				$subject = "Notification Subscribed Successfully";
				$body = $notification_details['subscriber_success_admin_mail'];
				wp_mail( $to, $subject, $body );
				
				$ajaxresponse['status'] = true;
				$ajaxresponse['message'] = $notification_details['subscriber_success_message'];
				echo json_encode($ajaxresponse);
				die;
			}
			else
			{
				$ajaxresponse['status'] = false;
				$ajaxresponse['message'] = $notification_details['subscriber_failure_message'];
				echo json_encode($ajaxresponse);
				die;
			}	
			
			exit ();
		}
		
		/**
		 * This function is for register all scripts and style
		 *
		 * @name register_script()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function register_script() 
		{
			wp_register_style ( 'advanced-newsletter-style', WANL_DIR_URL . 'assets/css/style.css' );
			wp_register_style ( 'advanced-newsletter-style-animate', WANL_DIR_URL . 'assets/css/animate.min.css' );
			wp_register_script( 'advanced_newsletter_main_js', WANL_DIR_URL . 'assets/js/advancednewsletter_main.js',array('jquery') );
			wp_register_script( 'jquery.colorbox', WANL_DIR_URL . 'colorbox/jquery.colorbox.js' );
			wp_register_style ( 'colorbox-css', WANL_DIR_URL . 'colorbox/colorbox.css' );
			wp_register_script( 'wp-advanced-nesletter-jquery-ui', WANL_DIR_URL . 'assets/js/jquery-ui.js');
				
		}
			
		/**
		 * This function is for enqueue scripts and style in admin panel
		 *
		 * @name admin_panel_scripts()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function admin_panel_scripts() 
		{
			wp_register_script ( 'wp-advanced-nesletter-action', WANL_DIR_URL . 'assets/js/wp_advanced_newsletter_action.js',array('jquery') );
			wp_enqueue_script ( 'wp-advanced-nesletter-action' );
		}
			
		function enqueue_script()
		{
			wp_enqueue_style ( 'advanced-newsletter-style-animate' );
			wp_enqueue_script ( 'advanced_newsletter_main_js' );
			wp_enqueue_script ( 'jquery.colorbox' );
			wp_enqueue_style ( 'colorbox-css' );
			wp_enqueue_script ( 'wp-advanced-nesletter-jquery-ui' );
			$email_subscriber_field_value=get_option(PLUGIN_CONSTANT.'email_subscriber');
			$cookie_time_js=get_option(PLUGIN_CONSTANT.'advanced_newsletter_cookie_time')['c_days'];
			$wp_popup_animation_value=get_option(PLUGIN_CONSTANT.'advanced_newsletter_wp_popup_animation')['wp_popup_animation'];
			$r=get_option(PLUGIN_CONSTANT.'notification');
			$r=json_decode($r);
			$message_popup= $r->subscriber_success_message;
			wp_localize_script( 'advanced_newsletter_main_js', 'advanced_newsletter_main_js_ajax', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'email_subscriber_field_value' => $email_subscriber_field_value,
				'cookie_time_js' => $cookie_time_js,
				'ADVANCENEWSLETTER_POPUP_URL' =>WANL_DIR_URL,
				'wp_popup_animation_value' => $wp_popup_animation_value,
				'wp_popup_message'=>$message_popup
			));
			wp_enqueue_style( 'advanced-newsletter-style_css', WANL_DIR_URL . 'assets/css/style.css' );
		}
			
			
			
			
		function prefix_ajax_mailchimp_delete_campaign()
		{
			require_once WANL_DIR.'email-service/mailchimp-api/src/Mailchimp/MAPI.class.php';
			$id = $_POST['id'];
			$api_key = get_option ( PLUGIN_CONSTANT . 'mailchimp_api_key' );
			$api = new MCAPI ( $api_key );
			if($api->campaignDelete($id))
			{
				echo 'success';
			}
			else 
			{
				echo 'failed';
			}
			die;		
		}
			
	}
	new wpanl ();
}