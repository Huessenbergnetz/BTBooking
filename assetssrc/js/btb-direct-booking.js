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
        } else {
            jQuery('#btb_direct_booking_free_slots_' + event).html(BTBooking.fully_booked);
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
        } else {
            jQuery('#btb_direct_booking_free_slots_' + event).html(BTBooking.fully_booked);
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


jQuery(document).ready(function() {
    if (BTBooking.radiolist) {
        btb_change_radio(jQuery('.btb_direct_booking_selector input:checked'));
    } else {
        btb_change_selection(jQuery('.btb_direct_booking_selector'));
    }
    
    if (jQuery('.btb_direct_booking_box').length > 0) {
        var updFreeIntvl = window.setInterval(function() {
            var box = jQuery('.btb_direct_booking_box')
            var id = box.data('eventid');
            var apiUrl = box.data('apiurl');
            jQuery.ajax({
                url: apiUrl + '/wp-json/btbooking/v1/event/' + id + '/free'
            }).done(function(data) {
                jQuery.each(data, function(key, value) {
                    var opt = jQuery('#btb_direct_booking_selector_' + id + ' option[value="' +  key + '"]');
                    opt.data('slots', value);
                    if (opt.prop("selected")) {
                        if (value > 0) {
                            jQuery('#btb_direct_booking_free_slots_' + id).html(value + ' ' +  BTBooking.available);
                        } else {
                            jQuery('#btb_direct_booking_free_slots_' + id).html(BTBooking.fully_booked);
                        }
                        jQuery('#btb_direct_booking_amount_' + id).attr({"max" : value});
                    }
                });
            })
        }, 60000);
    }
});
