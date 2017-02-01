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
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\EmailMarketing\Campaign;
use Ctct\Components\EmailMarketing\MessageFooter;
use Ctct\Components\EmailMarketing\Schedule;
use Ctct\Exceptions\CtctException;
use Ctct\Components\EmailMarketing\TestSend;

$cc = new ConstantContact($cons_cont_api);
$constant_contact_lists = $cc->getLists($cons_cont_access);
$ems = new EmailMarketingService ( $cons_cont_api );
$cons_contact_campaigns = $ems->getCampaign($cons_cont_access, $_GET['campaign_id']);
$cons_contact_campaign = (array)$cons_contact_campaigns;
$cons_contact_msg_footer = (array)$cons_contact_campaign['message_footer'];

?>
<h2><?php _e('Update Campaign', 'wp-advanced-newsletter')?></h2>
<form action="?page=advanced_newsletter_mail&tab=Campaigns" method="post" id="constant_contact">
	<table class="wp-list-table widefat fixed pages" style="width: 98%;">
		<tr>
			<th><?php _e('Name', 'wp-advanced-newsletter')?></th>
			<td>
				<?php echo $cons_contact_campaign['name']?>
				<input type="hidden" name="constant_contact_camp_title" value="<?php echo $cons_contact_campaign['name']?>">
				<input type="hidden" name="constant_contact_camp_id" value="<?php echo $cons_contact_campaign['id']?>">
			</td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('Subject', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_subject" value="<?php echo $cons_contact_campaign['subject']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('From Name', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_name" value="<?php echo $cons_contact_campaign['from_name']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('From Email', 'wp-advanced-newsletter')?></th>
			<td><input type="email" name="constant_contact_camp_email" value="<?php echo $cons_contact_campaign['from_email']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('Text Content', 'wp-advanced-newsletter')?></th>
			<td colspan="2">
			<textarea name="constant_contact_camp_text_content" rows="8" cols="88" class="wan_required"><?php echo $cons_contact_campaign['text_content']?></textarea>
			</td>
		</tr>
		<tr>	
			<th><?php _e('Email Content', 'wp-advanced-newsletter')?></th>
			<td colspan="2">
			<?php 
					$content = $cons_contact_campaign['email_content'];
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
			<th><?php _e('Reply-To Email', 'wp-advanced-newsletter')?></th>
			<td><input type="email" name="constant_contact_camp_reply_email" value="<?php echo $cons_contact_campaign['reply_to_email']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('Lists to send to', 'wp-advanced-newsletter')?></th>
			<td>
				<select multiple="multiple" name="constant_contact_camp_list_id[]" class="wan_required">
                   <?php
                   foreach ($constant_contact_lists as $list) {
					  if($list->id == $cons_contact_campaign['sent_to_contact_lists'][0]->id)
					  {
					  	echo '<option selected="selected" value="' . $list->id . '" >' . $list->name . '</option><br />';
					  }
					  else
					  {
					  		echo '<option value="' . $list->id . '" >' . $list->name . '</option><br />';
					  }	
                      
                   }
                   ?>
                </select>
			</td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('Send Time', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_send_time" id="constant_contact_camp_send_time" class="wan_required"></td>
			<td></td>
		</tr>
		<tr>	
			<th><h3><?php _e('Address', 'wp-advanced-newsletter')?></h3></th>
		</tr>
		
		<tr>	
			<th><?php _e('Organization Name', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_organization_name" value="<?php echo $cons_contact_msg_footer['organization_name']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('Address Line 1', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_address_line_1" value="<?php echo $cons_contact_msg_footer['address_line_1']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('Address Line 2', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_address_line_2" value="<?php echo $cons_contact_msg_footer['address_line_2']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('Address Line 3', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_address_line_3" value="<?php echo $cons_contact_msg_footer['address_line_3']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('City', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_city" value="<?php echo $cons_contact_msg_footer['city']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('State', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_state" value="<?php echo $cons_contact_msg_footer['state']?>" class="wan_required"></td>
			<td></td>
		</tr>	
		<tr>	
			<th><?php _e('Postal Code', 'wp-advanced-newsletter')?></th>
			<td><input type="text" name="constant_contact_camp_postal_code" value="<?php echo $cons_contact_msg_footer['postal_code']?>" class="wan_required"></td>
			<td></td>
		</tr>
		<tr>	
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
					
					<th><?php _e('Country', 'wp-advanced-newsletter')?></th>
					<td>
					<select name="constant_contact_camp_country" style="width: 100%;" class="wan_required">
					<?php 
					foreach($countries as $cid=>$country)
					{
						$selected = "";
						if($cons_contact_msg_footer['country'] == "$cid")
						{
							$selected = 'selected="selected"';
						}
						?>
						<option value="<?php echo $cid?>" <?php echo $selected;?>><?php echo $country;?></option>
						<?php 
					}	
					?>
					</select>
					
					<td></td>
				</tr>
		
		<tr>
			<td><input type="submit" value="Update Campaign" class="button button-primary" name="constant_contact_update_camapign"></td>
		</tr>
	</table>
</form>
		
