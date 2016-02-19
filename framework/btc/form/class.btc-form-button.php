<?php

require_once(__DIR__.'/../btc-functions.php');

class BTCFormButton {

	public $id = '';

	public $name = '';

	public $value = '';

	public $htmlClass = '';

	public $type = 'submit';

	public $content = null;

	public $form = '';

	public $formaction = '';

	public $formmethod = '';

	public $formenctype = '';

	public $formnovalidate = false;

	public $style = null;

	public function __construct(array $attrs = array(), $content =  null) {

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

		$ret = '<button';

		btc_gen_attr($ret, 'id', $this->id);
		btc_gen_attr($ret, 'name', $this->name);
		btc_gen_attr($ret, 'type', $this->type);
		btc_gen_attr($ret, 'value', $this->value);
		btc_gen_attr($ret, 'class', $this->htmlClass);
		btc_gen_attr($ret, 'form', $this->form);
		btc_gen_attr($ret, 'formaction', $this->formaction);
		btc_gen_attr($ret, 'formmethod', $this->formmethod);
		btc_gen_attr($ret, 'formenctype', $this->formenctype);
		btc_gen_attr($ret, 'formnovalidate', $this->formnovalidate);
		btc_gen_attr($ret, 'style', $this->style);
// 		if (!empty($this->type)) $ret .= ' type="' . $this->type . '"';
// 		if (!empty($this->htmlClass)) $ret .= ' class="' . $this->htmlClass . '"';
// 		if (!empty($this->form)) $ret .= ' form="' . $this->form . '"';
// 		if (!empty($this->formaction)) $ret .= ' formaction="' . $this->formaction . '"';

		$ret .= '>';

		if ($this->content) {
			if (is_object($this->content)) {
				$ret .= $this->content->render(false);
			} elseif (is_scalar($this->content)) {
				$ret .= $this->content;
			}

		}

		$ret .= '</button>';

		return $ret;
	}
}

?>