<?php

require_once 'class.btc-input-basic.php';

class BTCInputCheckbox extends BTCHtmlInput {

	protected $type = 'checkbox';

	/**
	 * Checked attribute.
	 *
	 * @var bool
	 */
	public $checked = false;


	public function __construct(array $attrs = array()) {

		if (is_array($attrs)) {

			if (!empty($attrs)) {

				foreach($attrs as $key => $value) {

					$this->$key = $value;

				}
			}

		} else {

			throw new Exception('Wrong input type');

		}

	}

	protected function _render() {

		parent::_render();

		$this->add_attr('checked', $this->checked);

		$this->closeTag(false);
	}

}

?>