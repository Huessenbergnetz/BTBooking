<?php

class BTCTableHead {

	public $id = '';

	public $htmlClass = '';

	public $content = null;

	public function __construct(array $attrs = array(), BTCTableRow $content = null) {

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

		if (!$this->content) {
			return "";
		}

		$ret = '<thead';

		if (!empty($id)) $ret .= ' id="' . $this->id . '"';

		if (!empty($htmlClass)) $ret .= ' class="' . $this->htmlClass . '"';

		$ret .= '>';

		$ret .= $this->content->render(false);

// 		if (!empty($this->content)) {
// 			foreach($this->content as $key => $object) {
// 				$ret .= $object->render(false);
// 			}
// 		}

		$ret .= '</thead>';

		return $ret;
	}
}

?>