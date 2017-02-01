<?php 
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$campaign_id = $_GET['campaign_id'];
$camp_details = $ac->api("campaign/list_?ids=$campaign_id");
$camp_lists = $ac->api("list/list_?ids=all");
if(isset($camp_details) && $camp_details->result_code == 1)
{
	foreach($camp_details as $camp_detail)
	{
		if(isset($camp_detail->id))
		{
			$campaign_detail = $camp_detail;
		}
	}
	
	if($campaign_detail->status == 0)
	{
		$status = 'Draft';
	}
	if($campaign_detail->status == 1)
	{
		$status = 'Scheduled';
	}
	if($campaign_detail->status == 2)
	{
		$status = 'Sending';
	}
	if($campaign_detail->status == 3)
	{
		$status = 'Paused';
	}
	if($campaign_detail->status == 4)
	{
		$status = 'Stopped';
	}
	if($campaign_detail->status == 5)
	{
		$status = 'Completed';
	}
	if($campaign_detail->status == 6)
	{
		$status = 'Disabled';
	}
	if($campaign_detail->status == 7)
	{
		$status = 'Pending Approval';
	}
	
	$campaign_msg = (array)$campaign_detail->messages[0];
	$campaign_list = $campaign_msg['lists'];
	$campaign_list_id = $campaign_list[0]->id;
	
	
?>
<h2><?php _e('Campaign Detail', 'wp-advanced-newsletter')?></h2>
	<form action="" method="post">
		<table class="wp-list-table widefat fixed pages" style="width: 98%;">
			<tr>
				<th><?php _e('Name', 'wp-advanced-newsletter')?></th>
				<td><?php echo $campaign_detail->name?></td>
				<td></td>
			</tr>	
			<tr>
				<th><?php _e('Schedule Date', 'wp-advanced-newsletter')?></th>
				<td><?php echo $campaign_detail->sdate?></td>
				<td></td>
			</tr>	
			<tr>
				<th><?php _e('Status', 'wp-advanced-newsletter')?></th>
				<td><?php echo $status?></td>
				<td></td>
			</tr>
			
			<tr>
				<th><?php _e('From Name', 'wp-advanced-newsletter')?></th>
				<td><?php echo $campaign_msg['fromname']?></td>
				<td></td>
			</tr>
			
			<tr>
				<th><?php _e('From Email', 'wp-advanced-newsletter')?></th>
				<td><?php echo $campaign_msg['fromemail']?></td>
				<td></td>
			</tr>
			
			<tr>
				<th><?php _e('Subject', 'wp-advanced-newsletter')?></th>
				<td><?php echo $campaign_msg['subject']?></td>
				<td></td>
			</tr>
			<tr>	
				<th><?php _e('Email Content', 'wp-advanced-newsletter')?></th>
				<td colspan="2">
				<?php 
						echo $content = $campaign_msg['html'];
				?>
				</td>
			</tr>	
			<tr>
				<th><?php _e('List', 'wp-advanced-newsletter')?></th>
				<td>
					<?php 
						if(isset($camp_lists))
						{
							?>
							<?php 
							foreach($camp_lists as $camp_list)
							{
								$camp_list = (array)$camp_list;
								if(isset($camp_list['id']))
								{
									if($campaign_list_id == $camp_list['id'])
									{
										echo $camp_list['name'];
									}
								}
							}	
						}	
					?>
				</td>
				<td></td>
			</tr>
		</table>
	</form>	
<?php }?>			