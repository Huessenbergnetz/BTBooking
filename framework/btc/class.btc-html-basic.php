<?php

// require_once 'btc-functions.php';


/**
 * Base class for all HTML elements.
 */
class BTCHtml {

	public $id = '';

	public $htmlClasses = null;

	public $accesskey = '';

	public $aria = array();

	public $contenteditable = false;

	public $contextmenu = '';

	public $data = array();

	public $dir = '';

	public $draggable = false;

	public $hidden = false;

	public $lang = '';

	public $style = null;

	public $tabindex = '';

	public $title = '';

	public $hide = false;

	protected $tag_name = '';

	protected $output = '';


	public function render($echo = true) {

		if ($echo) {
			$this->_render();
			echo $this->output;
		} else {
			$this->_render();
			return $this->output;
		}

	}

	protected function _render() {

		if ($this->hide) {
			if (is_array($this->style)) {
				$this->style['display'] = 'none';
			} else {
				$this->style .= " display:none;";
			}
		}

		$this->output = '<' . $this->tag_name;

		$this->add_attr('id', $this->id);

		// adding class
		if (!empty($this->htmlClasses)) {
			if (is_array($this->htmlClasses)) {
				$_classes = implode(" ", $this->htmlClasses);
				$this->output .= " class=\"$_classes\"";
			} else {
				$this->output .= " class=\"$this->htmlClasses\"";
			}
		}

		$this->add_attr('accesskey', $this->accesskey);

		// adding aria values
		if (!empty($this->aria) && is_array($this->aria)) {
			foreach($this->aria as $key => $value) {
				$this->output .= sprintf(' aria-%s="%s"', $key, $value);
			}
		}

		$this->add_attr('contenteditable', $this->contenteditable);
		$this->add_attr('contextmenu', $this->contextmenu);

		// adding data attributes
		if (!empty($this->data) && is_array($this->data)) {
			foreach($this->data as $key => $value) {
				$this->output .= sprintf(' data-%s="%s"', $key, $value);
			}
		}

		$this->add_attr('dir', $this->dir);
		$this->add_attr('draggable', $this->draggable);
		$this->add_attr('hidden', $this->hidden);
		$this->add_attr('lang', $this->lang);

		// adding style
		if (!empty($this->style)) {
			if (is_array($this->style)) {
				$this->output .= " style=\"";
				foreach($this->style as $key => $value) {
					$this->output .= "$key:$value;";
				}
				$this->output .= "\"";
			} elseif (is_string($this->style)) {
				$this->output .= " style=\"$this->style\"";
			}
		}

		$this->add_attr('tabindex', $this->tabindex);
		$this->add_attr('title', $this->title);
	}

	protected function closeTag($endtag = true) {
		if ($endtag) {
			$this->output .= '</' . $this->tag_name . '>';
		} else {
			$this->output .= '>';
		}
	}

	protected function add_attr($attr, $val = null) {

		if (!empty($val)) {
			if (is_bool($val)) {
				$this->output .= " $attr=\"$attr\"";
			} else {
				$this->output .= " $attr=\"$val\"";
			}
		}
	}

}

?>