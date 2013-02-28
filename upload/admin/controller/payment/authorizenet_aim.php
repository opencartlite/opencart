<?php 
class ControllerPaymentAuthorizenetAim extends Controller {
	private $error = array(); 

	public function index() {
		$this->data += $this->language->load('payment/authorizenet_aim');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('authorizenet_aim', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}					

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['login'])) {
			$this->data['error_login'] = $this->error['login'];
		} else {
			$this->data['error_login'] = '';
		}

 		if (isset($this->error['key'])) {
			$this->data['error_key'] = $this->error['key'];
		} else {
			$this->data['error_key'] = '';
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/authorizenet_aim', 'token=' . $this->session->data['token'], 'SSL')
   		);
				
		$this->data['action'] = $this->url->link('payment/authorizenet_aim', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['authorizenet_aim_login'])) {
			$this->data['authorizenet_aim_login'] = $this->request->post['authorizenet_aim_login'];
		} else {
			$this->data['authorizenet_aim_login'] = $this->config->get('authorizenet_aim_login');
		}
	
		if (isset($this->request->post['authorizenet_aim_key'])) {
			$this->data['authorizenet_aim_key'] = $this->request->post['authorizenet_aim_key'];
		} else {
			$this->data['authorizenet_aim_key'] = $this->config->get('authorizenet_aim_key');
		}
		
		if (isset($this->request->post['authorizenet_aim_hash'])) {
			$this->data['authorizenet_aim_hash'] = $this->request->post['authorizenet_aim_hash'];
		} else {
			$this->data['authorizenet_aim_hash'] = $this->config->get('authorizenet_aim_hash');
		}

		if (isset($this->request->post['authorizenet_aim_server'])) {
			$this->data['authorizenet_aim_server'] = $this->request->post['authorizenet_aim_server'];
		} else {
			$this->data['authorizenet_aim_server'] = $this->config->get('authorizenet_aim_server');
		}
		
		if (isset($this->request->post['authorizenet_aim_mode'])) {
			$this->data['authorizenet_aim_mode'] = $this->request->post['authorizenet_aim_mode'];
		} else {
			$this->data['authorizenet_aim_mode'] = $this->config->get('authorizenet_aim_mode');
		}
		
		if (isset($this->request->post['authorizenet_aim_method'])) {
			$this->data['authorizenet_aim_method'] = $this->request->post['authorizenet_aim_method'];
		} else {
			$this->data['authorizenet_aim_method'] = $this->config->get('authorizenet_aim_method');
		}
		
		if (isset($this->request->post['authorizenet_aim_total'])) {
			$this->data['authorizenet_aim_total'] = $this->request->post['authorizenet_aim_total'];
		} else {
			$this->data['authorizenet_aim_total'] = $this->config->get('authorizenet_aim_total'); 
		} 
				
		if (isset($this->request->post['authorizenet_aim_order_status_id'])) {
			$this->data['authorizenet_aim_order_status_id'] = $this->request->post['authorizenet_aim_order_status_id'];
		} else {
			$this->data['authorizenet_aim_order_status_id'] = $this->config->get('authorizenet_aim_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['authorizenet_aim_geo_zone_id'])) {
			$this->data['authorizenet_aim_geo_zone_id'] = $this->request->post['authorizenet_aim_geo_zone_id'];
		} else {
			$this->data['authorizenet_aim_geo_zone_id'] = $this->config->get('authorizenet_aim_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['authorizenet_aim_status'])) {
			$this->data['authorizenet_aim_status'] = $this->request->post['authorizenet_aim_status'];
		} else {
			$this->data['authorizenet_aim_status'] = $this->config->get('authorizenet_aim_status');
		}
		
		if (isset($this->request->post['authorizenet_aim_sort_order'])) {
			$this->data['authorizenet_aim_sort_order'] = $this->request->post['authorizenet_aim_sort_order'];
		} else {
			$this->data['authorizenet_aim_sort_order'] = $this->config->get('authorizenet_aim_sort_order');
		}

		$this->template = 'payment/authorizenet_aim.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/authorizenet_aim')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['authorizenet_aim_login']) {
			$this->error['login'] = $this->language->get('error_login');
		}

		if (!$this->request->post['authorizenet_aim_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>