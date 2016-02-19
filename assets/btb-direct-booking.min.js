function btb_change_selection(e) {

    var selected = jQuery(e).children('option:selected');
    var event = jQuery(e).data('event-id');

    if (jQuery(e).val() === "") {
        jQuery('#btb_direct_booking_checkout_' + event).hide();
        jQuery('#btb_direct_booking_price_value_' + event).html(jQuery('#btb_direct_booking_price_value_' + event).data('default-price'));
    } else {
        var slots = selected.data('slots');
        var price = selected.data('price');
        if (slots > 0) {
            jQuery('#btb_direct_booking_free_slots_' + event).html(slots + ' ' +  BTBooking.available);
// 			jQuery('.btb_direct_booking_amount_submit').show();
        } else {
            jQuery('#btb_direct_booking_free_slots_' + event).html(BTBooking.fully_booked);
// 			jQuery('.btb_direct_booking_amount_submit').hide();
        }
        if (price) {
            jQuery('#btb_direct_booking_price_value_' + event).html(price);
        }
        jQuery('#btb_direct_booking_amount_' + event).attr({"max" : slots});
        jQuery('#btb_direct_booking_checkout_' + event).show();
    }
}


function btb_change_radio(e) {
	var selected = jQuery(e);
	var event = jQuery(e).data('event-id');

	if (selected.val() === "") {
		jQuery('#btb_direct_booking_checkout_' + event).hide();
        jQuery('#btb_direct_booking_price_value_' + event).html(jQuery('#btb_direct_booking_price_value_' + event).data('default-price'));
	} else {
		var slots = selected.data('slots');
        var price = selected.data('price');
		if (slots > 0) {
            jQuery('#btb_direct_booking_free_slots_' + event).html(slots + ' ' +  BTBooking.available);
// 			jQuery('.btb_direct_booking_amount_submit').show();
        } else {
            jQuery('#btb_direct_booking_free_slots_' + event).html(BTBooking.fully_booked);
// 			jQuery('.btb_direct_booking_amount_submit').hide();
        }
        if (price) {
            jQuery('#btb_direct_booking_price_value_' + event).html(price);
        }
        jQuery('#btb_direct_booking_amount_' + event).attr({"max" : slots});
        jQuery('#btb_direct_booking_checkout_' + event).show();
	}
}


function btb_clear_selection(e) {
    var event = jQuery(e).data('event-id');

    jQuery('#btb_direct_booking_checkout_' + event).hide();
	if (BTBooking.radiolist) {
		jQuery('input#time_0').prop({checked: true});
	} else {
		jQuery('#btb_direct_booking_selector_' + event).val("");
	}
    jQuery('#btb_direct_booking_price_value_' + event).html(jQuery('#btb_direct_booking_price_value_' + event).data('default-price'));
    jQuery('#btb_direct_booking_amount_' + event).val(1);
}

function btb_toggle_button(e) {
	var event = jQuery(e).data('event-id');

	if (jQuery(e).val() > 0) {
		jQuery('#btb_direct_submit_button_' + event).prop({disabled: false});
	} else {
		jQuery('#btb_direct_submit_button_' + event).prop({disabled: true});
	}
};


function btb_direct_booking_checkForm(e) {
	if (BTBooking.radiolist) {
		if (jQuery('input#time_0').prop("checked")) {
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}