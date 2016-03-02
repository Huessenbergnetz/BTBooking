<?php

require_once(__DIR__.'/../class.btc-html-basic.php');

class BTCFormFieldset extends BTCHtml {

	public $disabled = false;

	public $form = '';

	public $name = '';

	public $content = array();

	protected $tag_name = 'fieldset';

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

	protected function _render() {

		parent::_render();

		$this->add_attr('name', $this->name);
		$this->add_attr('form', $this->form);
		$this->add_attr('disabled', $this->disabled);

		$this->output .= '>';

		if (!empty($this->content)) {
			foreach($this->content as $key => $object) {
				$this->output .= $object->render(false);
			}
		}

		$this->closeTag();
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