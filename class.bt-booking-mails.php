<?php
/**
 * @file
 * @brief Implements the BTBoking_Mails class.
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
 * Core class used to implement the BTBooking_Mails object.
 *
 * This class is used to send confirmation mails to customers and notification mails to site owner when
 * a new booking has been created.
 */
class BTBooking_Mails {

	/**
	 * Sender e-mail address.
	 *
	 * @var string $sender
	 */
	public $sender;

	/**
	 * Receiver e-mail address.
	 *
	 * @var string $recipient
	 */
	public $recipient;

	/**
	 * Subject of the e-mail.
	 *
	 * @var string $subject
	 */
	public $subject;

	/**
	 * Mail header.
	 *
	 * @var array $header
	 */
	public $header = array();

	/**
	 * E-mail body.
	 *
	 * @var string $body
	 */
	public $body;

	/**
	 * Reply to address.
	 *
	 * @var string $replyto
	 */
	public $replyto;

	/**
	 * HTML e-mail.
	 *
	 * Set to true if you want to sent the e-mail in HTML format.
	 */
	public $html = false;

	/**
	 * Constructor
	 *
	 * @param array $params Array of parameters:
	 * - @c recipient The e-mail address of the recipient.
	 * - @c sender The e-mail address of the sender.
	 * - @c subject The subject of the e-mail.
	 * - @c header The header of the e-mail.
	 * - @c body The body of the e-mail.
	 * - @c html True for HTML e-mail.
	 */
	public function __construct(array $params = array()) {
		if (!empty($params)) {
			foreach($params as $key => $value) {

				$this->$key = $value;
			}
		}
	}


	/**
	 * Sets the e-mail header on $header.
	 *
	 * You have to specify $sender to let this work.
	 */
	public function create_header() {
		if (empty($this->sender)) {
			$this->header = array();
		} else {
			$contenttype = $this->html ? 'Content-Type: text/html; charset=UTF-8' : 'Content-Type: text/plain; charset=UTF-8';
			$header = array(
				$contenttype,
				'Content-Transfer-Encoding: 8bit',
				'From: ' . get_option('blogname') . ' <' . $this->sender . '>',
			);

			if ($this->replyto) {
				$header[] = 'Reply-To: ' . $this->replyto;
			}

			$this->header = $header;
		}
	}

	/**
	 * Send the mail if all needed information is available.
	 *
	 * Needs a body, a sender, a subject and recipient.
	 *
	 * @return bool True if successfully sent.
	 */
	public function send() {
		if ($this->body && $this->sender && $this->recipient && $this->subject) {
			$this->create_header();
			return wp_mail($this->recipient, $this->subject, $this->body, $this->header);
		} else {
			return false;
		}
	}
}