jQuery(document).ready(function() {
	jQuery('#edit-checker').prop("checked", false);

	toggle_editability(false);
});

jQuery("#edit-checker").change(function() {

	var checked = jQuery(this).prop("checked");

	toggle_editability(checked);
});

function toggle_editability(checked) {
	var texts = ['btb_company', 'btb_first_name', 'btb_last_name', 'btb_address', 'btb_address2', 'btb_city', 'btb_zip', 'btb_mail', 'btb_phone'];
	var textsSize = texts.length;
	for (var i = 0; i <= textsSize; i++) {
		jQuery('#' + texts[i]).prop("readonly", !checked);
	}

	var selectors = ['post_status', 'btb_title', 'btb_country'];
	var selectorsSize = selectors.length;
	for (var i = 0; i <= selectorsSize; i++) {
		jQuery('#' + selectors[i]).prop("disabled", !checked);
	}
}