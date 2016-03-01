var newTimes = 0;

function btb_render_input(type, label, part, desc, och, min, max, step, className, value) {
    var retHtml = [];
    var fieldId = 'btb_new_times_' + newTimes + '_' + part;
    var descTag = "";
    var classTag = "";

    if (desc) {
        if (type === "checkbox") {
            descTag = '<span class="btb_admin_desc">' + desc + '</span>';
        } else {
            descTag = '<p class="btb_admin_desc">' + desc + '</p>';
        }
    }


    retHtml.push('<tr>',
                    '<th scope="row"><label for="', fieldId, '">', label, '</label></th>',
                    '<td>',
                        '<input type="', type ,'"',
                        ' id="', fieldId, '"',
                        ' name="btb_new_times[', newTimes, '][', part, ']"',
                        ' data-time-id="', newTimes, '"',
                        och ? ' onchange="' + och + '"' : '' ,
                        min ? ' min="' + min + '"' : '',
                        max ? ' max="' + max + '"' : '',
                        step ? ' step="' + step + '"' : '',
                        className ? ' class="' + className + '"' : '',
                        value ? ' value="' + value + '"' : '',
                        ' />',
                        desc ? descTag : '',
                    '</td>',
                 '</tr>');

    return retHtml.join('');
}


function btb_render_name_header(idBase) {
    var retHtml = [];
    var now = new Date();
    retHtml.push('<div class="btb_time_header" data-time-id="', newTimes, '" onclick="btb_toggle_content(this)"><h4 id="btb_new_times_', newTimes, '_header">',
                 jQuery.datepicker.formatDate(BTBooking.date_format, new Date()),
        '</h4><a class="btb_delete_time" href="#" onclick="btb_remove_new_time(this)">remove</a></div>'
    );

    return retHtml.join('');
}

function btb_render_times_entry() {
    var retHtml = [];
    var idBase = 'btb_new_time_' + newTimes;
    retHtml.push('<div id="', idBase ,'" class="btb_new_time btb_time_box">',
                 btb_render_name_header(idBase),
                 '<input type="hidden" name="btb_new_times[', newTimes, '][time_name]" id="btb_new_times_', newTimes, '_time_name" value="', jQuery.datepicker.formatDate(BTBooking.date_format, new Date()) ,'" />',
                 '<input type="hidden" name="btb_new_times[', newTimes, '][start_date_secs]" id="btb_new_times_', newTimes, '_start_date_secs" value="', jQuery.datepicker.formatDate('@', new Date()) ,'" />',
                 '<input type="hidden" name="btb_new_times[', newTimes, '][end_date_secs]" id="btb_new_times_', newTimes, '_end_date_secs" value="', jQuery.datepicker.formatDate('@', new Date()) ,'" />',
                 '<table id="btb_new_times_', newTimes, '_table" class="form-table"><tbody>',
        btb_render_input('text', BTBooking.strings['Start date'], 'start_date', null, 'btb_update_name_header(this)', null, null, null, 'btb_start_date btb_date_picker', jQuery.datepicker.formatDate(BTBooking.date_format, new Date())),
        btb_render_input('text', BTBooking.strings['End date'], 'end_date', null, 'btb_update_name_header(this)', null, null, null, 'btb_end_date btb_date_picker', jQuery.datepicker.formatDate(BTBooking.date_format, new Date())),
        btb_render_input('checkbox', BTBooking.strings['Date only'], 'date_only', BTBooking.strings['date_only_desc'], 'btb_toggle_time_input(this)'),
        btb_render_input('number', BTBooking.strings['Slots'], 'slots', null, null, 1, null, 1, null, 5),
        btb_render_input('number', BTBooking.strings['Price'], 'price', null, null, 0),
        '</tbody></table></div>');

    return retHtml.join('');
}


function btb_delete_time(e) {
    var ele = jQuery(e);

    if (jQuery('#btb_times_' + ele.data('time-id') + '_delete').val() == "false") {
        jQuery('#btb_times_' + ele.data('time-id') + '_header').addClass("header_delete");
        ele.html(BTBooking.strings['cancel']);
        jQuery('#btb_times_' + ele.data('time-id') + '_delete').val("true");
    } else {
        jQuery('#btb_times_' + ele.data('time-id') + '_header').removeClass("header_delete");
        ele.html(BTBooking.strings['delete']);
        jQuery('#btb_times_' + ele.data('time-id') + '_delete').val("false");
    }
}

