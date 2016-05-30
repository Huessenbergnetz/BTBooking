<?php
/**
 * @file
 * @brief Implements the BTB_Time class.
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

/**
 * Core class used to implement the BTB_Time object.
 *
 * @since 1.0.0
 */
class BTB_Time {

	/**
	 * Time ID.
	 *
	 * @var int $ID
	 */
	public $ID;

	/**
	 * Time name.
	 *
	 * This is normally a combination of start and end date.
	 *
	 * @var string $name
	 */
	public $name = '';

	/**
	 * Start time and date as unix time stamp.
	 *
	 * @var int $start
	 */
	public $start = 0;

	/**
	 * End time and date as unix time stamp.
	 *
	 * @var int $end
	 */
	public $end = 0;

	/**
	 * Amount of slots for this time.
	 *
	 * @var int $slots
	 */
	public $slots = 0;

	/**
	 * The price for this time.
	 *
	 * @var float $price
	 */
	public $price = 0.0;

	/**
	 * Should only the date be valid, not the time.
	 *
	 * @var bool $date_only
	 */
	public $date_only = false;

	/**
	 * ID of the event this time belongs to.
	 *
	 * @var int $event
	 */
	public $event = 0;

	/**
	 * The post type. Should be btb_time.
	 *
	 * @var string $post_type
	 */
	public $post_type = 'btb_time';

	/**
	 * The status is publish by default.
	 *
	 * @var string $post_status
	 */
	public $post_status = 'publish';

	/**
	 * Stores the time object's sanitization level.
	 *
	 * @var string $filter
	 */
	public $filter;

	/**
	 * Defines the creation time of the booking.
	 *
	 * @var string $post_date
	 */
	public $post_date = '';

	/**
	 * Defines the creation time of the booking at GMT.
	 *
	 * @var string $post_date_gmt
	 */
	public $post_date_gmt = '';

	/**
	 * Sanitized version of the post_title/booking code.
	 *
	 * @var string $post_name
	 */
	public $post_name = '';

	/**
	 * Last booking modification time.
	 *
	 * @var string $post_modified
	 */
	public $post_modified = '';

	/**
	 * Last booking modificatin time at GMT.
	 *
	 * @var string $post_modified_gmt
	 */
	public $post_modified_gmt = '';

	/**
	 * Global Uniquie ID for referencing the booking.
	 *
	 * @var string $guid
	 */
	public $guid = '';

	/**
	 * ID of the user that created this time.
	 *
	 * @var int $post_author
	 */
	public $post_author = 0;

	/**
	 * Number of free slots for this time.
	 *
	 * @var int $free_slots
	 */
	public $free_slots = 0;

	/**
	 * Number of booked slots for this time.
	 *
	 * @var int $booked_slots
	 */
	public $booked_slots = 0;

	/**
	 * Number of pre booked slots for this time.
	 *
	 * @var int $prebooked_slots
	 */
	public $prebooked_slots = 0;

	/**
	 * Retrieve BTB_Time instance.
	 *
	 * @param int $time_id Time ID.
	 * @return BTB_Time|false BTB_Time object, false otherwise.
	 */
	public static function get_instance($time_id) {
		global $wpdb;
		$time_id = (int) $time_id;

		if (!$time_id) {
			return false;
		}

		$_time = wp_cache_get($time_id, 'btb_times');

		if (!$_time) {

			$_time = $wpdb->get_row($wpdb->prepare(
				"
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
				WHERE ti.ID = %d AND ti.post_type = 'btb_time' LIMIT 1;
				",
				$time_id
			));

			if (!$_time) {
				return false;
			}

			$_time = btb_sanitize_time($_time, 'raw');
			wp_cache_add($_time->ID, $_time, 'btb_times');

		} elseif (empty($_time->filter)) {
			$_time = btb_sanitize_time($_time, 'raw');
		}

		return new BTB_Time($_time);

	}


	/**
	 * Constructor
	 *
	 * @param BTB_Time|object $time Time object.
	 */
	public function __construct($time = null) {
		if ($time) {
			foreach(get_object_vars($time) as $key => $value) {
				$this->$key = $value;
			}
		}
	}


