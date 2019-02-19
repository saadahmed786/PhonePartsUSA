<?php 
class ControllerProductSearchMobile extends Controller { 	
	public function index() { 		
		$query = "";
		foreach($_POST as $key => $value) {
		   $query .= '&' . $key . '=' . $value;
		}		
		
		$this->redirect($this->url->link('product/search') . $query);	
  	}
}
?>