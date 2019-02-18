<?php
/**
 * Google Analytics PRO OpenCart Module
 * 
 * Adds standard and ecommerce analytics tracking to OpenCart.
 *
 * @author		Ian Gallacher (www.opencartstore.com)
 * @version		1.5.1.3
 * @support		www.opencartstore.com/support
 * @email		info@opencartstore.com
 */

/**
 * Controls the creation of the template file and the contents depending on
 * where the script is being run from within the site.
 */
class ControllerModuleGoogleAnalytics extends Controller {
	private $_submit_click_code = '';
	private $_tracking_code = '';
	private $_profile_id = '';
	
	public function __construct($registry)
	{
		parent::__construct($registry);
		
		// Set the UA-XXXXXXXX-X id.
		$this->_profile_id = trim($this->config->get('googleanalytics_profile_id'));
	}
	
	protected function index()
	{ 
		$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/googleanalytics.tpl';
		$this->id = 'googleanalytics';
		
		// Build header code used in all tracking snippets.
		$this->_build_header_code();

		// Build the "button-confirm" (Submit Order Button) click event handler if we are at the checkout.
		if (isset($this->request->get['route']) && strpos($this->request->get['route'], 'checkout') !== FALSE) {
			$this->_build_submit_button_event();
		}
		
		// Check if we are at the checkout/success page.
		if (isset($this->request->get['route']) && $this->request->get['route'] == 'checkout/success' && isset($this->session->data['ga'])) {	
			// Build the ecommerce tracking code.
			$this->_build_ecommerce_code();
		}
		
		$this->_build_footer_code();
		$this->_write_tracking_code();
		$this->render();
	}
	
	
	/**
	 * Called by the click event on the "Confirm Order" button to store the order
	 * data in a session variable for use on the checkout/success page.
	 */
	public function ajax_copy_order_data()
	{
		if (isset($this->session->data['order_id'])) {
			$this->session->data['ga']['products'] = $this->cart->getProducts();
			$this->session->data['ga']['order_id'] = $this->session->data['order_id'];
			
			// Check if shipping cost has been set.
			$this->session->data['ga']['shipping_cost'] = isset($this->session->data['shipping_method']['cost']) ? $this->session->data['shipping_method']['cost'] : 0;
		}
	}
	
	/**
	 * Builds the javascript click event handler and binds it to
	 * the confirm order button at checkout.
	 */
	private function _build_submit_button_event()
	{
		$this->_submit_click_code = "\n<script type='text/javascript'>\n" . 
					  		   		"$(document).on('click','#button-confirm', function () {\n" . 
					  		   		"$.ajax({\n" .  
					  		   		"type: 'GET',\n" . 
					  		   		"url: 'index.php?route=module/googleanalytics/ajax_copy_order_data'\n" . 
					  		   		"});\n" . 
					  		   		"});\n" . 
					  		   		"</script>\n";
	}
	
	/**
	 * Builds standard header code for Google Analytics tracking regardless
	 * of whether standard or ecommerce tracking is being used. 
	 */ 
	private function _build_header_code()
	{
		$this->_tracking_code .= "<script type='text/javascript'>\n" .
					  		    "var _gaq = _gaq || [];\n" .
					  		    "_gaq.push(['_setAccount', '$this->_profile_id']);\n" .
					  		    "_gaq.push(['_trackPageview']);\n";
	}
	
	/**
	 * Write the ecommerce portion of the tracking code.
	 */
	private function _build_ecommerce_code()
	{
		$this->load->model('catalog/googleanalytics');
		
		$order = $this->model_catalog_googleanalytics->get_order_details($this->session->data['ga']['order_id']);
			
		// Set the shipping cost.
		$order['shipping'] = $this->session->data['ga']['shipping_cost'];
		
		$ecommerce_code = "_gaq.push(['_addTrans'," .
						  "'" . $this->session->data['ga']['order_id'] . "'," .
						  "'" . HTTP_SERVER . "'," .
						  "'" . $order['total'] . "'," .
						  "'" . $order['tax'] . "'," .
						  "'" . $order['shipping'] . "'," .
						  "'" . $order['payment_city'] . "'," .
						  "'" . $order['payment_zone'] . "'," .
						  "'" . $order['payment_country'] . "'" .
						  "]);\n";
			
		// Build the javascript snippet for each item in the order.
		foreach ($this->session->data['ga']['products'] as $product) {
			// Get the products parent category name, set to uncateforzed if not found.
			$category = $this->model_catalog_googleanalytics->get_category_name($product['product_id']);
			
			// Lookup the order details for the product.
			$product_details = $this->model_catalog_googleanalytics->get_product_details($this->session->data['ga']['order_id'], $product['product_id']);
				
			$ecommerce_code .= "_gaq.push(['_addItem'," .
							   "'" . $this->session->data['ga']['order_id'] . "'," .
							   "'" . $product_details['model'] . "'," .
							   "'" . $product_details['name'] . "'," .
							   "'" . $category . "'," .
							   "'" . $product_details['price'] . "'," .
							   "'" . $product_details['quantity'] . "'" .
							   "]);\n";
		}
			
		$ecommerce_code .= "_gaq.push(['_trackTrans']);\n";
			
		// Concat the ecommerce snippet onto the existing javascript snippet.
		$this->_tracking_code .= $ecommerce_code;

		unset($this->session->data['ga']);
	}
	
	/**
	 * Builds standard footer code of google analytics tracking.
	 */
	private function _build_footer_code()
	{
		$this->_tracking_code .= "(function() {\n" .
			  		 		     "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n" .
			  		 		     "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n" .
			  		 		     "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n" .
			  		 		     "})();\n" .
			  		 		     "</script>";
	}
	
	/**
	 * Writes the tracking code to the googleanalytics.tpl file. If there is a permissions
	 * error then display a message explaining how to fix it.
	 */
	private function _write_tracking_code()
	{
		$path = DIR_TEMPLATE . $this->template;

		try {
			if (is_writable(dirname($path))) {
				file_put_contents($path, $this->_submit_click_code . $this->_tracking_code);
			} else { 
				throw new Exception('Incorrect directory permissions. Change the following directory permissions to 755 or 777.<br /> ' . dirname($path));
			}
		} catch (Exception $e) {
			$this->_display_exception($e);
			exit();
		}
	}

	/**
	 * Display exceptions to the screen.
	 */
	private function _display_exception(Exception $e)
	{
		$message = $e->getMessage();
		$file = $e->getFile();
		$line = $e->getLine();
		$date = date('M d, Y h:iA');

		$html = '<pre>' .
				'<h3>Exception Information</h3>' .
				'<p><strong>Date:</strong> ' . $date . '</p>' .
				'<p><strong>Message:</strong> ' . $message . '</p>' .
				'<p><strong>File:</strong> ' . $file . '</p>' .
				'<p><strong>Line:</strong> ' . $line . '</p>' .
				'</pre>';
		echo $html;
	}
}
?>
