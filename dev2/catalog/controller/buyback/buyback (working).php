<?php

class ControllerBuybackBuyBack extends Controller {

    private $error = array();

    public function index() {
		/*	if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('buyback/buyback', '', 'SSL');

			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}*/

		$user_agent = $_SERVER["HTTP_USER_AGENT"];      // Get user-agent of browser

		$safariorchrome = strpos($user_agent, 'Safari') ? true : false;     // Browser is either Safari or Chrome (since Chrome User-Agent includes the word 'Safari')
		$chrome = strpos($user_agent, 'Chrome') ? true : false;             // Browser is Chrome
		$is_safari = false;
		if($safariorchrome == true AND $chrome == false){ $is_safari = true; }     // Browser should be Safari, because there is no 'Chrome' in the User-Agent
		$this->data['is_safari'] = $is_safari;

        $this->document->setTitle('LCD Buy Back Program');
        $this->load->model('account/address');
        $this->load->model('buyback/buyback');
		 $this->load->model('localisation/zone');
        if ($this->customer->isLogged()) {
            $this->data['isLogged'] = true;
        } else {
            $this->data['isLogged'] = false;
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate())) {
           $buyback_id =  $this->model_buyback_buyback->saveData($this->request->post);
		   $this->session->data['buyback_id'] = $buyback_id;
			$this->redirect($this->url->link('buyback/buyback/printout'));
			exit;
        }
		
		if ($this->error) {
			$this->data['error_form'] = 'Please complete the fields highlighted in red or choose LCDs to ship!';
		} else {
			$this->data['error_form'] = '';
		}	
        $this->data['action'] = $this->url->link('buyback/buyback');
        $addresses = array();
        $addresses = $this->model_account_address->getAddresses();
        $this->data['addresses'] = $addresses;
        
        $general = $this->model_buyback_buyback->getGeneralDetails();
        $products = $this->model_buyback_buyback->getProducts();

		if (isset($this->request->post['address_id'])) {
			$this->data['address_id'] = $this->request->post['address_id'];
		} else {
			$this->data['address_id'] = '';
		}
		if (isset($this->request->post['firstname'])) {
			$this->data['firstname'] = $this->request->post['firstname'];
		} else {
			$this->data['firstname'] = '';
		}
		
