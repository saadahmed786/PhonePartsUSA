<?php
class ControllerMiscShipping extends Controller {
	public function index() {
// print_r($this->request->post);exit;
		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body);

		// echo $data->address->lastName;exit;
		$json_string = '







{"address":{"addrId":"3DB4508AB57","lastName":"'.$data->address->lastName.'","zip":"'.$data->address->zip.'","hasAssociatedCreditCards":false,"phone":"'.$data->address->phone.'","taxRate":"0","isPOBox":false,"state":"'.$data->address->state.'","address1":"'.$data->address->address1.'","address2":"'.$data->address->address2.'","address3":"","companyName":"'.$data->address->companyName.'","phoneTwo":"'.$data->address->phoneTwo.'","city":"'.$data->address->city.'","country":"'.$data->address->country.'","phoneExt":"'.$data->address->phoneExt.'","email":"'.$data->address->email.'","isTaxExempt":false,"phoneTwoExt":"'.$data->address->phoneTwoExt.'","cpf":"","firstName":"'.$data->address->firstName.'"},"status":"success"}';


echo $json_string;
}
}
 ?>