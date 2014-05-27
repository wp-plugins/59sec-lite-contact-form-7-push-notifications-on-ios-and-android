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
<h2>Statistics</h2>
<p>Compare agent performance</p>
<table class="wp-list-table widefat fixed">
	<thead>
		<tr>
			<th scope="col">Agent</th>
			<th scope="col">Total Leads</th>
			<th scope="col">Finalized Leads</th>
			<th scope="col">Rejected Leads</th>
			<th scope="col">Average Response Time</th>
		</tr>
	</thead>
	<tbody>
	<?php 	
	foreach ($users as $key => $user):?>
    <?php
		$uLeads = $leadsModel->getAgentLeads($user->ID);
		$uFinalized = $leadsModel->getAgentLeads($user->ID, 'finalized');
		$uRejected = $leadsModel->getAgentLeads($user->ID, 'rejected');
		
		$totalLeads += $uLeads;
		$totalFinalized += $uFinalized;
		$totalRejected += $uRejected;
	?>
	<tr class="<?php if ($key % 2 != 0) echo 'alternate'?>">
		<th scope="row"><?php echo $user->display_name?></th>
		<td><?php echo $uLeads?></td>
		<td><?php echo $uFinalized?></td>
		<td><?php echo $uRejected?></td>
		<td><?php echo $leadsModel->getAgentAverageResponse($user->ID)?></td>
	</tr>
	<?php endforeach?>
		<tr>
			<th scope="col"><b>TOTAL</b></th>
			<th scope="col"><b><?php echo $totalLeads?></b></th>
			<th scope="col"><b><?php echo $totalFinalized?></b></th>
			<th scope="col"><b><?php echo $totalRejected?></b></th>
			<th scope="col">&nbsp;</th>
		</tr>
	</tbody>
</table>
<p></p>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
	var data = google.visualization.arrayToDataTable([
		[
			'Date',
			'Total',
			<?php foreach ($users as $key => $user):?>
			'<?php echo $user->display_name?>',
			<?php endforeach?>
		],
		<?php for ($n = time(), $i = $n - 2592000; $i <= $n; $i = $i + 86400):?>
		[
			'<?php echo date('d/m', $i)?>',
			<?php echo $leadsModel->getTotalLeads($i)?>,
			<?php foreach ($users as $key => $user):?>
			<?php echo $leadsModel->getTotalAgentLeads($user->ID, $i)?>,
			<?php endforeach?>
		],
		<?php endfor?>
	]);

	var options = {
		title: 'Over the last 30 days'
	};

	var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	chart.draw(data, options);
}
</script>
<div id="chart_div"></div>