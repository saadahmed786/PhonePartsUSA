<?php

class PaypalPayment{

	public $api_username;

	public $api_password;

	public $api_signature;

	public $environment;

	/**
	 * @param string $api_username
	 * @param string $api_password
	 * @param string $api_signature
	 * @param string $environment
	 * @return none
	 */
	public function __construct($api_username,$api_password,$api_signature,$environment = 'Live'){
		$this->api_username  = $api_username;
		$this->api_password  = $api_password;
		$this->api_signature = $api_signature;
		$this->environment = $environment;
	}


	/**
	 * @return String
	 */
	public function PPHttpPost($methodName_, $nvpStr_){
		$environment = $this->environment;

		// Set up your API credentials, PayPal end point, and API version.
		$API_UserName  = urlencode($this->api_username);
		$API_Password  = urlencode($this->api_password);
		$API_Signature = urlencode($this->api_signature);

		$API_Endpoint = "https://api-3t.paypal.com/nvp";
		if("sandbox" === $environment || "beta-sandbox" === $environment) {
			$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
		}

		$version = urlencode('87.0');

		// setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// Set the curl parameters.
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

		//print $nvpreq; exit;

		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		// Get response from the server.
		$httpResponse = curl_exec($ch);

		if(!$httpResponse) {
			exit('$methodName_ failed: '.curl_error($ch).'('.curl_errno($ch).')');
		}

		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);

		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}

		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}

		return $httpParsedResponseAr;
	}

	public function getTransactions($start_date , $end_date){
		$nvpStr = "&STARTDATE=".urlencode($start_date)."&ENDDATE=".urlencode($end_date);
		
		$transactions = $this->PPHttpPost("TransactionSearch",$nvpStr);
		return $transactions;
	}

	public function getTransactionByInvoice($start_date , $order_id,$email){
		$nvpStr = "&STARTDATE=".urlencode($start_date)."&INVNUM=".urlencode($order_id).'';
		
		$transactions = $this->PPHttpPost("TransactionSearch",$nvpStr);
		return $transactions;
	}
	public function getTransactionManual($params){
		
	$nvpStr = "&".$params;
		
		$transactions = $this->PPHttpPost("TransactionSearch",$nvpStr);
		return $transactions;
	}
	
	public function getTransctionDetails($transaction_id){
		$nvpStr = "&TRANSACTIONID=".urlencode($transaction_id);
		
		$transaction = $this->PPHttpPost("gettransactionDetails",$nvpStr);
		return $transaction;
	}

	
	/** This function will take NVPString and convert it to an Associative Array and it will decode the response.
	 * It is usefull to search for a particular key and displaying arrays.
	 * @nvpstr is NVPString.
	 * @nvpArray is Associative Array.
	 */
	public function deformatNVP($nvpstr){
		$intial=0;
		$nvpArray = array();

		while(strlen($nvpstr)){
			//postion of Key
			$keypos= strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
		}
		return $nvpArray;
	}
	
	public function isAddressVerified($email , $street , $zip){
		$nvpStr = "&EMAIL=".urlencode($email)."&STREET=".urlencode($street)."&ZIP=".urlencode($zip);
		
		$result = $this->PPHttpPost("AddressVerify" , $nvpStr);
		return $result;
		
		$confirmation_code = urldecode($result['CONFIRMATIONCODE']);
		return $confirmation_code;
	}
}