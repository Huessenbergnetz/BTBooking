<?php

require_once(__DIR__.'/../btc-functions.php');

class BTCFormLabel {

	public $for = '';

	public $htmlClass = '';

	public $form = '';

	public $content = '';

	public $contentObject;

	public $style = null;

	public $lineBreak = false;

	public function __construct(array $attrs = array(), $content = '', $contentObject = null) {

		$this->content = $content;
		$this->contentObject = $contentObject;
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

		$_for = '';

		if (empty($_for) && $this->contentObject) {
			$_for = $this->contentObject->id;
		}

		$ret = '<label';

		if (!empty($this->htmlClass)) $ret .= ' class="' . $this->htmlclass . '"';
		if (!empty($this->form)) $ret .= ' form="' . $this->form . '"';
		if (!empty($_for)) $ret .= ' for="' . $_for . '"';
		btc_gen_style_attr($ret, $this->style);

		$ret .= '>';

		if ($this->contentObject) {
			$ret .= $this->contentObject->render(false);
			$ret .= ' ';
		}

		if (!empty($this->content)) {

			if (is_scalar($this->content)) {
				$ret .= $this->content;
			}
		}

		$ret .= '</label>';

		if ($this->lineBreak) {
			$ret .= '<br>';
		}

		return $ret;
	}
}

?>