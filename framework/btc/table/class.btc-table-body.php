<?php

require_once(__DIR__.'/../class.btc-html-basic.php');

class BTCTableBody extends BTCHtml {

	public $content = array();

	protected $tag_name = 'tbody';

	public function __construct(array $attrs = array(), array $content = array()) {

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

		$this->closeTag();
	}
}

?>