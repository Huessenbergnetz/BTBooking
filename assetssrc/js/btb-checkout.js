/**
 * @file
 * @brief Provides JavaScript functions for the checkout form.
 * @author Matthias Fehring
 * @version 1.0.0
 * @date 2016
 *
 * @copyright
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Checks the form for validity
 */
function checkForm() {
	if (jQuery('#btb_checkout_cancel').val() == "true") {
		return true;
	}

	jQuery('#error_message_container').html("");
	jQuery('#error_message_container').hide();

	var check = true;

	var missingParts = [];

	var mailUnconfirmed = false;
	var mailInvalid = false;
	var termsNotChecked = false;

	if (BTBooking.require_terms == 1) {

		if (!jQuery('#btb_checkout_terms_accepted').prop('checked')) {
			termsNotChecked = true;
			check = false;
		}

	}

	var first_name = jQuery('#btb_checkout_first_name').val();

	if (first_name.trim().length < 2) {
		missingParts.push(BTBooking.strings['first_name']);
		check = false;
	}

	if (jQuery('#btb_checkout_last_name').val().trim().length < 2) {
		missingParts.push(BTBooking.strings['last_name']);
		check = false;
	}

	if (jQuery('#btb_checkout_address').val().trim().length < 2) {
		missingParts.push(BTBooking.strings['address']);
		check = false;
	}

	if (jQuery('#btb_checkout_zip').val().trim().length < 2) {
		missingParts.push(BTBooking.strings['zip']);
		check = false;
	}

	if (jQuery('#btb_checkout_city').val().trim().length < 2) {
		missingParts.push(BTBooking.strings['city']);
		check = false;
	}

	if (jQuery('#btb_checkout_country').val().trim().length < 2) {
		missingParts.push(BTBooking.strings['country']);
		check = false;
	}

	if (jQuery('#btb_checkout_mail').val().trim().length < 2) {
		missingParts.push(BTBooking.strings['email']);
		check = false;
	}

	if (jQuery('#btb_checkout_phone').val().trim().length < 2) {
		missingParts.push(BTBooking.strings['phone']);
		check = false;
	}

// 	if (jQuery('#btb_checkout_mail').val() !== jQuery('#btb_checkout_mail2').val()) {
// 		mailUnconfirmed = true;
// 		check = false;
// 	}

	var eMail = jQuery('#btb_checkout_mail').val();
	var atpos = eMail.indexOf("@");
	var dotpos = eMail.lastIndexOf(".");
	if (atpos < 1 || dotpos < atpos + 2 || dotpos + 3 > eMail.length) {
		check = false;
		mailInvalid = true;
	}

	if (check) {
		return true;
	} else {
		var em = [];

		if (missingParts.length > 0) {
			em.push('<p>', BTBooking.strings['missing_input'], '</p>');
			em.push('<ul>');
			missingParts.forEach(function(s, i, o) {
				em.push('<li>', s, '</li>');
			});
			em.push('</ul>');
		}

		if (mailUnconfirmed) {
			em.push('<p>', BTBooking.strings['email_confirmation_failed'], '</p>');
		}

		if (mailInvalid) {
			em.push('<p>', BTBooking.strings['email_check_failed'], '</p>');
		}

		if (termsNotChecked) {

		}

		jQuery('#error_message_container').html(em.join(''));
		jQuery('#error_message_container').show();

		return false;
	}
}


/**
 * Used to prepare the form for canceling the booking process.
 */
function cancelBooking() {
	jQuery('#btb_checkout_cancel').val("true");
	jQuery('#btb_checkout_form').submit();
}


jQuery(document).ready(function() {
    if (jQuery('#btb_checkout_form').length > 0) {
        var formSubmit = false;
        var btbCheckoutBookingId = jQuery('#btb_checkout_bookingid').val();
        var btbCheckoutNonce = jQuery('#btb_checkout_nonce').val();
        jQuery('#btb_checkout_form').on('submit', function() {
            formSubmit = true;
        });
        jQuery(window).on('beforeunload', function() {
            if (!formSubmit) {
                jQuery.ajax({
                    method: 'POST',
                    data: {btb_checkout_cancel: true, btb_checkout_bookingid: btbCheckoutBookingId, btb_checkout_nonce: btbCheckoutNonce }
                });
            }
        })
        var btb_timeLeft = 8; //minutes
        jQuery('span.btb_booking_time_left_time').html(btb_timeLeft);
        var btb_booking_time_left_interval = window.setInterval(function() {
            btb_timeLeft--;
            if (btb_timeLeft <= 0) {
                cancelBooking();
            }
            jQuery('span.btb_booking_time_left_time').html(btb_timeLeft);
        }, 60000);        
    }
});
