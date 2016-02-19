<?php

require_once(__DIR__.'/../btc-functions.php');

class BTCTable {

	public $body;

	public $head;

	public $htmlClass = '';

	public $id = '';

	public $style = null;

	public function __construct(array $attrs = array(), BTCTableBody $body = null, BTCTableHead $head = null) {

		$this->body = $body;
		$this->head = $head;
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

		$ret = '<table';

		if ($this->id) $ret .= ' id ="' . $this->id . '"';

		if ($this->htmlClass) $ret .= ' class="' . $this->htmlClass . '"';

		btc_gen_attr($ret, 'style', $this->style);

		$ret .= '>';

		if ($this->head) {
			$ret .= $this->head->render(false);
		}

		if ($this->body) {
			$ret .= $this->body->render(false);
		}

		$ret .= '</table>';

		return $ret;
	}
}


?>