<?php
class api_command_default extends api_command{

    public function __construct($attribs) {
        parent::__construct($attribs);
         $this->data[] = new api_model_queryinfo($this->request, $this->route);        
    }	

	public function index() {
		
	}	
	
	
}

?>