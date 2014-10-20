<?php
if (!empty($_POST))
{
	if (isset($_POST['59sec_subscribe_mail']) && !empty($_POST['59sec_subscribe_mail']))
	{
		update_option('59sec_subscribe_mail', $_POST['59sec_subscribe_mail']);
		
		$url = 'https://59sec.com/licence/newsletter.php?key='.md5(get_real_site_url()).'&email='.$_POST['59sec_subscribe_mail'];
		
		wp_remote_get($url);
	}
}

$subscribe_mail = get_option('59sec_subscribe_mail', '');
if (empty($subscribe_mail)):
?>
<div id="sec59-subscribe">
59sec is a business critical service.
Please make sure you are subscribed at our newsletter,
in order not to miss an opportunity:
<form id="subscribeForm" name="subscribeForm" method="post" action="">
	<?php settings_fields('59sec-subscribe');?>
	<input type="text" id="59sec_subscribe_mail" name="59sec_subscribe_mail" value=""/>
	<?php submit_button('Subscribe', 'secondary', false, false, array('onclick' => 'showSubscribeThx()'))?>
	<span id="sec59-subscribe-thx" style="display:none;">
	Thank you
	</span>
</form>
</div>
<script type="text/javascript">
function showSubscribeThx() {
	document.getElementById('sec59-subscribe-thx').style.display = 'block';
}
</script>
<?php endif?>