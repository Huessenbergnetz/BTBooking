<?php

class BTCInputCheckbox {

	/**
	 * Element ID.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Element name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Checked attribute.
	 *
	 * @var bool
	 */
	public $checked = false;

	/**
	 * Readonly attribute.
	 *
	 * @var bool
	 */
	public $readonly = false;

	/**
	 * Disabled attribute.
	 *
	 * @var bool
	 */
	public $disabled = false;

	/**
	 * Value attribute.
	 *
	 * @var string
	 */
	public $value = '';

	/**
	 * Required attribute.
	 *
	 * @var bool
	 */
	public $required = false;

	/**
	 * class attribute
	 *
	 * @var string
	 */
	public $htmlClass = '';

	public $tabindex = null;

	public $title = '';

	public $aria = array();

	public $data = array();



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


	public function render($echo = true) {

		if ($echo) {
			echo $this->_render();
		} else {
			return $this->_render();
		}

	}

	private function _render() {

		$_name = !empty($this->name) ? $this->name : $this->id;

		$ret = '<input type="checkbox"';
		if ($this->id) $ret .= ' id="' . $this->id . '"';
		if ($_name) $ret .= ' name="' . $_name . '"';
		if ($this->htmlClass) $ret .= ' class="' .$this->htmlClass . '"';
		if ($this->value) $ret .= ' value="' . $this->value . '"';
		if ($this->checked) $ret .= ' checked="checked"';

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

		if ($this->readonly) $ret .= ' readonly';

		if ($this->required) $ret .= ' required';

		$ret .= '>';

		return $ret;
	}

}

?>