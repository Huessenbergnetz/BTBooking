<?php

require_once(__DIR__.'/../btc-functions.php');

class BTCTableRow {

	public $id = '';

	public $content = array();

	public function __construct(array $content = array(), array $attrs = array()) {

		$this->content = $content;
		if (!empty($attrs)) {
		foreach($attrs as $key => $value) {

					$this->$key = $value;

				}
				}
	}

	public function render($echo = true) {

		if ($echo) {
			echo $this->_render();
		} else {
			return $this->_render();
		}

	}

	private function _render() {

		$ret = '<tr';

		btc_gen_attr($ret, 'id', $this->id);

		$ret .= '>';

		if (!empty($this->content)) {
			foreach($this->content as $key => $object) {
				$ret .= $object->render(false);
			}
		}

		$ret .= '</tr>';

		return $ret;
	}

	public function add_content($additional) {

		if (is_array($additional)) {

			if (!empty($additional)) {

				foreach ($additional as $add) {

					$this->content[] = $add;

				}

			}

		} else if (is_object($additional)) {

			$this->content[] = $additional;

		}
	}
}

?>