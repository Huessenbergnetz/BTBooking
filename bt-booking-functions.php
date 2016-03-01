<?php
/**
 * @file
 * @brief Implements the global functions.
 * @author Matthias Fehring
 * @version 1.0.0
 * @date 2016
 *
 * @copyright
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

defined( 'ABSPATH' ) or die (' Am Arsch die R&auml;uber! ');

/** @defgroup globalfns Global functions
 * Globally available functions.
 * @{
 */

/**
 * Returns the total amount of slots for a BTB_Event.
 *
 * @param int $event_id BTB_Event ID.
 * @return int Amount of slots.
 */
function btb_get_event_total_slots($event_id) {
	global $wpdb;
	$total_slots = $wpdb->get_var($wpdb->prepare("SELECT SUM(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'btb_slots' AND post_id IN (SELECT ID FROM $wpdb->posts WHERE post_parent = %u AND post_type = 'btb_time')", $event_id));
	return intval($total_slots);
}


/**
 * Returns the total amount of booked slots for a BTB_Event.
 *
 * @param int $event_id BTB_Event ID.
 * @return int Amount of booked slots.
 */
function btb_get_event_booked_slots($event_id) {
	global $wpdb;
	$slots = $wpdb->get_var($wpdb->prepare("SELECT sum(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'btb_slots' AND post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'btb_booking' AND post_status = 'btb_booked' AND post_parent IN (SELECT ID FROM $wpdb->posts WHERE post_parent = %u AND post_type = 'btb_time'))", $event_id));
	return intval($slots);
}


/**
 * @brief Returns the total amount of pre booked slots for a BTB_Event.
 *
 * @param int $event_id BTB_Event ID.
 * @return int Amount of pre-booked slots.
 */
function btb_get_event_prebooked_slots($event_id) {
	global $wpdb;
	$slots = $wpdb->get_var($wpdb->prepare("SELECT sum(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'btb_slots' AND post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'btb_booking' AND post_status = 'btb_prebook' AND post_parent IN (SELECT ID FROM $wpdb->posts WHERE post_parent = %u AND post_type = 'btb_time'))", $event_id));
	return intval($slots);
}


/**
 * @brief Returns the total amount of free slots for a BTB_Event.
 *
 * @param int $event_id BTB_Event ID.
 * @return int Amount of free slots.
 */
function btb_get_event_free_slots($event_id) {
	$prebooked_slots = btb_get_event_prebooked_slots($event_id);
	$booked_slots = btb_get_event_booked_slots($event_id);
	$total_slots = btb_get_event_total_slots($event_id);

	return $total_slots - $prebooked_slots - $booked_slots;
}


/**
 * @brief Returns an array with a summary of slots of a BTB_Event.
 *
 * @par Exmaple output
 * @verbatim
 Array(
	[free] => 5
	[prebooked] => 1
	[booked] => 4
	[total] => 10
 )@endverbatim
 *
 * @param int $event_id BTB_Event ID.
 * @return array Associative array containing summary about slots.
 */
function btb_get_event_slots_summary($event_id) {
	$prebooked_slots = btb_get_event_prebooked_slots($event_id);
	$booked_slots = btb_get_event_booked_slots($event_id);
	$total_slots = btb_get_event_total_slots($event_id);

	$slots = array();
	$slots["free"] = $total_slots - $prebooked_slots - $booked_slots;
	$slots["prebooked"] = $prebooked_slots;
	$slots["booked"] = $booked_slots;
	$slots["total"] = $total_slots;

	return $slots;
}



/**
 * @brief Returns the total amount of slots for a BTB_Time.
 *
 * @param int $time_id BTB_Time ID.
 * @return int Amount of slots.
 */
function btb_get_time_total_slots($time_id) {
	return intval(get_post_meta($time_id, 'btb_slots', true));
}


/**
 * @brief Returns the total amount of booked slots for a BTB_Time.
 *
 * @param int $time_id BTB_Time ID.
 * @return int Amount of booked slots.
 */
function btb_get_time_booked_slots($time_id) {
	global $wpdb;
// 	$slots = $wpdb->get_var($wpdb->prepare("SELECT sum(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'btb_slots' AND post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'btb_booking' AND post_status = 'btb_booked' AND post_parent IN (SELECT ID FROM $wpdb->posts WHERE post_parent = %u AND post_type = 'btb_time'))", $event_id));
	$slots = $wpdb->get_var($wpdb->prepare("SELECT SUM(meta_value) FROM wp_postmeta WHERE meta_key = 'btb_slots' AND post_id IN (SELECT ID FROM wp_posts WHERE post_parent = %d AND post_status = 'btb_booked')", $time_id));
	return intval($slots);
}


/**
 * @brief Returns the total amount of pre booked slots for a BTB_Time.
 *
 * @param int $time_id BTB_Time ID.
 * @return int Amount of pre-booked slots.
 */
