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
<h2>Notifications</h2>
<form method="post" action="options.php"> 
	<?php settings_fields('59sec-notifications');?>
	<table class="form-table">
		<tr>
			<th scope="row"><h3>SITE ALERT (default)</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>- sound<br />- pop-up alert<br />
			  <p class="description">Explanation: during workhours you should instruct your sales agents to be logged in on the site, on the LEADS tab. When a new lead kicks in, the browser will play a sound + a warning pop-up will appear.</p>
			  <p class="description">
			    <label><a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version!</strong></a></label>
                <br />
              <a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a></p></td>
		</tr>
		<tr>
			<th scope="row"><h3>EMAIL NOTIFICATIONS</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<label>Explanation: You can set up more emails where to be notified that you have new leads.<br />
				  <br />
			    <a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version!</strong></a></label>
                <br />
                <a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a>
                <label>                </label>
			</td>
		</tr>
		<tr>
			<th scope="row"><h3>BOSS NOTIFICATION</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<p>
				  <label>Explanation: </label>
				The sales manager/business owner can be notified via email that the sales team doesn't do its job properly. So, if a lead is not grabbed in a specific number of seconds you specify, you are getting an alert via email. Very cool feature!</p>
				<p>
				  <label><a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version!</strong></a></label>
                  <br />
                <a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a></p></td>
		</tr>
		<tr>
			<th scope="row"><h3>IPHONE NOTIFICATIONS</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<label>32 characters Secret Key :</label>
				<?php echo $pluginkey?> <p class="description">Explanation: Input your key in the 59sec iPhone app to order to synchronize the apps and get push notifications on the phone in real time!</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><h3>ANDROID NOTIFICATIONS</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<label>32 characters Secret Key :</label>
				<?php echo $pluginkey?> <p class="description">Explanation: Input your key in the 59sec Android app to order to synchronize the apps and get push notifications on the phone in real time!</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><h3>SMS NOTIFICATIONS</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td><p>(under development)</p>
		    <p>It will be available for 59sec PRO version.</p>
		    <p>
		      <label><a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version!</strong></a></label>
              <br />
            <a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a></p></td>
		</tr>
	</table>
	<?php submit_button()?>
</form>