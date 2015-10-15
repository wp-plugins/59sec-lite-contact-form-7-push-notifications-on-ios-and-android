<p>UNANSWERED LEADS: <?php echo $leadsModel->getTotalUnansweredLeads()?></p>
<ul class="subsubsub">
	<li><?php echo $leadsLink?></li>
	<li><?php echo $crmLink?></li>
</ul>
<br class="clear-subsubsub"/>
<ul class="subsubsub">
	<li><?php echo $statisticsLink?></li>
	<li><?php echo $sourcesLink?></li>
	<li><?php echo $usersLink?></li>
	<li><?php echo $notificationsLink?></li>
	<li><?php echo $otherOptionsLink?></li>
	<li><?php echo $helpLink?></li>
</ul>
<h2>Notifications</h2>
<form id="form59" name="form59" method="post" action="">
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
			    <label><a href=https://www.59sec.biz/site/subscribe" target="_blank"><strong>Download now the 59sec PRO plugin!</strong></a></label>
                <br />
              <a href="http://www.59sec.com">First month is FREE to test. No credit card, no strings attached! </a>:)</p></td>
		</tr>
		<tr>
			<th scope="row"><h3>EMAIL NOTIFICATIONS</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<label>Explanation: You can set up more emails where to be notified that you have new leads.<br />
				  <br /></label>
				<span class="description">
				<label><a href="https://www.59sec.biz/site/subscribe&quot;" target="_blank"><strong>Download now the 59sec PRO plugin!</strong></a></label>
                <br />
                <a href="http://www.59sec.com">First month is FREE to test. No credit card, no strings attached! </a></span>				<label>                </label>
			:)</td>
		</tr>
		<tr>
			<th scope="row"><h3>BOSS NOTIFICATION</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr id="row-bossemail-notice" class="notice-row">
			<th scope="row">&nbsp;</th>
			<td>Changes will not take place untill you click the save button!</td>
		</tr>
		<?php if (!empty($bosses)):?>
		<?php foreach ($bosses as $key => $item):?>
		<?php if (!empty($item)):?>
		<tr id="row-bossemail-<?php echo $key?>">
			<th scope="row">&nbsp;</th>
			<td>
				<label for="bossemail-<?php echo $key?>">Email <?php echo ($key + 1)?></label>
				<input disabled="disabled" id="bossemail-<?php echo $key?>" name="59sec_bosses[]" type="text" value="<?php echo $item?>" class="regular-text">
				</td>
		</tr>
		<?php endif?>
		<?php endforeach?>
		<?php endif?>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<label for="">Alert in :</label>
				<input name="59sec_bossseconds" type="text" value="600" class="regular-text" disabled="disabled" />
				<p class="description">Explanation: the number of seconds after a lead arrived until the bosses are alerted by email. Then the boss can follow up on the sales agents why they are not doing their job! Cool, huh? :)</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><h3>IPHONE NOTIFICATIONS</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				Go to "<a href="?page=59sec_users">Users</a>" tab to grab your own secret key. Also from there you can take the Secret Keys for each sales person.
			</td>
		</tr>
		<tr>
			<th scope="row"><h3>ANDROID NOTIFICATIONS</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				Go to "<a href="?page=59sec_users">Users</a>" tab to grab your own secret key. Also from there you can take the Secret Keys for each sales person.</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><h3>WINDOWS PHONE NOTIFICATIONS</h3></th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th scope="row">&nbsp;</th>
			<td><p>Go to &quot;<a href="?page=59sec_users">Users</a>&quot; tab to grab your own secret key. Also from there you can take the Secret Keys for each sales person.
			    </p>
            </p></td>
		</tr>
	</table>
	<?php submit_button()?>
</form>
<script type="text/javascript">
function deleteBossEmail(id) {
	jQuery('#bossemail-'+id).val('');
	jQuery('#row-bossemail-'+id).hide();
	jQuery('#row-bossemail-notice').show();
}
</script>