function btb_get_time_prebooked_slots($time_id) {
	global $wpdb;
// 	$slots = $wpdb->get_var($wpdb->prepare("SELECT sum(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'btb_slots' AND post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'btb_booking' AND post_status = 'btb_prebook' AND post_parent IN (SELECT ID FROM $wpdb->posts WHERE post_parent = %u AND post_type = 'btb_time'))", $event_id));
	$slots = $wpdb->get_var($wpdb->prepare("SELECT SUM(meta_value) FROM wp_postmeta WHERE meta_key = 'btb_slots' AND post_id IN (SELECT ID FROM wp_posts WHERE post_parent = %d AND post_status = 'btb_prebook')", $time_id));
	return intval($slots);
}




/**
 * @brief Returns the total amount of free slots for a BTB_Time.
 *
 * @param int $time_id BTB_Time ID.
 * @return int Amount of free slots.
 */
function btb_get_time_free_slots($time_id) {
	$prebooked_slots = btb_get_time_prebooked_slots($time_id);
	$booked_slots = btb_get_time_booked_slots($time_id);
	$total_slots = btb_get_time_total_slots($time_id);

	return $total_slots - $prebooked_slots - $booked_slots;
}


/**
 * @brief Returns an array with a summary of slots of a BTB_Time.
 *
 * @par Exmaple output
 * @verbatim
 Array(
	[free] => 5
	[prebooked] => 1
	[booked] => 4
	[total] => 10
 )@endverbatim
 *
 * @param int $time_id BTB_Time ID.
 * @return array Associative array containing summary about slots.
 */
function btb_get_time_slots_summary($time_id) {
	$prebooked_slots = btb_get_time_prebooked_slots($time_id);
	$booked_slots = btb_get_time_booked_slots($time_id);
	$total_slots = btb_get_time_total_slots($time_id);

	$slots = array();
	$slots["free"] = $total_slots - $prebooked_slots - $booked_slots;
	$slots["prebooked"] = $prebooked_slots;
	$slots["booked"] = $booked_slots;
	$slots["total"] = $total_slots;

	return $slots;
}




/**
 * Sanitize every booking field.
 *
 * If the context is 'raw', then the booking object or array will get minimal
 * sanitization of the integer fields.
 *
 * @since 1.0.0
 *
 * @see BTB_Booking::sanitize_post_field()
 *
 * @param object|BTB_Booking|array	$booking	The booking object or array.
 * @param string					$context	Optional. How to sanitize the booking fields.
												Accepts 'raw', 'edit', 'db', 'attribute', 'js' or 'display'.
												Default 'display'.
 * @return object|BTB_Booking|array	The now snitized BTB_Booking object or array.
 */
function btb_sanitize_booking($booking, $context = 'display') {
	if (is_object($booking)) {
		// Check if booking already filtered for this context.
		if (isset($booking->filter) && $context == $booking->filter) {
			return $booking;
		}
		if (!isset($booking->ID)) {
			$booking->ID = 0;
		}
		foreach(array_keys(get_object_vars($booking)) as $field) {
			$booking->$field = btb_sanitize_booking_field($field, $booking->$field, $booking->ID, $context);
		}
		$booking->filter = $context;
	} elseif (is_array($booking)) {
		// Check if booking already filtered for this context.
		if (isset($booking['filter']) && $context == $booking['filter']) {
			return $booking;
		}
		if (!isset($booking['ID'])) {
			$booking['ID'] = 0;
		}
		foreach(array_keys($booking) as $field) {
			$booking[$field] = btb_sanitize_booking_field($field, $booking[$field], $booking['ID'], $context);
		}
		$booking['filter'] = $context;
	}
	return $booking;
}


/**
 * Sanitize booking field based on context.
 *
 * Possible context values are: 'raw', 'edit', 'db', 'display', 'attribute' and 'js'.
 * The 'display' context is used by default. 'attribute' and 'js' contexts are treated like 'display' when
 * calling filters.
 *
 * @todo Implement format_to_edit as in WordPress sanitize_post_field.
 *
 * @since 1.0.0
 *
 * @param string	$field		The Booking Object field name.
 * @param mixed		$value		The Booking Object value.
 * @param int 		$booking_id	The Booking ID.
 * @param string	$context	Optional. Ho to sanitize booking fields. Looks for 'raw', 'edit',
								'db', 'display', 'attribute' and 'js'. Default 'display'.
 * @return mixed Sanitized value.
 */
