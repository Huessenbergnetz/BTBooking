<?php

require_once(__DIR__.'/../class.btc-html-basic.php');

class BTCTableRow extends BTCHtml {

    protected $tag_name = 'tr';

	public $content = array();

	public function __construct(array $content = array(), array $attrs = array()) {

		$this->content = $content;
		if (!empty($attrs)) {
			foreach($attrs as $key => $value) {
				$this->$key = $value;
			}
		}
	}

	protected function _render() {

		parent::_render();

		$this->output .= '>';

		if (!empty($this->content)) {
			foreach($this->content as $key => $object) {
				$this->output .= $object->render(false);
			}
		}

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