<?php


class BTCInputRadio {

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

		$ret = '<input type="radio"';
		btc_gen_attr($ret, 'id', $this->id);
		btc_gen_attr($ret, 'name', $_name);
		btc_gen_attr($ret, 'class', $this->htmlClass);
		btc_gen_attr($ret, 'value', $this->value);
		btc_gen_attr($ret, 'checked', $this->checked);
		btc_gen_aria_attrs($ret, $this->aria);
		btc_gen_data_attrs($ret, $this->data);
		btc_gen_attr($ret, $this->readonly);
		btc_gen_attr($ret, $this->required);

		$ret .= '>';

		return $ret;
	}

}

?>