function checkLead(id, url) {
	var data = {
        'action': 'grapit',
        'id': id
    };
	var localUrl = url;
	if(window.console) window.console.log('Redirecting to: ' + localUrl);
	
	jQuery.post(ajaxurl, data, function(response) {
		if (response != null && response != '') {
			alert(response);
		} else {			
			window.location.href = localUrl;
		}
	});
}

function tryFix(id) {
	var data = {
        'action': 'tryfix',
        'id': id
    };
	
	jQuery.post(ajaxurl, data, function(response) {
		if (response != null && response != '') {
			alert(response);
		} else {			
			window.location.reload();
		}
	});
}

function editNote(id) {
	var data = {
        'action': 'edit_note',
        'id': id
    };
	
	jQuery.post(ajaxurl, data, function(response) {
		if (typeof tinyMCE == 'undefined') {
			jQuery('#note').html(response);
		} else {
			var editor = tinyMCE.get('note');
			
			if (editor != null) {
				if(/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
					jQuery('#note-html').click();
				}
				
				editor.setContent(response);
			} else {
				jQuery('#note').html(response);
			}
		}
		
		jQuery('#lead_id').val(id);
		jQuery('#popup-note').show();
	});
}

function saveNote() {
	var id = jQuery('#lead_id').val();
	var note = '';
	
	if (typeof tinyMCE == 'undefined') {
		note = jQuery('#note').val();
	} else {
		var editor = tinyMCE.get('note');
		
		if (editor != null) {
			note = editor.getContent();
		} else {
			note = jQuery('#note').val();
		}
	}
	var data = 'id='+id+'&note='+note;
	
	jQuery.post(window.location.href, data, function(response) {
		jQuery('.lead-'+id+' span.note').html(note);
	});
	
	closeNote();
}

function closeNote() {
	jQuery('#popup-note').hide();
}

function changeStatus(id, element) {
	var value = jQuery(element).val();
	
	var data = {
        'action': 'change_lead_status',
        'id': id,
		'status': value
    };
	
	jQuery.post(ajaxurl, data);
}

window.mTimers = [];

function initTimers() {
	jQuery('.timer').each(function() {
		window.mTimers.push(jQuery(this));
	});
	
	timePassed();
	setTimeout('liveUpdate()', window.checkInterval * 1000);
}

function timePassed() {
	var val = 0;

	for (var i = 0, n = window.mTimers.length; i < n; i = i + 1) {
		val = 0;
		val = parseInt(window.mTimers[i].attr('data-time'));
		val = val + 1;
		window.mTimers[i].attr('data-time', val);
		window.mTimers[i].html(timeFormat(val));
	}
	
	setTimeout('timePassed()', 1000);
}

function timeFormat(seconds)
{
	var time = '';
	hours = parseInt(seconds / 3600);
	seconds = seconds - (hours * 3600);
	minutes = parseInt(seconds / 60);
	seconds = seconds - (minutes * 60);
	
	if (hours > 0)
	{
		time += hours+'h ';
	}
	if (minutes > 0)
	{
		time += minutes+'min ';
	}
	time += seconds+'sec';
	
	return time;
}

function liveUpdate() {

	var data = {
        'action': 'liveupdate',
		'lastcheck': window.lastCheck
    };
	
	jQuery.post(ajaxurl, data, function(response) {
		if (response != null && response != '') {
            jQuery('#wrapper-leads').html(response);

            // update counters
            window.mTimers = [];

            jQuery('.timer').each(function() {
				window.mTimers.push(jQuery(this));
            });
        }
				
		setTimeout('liveUpdate()', window.checkInterval * 1000);
	});
}

function formPage(item, page) {
	var status = jQuery('#filter-status-'+item).val();
	var keyword = jQuery('#filter-keyword-'+item).val();
	
	var data = {
		'action': 'crm_page',
		'item': item,
		'page': page,
		'status': status,
		'keyword': keyword
	};
	
	jQuery.post(ajaxurl, data, function(response) {
		if (response != null && response != '') {
			jQuery('#wrapper-form-'+item).html(response);
			jQuery('#crm-page-'+item).val(page);
		}
	});
}

function changeFilter(item) {
	var status = jQuery('#filter-status-'+item).val();
	var keyword = jQuery('#filter-keyword-'+item).val();
	page = 0;
	
	var data = {
		'action': 'crm_page',
		'item': item,
		'page': page,
		'status': status,
		'keyword': keyword
	};
	
	jQuery.post(ajaxurl, data, function(response) {
		if (response != null && response != '') {
			jQuery('#wrapper-form-'+item).html(response);
		}
	});
}

function updateLeadsCount(total){
	jQuery('.leadsTotalRemaining').html(total);
}

function sortBy(field){
	var order = wpCookies.get('59sec_sd') || '';
		
	if(order=='') 
		order = 'asc';
	else if(order=='asc')
		order = 'desc'
	else
		order = 'asc';
	
	wpCookies.set('59sec_sb', field);
	wpCookies.set('59sec_sd', order);
	
	document.location = document.location;
}
