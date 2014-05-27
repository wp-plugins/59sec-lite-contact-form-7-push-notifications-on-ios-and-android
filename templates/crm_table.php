<div class="crm-paging">
	<input id="crm-page-<?php echo $item->id?>" type="hidden" name="current_page" value="<?php echo $paging -> current?>"/>
	<?php if ($paging->prev !== FALSE):?>
	<a onclick="formPage(<?php echo $item->id?>, <?php echo $paging->prev?>);">Prev</a>
	<?php endif?>
	<?php foreach($paging->pages as $page):?>
	<a class="<?php echo $page['active']?>" onclick="formPage(<?php echo $item->id?>, <?php echo $page['page']?>);"><?php echo $page['title']?></a>
	<?php endforeach?>
	<?php if ($paging->next !== FALSE):?>
	<a onclick="formPage(<?php echo $item->id?>, <?php echo $paging->next?>);">Next</a>
	<?php endif?>
</div>
<div class="crm-filters">
	<label for="filter-status-<?php echo $item->id?>">Status: </label>
	<select id="filter-status-<?php echo $item->id?>" name="filter-status" onchange="changeFilter(<?php echo $item->id?>)"><?php echo $leadsModel->leadFilterStatusOptions()?></select>
	<label for="filter-keyword-<?php echo $item->id?>">Keyword: </label>
	<input id="filter-keyword-<?php echo $item->id?>" type="text" name="filter-keyword" value="<?php echo $keyword?>"/>
	<input type="button" name="filter-op" value="Search" onclick="changeFilter(<?php echo $item->id?>)" class="button button-primary"/>
</div>
<table class="fiftyninesec_table wp-list-table widefat fixed crm-table rs-table">
	<thead>
	<tr>
		<th onclick="sortBy('time')" class="sortable <?php if($sortBy=='time') echo $sortDir;?>"><u>Time taken</u></th>
	<?php foreach ($headers as $head):?>
		<th><?php echo $head?></th>
	<?php endforeach?>
		<th onclick="sortBy('grabbed')" class="sortable <?php if($sortBy=='grabbed') echo $sortDir;?>"><u>Grabbed at</u></th>
		<th onclick="sortBy('agent')" class="sortable <?php if($sortBy=='agent') echo $sortDir;?>"><u>Agent</u></th>
		<th class="status">Status</th>
		<th>Notes</th>
	</tr>
    </thead>
    <tbody>
    <?php
	$current_lead = $_GET['lead']*1;
	?>
	<?php foreach($leads as $key => $lead): $data = unserialize($lead['postdata']);?>
		<tr class="lead-<?php echo $lead['id']?> <?php if ($key % 2 != 0) echo 'alternate'?><?php echo $current_lead==$lead['id']?' current_lead':''?>">
			<td data-title="Time Taken"><?php echo $leadsModel->timeFormat($lead['created_time'], $lead['reserved_time'])?></td>
			<?php if (!empty($data)):?>
			<?php foreach($data as $index => $cell): $cell = trim($cell); ?>
			<td data-title="<?php echo $index?>"><?php if (!empty($cell)) echo stripslashes($cell); else echo '-';?></td>
			<?php endforeach?>
			<?php else:?>
			<td colspan="<?php echo $cc?>">
				<b>Broken data for #<?php echo $lead['id']?>, data:</b> <?php echo $lead['postdata']?>
				<a class="try-fix" onclick="tryFix(<?php echo $lead['id']?>)">try fix</a>
			</td>
			<?php endif?>
			<td data-title="Grabbed at"> <?php echo $leadsModel->grabedAt($lead['reserved_time'])?></td>
			<td data-title="Agent"><?php echo $lead['user_name']?></td>
			<td data-title="Status"><select name="status" onchange="changeStatus(<?php echo $lead['id']?>, this)"><?php echo $leadsModel->leadStatusOptions($lead['status'])?></select></td>
			<td data-title="Notes"><span class="note"><?php echo $lead['user_comments']?></span> <a onclick="editNote(<?php echo $lead['id']?>)">edit</a></td>
		</tr>
	<?php endforeach?>
    </tbody>
</table>