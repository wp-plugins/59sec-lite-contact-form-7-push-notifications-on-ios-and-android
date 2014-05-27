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
<h2>Users</h2>
<p>Explanation: Wordpress administrators have full access by default, as sales manager! </p>
<h3>Sales Agents</h3>
<p>If you have sales agents, you should create normal &quot;subscribers&quot; wordpress users,  than check them here to grant them Agent Level access (Leads, Statistics, CRM). This way, they cannot mess up your site! :). This &quot;sales agents&quot; feature is available only for 59sec PRO.</p>
<p>
  <label><a href="http://www.59sec.com" target="_blank"><strong>Upgrade now to 59sec PRO version!</strong></a></label>
  <br />
<a href="http://www.59sec.com">First month is FREE, no credit card needed, no strings attached! </a></p>
<form name="capForm" method="post" action="">
<table class="wp-list-table widefat fixed users" cellspacing="0">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th><th scope="col" id="username" class="manage-column column-username" style=""><span>Username</span></th><th scope="col" id="name" class="manage-column column-name" style=""><span>Name</span></th><th scope="col" id="email" class="manage-column column-email" style=""><span>E-mail</span></th><th scope="col" id="role" class="manage-column column-role" style="">Role</th></tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></th><th scope="col" class="manage-column column-username" style=""><span>Username</span></th><th scope="col" class="manage-column column-name" style=""><span>Name</span></th><th scope="col" class="manage-column column-email" style=""><span>E-mail</span></th><th scope="col" class="manage-column column-role" style="">Role</th></tr>
	</tfoot>

	<tbody id="the-list" data-wp-lists="list:user">
	<?php foreach($users as $key => $user):?>
	<?php $checked = ($user->allcaps['agent'] == 1) ? 'checked="checked"' : '';?>
	<tr id="user-<?php echo $user->ID?>" class="<?php if ($key % 2 != 0) echo 'alternate'?>">
		<th scope="row" class="check-column">
			<label class="screen-reader-text" for="cb-select-<?php echo $user->ID?>">Select Agent</label>
			<input type="checkbox" name="users[]" id="user_<?php echo $user->ID?>" class="subscriber" value="<?php echo $user->ID?>" <?php echo $checked?> />
		</th>
		<td class="username column-username">
			<?php echo get_avatar($user->ID, 32)?>
			<strong><?php echo $user->user_login?></strong>
		</td>
		<td class="name column-name"><?php echo $user->display_name?></td>
		<td class="email column-email">
			<a href="mailto:<?php echo $user->user_email?>" title="E-mail: <?php echo $user->user_email?>"><?php echo $user->user_email?></a>
		</td>
		<td class="role column-role"><?php echo $user->roles['0']?></td>
	</tr>
	<?php endforeach?>
	</tbody>
</table>
<input type="submit" name="op" value="Save" class="button button-primary" />
</form>