function btb_sanitize_booking_field($field, $value, $booking_id, $context = 'display') {
	$int_fields = array('ID', 'booked_event', 'booked_time', 'booked_slots', 'booking_time');
	if (in_array($field, $int_fields)) {
		$value = (int) $value;
	}

	$float_fields = array('price');
	if (in_array($field, $float_fields)) {
		$value = (float) $value;
	}

	if ('raw' == $context) {
		return $value;
	}

	if ('edit' == $context) {

		$value = apply_filters("edit_btb_booking_${field}", $value, $booking_id);

		$value = esc_attr($value);
	} elseif ( 'db' == $context) {
		$value = apply_filters("btb_booking_{$field}_pre", $value);
	} else {
		// Use display filters by default.
		$value = apply_filters("btb_booking_{$field}", $value, $booking_id, $context);
	}

	if ('attribute' == $context) {
		$value = esc_attr($value);
	} elseif ('js' == $context) {
		$value = ecs_js($value);
	}


	return $value;
}


/**
 * @brief Retrieves booking data given a booking ID or booking object.
 *
 * @see sanitize_booking() for optional $filter values. Also, the parameter $booking
 * must be given as a variable, since it is passed by reference.
 *
 * @param int|BTB_Booking	$booking Booking ID or BTB_Booking object.
 * @param string 			$output Optional, default is Object. Accepts OBJECT, ARRAY_A or ARRAY_N.
 * @param string			$filter Optional. Type fo filter to apply. Accepts 'raw', 'edit', 'db', 'display',
 *									'attribute' or 'js'. Default 'raw'.
 * @return BTB_Booking|array|null	Type corresponding to $output on success or null on failure.
 *						      		When $output is OBJECT, a `BTB_Booking` instance is returned.
 */
function btb_get_booking($booking, $output = OBJECT, $filter = 'raw') {
	if ($booking instanceof BTB_Booking) {
		$_booking = $booking;
	} elseif (is_object($booking)) {
		if (empty($booking->filter)) {
			$_booking = btb_sanitize_booking($booking, 'raw');
			$_booking = new BTB_Booking($_booking);
		} elseif ('raw' == $booking->filter) {
			$_booking = new BTB_Booking($booking);
		} else {
			$_booking = BTB_Booking::get_instance($booking->ID);
		}
	} else {
		$_booking = BTB_Booking::get_instance($booking);
	}

	if (!$_booking) {
		return null;
	}

	$_booking = $_booking->filter($filter);

	if ($output == ARRAY_A) {
		return $_booking->to_array();
	} elseif ($output == ARRAY_N) {
		return array_values($_booking->to_array());
	}

	return $_booking;
}


/**
 * @brief Creates a new booking.
 *
 * @param int $event_id ID of the BTB_Event this booking is created for.
 * @param int $time_id ID of the BTB_Time this booking is created for.
 * @param array $bookingarr WP_Post meta data array used for creating the new booking and directly store
 *                          meta data like slots, booking time and price.
 * @param bool $obj Set to true if you want to return the newly created booking as a BTB_Booking object.
 * @return int|BTB_Booking If \a $obj is false, the ID of the new booking will be returned, 0 on failure.
 *                         With \a $obj true a BTB_booking object will be returned.
 */
function btb_create_booking($event_id, $time_id, $bookingarr, $obj = false) {

	$booking_code = btb_gen_booking_number();

	$new_booking = wp_insert_post(array(
		'post_title' => $booking_code,
		'post_parent' => $time_id,
		'post_type' => 'btb_booking',
		'post_status' => 'btb_prebook',
		'meta_input' => $bookingarr
	));

	if (!$new_booking) {
		return 0;
	}

	if ($obj) {
		return BTB_Booking::get_instance($new_booking);
	} else {
		return $new_booking;
	}
}



/**
 * Trash or delete a booking.
 *
 * When the booking is permanently deleted, everything that is tied to it is delted also.
 *
 * @param int $booking_id ID of the booking to delete.
 * @param bool $force_delete Wether to bypass trash and force deletion.
 * @return array|false|WP_Post False on failure.
 */
function btb_delete_booking($booking_id, $force_delete = false) {

	wp_cache_delete($booking_id, 'btb_bookings');

	return wp_delete_post($booking_id, $force_delete);
}


/**
 * @brief Generates a unique booking code.
 *
 * @return string Booking code.
 */
function btb_gen_booking_number() {
	$unique = false;
	$rand_string = btb_gen_random_string();

	global $wpdb;

	while (!$unique) {

		$enemies = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_title = %s", $rand_string));
		if ($enemies) {
			$rand_string = btb_gen_random_string();
		} else {
			$unique = true;
		}
	}

	return $rand_string;
}

/**
 * @brief Generates a random string.
 *
 * Generates a random string of \a $length characters,
 * containing uppercase latin alphanumerics.
 *
 * @param int $length The length of the string.
 * @return string Random string.
 */
function btb_gen_random_string($length = 6) {
	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}


/**
 * @brief Updates the BTB_Booking \a $bookingarr.
 *
 * @param BTB_Booking|array $bookingarr The booking that should be updated.
 * @return int The value 0 on failure. The booking ID on success.
 */
function btb_update_booking($bookingarr) {
	if (is_object($bookingarr)) {
		$bookingarr = $bookingarr->to_array(true);
		$bookingarr = wp_slash($bookingarr);
	}

	return wp_update_post($bookingarr);
}



