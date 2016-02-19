<?php

class BTCFormTextarea {

	public $id = '';

	public $name = '';

	public $value = '';

	public $htmlClass = '';

	public $style = '';

	public $readonly = false;

	public $minlength = 0;

	public $maxlength = 0;

	public $required = false;

	public $pattern = '';

	public $cols = 0;

	public $rows = 0;

	public $aria = array();

	public $data = array();

	public function __construct(array $attrs = array(), $value = '') {

		$this->value = $value;
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

		$ret = '<textarea';

		if ($this->id)				$ret .= ' id="' . $this->id . '"';
		if ($_name)					$ret .= ' name="' . $_name . '"';
		if ($this->htmlClass)		$ret .= ' class="' .$this->htmlClass . '"';
		if ($this->style)			$ret .= ' style="' .$this->style . '"';
		if ($this->readonly)		$ret .= ' readonly="readonly"';
		if ($this->required)		$ret .= ' required="required"';
		if ($this->minlength)		$ret .= ' minlength="' . $this->minlength . '"';
		if ($this->maxlength)		$ret .= ' maxlength="' . $this->maxlength . '"';
		if (!empty($this->pattern))	$ret .= ' pattern="' . $this->pattern . '"';
		if ($this->cols)			$ret .= ' cols="' . $this->cols . '"';
		if ($this->rows)			$ret .= ' rows="' . $this->rows . '"';

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

		$ret .= '>';

		if (!empty($this->value)) {

			$ret .= $this->value;

		}

		$ret .= '</textarea>';

		return $ret;
	}
}

?>