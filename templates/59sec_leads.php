<p>UNANSWERED LEADS: <span class="leadsTotalRemaining"><?php echo $leadsModel->getTotalUnansweredLeads()?></span> <?php echo get_real_site_url()?></p>
<p><b> LEADS left this month: <?php echo $leadsModel->leadsLeft()?>.</b> Upgrade <a href="https://www.59sec.com/prices/">now</a>.</p>
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
	window.is_boss_59sec = <?php echo $isBoss?>;
	initTimers();
});
</script>
