<?php

require_once(__DIR__.'/../btc-functions.php');

class BTCTableData {

	public $content;

	public $header = false;

	public $scope = '';

	public $colspan = -1;

	public $style = null;

	public function __construct($content = null, array $attrs = array(), $header = false) {

		$this->content = $content;

		$this->header = $header;

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

		if ($this->header) {
			$ret = '<th';
		} else {
			$ret = '<td';
		}

		if (!empty($this->scope)) $ret .= ' scope="' . $this->scope . '"';
		if ($this->colspan > 1) $ret .= ' colspan="' . $this->colspan . '"';
		btc_gen_attr($ret, 'style', $this->style);

		$ret .= '>';

		if (!empty($this->content)) {
			if (is_array($this->content)) {
				foreach($this->content as $key => $object) {
					if (is_scalar($object)) {
						$ret .= $object;
					} else if (is_object($object)) {
						$ret .= $object->render(false);
					}
				}
			} else if (is_scalar($this->content)) {
				$ret .= $this->content;
			} else if (is_object($this->content)) {
				$ret .= $this->content->render(false);
			}
		}

		if ($this->header) {
			$ret .= '</th>';
		} else {
			$ret .= '</td>';
		}

		return $ret;
	}
}

?>