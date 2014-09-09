<p>UNANSWERED LEADS: <?php echo $leadsModel->getTotalUnansweredLeads()?> <?php echo get_real_site_url()?></p>
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
<h2>CRM</h2>
Much more great features available in 59sec PRO version. <a href="http://www.59sec.com">Upgrade now!</a>
<?php foreach ($items as $item):?>
<?php if (!empty($forms) && in_array($item->id, $forms)):?>
<?php
	$sortBy = $_COOKIE['59sec_sb'];if($sortBy=='') $sortBy='grabbed';
	$sortDir = $_COOKIE['59sec_sd'];if($sortDir=='') $sortDir='desc';
	
	$leads = $leadsModel->getUserLeads($item->id, $user_id, 0, array('flag' => 0));
	$headers = $leadsModel->tableHeaders($leads);
	$paging = $leadsModel->pagerUserLeads($item->id, $user_id, 0, array('flag' => 0));
?>
<?php if (!empty($leads)):?>
<h3><?php echo $item->title?></h3>
<div id="wrapper-form-<?php echo $item->id?>">
	<?php include _59SEC_INCLUDE_PATH . '/templates/crm_table.php'?>
</div>
<?php endif?>
<?php endif?>
<?php endforeach?>

<script>
function scrollToCurrentLead(){
	var pos = jQuery('tr.current_lead').position();
	if(pos)
		jQuery(document).scrollTop(pos.top - 10);
}
jQuery(document).ready(function(e) {
    scrollToCurrentLead();
});
</script>
<div id="popup-note">
	<span>add / edit note</span>
	<a class="popup-note-close" onclick="closeNote()">close</a>
	<form id="popupNoteForm" name="popupNoteForm" method="post" action="">
		<input id="lead_id" type="hidden" name="id" value="0"/>
		<?php wp_editor('', 'note')?>
		<br />
		<input onclick="saveNote()" type="button" name="op" value="Submit" class="button button-primary" />
	</form>
</div>