		if (isset($this->request->post['lastname'])) {
			$this->data['lastname'] = $this->request->post['lastname'];
		} else {
			$this->data['lastname'] = '';
		}
		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];
		} else {
			$this->data['email'] = '';
		}
		
		if (isset($this->request->post['telephone'])) {
			$this->data['telephone'] = $this->request->post['telephone'];
		} else {
			$this->data['telephone'] = '';
		}
		
		if (isset($this->request->post['address_1'])) {
			$this->data['address_1'] = $this->request->post['address_1'];
		} else {
			$this->data['address_1'] = '';
		}
		
		if (isset($this->request->post['city'])) {
			$this->data['city'] = $this->request->post['city'];
		} else {
			$this->data['city'] = '';
		}
		
		if (isset($this->request->post['postcode'])) {
			$this->data['postcode'] = $this->request->post['postcode'];
		} else {
			$this->data['postcode'] = '';
		}
		
		if (isset($this->request->post['zone_id'])) {
			$this->data['zone_id'] = $this->request->post['zone_id'];
		} else {
			$this->data['zone_id'] = '';
		}
		
		
        $this->data['products'] = $products;
        $this->data['general'] = $general;
		$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId(223);


        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => 'Home',
            'href' => $this->url->link('common/home'),
            'separator' => false
        );



        $this->data['breadcrumbs'][] = array(
            'text' => 'LCD Buy Back',
            'href' => $this->url->link('buyback/buyback', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = 'LCD Buy Back Program';



        $this->template = $this->config->get('config_template') . '/template/buyback/buyback.tpl';


        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
	public function printout()
	{
		$this->load->model('buyback/buyback');
		$buyback_id = $this->session->data['buyback_id'];	
		if(!$buyback_id)
		{
			$this->redirect($this->url->link('buyback/buyback'));
			exit;	
		}
		 require_once(DIR_SYSTEM . 'html2_pdf_lib/html2pdf.class.php');



        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $server = HTTPS_IMAGE;
        } else {
            $server = HTTP_IMAGE;
        }



        if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
            $logo = $server . $this->config->get('config_logo');
        } else {
            $logo = '';
        }

        $detail = $this->model_buyback_buyback->getBuyBackDetail($this->session->data['buyback_id']);
        $products = $this->model_buyback_buyback->getBuyBackProducts($this->session->data['buyback_id']);
        $address = $this->model_buyback_buyback->getAddress($this->session->data['buyback_id']);

        $barcode_image = '<barcode type="C128A" value="LBB:' . $detail['shipment_number'] . '" label="label" style="width:50mm; height:8mm; color: #000; font-size: 3mm"></barcode>';
        $html = '
		<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/stylesheet.css" />
		<div style="text-align:left">';
        $html.='<img src="' . $logo . '" /><br /><br>';
        $html.='<h4>Label # ' . $detail['shipment_number'] . '</h4><br />';
        $html.='Date: ' . date($this->language->get('date_format_short'), strtotime($detail['date_added'])) . '<br />';
        $html.='Payment Type: ' . $detail['payment_type'] . ' / '.$this->currency->format($detail['total']).'<br /><br>';
        $html.='<div style="border-top:1px dotted black">
		<br />
		<table>
		<tr>
			<td style="width:250px">
			<strong>Return Address</strong><br />
			' . $address['address_1'] . ' <br>
			' . $address['city'] . ', ' . $address['zone'] . ' <br />
			' . $address['postcode'] . '.
			 </td>
			<td style="width:300px"><strong>Customer Information</strong><br />
			' . $address['firstname'] . ' ' . $address['lastname'] . ' <br>
			' . $address['email'] . ' <br />
			' . $address['telephone'] . '</td>
		
		</tr>
		
		</table>
		<br>
		<table class="list">
		
		<thead>
		<tr>
		<td style="width:100px">SKU</td>
		<td style="width:150px">Item Name</td>
		<td style="width:80px">OEM</td>
		<td style="width:60px">Qty</td>
		<td style="width:80px">Non-OEM</td>
		<td style="width:60px">Qty</td>
		<td style="width:85px">Sub Total</td>
		</tr>
		
		</thead>
		<tbody>
		';
        foreach ($products as $item) {
            $html.='<tr>';
            $html.='<td style="width:100px">' . $item['sku'] . '</td>';
            $html.='<td style="width:150px">' . $item['description'] . '</td>';
            $html.='<td style="width:80px">' . $this->currency->format($item['oem_price']) . '</td>';
			$html.='<td style="width:60px">' . ($item['oem_quantity']) . '</td>';
			$html.='<td style="width:80px">' . $this->currency->format($item['non_oem_price']) . '</td>';
			$html.='<td style="width:60px">' . ($item['non_oem_quantity']) . '</td>';
           $html.='<td style="width:85px">' . $this->currency->format($item['sub_total']) . '</td>';
            $html.='</tr>';
        }

        $html.='
		</tbody>
		</table>
		<br><br>
		';

        $html.='
		<div style="text-align:center;border-top:1px dotted black;margin-bottom:5px">
		<br />
		<span style="color:blue">Print this mailing label</span> and affix to your return packages<br>
		<small style="font-size:12px;margin-top:5px" ><img src="' . HTTP_IMAGE . 'content-cutout.png"> Cut or fold the label along this line and affix to the outside of the return package.</small>
		
		</div>
		<div style="border: 2px dashed grey;width:530px;margin-left:85px;padding:8px">
		<div style="border:1px solid black">
		<table style="padding:20px" cellspacing="5">
		<tr>
		<td style="border-bottom:0.7px solid black;width:320px">' . $address['firstname'] . ' ' . $address['lastname'] . '</td>
		<td style="width:100px">&nbsp;</td>
		<td rowspan="4" style="vertical-align:middle;border:1px solid black;padding:5px;font-size:10px;width:60px;text-align:center">POSTAGE <br> REQUIRED</td>
		</tr>
		<tr>
		<td style="border-bottom:0.7px solid black;width:320px">' . $address['address_1'] . '</td>
		<td >&nbsp;</td>
		
		</tr>
		<tr>
		<td style="border-bottom:0.7px solid black;width:320px">' . $address['city'] . ', ' . $address['zone'] . ', ' . $address['postcode'] . '</td>
		<td >&nbsp;</td>
		
		</tr>
		<tr>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		
		</tr>
		
		<tr>
		<td  >&nbsp;</td>
		<td >&nbsp;</td>
		<td style="color:#FFF;background-color:black;font-size:42px;height:40px;text-align:center;vertical-align:middle;font-family:Arial;font-weight:bold">CP</td>
		</tr>
		<tr>
		<td  style="text-align:center">
		<div style="text-align:left">
		PPUSA Returns<br/>
		5145 South Arville Street<br>Suite A<br>Las Vegas, NV 89118
		</div>
		</td>
		</tr>
		<tr >
		<td colspan="3" style="text-align:center;">
		<br><br>
		' . $barcode_image . '
		</td>
		</tr>
		</table>
		</div>
		</div>
		';


        $html.='</div>';

        $html.='</div>';


        // zaman commented


        try {



            $html2pdf = new HTML2PDF('P', 'A4', 'en');

            $html2pdf->setDefaultFont('courier');
            $html2pdf->writeHTML($html);

            $filename = time();
            $file = $html2pdf->Output(DIR_IMAGE . 'returns/' . $filename . '.pdf', 'F');





//pdf creation
//now magic starts
// instantiate Imagick 

            $img_name = $filename . '.jpg';
            $im = new Imagick();

            $im->setResolution(500, 500);
            $im->readimage(DIR_IMAGE . 'returns/' . $filename . '.pdf');
            $im->setImageFormat('jpeg');
            $im->writeImage(DIR_IMAGE . "returns/" . $img_name);
            $im->clear();
            $im->destroy();

//remove temp pdf
//unlink('temp.pdf');
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
		
		$this->data['printout'] = $img_name;


        $html2 = 'Thank you for submitting your LCD Buy Back Request'."<br>";
       // $html2.='Please read our returns policy (<a href="http://phonepartsusa.com/returns-or-exchanges">http://phonepartsusa.com/returns-or-exchanges</a>) and note the following:';

        $html2.='<ul>';
        $html2.='<li>Please ship the damaged LCDs within 5 business days after request creation.</li>';
        $html2.='<li>The Cash and Store Credit value issued for each LCD are estimates and only valid for 3 business day after the creation of this request.</li>';
        $html2.='<li>Exchanges and refunds will only be offered for items in their original unused condition. Damaged items will <strong>NOT</strong> be refunded.</li>';
        $html2.='<li>Please allow 1-3 business days for processing after we receive the LCD Shipment.</li>';


        $html2.='</ul>';

        $mail = new Mail();
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->hostname = $this->config->get('config_smtp_host');
        $mail->username = $this->config->get('config_smtp_username');
        $mail->password = $this->config->get('config_smtp_password');
        $mail->port = $this->config->get('config_smtp_port');
        $mail->timeout = $this->config->get('config_smtp_timeout');
        $mail->setTo($address['email']);
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender($this->config->get('config_name'));
        $mail->setSubject(html_entity_decode("LCD Buy Back", ENT_QUOTES, 'UTF-8'));
        $mail->addAttachment(DIR_IMAGE . 'returns/' . $filename . '.pdf');
        $mail->setHtml($html2);
        $mail->send();
		
		
		
		
		
		
        $this->data['breadcrumbs'][] = array(
            'text' => 'Home',
            'href' => $this->url->link('common/home'),
            'separator' => false
        );



        $this->data['breadcrumbs'][] = array(
            'text' => 'LCD Buy Back',
            'href' => $this->url->link('buyback/buyback', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
        );
		

        $this->data['heading_title'] = 'Thank you!';

		$this->load->model('catalog/information');

        $return_policy = $this->model_catalog_information->getInformation(3);

        $html = '<div class="selet-item-holder">
        	<div class="content-header">
            	<h2>Label # ' . $detail['shipment_number'] . '</h2>
                <p>Print the Merchandise Authorization Label below and affix to your package.<br> 
Using this Label will help expediate the return processing.</p>
            </div>
            <div class="selet-item-inner">
            	<div class="selet-item-header">
                	<div class="pull-left">' . strtoupper($address['firstname'] . ' ' . $address['lastname']) . '</div>
                    <div class="pull-right"><strong>Total</strong> ' . $this->currency->format($detail['total']) . ' / ' . date($this->language->get('date_format_short'), strtotime($detail['date_added'])) . '</div>
                </div>
                <div class="print-inner">
                	<div class="product-img" id="printarea"><img src="' . HTTP_IMAGE . 'returns/' . $img_name . '" style="width:100%;height:100%"></div>
                   	<div class="product-detail">
                    	<a class="return-btn" href="javascript:void(0)" onclick="printThis();"><img src="image/print-icon.png" alt="print-icon">Print LBB Label</a>
                        <strong>Print & Affix Label on the exterior of the package</strong>
                        <div class="follwing-note">
                        	<ul>
                            	<li style="font-weight:bold"><img src="image/alert.png" alt="alert">Please note the following</li>
                                <li>• Please ship the damaged LCDs within 5 business days after request creation.</li>
                                <li>• The Cash and Store Credit value issued for each LCD are estimates and only valid for 3 business day after the creation of this request.</li>
                                <li>• Please allow 1-3 business days for processing after we receive the LCD Shipment.</li>
                            </ul>
                        </div>
                        <div class="sipmle-box">
                        	
                        </div>
                    </div>
                </div>
			</div>
        </div>
		
		
		
		<script>
function printThis()
{
var mywindow = window.open("", "LBB Print", "height=400,width=600");
        mywindow.document.write("<html><head><title>LBB Print</title>");
        
        mywindow.document.write("</head><body >");
        mywindow.document.write($("#printarea").html());
        mywindow.document.write("</body></html>");

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

setTimeout(function () {
       mywindow.print();
        mywindow.close();

        return true;	
    }, 2000);
       
}
</script>

		
		
		';
        $this->data['html'] = $html;
		 $this->template = $this->config->get('config_template') . '/template/buyback/thank_you.tpl';


        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
		
	}
	private function validate()
	{
		if($this->request->post['address_id']=='-1')
		{
		if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
			$this->error['firstname'] ='Firstname is either invalid or left blank';
		}	
		if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
			$this->error['lastname'] ='Lastname is either invalid or left blank';
		}	
		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
			$this->error['email'] ='Provide a valid email address';
		}
		
		if ((utf8_strlen($this->request->post['address_1']) < 1) || (utf8_strlen($this->request->post['address_1']) > 100)) {
			$this->error['address_1'] ='Provide valid address details';
		}
		
		if ((utf8_strlen($this->request->post['city']) < 1) || (utf8_strlen($this->request->post['city']) > 40)) {
			$this->error['city'] ='Provide city name';
		}
		
		if ((utf8_strlen($this->request->post['postcode']) < 3) || (utf8_strlen($this->request->post['postcode']) > 10)) {
			$this->error['postcode'] ='Provide valid city postcode';
		}	
		
		if ($this->request->post['zone_id']==0) {
			$this->error['postcode'] ='Select a postcode';
		}	
		}
		
		if(!$this->request->post['cash_total'] && !$this->request->post['credit_total'])
		{
			
			$this->error['postcode'] ='Select a postcode';
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
		
	}
}
?>
