jQuery( document ).ready(function($) {
	
	/******************************************************************
	 *                  MAILCHIMP SETTINGS                            *                    
	 ******************************************************************/
	
	/**
	 * This function is for append mailchimp list content
	 * @author CedCommerce <plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	jQuery(document).on('click','.mailchimp_api_key',function(){
		var email_service_name=jQuery("#advancednewsletter-email-service-option option:selected").attr('data-val');
		var api_key=jQuery(this).parent().prev('td').children('input').val();
		jQuery('#overlay-loading').show();
		if(api_key)
		{
			jQuery.post(
			    ajaxurl, 
			    {
			        'action': 'mailchimp_get_api',
			        'mailchimp_api_key':api_key   
			    }, 
			    function(response){
				    	jQuery('#mailchimp_data_val').children('option:not(:first)').remove();
				    	jQuery('#overlay-loading').fadeOut(10);
				    	jQuery("#mailchimp_data_val").append(response);
			    }
			);
		}
		else
		{
			jQuery('#mailchimp_data_val').children('option:not(:first)').remove();
			alert("Please Enter Api Key !");
			jQuery('#overlay-loading').fadeOut(10);
		}
	});
	

	/**
	 * This function is for update mailchimp list id
	 * @author CedCommerce <plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	jQuery(document).on('click','#mailchimp_api_list',function(){
		jQuery('#overlay-loading').show();
		var list_id=jQuery(this).parent().prev('td').children('#mailchimp_data_val').val();
		jQuery("#mailchimp_data_val  option[value="+list_id+"]").attr("selected","selected");
		jQuery.post(
		    ajaxurl, 
		    {
		        'action': 'mailchimp_add_data',
		        'mailchimp_list_id':list_id   
		    }, 
		    function(response){  
			    if(response == 0)
			    {
			    	alert("Please select list !");
			    	jQuery('#overlay-loading').fadeOut(10); 	
			    }
			    else
			    {
			    	jQuery('#overlay-loading').fadeOut(10); 	
			    }
		    }
		);
	});

	
	//Delete Campaign
	
	jQuery(".ced_ad_new_delete").click(function(){
		jQuery(this).next('img').show();
		var campaign_id = jQuery(this).data('id');
		var remove_id = jQuery(this);
		jQuery.post(
		    ajaxurl, 
		    {
		    	action : 'mailchimp_delete_campaign',
		    	id : campaign_id
		    }, 
		    function(response){ 
		    	if(response == 'success')
		    	{
		    		remove_id.parent().parent().hide();
		    	}
		    	else
		    	{
		    		alert('Campaign deletion Failed');
		    	}	
		    });
		
	});
	
	//Send Mail
	
	jQuery(".mailchimp_send_mail").click(function(){
		var campaign_id = jQuery(this).data('id');
		var remove_id = jQuery(this);
		jQuery.post(
		    ajaxurl, 
		    {
		    	action : 'mailchimp_send_mail',
		    	id : campaign_id
		    }, 
		    function(response){ 
		    	if(response.status == true)
		    	{
		    		remove_id.attr('disabled', 'disabled');
		    		remove_id.val('Mail Sent');
		    	}
		    	alert(response.message);
		    },'json');
	});
	
	//Unsubscribe Contact
	
	jQuery(".mail_chimp_unsubscribe").click(function(){
		jQuery(this).next('img').show();
		var user_email = jQuery(this).data('email');
		var id = jQuery(this).data('id');
		var remove_id = jQuery(this);
		jQuery.post(
			    ajaxurl, 
			    {
			    	action : 'mailchimp_unsubscribe_mail',
			    	email : user_email,
			    	id : id
			    	
			    }, 
			    function(response){ 
			    	if(response == 'success')
			    	{
			    		remove_id.parent().parent().hide();
			    	}
			    	else
			    	{
			    		alert('Unsubscription failed');
			    	}	
			    });
		
	});
	
	
	/******************************************************************
	 *                  CONSTANT CONTACT SETTINGS                     *                    
	 ******************************************************************/
	
	/**
	 * This function is for append constant contact list content
	 * @author CedCommerce <plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	jQuery(document).on('click','#advancednewsletter-constantcontacttoken_save',function(){
		var email_service_name=jQuery("#advancednewsletter-email-service-option option:selected").attr('data-val');
		var ConsCont_api_key=jQuery(this).parent().parent('tr').prev('tr').children('td:eq(1)').children('input').val();
		var ConsCont_access_token=jQuery('#advancednewsletter-constantcontact_token').val();
		jQuery('#overlay-loading').show();
		if(ConsCont_api_key && ConsCont_access_token)
		{
			jQuery.post(
			    ajaxurl, 
			    {
			        'action': 'constant_contact_api_func',
			        'const_cont_api_key':ConsCont_api_key,
			        'const_cont_access_token':ConsCont_access_token
			    }, 
			    function(response){
				    jQuery('#constantcontact_data_val').children('option:not(:first)').remove();
		    		jQuery('#overlay-loading').fadeOut(10);
		    		jQuery("#constantcontact_data_val").append(response);	jQuery('#imga').fadeOut(10); 
			    }
			);
		}
		else
		{
			jQuery('#constantcontact_data_val').children('option:not(:first)').remove();
			alert("Please fill all the details !");
			jQuery('#overlay-loading').fadeOut(10);
		}
	});
	

	/**
	 * This function is for update constant contact list id
	 * @author CedCommerce <plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	jQuery(document).on('click','#constantcontact_api_list',function(){
		jQuery('#overlay-loading').show();
		var list_id=jQuery(this).parent().prev('td').children('#constantcontact_data_val').val();
		 jQuery("#constantcontact_data_val  option[value="+list_id+"]").attr("selected","selected");
		jQuery.post(
		    ajaxurl, 
		    {
		        'action': 'const_cont_update_list',
		        'const_cont_list_id':list_id   
		    }, 
		    function(response){  
		    	if(response == 0)
			    {
			    	alert("Please select list !");
			    	jQuery('#overlay-loading').fadeOut(10); 	
			    }
			    else
			    {
			    	jQuery('#overlay-loading').fadeOut(10); 	
			    }	
		    }
		);
	});


	
	
	//Datetime picker
	jQuery("#constant_contact_camp_send_time").datetimepicker({minDate:'0'});
	
	//Delete Campaign
	
	jQuery(".ced_cons_con_delete").click(function(){
		jQuery(this).next('img').show();
		var campaign_id = jQuery(this).data('id');
		var remove_id = jQuery(this);
		jQuery.post(
		    ajaxurl, 
		    {
		    	action : 'constant_contact_delete_campaign',
		    	id : campaign_id
		    }, 
		    function(response){ 
		    	if(response == 'success')
		    	{
		    		remove_id.parent().parent().hide();
		    	}
		    	else
		    	{
		    		alert('Campaign deletion Failed');
		    	}	
		    });
		
	});
	
	//Unsubscribe Contact
	
	jQuery(".constant_contact_unsubscribe").click(function(){
		jQuery(this).next('img').show();
		var id = jQuery(this).data('id');
		var remove_id = jQuery(this);
		jQuery.post(
			    ajaxurl, 
			    {
			    	action : 'constant_contact_unsubscribe_mail',
			    	id : id
			    }, 
			    function(response){ 
			    	if(response == 'success')
			    	{
			    		remove_id.parent().parent().hide();
			    	}
			    	else
			    	{
			    		alert('Unsubscription failed');
			    	}	
			    });
		
	});
	
	
	/******************************************************************
	 *                  ACTIVE CAMPAIGN SETTINGS                      *                    
	 ******************************************************************/
	
	/**
	 * This function is for append active campaign list content
	 * @author CedCommerce <plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	jQuery(document).on('click','.advancednewsletter-activecampaigntoken_save',function(){
		var email_service_name=jQuery("#advancednewsletter-email-service-option option:selected").attr('data-val');
		var ActiveCampaign_url=jQuery(this).parent().parent('tr').prev('tr').children('td:eq(1)').children('input').val();
		var ActiveCampaign_api_key=jQuery('#advancednewsletter-activecampaign_accesskey').val();
		jQuery('#overlay-loading').show();
		if(ActiveCampaign_api_key && ActiveCampaign_url)
		{
			jQuery.post(
			    ajaxurl, 
			    {
			        'action': 'active_campaign_func',
			        'active_campaign_api_key':ActiveCampaign_api_key,
			        'active_campaign_access_url':ActiveCampaign_url
			    }, 
			    function(response){
			    	jQuery('#activecampaign_val').children('option:not(:first)').remove();
			    	jQuery('#overlay-loading').fadeOut(10);
			    	jQuery("#activecampaign_val").append(response);	
			    }
			);
		}
		else
		{
			jQuery('#activecampaign_val').children('option:not(:first)').remove();
			alert("Please fill all the details !");
			jQuery('#overlay-loading').fadeOut(10);
		}
	});

	

	/**
	 * This function is for update active campaign list id
	 * @author CedCommerce <plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	jQuery(document).on('click','#activecampaign_api_list',function(){
		jQuery('#overlay-loading').show();
		var list_id=jQuery(this).parent().prev('td').children('#activecampaign_val').val();
		jQuery("#activecampaign_val  option[value="+list_id+"]").attr("selected","selected");
		jQuery.post(
		    ajaxurl, 
		    {
		        'action': 'activecampaign_update_list',
		        'activecampaign_list_id':list_id   
		    }, 
		    function(response){  
			    if(response == 0)
			    {
				    alert("Please select list !");
				    jQuery('#overlay-loading').fadeOut(10); 
			    }
			    else{
		    		jQuery('#overlay-loading').fadeOut(10); 
			    }	
		    }
		);
	});

	
	
	//Datetime picker
	
	jQuery("#active_camp_schedule_time").datetimepicker({minDate:'0'});
	
	//Delete Campaign
	
	jQuery(".ced_actcamp_delete").click(function(){
		jQuery(this).next('img').show();
		var campaign_id = jQuery(this).data('id');
		console.log(campaign_id);
		var remove_id = jQuery(this);
		jQuery.post(
		    ajaxurl, 
		    {
		    	action : 'active_camp_delete_campaign',
		    	id : campaign_id
		    }, 
		    function(response){ 
		    	if(response == 'success')
		    	{
		    		remove_id.parent().parent().hide();
		    	}
		    	else
		    	{
		    		alert('Campaign deletion Failed');
		    	}	
		    });
		
	});

	//Send Mail
	
	jQuery(".ced_actcamp_send").click(function(){
		var campaign_id = jQuery(this).data('id');
		var remove_id = jQuery(this);
		jQuery.post(
		    ajaxurl, 
		    {
		    	action : 'activecamp_send_mail',
		    	id : campaign_id
		    }, 
		    function(response){ 
		    	if(response == 'success')
		    	{
		    		alert('Mail Sent');
		    	}
		    });
	});
	
	//Unsubscribe Contact
	
	jQuery(".active_camp_unsubscribe").click(function(){
		jQuery(this).next('img').show();
		var id = jQuery(this).data('id');
		var remove_id = jQuery(this);
		jQuery.post(
			    ajaxurl, 
			    {
			    	action : 'active_camp_unsubscribe_mail',
			    	id : id
			    }, 
			    function(response){ 
			    	if(response == 'success')
			    	{
			    		remove_id.parent().parent().hide();
			    	}
			    	else
			    	{
			    		alert('Unsubscription failed');
			    	}	
			    });
		
	});
	
	jQuery("#active_campaign").submit(function(e){
		var error = false;
		
		jQuery(".wan_required").each(function(){
			var val = jQuery(this).val();
			if(val == '' || val == null)
			{
				error = true;
				jQuery(this).addClass("wanl_error");
			}
			else
			{
				jQuery(this).removeClass("wanl_error");
			}	
		});
		if(error == true)
		{
			e.preventDefault();
		}
	});
	
	jQuery("#constant_contact").submit(function(e){
		var error = false;
		jQuery(".wan_required").each(function(){
			var val = jQuery(this).val();
			if(val == '' || val == null)
			{
				error = true;
				jQuery(this).addClass("wanl_error");
			}
			else
			{
				jQuery(this).removeClass("wanl_error");
			}	
		});
		if(error == true)
		{
			e.preventDefault();
		}
	});
	
	jQuery("#mailchimp").submit(function(e){
		var error = false;
		jQuery(".wan_required").each(function(){
			var val = jQuery(this).val();
			if(val == '' || val == null)
			{
				error = true;
				jQuery(this).addClass("wanl_error");
			}
			else
			{
				jQuery(this).removeClass("wanl_error");
			}	
		});
		if(error == true)
		{
			e.preventDefault();
		}
	});
	
	
});



/**
 * This function is for change email service name and display sections of that email service
 * @name service_value()
 * @author CedCommerce <plugins@cedcommerce.com>
 * @link http://cedcommerce.com/
 */
function service_value(obj){
	jQuery('#overlay-loading').show();
	var email_service_name=jQuery("#advancednewsletter-email-service-option option:selected").attr('data-val');
	jQuery.post(
	    ajaxurl, 
	    {
	        'action': 'ced_email_service',
	        'email_service_name':email_service_name     
	    }, 
	    function(response){  
	    	jQuery('#overlay-loading').fadeOut(10);	
	    	location.reload(); 
	    }
	);
	var atri = jQuery("#advancednewsletter-email-service-option option:selected").attr('data-val');
 	var service_val=obj.replace(/\s+/g, '-').toLowerCase();
	var attr=jQuery('.'+service_val).attr('class');
	  
	if(service_val != null){
		jQuery('#service_list').children('li').children('div').each(function (index, value){
			var attr_new=jQuery(this).attr('class');
			var attr_new=attr_new.split(" ");
			if(attr_new[2] != null)
			{
				var class_name=attr_new[0]+" "+attr_new[1];
			}
			jQuery(this).attr('class',class_name);
		});
		jQuery('.'+service_val).attr('class',attr+" "+"email_service_selected");
	}
}