function btb_update_name_header(e) {
    var ele = jQuery(e);
    if (ele.val() !== undefined) {

        var startDate = jQuery.datepicker.parseDate(BTBooking.date_format, jQuery('#btb_new_times_' + ele.data('time-id') + '_start_date').val());

        var endDate = jQuery.datepicker.parseDate(BTBooking.date_format, jQuery('#btb_new_times_' + ele.data('time-id') + '_end_date').val());

        jQuery('#btb_new_times_' + ele.data('time-id') + '_start_date_secs').val(jQuery.datepicker.formatDate('@', startDate))
        jQuery('#btb_new_times_' + ele.data('time-id') + '_end_date_secs').val(jQuery.datepicker.formatDate('@', endDate))

        var name = "";

        if (startDate.getDate() !== endDate.getDate() || startDate.getMonth() !== endDate.getMonth() || startDate.getFullYear() !== endDate.getFullYear()) {
            name = jQuery.datepicker.formatDate(BTBooking.date_format, startDate) + ' - ' + jQuery.datepicker.formatDate(BTBooking.date_format, endDate);
        } else {
            name = jQuery.datepicker.formatDate(BTBooking.date_format, startDate);
        }

        jQuery('#btb_new_times_' + ele.data('time-id') + '_header').html(name);
        jQuery('#btb_new_times_' + ele.data('time-id') + '_time_name').val(name);
    }
}


function btb_update_name_header_saved(e) {
    var ele = jQuery(e);
    if (ele.val() !== undefined) {

        var startDate = jQuery.datepicker.parseDate(BTBooking.date_format, jQuery('#btb_times_' + ele.data('time-id') + '_start_date').val());

        var startString = jQuery.datepicker.formatDate( "M d, yy", startDate, { monthNamesShort: jQuery.datepicker.regional["en"].monthNamesShort});
        startString += " ";
        startString += jQuery('#btb_times_' + ele.data('time-id') + '_start_time').val();
        startString += ":00";

        var startTime = new Date(startString);


        var endDate = jQuery.datepicker.parseDate(BTBooking.date_format, jQuery('#btb_times_' + ele.data('time-id') + '_end_date').val());

        var endString = jQuery.datepicker.formatDate( "M d, yy", endDate, { monthNamesShort: jQuery.datepicker.regional["en"].monthNamesShort});
        startString += " ";
        startString += jQuery('#btb_times_' + ele.data('time-id') + '_end_time').val();
        startString += ":00";

        var endTime = new Date(endString);


        jQuery('#btb_times_' + ele.data('time-id') + '_start_date_secs').val(jQuery.datepicker.formatDate('@', startTime));
        jQuery('#btb_times_' + ele.data('time-id') + '_end_date_secs').val(jQuery.datepicker.formatDate('@', endTime));

		var date_only = jQuery('#btb_times_' + ele.data('time-id') + '_date_only').prop('checked');

        var name = "";

        if (startTime.getDate() !== endTime.getDate() || startTime.getMonth() !== endTime.getMonth() || startTime.getFullYear() !== endTime.getFullYear()) {
            name = jQuery.datepicker.formatDate(BTBooking.date_format, startTime) + ' - ' + jQuery.datepicker.formatDate(BTBooking.date_format, endTime);
        } else {
			if (date_only) {
				name = jQuery.datepicker.formatDate(BTBooking.date_format, startTime);
			} else {
				var hours = startTime.getHours();
				var minutes = startTime.getMinutes();
				var hoursDisplay = ((hours < 10) ? "0" + hours : hours);
				var minutesDisplay = ((minutes < 10) ? "0" + minutes : minutes);
				name = jQuery.datepicker.formatDate(BTBooking.date_format, startTime) + ' ' + hoursDisplay + ':' + minutesDisplay;
			}
        }

        jQuery('#btb_times_' + ele.data('time-id') + '_header').html(name);
        jQuery('#btb_times_' + ele.data('time-id') + '_time_name').val(name);
    }
}


function btb_remove_new_time(e) {
    var ele = jQuery(e);
    ele.parent().parent().remove();
    newTimes--;
    jQuery('#btb_new_times_count').val(newTimes);
}


function btb_toggle_content(e) {
    var ele = jQuery(e);
    ele.siblings('table').toggle();
}

function btb_toggle_time_input(e) {

}


jQuery(document).ready(function() {
    jQuery('.btb_date_picker').datepicker({dateFormat: BTBooking.date_format});
    jQuery('#add_time').click(function () {
        jQuery('#times_container').append(btb_render_times_entry());
        jQuery('#btb_new_times_count').val(newTimes);
        jQuery('.btb_date_picker').datepicker({dateFormat: BTBooking.date_format});
        newTimes++;
    });

	jQuery('input[name=btb_struct_data_type]:radio').click(function() {
        if (jQuery('input[name=btb_struct_data_type]:checked').val() != 'event') {
			jQuery('#eventTypeRow').hide();
            jQuery('#venueRow').hide();
        } else {
            jQuery('#eventTypeRow').show();
            jQuery('#venueRow').show();
        }
    });
});