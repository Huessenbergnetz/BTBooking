<?php
/**
 * @file
 * @brief Implements the BTB_Booking class.
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
 * Core class used to implement the BTB_Booking object.
 *
 * Information about attributes are stored in the default fields of the @a wp_posts tables
 * as well as in the @a wp_postmeta table. You can find information about each storage
 * place in the attribute description.
 *
 * @todo Implement seller notes as post_content value.
 *
 * @since 1.0.0
 */
class BTB_Booking {

	/**
	 * Booking ID.
	 *
	 * @postsdb{ID}
	 *
	 * @var int $ID
	 */
	public $ID;

	/**
	 * Booking code.
	 *
	 * @postsdb{post_title}
	 *
	 * @var string $code
	 */
	public $code = '';

	/**
	 * ID of the booked event.
	 *
	 * @par Database field
	 * post_parent of @c wp_posts » post_parent, in other words, the parent of the booked time.
	 *
	 * @var int $booked_event
	 */
	public $booked_event = 0;

	/**
	 * ID of the booked event time/date.
	 *
	 * @postsdb{post_parent}
	 *
	 * @var int $booked_time
	 */
	public $booked_time = 0;

	/**
	 * Number of booked slots.
	 *
	 * @metadb{btb_slots}
	 *
	 * @var int $booked_slots
	 */
	public $booked_slots = 0;

	/**
	 * Booking time unix timestamp.
	 *
	 * The time when the user has successfully finished the booking process.
	 *
	 * @metadb{btb_booking_time}
	 *
	 * @var int $booking_time
	 */
	public $booking_time = 0;

	/**
	 * Booking status.
	 *
	 * The status of the booking. Can be
	 * - btb_booked
	 * - btb_prebook
	 * - btb_canceled
	 *
	 * @postsdb{post_status}
	 *
	 * @var string $booking_status
	 */
	public $booking_status = 'btb_prebook';

	/**
	 * Booked single price.
	 *
	 * To get the total price, multiply it with $booked_slots.
	 *
	 * @metadb{btb_price}
	 *
	 * @var float $price
	 */
	public $price = 0.0;

	/**
	 * Title of the ordering person.
	 *
	 * Valid values are \a mr and \a mrs.
	 *
	 * @metadb{btb_title}
	 *
	 * @var string $title
	 */
	public $title = 'mr';

	/**
	 * First name of the ordering person.
	 *
	 * @metadb{btb_first_name}
	 *
	 * @var string $first_name
	 */
	public $first_name = '';

	/**
	 * Last name of the ordering person.
	 *
	 * @metadb{btb_last_name}
	 *
	 * @var string $last_name
	 */
	public $last_name = '';

	/**
	 * Name of the ordering person's company.
	 *
	 * @metadb{btb_company}
	 *
	 * @var string $company
	 */
	public $company = '';

	/**
	 * Street and house number of the ordering person's address.
	 *
	 * @metadb{btb_address}
	 *
	 * @var string $address
	 */
	public $address = '';

	/**
	 * Additional parts of the ordering person's address.
	 *
	 * @metadb{btb_address}
	 *
	 * @var string $address2
	 */
	public $address2 = '';

	/**
	 * Postal code of the ordering person's address.
	 *
	 * @metadb{btb_address}
	 *
	 * @var string $zip
	 */
	public $zip = '';

	/**
	 * City of the ordering person's address.
	 *
	 * @metadb{btb_address}
	 *
	 * @var string $city
	 */
	public $city = '';

	/**
	 * ISO 3166-1 alpha2 two letter country code of the ordering person's country
	 *
	 * @metadb{btb_address}
	 *
	 * @var string $country
	 */
	public $country = '';

	/**
	 * E-mail address of the ordering person.
	 *
	 * @metadb{btb_mail}
	 *
	 * @var string $email
	 */
	public $email = '';

	/**
	 * Phone number of the ordering person.
	 *
	 * @metadb{btb_phone}
	 *
	 * @var string $phone
	 */
	public $phone = '';

	/**
	 * Notes added by the ordering person.
	 *
	 * @metadb{btb_notes}
	 *
	 * @var string $notes
	 */
	public $notes = '';

