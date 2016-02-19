<?php

class BTCFormSelect {

	public $id = '';

	public $name = '';

	public $options = array();

	public $value = null;

	public $htmlClass = '';

	public $style = '';

	public $aria = array();

	public $data = array();

	public $disabled = false;

	public $required = false;

	public $multiple = false;

	public function __construct(array $options = array(), array $attrs = array()) {

		$this->options = $options;
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

		$_name = !empty($this->name) ? $this->name : $this->id;

		$ret = '<select';

		if ($this->id) $ret .= ' id="' . $this->id . '"';
		if ($_name) $ret .= ' name="' . $_name . '"';
		if ($this->htmlClass) $ret .= ' class="' .$this->htmlClass . '"';
		if ($this->style) $ret .= ' style="' .$this->style . '"';

		if (!empty($this->aria)) {
			foreach($this->aria as $tag => $value) {

				$ret .= sprintf(' aria-%s="%s"', $tag, $value);

			}
		}

		if (!empty($this->data)) {
			foreach($this->data as $tag => $value) {

				$ret .= sprintf(' data-%s="%s"', $tag, $value);

			}
		}

		if ($this->disabled) {
			$ret .= ' disabled';
		}

		if ($this->required) {
			$ret .= ' required';
		}

		if ($this->multiple) {
			$ret .= ' multiple';
		}

		$ret .= '>';

		if (!empty($this->options)) {

			foreach($this->options as $value => $text) {

				if (is_scalar($text)) {

					$ret .= '<option value="' . $value . '"';

					if ($this->multiple) {
						if (in_array($value, $this->value)) $ret .= ' selected';
					} else {
						if ($value == $this->value) $ret .= ' selected';
					}

					$ret .= '>' . $text . '</option>' ;

				} else if (is_object($text)) {

				}

			}

		}

		$ret .= '</select>';

		return $ret;
	}
}

?>