/**
 * @brief Returns the ID of the event description page or permalink to it.
 *
 * If \a $permalink is true, it returns the permalink to the
 * description page.
 *
 * @param BTB_Event|BTB_Time|BTB_Booking $obj Object to retrieve the description page for.
 * @param bool $permalink If true, a permalink to the description page will be returned.
 * @return int|string Returns either the ID of the description page or the permalink to it.
 *					  Returns 0, if the page could not be found.
 */
function btb_get_description_page(&$obj, $permalink = false) {
	if (!is_object($obj)) {
		return 0;
	}

	$desc_page = 0;

	if ($obj->post_type == 'btb_booking') {
		if ($obj->booked_event) {
			$desc_page = (int) get_post_meta($obj->booked_event, 'btb_desc_page', true);
			if (0 == $desc_page) {
				$desc_page = $obj->ID;
			}
		} elseif ($obj->booked_time) {
			$event_id = (int) get_post_field('post_parent', $obj->booked_time);
			$desc_page = (int) get_post_meta($event_id, 'btb_desc_page', true);
			if (0  == $desc_page) {
				$desc_page = $event_id;
			}
		}
	} elseif ($obj->post_type == 'btb_time') {
		$desc_page = (int) get_post_meta($obj->post_parent, 'btb_desc_page', true);
		if (0 == $desc_page) {
			$desc_page = $obj->post_parent;
		}
	} elseif ($obj->post_type == 'btb_event') {
		$desc_page = $obj->desc_page;
		if (0 == $desc_page) {
			$desc_page = $obj->ID;
		}
	}

	if ($permalink && 0 != $desc_page) {
		return get_permalink($desc_page);
	}

	return $desc_page;
}



/**
 * @brief Returns the full or short description of an event.
 *
 * Tries to find a description at first in the event itself, if that is empty and
 * the event has a description page, it searaches for a description in the description
 * page.
 *
 * If @a $short_desc is true, the short description (excerpt) is returned.
 *
 * @param BTB_Event $event The event to request the description for.
 * @param bool $short_desc If true, the short description (excerpt) will be returned.
 *
 * @return string The description or an empty string.
 */
function btb_get_event_description(BTB_Event &$event, $short_desc = false) {

	if ($short_desc) {
		if (!empty($event->short_desc)) {
			return $event->short_desc;
		} else {
			$desc_page = btb_get_description_page($event);
			if ($desc_page != $event->ID) {
				$dp = get_post($desc_page);
				return $dp->post_excerpt;
			} else {
				return '';
			}
		}
	} else {
		if (!empty($event->description)) {
			return $event->description;
		} else {
			$desc_page = btb_get_description_page($event);
			if ($desc_page != $event->ID) {
				$dp = get_post($desc_page);
				return $dp->post_content;
			} else {
				return '';
			}
		}
	}
}



/**
 * @brief Returns the image for the event in an array with specified sizes.
 *
 * The image is returned in the specified @a sizes, by default: full and thumbnail. The returned
 * associative array will have the key named by the size, the value will be an array returned
 * by @a wp_get_attachment_image_src.
 *
 * @param BTB_Event $event The event to request the image for.
 * @param array $sizes The sizes to return.
 *
 * @return array Associative array containing imag data, if nothing found, array will be empty.
 */
function btb_get_event_images(BTB_Event &$event, array $sizes = array('thumbnail', 'full')) {

	$eventImageID = get_post_thumbnail_id($event->ID);
	if (empty($eventImageID) && !empty($event->desc_page)) {
		$eventImageID = get_post_thumbnail_id($event->desc_page);
	}

	if (!empty($eventImageID)) {
		$images = array();
		foreach ($sizes as $size) {
			$images[$size] = wp_get_attachment_image_src($eventImageID, $size);
		}
		return $images;
	} else {
		return array();
	}
}



/**
 * Sanitize every time field.
 *
 * If the context is 'raw', then the time object or array will get minimal
 * sanitization of the integer fields.
 *
 * @since 1.0.0
 *
 * @see btb_sanitize_time_field($field, $value, $booking_id, $context = 'display')
 *
 * @param object|BTB_Time|array	$time		The time object or array.
 * @param string				$context	Optional. How to sanitize the time fields.
											Accepts 'raw', 'edit', 'db', 'attribute', 'js' or 'display'.
											Default 'display'.
 * @return object|BTB_Time|array The now sanitized BTB_Time object or array.
 */
