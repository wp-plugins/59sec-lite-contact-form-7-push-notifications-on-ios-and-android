/**
 * Schedule cron tasks with ajax,
 * so user does not experience the page load
 */
function _59sec_check_cron() {
	var data = {
        'action': '59sec_cron'
    };
	
	jQuery.post(ajaxurl, data);
	
	setTimeout('_59sec_check_cron();', 10000);
}

setTimeout('_59sec_check_cron();', 333);