<?php  
class ControllerCheckoutLogin extends Controller { 
	public function index() {
		$this->data += $this->language->load('checkout/checkout');
		
		$this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());
		
		if (isset($this->session->data['account'])) {
			$this->data['account'] = $this->session->data['account'];
		} else {
			$this->data['account'] = 'register';
		}
		
		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/login.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/checkout/login.tpl';
		} else {
			$this->template = 'default/template/checkout/login.tpl';
		}
				
		$this->response->setOutput($this->render());
	}
	
	public function save() {
		$this->data += $this->language->load('checkout/checkout');
		
		$json = array();
		
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');			
		}
		
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}	
		
		if (!$json) {
			if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
				$json['error']['warning'] = $this->language->get('error_login');
			}
		
			$this->load->model('account/customer');
		
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
			
			if ($customer_info && !$customer_info['approved']) {
				$json['error']['warning'] = $this->language->get('error_approved');
			}		
		}
		
		if (!$json) {
			unset($this->session->data['guest']);
				
			$this->load->model('account/address');
				
			if ($this->config->get('config_tax_customer') == 'payment') {
				$this->session->data['payment_addess'] = $this->model_account_address->getAddress($this->customer->getAddressId());						
			}
			
			if ($this->config->get('config_tax_customer') == 'shipping') {
				$this->session->data['shipping_addess'] = $this->model_account_address->getAddress($this->customer->getAddressId());	
			}	
			
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
					
		$this->response->setOutput(json_encode($json));		
	}
}
?>