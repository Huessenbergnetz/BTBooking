<?php

class BTCInputUrl {

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
	 * Size attribute.
	 *
	 * @var int
	 */
	public $size = 0;

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
	 * Maxlength attribute.
	 *
	 * @var int
	 */
	public $maxlength = 0;

	/**
	 * Minlength attribute.
	 *
	 * @var int
	 */
	public $minlength = 0;

	/**
	 * Required attribute.
	 *
	 * @var bool
	 */
	public $required = false;

	/**
	 * Patter attribute
	 *
	 * @var string
	 */
	public $pattern = '';

	/**
	 * Placeholder attribute
	 *
	 * @var string
	 */
	public $placeholder = '';

	/**
	 * Autocomplete attribute
	 *
	 * @var string
	 */
	public $autocomplete = '';

	/**
	 * inputmode attribute
	 *
	 * @var string
	 */
	public $inputmode = '';

	/**
	 * class attribute
	 *
	 * @var string
	 */
	public $htmlClass = '';

	/**
	 * contenteditable attribute
	 *
	 * @var bool
	 */
	public $contenteditable = false;

	public $contextmenu = '';

	public $direction = '';

	public $hidden = false;

	public $lang = '';

	public $spellcheck = false;

	public $style = '';

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

		$ret = '<input type="url"';
		if ($this->id) $ret .= ' id="' . $this->id . '"';
		if ($_name) $ret .= ' name="' . $_name . '"';
		if ($this->htmlClass) $ret .= ' class="' .$this->htmlClass . '"';
		if ($this->value) $ret .= ' value="' . $this->value . '"';
		if ($this->style) $ret .= ' style="' . $this->style . '"';
		if ($this->placeholder) $ret .= ' placeholder="' . $this->placeholder . '"';

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

		if (!empty($this->pattern)) {
			$ret .= ' pattern="' . $this->pattern . '"';
		}

		if ($this->readonly) $ret .= ' readonly';

		if ($this->required) $ret .= ' required';

		$ret .= '>';

		return $ret;
	}
}

?>