function btb_sanitize_time($time, $context = 'display') {
	if (is_object($time)) {
		// Check if time already filtered for this context.
		if (isset($time->filter) && $context == $time->filter) {
			return $time;
		}
		if (!isset($time->ID)) {
			$time->ID = 0;
		}
		foreach(array_keys(get_object_vars($time)) as $field) {
			$time->$field = btb_sanitize_time_field($field, $time->$field, $time->ID, $context);
		}
		$time->filter = $context;
	} elseif (is_array($time)) {
		// Check if time already filtered for this context.
		if (isset($time['filter']) && $context == $time['filter']) {
			return $time;
		}
		if (!isset($time['ID'])) {
			$time['ID'] = 0;
		}
		foreach(array_keys($time) as $field) {
			$time[$field] = btb_sanitize_time_field($field, $time[$field], $time['ID'], $context);
		}
		$time['filter'] = $context;
	}
	return $time;
}


/**
 * Sanitize time field based on context.
 *
 * Possible context values are: 'raw', 'edit', 'db', 'display', 'attribute' and 'js'.
 * The 'display' context is used by default. 'attribute' and 'js' contexts are treated like 'display' when
 * calling filters.
 *
 * @todo Implement format_to_edit as in WordPress sanitize_post_field.
 *
 * @since 1.0.0
 *
 * @param string	$field		The Time Object field name.
 * @param mixed		$value		The Time Object value.
 * @param int 		$time_id	The Time ID.
 * @param string	$context	Optional. Ho to sanitize time fields. Looks for 'raw', 'edit',
								'db', 'display', 'attribute' and 'js'. Default 'display'.
 * @return mixed Sanitized value.
 */
function btb_sanitize_time_field($field, $value, $time_id, $context = 'display') {
	$int_fields = array('ID', 'start', 'end', 'slots', 'event', 'post_author');
	if (in_array($field, $int_fields)) {
		$value = (int) $value;
	}

	$float_fields = array('price');
	if (in_array($field, $float_fields)) {
		$value = (float) $value;
	}

	$bool_fields = array('date_only');
	if (in_array($field, $bool_fields)) {
		$value = intval($value) != 0;
	}

	if ('raw' == $context) {
		return $value;
	}

	if ('edit' == $context) {

		$value = apply_filters("edit_btb_time_${field}", $value, $time_id);

		$value = esc_attr($value);
	} elseif ( 'db' == $context) {
		$value = apply_filters("btb_time_{$field}_pre", $value);
	} else {
		// Use display filters by default.
		$value = apply_filters("btb_time_{$field}", $value, $time_id, $context);
	}

	if ('attribute' == $context) {
		$value = esc_attr($value);
	} elseif ('js' == $context) {
		$value = ecs_js($value);
	}


	return $value;
}


/**
 * @brief Retrieves time data given a time ID or time object.
 *
 * @see sanitize_time() for optional $filter values. Also, the parameter $time
 * must be given as a variable, since it is passed by reference.
 *
 * @param int|BTB_Time		$time Time ID or BTB_Time object.
 * @param string 			$output Optional, default is Object. Accepts OBJECT, ARRAY_A or ARRAY_N.
 * @param string			$filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db', 'display',
 *									'attribute' or 'js'. Default 'raw'.
 * @return BTB_Time|array|null	Type corresponding to $output on success or null on failure.
 *						      	When $output is OBJECT, a `BTB_Time` instance is returned.
 */
function btb_get_time($time, $output = OBJECT, $filter = 'raw') {
	if ($time instanceof BTB_Time) {
		$_time = $time;
	} elseif (is_object($time)) {
		if (empty($time->filter)) {
			$_time = btb_sanitize_time($time, 'raw');
			$_time = new BTB_Time($_time);
		} elseif ('raw' == $time->filter) {
			$_time = new BTB_Time($time);
		} else {
			$_time = BTB_Time::get_instance($time->ID);
		}
	} else {
		$_time = BTB_Time::get_instance($time);
	}

	if (!$_time) {
		return null;
	}

	$_time = $_time->filter($filter);

	if ($output == ARRAY_A) {
		return $_time->to_array();
	} elseif ($output == ARRAY_N) {
		return array_values($_time->to_array());
	}

	return $_time;
}



/**
 * @brief Retrieve list of times, either all or for specific event.
 *
 * @param int $event			BTB_Event ID.
 * @param string $filter		Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db', 'display',
 *								'attribute' or 'js'. Default 'raw'.
 * @param bool $upcoming_only	If true, only upcoming event times will be returned.
 * @return array of BTB_Event objects.
 */
