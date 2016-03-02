<?php

require_once 'class.btc-input-basic.php';


class BTCInputNumber extends BTCHtmlInput {

	protected $type = 'number';

	public $max = null;

	public $min = null;

	public $step = 0;


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

	protected  function _render() {

		parent::_render();

		$this->add_attr('min', $this->min);
		$this->add_attr('max', $this->max);
		$this->add_attr('step', $this->step);

		$this->closeTag(false);
	}
}

?>