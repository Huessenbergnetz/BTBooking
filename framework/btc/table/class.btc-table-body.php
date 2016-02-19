<?php

require_once(__DIR__.'/../btc-functions.php');

class BTCTableBody {

	public $id = '';

	public $htmlClass = '';

	public $content = array();

	public function __construct(array $attrs = array(), array $content = array()) {

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

		$ret = '<tbody';

		btc_gen_attr($ret, 'id', $this->id);

		$ret .= '>';

		if (!empty($this->content)) {
			foreach($this->content as $key => $object) {
				$ret .= $object->render(false);
			}
		}

		$ret .= '</tbody>';

		return $ret;
	}
}

?>