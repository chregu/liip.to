<?php
class api_command_default extends api_command {
	
	public function __construct($attribs) {
		parent::__construct ( $attribs );
		$this->data [] = new api_model_queryinfo ( $this->request, $this->route );
	}
	
	public function index() {
	
	}
	public function getXslParams() {
	    //add webpaths
		$cfg = api_config::getInstance();
		$webpaths = $cfg->webpaths;
		$attrib = array ( );
		if (! empty ( $webpaths ) && is_array ( $webpaths )) {
			if (! (isset ( $attrib ['xslproc'] ) && is_array ( $attrib ['xslproc'] ))) {
				$attrib ['xslproc'] = array ( );
			}
			foreach ( $webpaths as $key => $value ) {
				if (strpos ( $value, '/' ) === 0 || strpos ( $value, 'http://' ) === 0) {
				} else {
					// Relative URL
					$value = API_WEBROOT . $value;
				}
				$attrib ['xslproc'] ['webroot_' . $key] = $value;
			}
		
		}
		return $attrib;
	}
}
