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
<h2>Entry Sources</h2>
<p>Select the leads source. You can add one  contact form (generated with Contact Form 7 plugin). All the leads generated  will be visible under the "LEADS" tab. Don't forget to install 59sec iOS or Android app on your phone to receive the notifications!</p>
<form id="sourceForm" name="sourceForm" method="post" action="">
	<?php settings_fields('59sec-entry-sources');?>
	<table class="form-table">
		<tr>
			<th scope="row">Contact Form 7 Forms</th>
			<td><p>Explanation: You can select only one available form from the list below. If you want to be able to select more (or all), you should upgrade to 59sec PRO.</p>
		    <p>
		      <label><a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version!</strong></a></label>
              <br />
            <a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a></p></td>
		</tr>
		<?php foreach ($items as $item):?>
		<?php $checked = (in_array($item->id, $forms)) ? 'checked' : '';?>
		<tr>
			<th scope="row">&nbsp;</th>
			<td>
				<input name="59sec_wpcf7[]" type="radio" id="wpcf7-<?php echo $item->id?>" value="<?php echo $item->id?>" <?php echo $checked?>/>
				<label for="wpcf7-<?php echo $item->id?>"><?php echo $item->title?></label>
			</td>
		</tr>
		<?php endforeach?>
		<tr>
			<th scope="row">The Sales Related Email</th>
			<td> <p>Explanation: Enter your  sales email here, so you'll get notified ONLY for the NEW emails  from potential clients, not the day to day discussions. This is the email present on your site contact page, like sales@domain.com</p>
		    <p>
		      <label><a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version!</strong></a></label>
              <br />
            <a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a></p></td>
		</tr>
	</table>
	<?php if (!empty($error)):?>
  <div class="error"><?php print_r($error)?></div>
	<?php endif?>
	<?php submit_button(); ?>
</form>