function btb_get_times($event = 0, $filter = 'raw', $upcoming_only = false) {
	global $wpdb;

	$querystring = "
		SELECT ti.ID, ti.post_author, ti.post_date, ti.post_date_gmt, ti.post_title AS name, ti.post_status, ti.post_name,
			   ti.post_modified, ti.post_modified_gmt, ti.post_parent AS event, ti.guid, sl.meta_value AS slots, pr.meta_value AS price,
			   dato.meta_value AS date_only, st.meta_value AS start, et.meta_value AS end
		FROM $wpdb->posts ti
		LEFT JOIN $wpdb->postmeta sl
				  ON sl.post_id = ti.ID
				  AND sl.meta_key = 'btb_slots'
		LEFT JOIN $wpdb->postmeta pr
				  ON pr.post_id = ti.ID
				  AND pr.meta_key = 'btb_price'
		LEFT JOIN $wpdb->postmeta dato
				  ON dato.post_id = ti.ID
				  AND dato.meta_key = 'btb_date_only'
		LEFT JOIN $wpdb->postmeta st
				  ON st.post_id = ti.ID
				  AND st.meta_key = 'btb_start'
		LEFT JOIN $wpdb->postmeta et
				  ON et.post_id = ti.ID
				  AND et.meta_key = 'btb_end'
		WHERE ti.post_type = 'btb_time'
	";

	if ($event) {
		$querystring .= " AND ti.post_parent = %d";
	}

	if ($upcoming_only) {
		$querystring .= " AND st.meta_value > " . time();
	}

	$querystring .= " ORDER BY st.meta_value ASC;";

	if ($event) {
		$times = $wpdb->get_results($wpdb->prepare($querystring, $event));
	} else {
		$times = $wpdb->get_results($querystring);
	}

	$santimes = array();

	if ($times) {
		foreach($times as $time) {
			$santimes[] = btb_get_time($time, $filter);
		}
	}

	return $santimes;
}



/**
 * Returns the ID of the event, the given page is the description page for.
 *
 * @param object|int $desc_page Page object or page ID.
 * @return int ID of the event, 0 if nothing was found.
 */
function btb_get_event_id_by_desc_page($desc_page) {
	global $wpdb;

	if (is_object($desc_page)) {
		$desc_page = $desc_page->ID;
	}

	if (!$desc_page) {
		return 0;
	}

	$id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM wp_postmeta WHERE meta_value = %d AND meta_key = 'btb_desc_page'", $desc_page));

	return intval($id);
}




/**
 * Sanitize every event field.
 *
 * If the context is 'raw', then the event object or array will get minimal
 * sanitization of the integer fields.
 *
 * @since 1.0.0
 *
 * @see btb_sanitize_event_field($field, $value, $event_id, $context = 'display')
 *
 * @param object|BTB_Event|array	$event		The event object or array.
 * @param string					$context	Optional. How to sanitize the event fields.
												Accepts 'raw', 'edit', 'db', 'attribute', 'js' or 'display'.
												Default 'display'.
 * @return object|BTB_Event|array The now sanitized BTB_Event object or array.
 */
function btb_sanitize_event($event, $context = 'display') {
	if (is_object($event)) {
		// Check if event already filtered for this context.
		if (isset($event->filter) && $context == $event->filter) {
			return $event;
		}
		if (!isset($event->ID)) {
			$event->ID = 0;
		}
		foreach(array_keys(get_object_vars($event)) as $field) {
			$event->$field = btb_sanitize_event_field($field, $event->$field, $event->ID, $context);
		}
		$event->filter = $context;
	} elseif (is_array($event)) {
		// Check if event already filtered for this context.
		if (isset($event['filter']) && $context == $event['filter']) {
			return $event;
		}
		if (!isset($event['ID'])) {
			$event['ID'] = 0;
		}
		foreach(array_keys($event) as $field) {
			$event[$field] = btb_sanitize_event_field($field, $event[$field], $event['ID'], $context);
		}
		$event['filter'] = $context;
	}
	return $event;
}


/**
 * Sanitize event field based on context.
 *
 * Possible context values are: 'raw', 'edit', 'db', 'display', 'attribute' and 'js'.
 * The 'display' context is used by default. 'attribute' and 'js' contexts are treated like 'display' when
 * calling filters.
 *
 * @todo Implement format_to_edit as in WordPress sanitize_post_field.
 *
 * @since 1.0.0
 *
 * @param string	$field		The Event Object field name.
 * @param mixed		$value		The Event Object value.
 * @param int 		$event_id	The Event ID.
 * @param string	$context	Optional. Ho to sanitize event fields. Looks for 'raw', 'edit',
								'db', 'display', 'attribute' and 'js'. Default 'display'.
 * @return mixed Sanitized value.
 */
function btb_sanitize_event_field($field, $value, $event_id, $context = 'display') {
	$int_fields = array('ID', 'post_author', 'desc_page', 'venue');
	if (in_array($field, $int_fields)) {
		$value = (int) $value;
	}

	$float_fields = array('price');
	if (in_array($field, $float_fields)) {
		$value = (float) $value;
	}

	if ('raw' == $context) {
		return $value;
	}

	if ('edit' == $context) {

		$value = apply_filters("edit_btb_event_${field}", $value, $event_id);

		$value = esc_attr($value);
	} elseif ( 'db' == $context) {
		$value = apply_filters("btb_event_{$field}_pre", $value);
	} else {
		// Use display filters by default.
		$value = apply_filters("btb_event_{$field}", $value, $event_id, $context);
	}

	if ('attribute' == $context) {
		$value = esc_attr($value);
	} elseif ('js' == $context) {
		$value = ecs_js($value);
	}


	return $value;
}