	/**
	 * Notes added by the seller.
	 *
	 * @postsdb{post_excerpt}
	 *
	 * @var string $seller_notes
	 */
	public $seller_notes = '';

	/**
	 * Stores the booking object's sanitization level.
	 *
	 * @var string $filter
	 */
	public $filter;

	/**
	 * Defines the creation time of the booking.
	 *
	 * @postsdb{post_date}
	 *
	 * @var string $post_date
	 */
	public $post_date = '';

	/**
	 * Defines the creation time of the booking at GMT.
	 *
	 * @postsdb{post_date_gmt}
	 *
	 * @var string $post_date_gmt
	 */
	public $post_date_gmt = '';

	/**
	 * Sanitized version of the post_title/booking code.
	 *
	 * @postsdb{post_name}
	 *
	 * @var string $post_name
	 */
	public $post_name = '';

	/**
	 * Last booking modification time.
	 *
	 * @postsdb{post_modified}
	 *
	 * @var string $post_modified
	 */
	public $post_modified = '';

	/**
	 * Last booking modificatin time at GMT.
	 *
	 * @postsdb{post_modified_gmt}
	 *
	 * @var string $post_modified_gmt
	 */
	public $post_modified_gmt = '';

	/**
	 * Global Uniquie ID for referencing the booking.
	 *
	 * @postsdb{guid}
	 *
	 * @var string $guid
	 */
	public $guid = '';

	/**
	 * @brief Retrieve BTB_Booking instance.
	 *
	 * @param int $booking_id Booking ID.
	 * @return BTB_Booking|false Booking object, false otherwise.
	 */
	public static function get_instance($booking_id) {
		global $wpdb;
		$booking_id = (int) $booking_id;

		if (!$booking_id) {
			return false;
		}

		$_booking = wp_cache_get($booking_id, 'btb_bookings');

		if (!$_booking) {
			$_booking = $wpdb->get_row($wpdb->prepare(
				"
				SELECT bo.ID, bo.post_status AS booking_status, bo.post_title AS code, bo.post_parent AS booked_time, btt.post_parent AS booked_event,
					slots.meta_value AS booked_slots, company.meta_value AS company, title.meta_value AS title, fn.meta_value AS first_name,
					ln.meta_value AS last_name, email.meta_value AS email, phone.meta_value AS phone, notes.meta_value AS notes, bt.meta_value AS booking_time,
					address.meta_value AS addressArray, pr.meta_value AS price, bo.post_date, bo.post_date_gmt, bo.post_excerpt AS seller_notes, bo.post_name,
					bo.post_modified, bo.post_modified_gmt, bo.guid
				FROM $wpdb->posts bo
				LEFT JOIN $wpdb->posts btt
						ON btt.ID = bo.post_parent
				LEFT JOIN $wpdb->postmeta slots
						ON slots.post_id = bo.ID
						AND slots.meta_key = 'btb_slots'
				LEFT JOIN $wpdb->postmeta company
						ON company.post_id = bo.ID
						AND company.meta_key = 'btb_company'
				LEFT JOIN $wpdb->postmeta title
						ON title.post_id = bo.ID
						AND title.meta_key = 'btb_title'
				LEFT JOIN $wpdb->postmeta fn
						ON fn.post_id = bo.ID
						AND fn.meta_key = 'btb_first_name'
				LEFT JOIN $wpdb->postmeta ln
						ON ln.post_id = bo.ID
						AND ln.meta_key = 'btb_last_name'
				LEFT JOIN $wpdb->postmeta email
						ON email.post_id = bo.ID
						AND email.meta_key = 'btb_mail'
				LEFT JOIN $wpdb->postmeta phone
						ON phone.post_id = bo.ID
						AND phone.meta_key = 'btb_phone'
				LEFT JOIN $wpdb->postmeta notes
						ON notes.post_id = bo.ID
						AND notes.meta_key = 'btb_notes'
				LEFT JOIN $wpdb->postmeta bt
						ON bt.post_id = bo.ID
						AND bt.meta_key = 'btb_booking_time'
				LEFT JOIN $wpdb->postmeta address
						ON address.post_id = bo.ID
						AND address.meta_Key = 'btb_address'
				LEFT JOIN $wpdb->postmeta pr
						ON pr.post_id = bo.ID
						AND pr.meta_key = 'btb_price'
				WHERE bo.ID = %d;
				",
				$booking_id
			));

			if (!$_booking) {
				return false;
			}

			$addressArray = maybe_unserialize($_booking->addressArray);
			unset($_booking->addressArray);

			if (is_array($addressArray)) {
				foreach($addressArray as $key => $value) {

					$_booking->$key = $value;
				}
			}

			$_booking = btb_sanitize_booking($_booking, 'raw');
			wp_cache_add($_booking->ID, $_booking, 'btb_bookings');
		} elseif (empty($_booking->filter)) {
			$_booking = btb_sanitize_booking($_booking, 'raw');
		}

		return new BTB_Booking($_booking);
	}


