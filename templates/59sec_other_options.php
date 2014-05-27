<p>UNANSWERED LEADS: <?php echo $leadsModel->getTotalUnansweredLeads()?></p>
<p>
	<?php echo $leadsLink?>
	<?php echo $crmLink?>
	<?php echo $statisticsLink?>
	<?php echo $sourcesLink?>
	<?php echo $usersLink?>
	<?php echo $notificationsLink?>
	<?php echo $otherOptionsLink?>
	<?php echo $helpLink?>
</p>
<h2>Other options</h2>
<p>Manage your own plugin options.</p>
<form method="post" action="options.php">
	<?php settings_fields('59sec-other-options');?>
	<table class="form-table">
		<tr>
			<th scope="row">CRM status options</th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<p>
				  <label>Explanation: You can define your own statuses for the grabbed leads  inside the 59sec CRM. In the LITE version you only have the default options. <br />
				    <a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version!</strong></a></label>
				  
		         <br />
			    <a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a></p></td>
		</tr>
		<tr>
			<th scope="row">Leads workflow</th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<label>Explanation: Select this option to be taken automatically to LEADS tab when loggin into Wordpress admin.</label>
			It saves precious seconds when grabbing the leads! Highly recommended!</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<input name="59sec_direct_login" type="checkbox" value="1" <?php if ($direct_login) echo 'checked';?> />
				<label>Take users to LEADS section directly when login into Wordpress.</label>
			</td>
		</tr>
		<tr>
			<th scope="row">Leads checked every</th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<select name="59sec_leadscheck">
					<option value="1" <?php if ($leadscheck == 1) echo 'selected'?>>every second</option>
					<option value="2" <?php if ($leadscheck == 2) echo 'selected'?>>every 2 seconds</option>
					<option value="3" <?php if ($leadscheck == 3) echo 'selected'?>>every 3 seconds</option>
					<option value="5" <?php if ($leadscheck == 5) echo 'selected'?>>every 5 seconds</option>
					<option value="10" <?php if ($leadscheck == 10) echo 'selected'?>>every 10 seconds</option>
					<option value="15" <?php if ($leadscheck == 15) echo 'selected'?>>every 15 seconds</option>
					<option value="20" <?php if ($leadscheck == 20) echo 'selected'?>>every 20 seconds</option>
					<option value="25" <?php if ($leadscheck == 25) echo 'selected'?>>every 25 seconds</option>
					<option value="30" <?php if ($leadscheck == 30) echo 'selected'?>>every 30 seconds</option>
					<option value="40" <?php if ($leadscheck == 40) echo 'selected'?>>every 40 seconds</option>
					<option value="59" <?php if ($leadscheck == 59) echo 'selected'?>>every 59 seconds</option>
				</select>
				<p class="description">Select the check interval apropriate for your servers performance.</p>
			</td>
		</tr>
		<tr>
			<th scope="row">Schedule</th>
			<td>&nbsp;</td>
		</tr>
		<th scope="row">&nbsp;</th>
			<td>
				<label>Explanation: Do you want NOT to be waken up in the middle of the night by new leads?:) Define your working hours and working days.<br /></label>
				<label><a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version!</strong></a></label>
                <br />
                <a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a></td>
	</table>
	<?php submit_button(); ?>
</form>