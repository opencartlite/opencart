<?php 
class ControllerTotalSubTotal extends Controller { 
	private $error = array(); 
	 
	public function index() { 
		$this->data += $this->language->load('total/sub_total');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('sub_total', $this->request->post);
		
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
					
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_total'),
			'href' => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('total/sub_total', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
		$this->data['action'] = $this->url->link('total/sub_total', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['sub_total_status'])) {
			$this->data['sub_total_status'] = $this->request->post['sub_total_status'];
		} else {
			$this->data['sub_total_status'] = $this->config->get('sub_total_status');
		}

		if (isset($this->request->post['sub_total_sort_order'])) {
			$this->data['sub_total_sort_order'] = $this->request->post['sub_total_sort_order'];
		} else {
			$this->data['sub_total_sort_order'] = $this->config->get('sub_total_sort_order');
		}

		$this->template = 'total/sub_total.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/sub_total')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>