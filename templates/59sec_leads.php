<p>UNANSWERED LEADS: <span class="leadsTotalRemaining"><?php echo $leadsModel->getTotalUnansweredLeads()?></span></p>
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
<h2>Leads</h2>
Important!!! After grabbing the lead, call it immediately!
<div id="wrapper-leads">
	<?php include _59SEC_INCLUDE_PATH . '/templates/leads_tables.php'?>
</div>
<p>On 59sec LITE version you have only 20 leads per month available.  </p>
<p>
  <label><a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version for UNLIMITED LEADS!</strong></a></label>
  <br />
<a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a> </p>
<script type="text/javascript">
jQuery(document).ready(function() {
	window.checkInterval = <?php echo $leadscheck?>;
	initTimers();
});
  </script>
