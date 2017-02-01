jQuery( document ).ready(function($) {
	jQuery.ajax({
		url : advanced_newsletter_main_js_ajax.ajaxurl,
		type : 'post',
		data : {action : 'newsletter_popup_content'},
		success : function( response ) 
		{
			var response_split_array=response.split("---");
	    	if(response.trim().length != 0)
	    	{
				function openColorBox()
				{
				    $.colorbox({html:response_split_array[0], innerWidth:"50%", innerHeight:"60%"});
				    $("#cboxContent").addClass(advanced_newsletter_main_js_ajax.wp_popup_animation_value+' '+'animated');
				    setTimeout(function(){ 
					   $("#cboxContent").removeClass(advanced_newsletter_main_js_ajax.wp_popup_animation_value+' '+'animated');
					}, 3000);
				}
				setTimeout(openColorBox, response_split_array[1]+'000');
	    	}
		}
	});
	
	jQuery(document).on('click','.close-btn',function($){
		jQuery('.popup_nl').hide();
	});
	
	function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}
	
	/**
	 * This function is for create cookie and add users in email subscriber list
	 * 
	 * @name ajax_url_funct()
	 * @author CedCommerce <plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	
	jQuery(document).on('click', '.advanced_newsletter_sumit_data', function($) {
		
		if(jQuery('#advanced_newsletter_email').val() == "")
		{
			jQuery("#advanced_newsletter_email").effect('shake', {distance:2}, 500);
		}
		else
		{
			if ( advanced_newsletter_main_js_ajax.email_subscriber_field_value != ""){
				jQuery('.mail_error').html("");
				jQuery('.mail_error_success').html("");
				jQuery('#load_img').show();
				var cust_email=jQuery('#advanced_newsletter_email').val();
				jQuery.ajax({
					url : advanced_newsletter_main_js_ajax.ajaxurl,
					type : 'post',
					data : {
								action : 'add_new_subscriber',
								'email':cust_email
						   },
					success : function( response ) 
					{
						if(response.status == 'true')
					    {
							jQuery('#load_img').hide();
					    	var message = response.message;
					    	jQuery('.mail_error_success').html(message);
					    	setCookie('advanced_newsletter_cookie','advanced_newsletter_cookie_val', 365);
					    }
					    else
					    {
				    		jQuery("#advanced_newsletter_email").effect('shake', {distance:2}, 500);
				    		var message = response.message;
				    		jQuery('.mail_error').html(message);
					    	jQuery('#load_img').hide();
				    	}
					},
					dataType:'json'
				});
			} 
			else 
			{
				jQuery('.mail_error').fadeIn(2000,function(){
					jQuery('.mail_error').html('Sorry no email service is currently active !');	
				});
			} 
		}
	});
});
