<?php

require_once(__DIR__.'/../btc-functions.php');

class BTCFormFieldset {

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
	 * Disabled attribute.
	 *
	 * @var bool
	 */
	public $disabled = false;

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

	public $style = array();

	public $content = array();


	public function __construct(array $content = array(), array $attrs = array()) {

		$this->content = $content;

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

		$ret = '<fieldset';

		btc_gen_attr($ret, 'id', $this->id);
		btc_gen_attr($ret, 'name', $this->name);
		btc_gen_attr($ret, 'class', $this->htmlClass);
		btc_gen_aria_attrs($ret, $this->aria);
		btc_gen_style_attr($ret, $this->style);
		btc_gen_data_attrs($ret, $this->data);
		btc_gen_attr($ret, 'disabled', $this->disabled);

		$ret .= '>';

		if (!empty($this->content)) {
			foreach($this->content as $key => $object) {
				$ret .= $object->render(false);
			}
		}

		$ret .= '</fieldset>';

		return $ret;
	}

	public function add_content($additional) {

		if (is_array($additional)) {

			if (!empty($additional)) {

				foreach ($additional as $add) {

					$this->content[] = $add;

				}

			}

		} else if (is_object($additional)) {

			$this->content[] = $additional;

		}
	}

}

?>