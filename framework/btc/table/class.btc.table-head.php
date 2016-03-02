<?php

require_once(__DIR__.'/../class.btc-html-basic.php');

class BTCTableHead extends BTCHtml {

	private $content = null;

	protected $tag_name = 'thead';

	public function __construct(array $attrs = array(), BTCTableRow $content = null) {

		$this->content = $content;
		if (!empty($attrs)) {
			foreach($attrs as $key => $value) {
				$this->$key = $value;
			}
		}
	}

	public function setContent(BTCTableRow $content) {
		$this->content = $content;
	}

	protected function _render() {

		parent::_render();

		$this->output .= $this->content->render(false);

		parent::closeTag();
	}
}

?>