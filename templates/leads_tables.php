<?php
global $current_user;
$destination = ($current_user->caps['administrator'] == 1) ? '?page=59sec_crm_boss' : '?page=59sec_crm';
$emptyflag = true;

/* Contact Form 7 */

foreach ($items as $item):
	if (!empty($forms) && in_array($item->id, $forms)):
	
	$leads = $leadsModel->getNewLeads($item->id);
	
	if (!empty($leads)):
		$emptyflag = false;
		$headers = $leadsModel->tableHeaders($leads);
		$cc = count($headers);
?>
<h3><?php echo $item->title?></h3>
<table class="fiftyninesec_table  wp-list-table widefat fixed rs-table">
	<thead>
	<tr>
		<th>Time Passed</th>
	<?php foreach ($headers as $head):?>
		<th><?php echo $head?></th>
	<?php endforeach?>
		<th>GRAB IT!</th>
	</tr>
    </thead>
    <tbody>
	<?php
	foreach($leads as $key => $lead): $data = unserialize($lead['postdata']);?>
		<tr class="<?php if ($key % 2 != 0) echo 'alternate'?>">
			<td data-title="Time Passed"><span id="timer-<?php echo $lead['id']?>" class="timer" data-time="<?php echo time() - $lead['created_time']?>">
				<?php echo $leadsModel->timeFormat($lead['created_time'])?>
			</span></td>
			<?php if (!empty($data)):?>
				<?php foreach($data as $index => $cell): $cell = trim($cell); ?>
				<td data-title="<?php echo $index?>"><?php if (!empty($cell)) echo $cell; else echo '-';?></td>
				<?php endforeach?>
			<?php else:?>
				<td colspan="<?php echo $cc?>"><b>Broken data for #<?php echo $lead['id']?>, data:</b> <?php echo substr($lead['postdata'], 0, 500)?></td>
			<?php endif?>
			<td data-title="GRAB IT">
				<?php if (!$oldest_mandatory || empty($key)):?>
				<a class="button" onclick="checkLead(<?php echo $lead['id']?>, '<?php echo $destination.'&lead='.$lead['id']?>')" title="Grab it!">Grab it!</a>
				<?php endif?>
			</td>
		</tr>
	<?php endforeach?>
    <tbody>
</table>
	<?php endif?>
	<?php endif?>
<?php endforeach?>

<?php if ($emptyflag):?>
<table class="fiftyninesec_table wp-list-table widefat fixed rs-table">
	<tr>
		<td class="nothing-here"><p>No leads so far. You should promote yourself more! :)</p></td>
	</tr>
</table>
<?php endif?>

<script type="text/javascript">
	window.lastCheck = <?php echo $lastCheck?>;
	if(typeof(updateLeadsCount) == 'function')
		updateLeadsCount(<?php echo $leadsModel->getTotalUnansweredLeads()?>);
</script>