	/**
	 * Constructor
	 *
	 * @param BTB_Booking|object $booking Booking object.
	 */
	public function __construct($booking = null) {
		if ($booking) {
			foreach(get_object_vars($booking) as $key => $value) {
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

		if ('post_status' == $key) {
			return true;
		}

		if ('post_title' == $key) {
			return isset($this->code);
		}

		if ('post_type' == $key) {
			return true;
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

		if ('post_status' == $key) {
			return $this->booking_status;
		}

		if ('post_title' == $key) {
			return $this->code;
		}

		if ('post_type' == $key) {
			return 'btb_booking';
		}

		if ('post_parent' == $key) {
			return $this->booked_time;
		}

		$value = get_post_meta($this->ID, $key, true);

		if ($this->filter) {
			$value = btb_sanitize_booking_field($key, $value, $this->ID, $this->filter);
		}

		return $value;
	}


	/**
	 * Returns a filtered version of this booking.
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

		return btb_sanitize_booking($this, $filter);
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
				'post_author' => 0,
				'post_date' => $this->post_date,
				'post_date_gmt' => $this->post_date_gmt,
				'post_content' => '',
				'post_content_filtered' => '',
				'post_title' => $this->code,
				'post_excerpt' => $this->seller_notes,
				'post_status' => $this->booking_status,
				'post_type' => 'btb_booking',
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
					'btb_slots' => $this->booked_slots,
					'btb_booking_time' => $this->booking_time,
					'btb_price' => $this->price,
					'btb_title' => $this->title,
					'btb_first_name' => $this->first_name,
					'btb_last_name' => $this->last_name,
					'btb_company' => $this->company,
					'btb_address' => array(
						'address' => $this->address,
						'address2' => $this->address2,
						'zip' => $this->zip,
						'city' => $this->city,
						'country' => $this->country
					),
					'btb_mail' => $this->email,
					'btb_phone' => $this->phone,
					'btb_notes' => $this->notes
				)
			);
		}

		return get_object_vars($this);
	}
	
	
	public function to_api_array() {
		
		return array(
			'status' => $this->booking_status,
			'title' => $this->code,
			'btb_slots' => $this->booked_slots,
			'btb_time_id' => $this->booked_time,
			'btb_title' => $this->title,
			'btb_first_name' => $this->first_name,
			'btb_last_name' => $this->last_name,
			'btb_company' => $this->company,
			'btb_address' => array(
				'address' => $this->address,
				'address2' => $this->address2,
				'zip' => $this->zip,
				'city' => $this->city,
				'country' => $this->country
			),
			'btb_mail' => $this->email,
			'btb_phone' => $this->phone,
			'btb_notes' => $this->notes
		);
		
	}
	
	
	/**
	 * @brief Loads the data for a single event from a API response.
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
			$this->code = $response->btb_booking_number;
			$this->price = $response->btb_price;
			$this->booked_event = $response->btb_event_id;
			$this->booked_time = $response->btb_time_id;
			$this->booked_slots = $response->btb_slots;
			$this->booking_time = $response->btb_booking_time;
			$this->booking_status = $response->status;
		}	
	}
}