/**
 * @brief Retrieves event data given an event ID or event object.
 *
 * @see sanitize_event() for optional $filter values. Also, the parameter $event
 * must be given as a variable, since it is passed by reference.
 *
 * @param int|BTB_Event		$event	Event ID or BTB_Event object.
 * @param string 			$output Optional, default is Object. Accepts OBJECT, ARRAY_A or ARRAY_N.
 * @param string			$filter Optional. Type fo filter to apply. Accepts 'raw', 'edit', 'db', 'display',
 *									'attribute' or 'js'. Default 'raw'.
 * @return BTB_Event|array|null	Type corresponding to $output on success or null on failure.
*						      	When $output is OBJECT, a `BTB_Event` instance is returned.
 */
function btb_get_event($event, $output = OBJECT, $filter = 'raw') {
	if ($event instanceof BTB_Event) {
		$_event = $event;
	} elseif (is_object($event)) {
		if (empty($event->filter)) {
			$_event = btb_sanitize_event($event, 'raw');
			$_event = new BTB_Event($_event);
		} elseif ('raw' == $event->filter) {
			$_event = new BTB_Event($event);
		} else {
			$_event = BTB_Event::get_instance($event->ID);
		}
	} else {
		$_event = BTB_Event::get_instance($event);
	}

	if (!$_event) {
		return null;
	}

	$_event = $_event->filter($filter);

	if ($output == ARRAY_A) {
		return $_event->to_array();
	} elseif ($output == ARRAY_N) {
		return array_values($_event->to_array());
	}

	return $_event;
}



/**
 * Sanitize every venue field.
 *
 * If the context is 'raw', then the venue object or array will get minimal
 * sanitization of the integer fields.
 *
 * @since 1.0.0
 *
 * @see btb_sanitize_venue_field($field, $value, $venue_id, $context = 'display')
 *
 * @param object|BTB_Event|array	$venue		The venue object or array.
 * @param string					$context	Optional. How to sanitize the venue fields.
												Accepts 'raw', 'edit', 'db', 'attribute', 'js' or 'display'.
												Default 'display'.
 * @return object|BTB_Event|array The now sanitized BTB_Event object or array.
 */
function btb_sanitize_venue($venue, $context = 'display') {
	if (is_object($venue)) {
		// Check if venue already filtered for this context.
		if (isset($venue->filter) && $context == $venue->filter) {
			return $venue;
		}
		if (!isset($venue->ID)) {
			$venue->ID = 0;
		}
		foreach(array_keys(get_object_vars($venue)) as $field) {
			$venue->$field = btb_sanitize_venue_field($field, $venue->$field, $venue->ID, $context);
		}
		$venue->filter = $context;
	} elseif (is_array($venue)) {
		// Check if venue already filtered for this context.
		if (isset($venue['filter']) && $context == $venue['filter']) {
			return $venue;
		}
		if (!isset($venue['ID'])) {
			$venue['ID'] = 0;
		}
		foreach(array_keys($venue) as $field) {
			$venue[$field] = btb_sanitize_venue_field($field, $venue[$field], $venue['ID'], $context);
		}
		$venue['filter'] = $context;
	}
	return $venue;
}


/**
 * Sanitize venue field based on context.
 *
 * Possible context values are: 'raw', 'edit', 'db', 'display', 'attribute' and 'js'.
 * The 'display' context is used by default. 'attribute' and 'js' contexts are treated like 'display' when
 * calling filters.
 *
 * @todo Implement format_to_edit as in WordPress sanitize_post_field.
 *
 * @since 1.0.0
 *
 * @param string	$field		The Event Object field name.
 * @param mixed		$value		The Event Object value.
 * @param int 		$venue_id	The Event ID.
 * @param string	$context	Optional. Ho to sanitize venue fields. Looks for 'raw', 'edit',
								'db', 'display', 'attribute' and 'js'. Default 'display'.
 * @return mixed Sanitized value.
 */
function btb_sanitize_venue_field($field, $value, $venue_id, $context = 'display') {
	$int_fields = array('ID', 'post_author');
	if (in_array($field, $int_fields)) {
		$value = (int) $value;
	}

	$float_fields = array('latitude', 'longitude');
	if (in_array($field, $float_fields)) {
		$value = (float) $value;
	}

	$bool_fields = array('use_map_coords');
	if (in_array($field, $bool_fields)) {
		$value = intval($value) != 0;
	}

	if ('raw' == $context) {
		return $value;
	}

	if ('edit' == $context) {

		$value = apply_filters("edit_btb_venue_${field}", $value, $venue_id);

		$value = esc_attr($value);
	} elseif ( 'db' == $context) {
		$value = apply_filters("btb_venue_{$field}_pre", $value);
	} else {
		// Use display filters by default.
		$value = apply_filters("btb_venue_{$field}", $value, $venue_id, $context);
	}

	if ('attribute' == $context) {
		$value = esc_attr($value);
	} elseif ('js' == $context) {
		$value = ecs_js($value);
	}


	return $value;
}