	/**
	 * Isset-er.
	 *
	 * @param string $key Property to check if set.
	 * @return bool
	 */
	public function __isset($key) {
		if ('ancestors' == $key) {
			return true;
		}

		if ('page_template' == $key) {
			return false;
		}

		if ('post_category' == $key) {
			return true;
		}

		if ('tags_input' == $key) {
			return true;
		}

		if ('post_title' == $key) {
			return isset($this->name);
		}

		if ('post_parent' == $key) {
			return true;
		}

		return metadata_exists('post', $this->ID, $key);
	}


	/**
	 * Getter
	 *
	 * @param string $key Key to get.
	 * @return mixed
	 */
	public function __get($key) {
		if ('page_template' == $key && $this->__isset($key)) {
			return get_post_meta( $this->ID, '_wp_page_template', true );
		}

		if ('post_category' == $key) {
			return array();
		}

		if ('tags_input' == $key) {
			return array();
		}

		if ('ancestors' == $key) {
			return array();
		}

		if ('post_title' == $key) {
			return $this->name;
		}

		if ('post_parent' == $key) {
			return $this->event;
		}

		$value = get_post_meta($this->ID, $key, true);

		if ($this->filter) {
			$value = btb_sanitize_time_field($key, $value, $this->ID, $this->filter);
		}

		return $value;
	}


	/**
	 * Returns a filtered version of this time.
	 *
	 * @param string $filter The filter context.
	 * @return self|array|bool|object|BTB_Booking
	 */
	public function filter($filter) {
		if ($this->filter == $filter) {
			return $this;
		}

		if ($filter == 'raw') {
			return self::get_instance($this->ID);
		}

		return btb_sanitize_time($this, $filter);
	}



	/**
	 * @brief Convert object to array.
	 *
	 * @param bool $wp_post_array If true, the array will be compatible to the WP_Post object/array.
	 * @return array Object as Array.
	 */
	public function to_array($wp_post_array = false) {

		if ($wp_post_array) {
			return array(
				'ID' => $this->ID,
				'post_author' => $this->post_author,
				'post_date' => $this->post_date,
				'post_date_gmt' => $this->post_date_gmt,
				'post_content' => '',
				'post_content_filtered' => '',
				'post_title' => $this->name,
				'post_excerpt' => '',
				'post_status' => $this->post_status,
				'post_type' => 'btb_time',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_password' => '',
				'post_name' => $this->post_name,
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => $this->post_modified,
				'post_modified_gmt' => $this->post_modified_gmt,
				'post_parent' => $this->booked_time,
				'menu_order' => 0,
				'post_mime_type' => '',
				'guid' => $this->guid,
				'tax_input' => array(),
				'meta_input' => array(
					'btb_start' => $this->start,
					'btb_end' => $this->end,
					'btb_slots' => $this->slots,
					'btb_price' => $this->price,
					'btb_date_only' => $this->date_only
				)
			);
		}

		return get_object_vars($this);
	}


	/**
	 * @brief Calculates the values for the booked, prebooked and free slots.
	 */
	public function calc_slots() {
		$this->prebooked_slots = btb_get_time_prebooked_slots($this->ID);
		$this->booked_slots = btb_get_time_booked_slots($this->ID);
		$this->free_slots = ($this->slots - $this->prebooked_slots - $this->booked_slots);
	}
	
	
	
	/**
	 * @brief Loads the data for a single time from a API response.
	 * 
	 * @param object $reponse JSON object from the API response.
	 */
	public function from_api_response($response) {
	
		if (is_object($response)) {
		
			$this->ID = $response->id;
			$this->post_date = $response->date;
			$this->post_date_gmt = $response->date_gmt;
			$this->guid = $response->guid->rendered;
			$this->post_modified = $response->modified;
			$this->post_modified_gmt = $response->modified_gmt;
			$this->post_name = $response->slug;
			$this->name = $response->title->rendered;
			$this->price = (float)$response->btb_price;
			$this->event = (int)$response->btb_event_id;
			$this->start = (int)$response->btb_start;
			$this->end = (int)$response->btb_end;
			$this->date_only = ($response->btb_date_only == "1");
			$this->free_slots = $response->btb_free_slots;
		}	
	}
}