/**
 * @brief Retrieves venue data given an venue ID or venue object.
 *
 * @see btb_sanitize_venue() for optional $filter values. Also, the parameter $venue
 * must be given as a variable, since it is passed by reference.
 *
 * @param int|BTB_Venue		$venue	Venue ID or BTB_Venue object.
 * @param string 			$output Optional, default is Object. Accepts OBJECT, ARRAY_A or ARRAY_N.
 * @param string			$filter Optional. Type fo filter to apply. Accepts 'raw', 'edit', 'db', 'display',
 *									'attribute' or 'js'. Default 'raw'.
 * @return BTB_Venue|array|null	Type corresponding to $output on success or null on failure.
*						      	When $output is OBJECT, a `BTB_Venue` instance is returned.
 */
function btb_get_venue($venue, $output = OBJECT, $filter = 'raw') {
	if ($venue instanceof BTB_Venue) {
		$_venue = $venue;
	} elseif (is_object($venue)) {
		if (empty($venue->filter)) {
			$_venue = btb_sanitize_venue($venue, 'raw');
			$_venue = new BTB_Venue($_venue);
		} elseif ('raw' == $venue->filter) {
			$_venue = new BTB_Venue($venue);
		} else {
			$_venue = BTB_Venue::get_instance($venue->ID);
		}
	} else {
		$_venue = BTB_Venue::get_instance($venue);
	}

	if (!$_venue) {
		return null;
	}

	$_venue = $_venue->filter($filter);

	if ($output == ARRAY_A) {
		return $_venue->to_array();
	} elseif ($output == ARRAY_N) {
		return array_values($_venue->to_array());
	}

	return $_venue;
}



/**
 * @brief Returns a list of schema.org Event types.
 *
 * @param bool $sort If true, the list will be sorted key value.
 * @return array
 */
function btb_get_event_types($sort = true) {

	$types = array(
	    'Event' => __('General', 'bt-booking'),
		'BusinessEvent' => __('Business', 'bt-booking'),
		'ChildrensEvent' => __('Childrens', 'bt-booking'),
		'ComedyEvent' => __('Comedy', 'bt-booking'),
		'DanceEvent' => __('Dance', 'bt-booking'),
		'EducationEvent' => __('Education', 'bt-booking'),
		'ExhibitionEvent' => __('Exhibition', 'bt-booking'),
		'Festival' => __('Festival', 'bt-booking'),
		'FoodEvent' => __('Food', 'bt-booking'),
		'LiteraryEvent' => __('Literary', 'bt-booking'),
		'MusicEvent' => __('Music', 'bt-booking'),
		'PublicationEvent' => __('Publication', 'bt-booking'),
		'SaleEvent' => __('Sale', 'bt-booking'),
		'ScreeningEvent' => __('Screening', 'bt-booking'),
		'SocialEvent' => __('Social', 'bt-booking'),
		'SportsEvent' => __('Sports', 'bt-booking'),
		'TheaterEvent' => __('Theater', 'bt-booking')
	);

	if ($sort) {
		setlocale(LC_COLLATE, get_option('WPLANG') . '.utf8');
		asort($types);
	}

	return $types;
}


/**
 * @brief Returns the name of the specified \a $event_type.
 *
 * @param string $event_type Event type to return the name for.
 * @return string Event type name.
 */
function btb_get_event_type_name($event_type) {
	$types = btb_get_event_types(false);
	return $types[$event_type];
}


/**
 * @brief Returns a list of schema.org organization types.
 *
 * @param bool $sort If true, the list will be sorted by value.
 * @return array
 */
function btb_get_organization_types($sort = true) {
	$types = array(
		'Organization' => __('General', 'bt-booking'),
		'Corporation' => __('Corporation', 'bt-booking'),
		'EducationalOrganization' => __('Educational', 'bt-booking'),
		'GovernmentOrganization' => __('Government', 'bt-booking'),
		'LocalBusiness' => __('Local Business', 'bt-booking'),
		'NGO' => __('NGO', 'bt-booking'),
		'PerformingGroup' => __('Performing Group', 'bt-booking'),
		'SportsOrganization' => __('Sports Organization', 'bt-booking')
	);

	if ($sort) {
		setlocale(LC_COLLATE, get_option('WPLANG') . '.utf8');
		asort($types);
	}

	return $types;
}

/**
 * @brief Returns the name of the specified \a $orga_type.
 *
 * @param string $orga_type Organization type to return the name for.
 * @return string Organization type name.
 */
function btb_get_organization_type_name($orga_type) {
	$types = btb_get_organization_types(false);
	return $types[$orga_type];
}

/** @} */ // end of globalfns