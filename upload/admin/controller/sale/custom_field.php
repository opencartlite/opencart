<?php
class ControllerSaleCustomField extends Controller {
	private $error = array();  
 
	public function index() {
		$this->data += $this->language->load('sale/custom_field');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/custom_field');
		
		$this->getList();
	}

	public function insert() {
		$this->data += $this->language->load('sale/custom_field');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/custom_field');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_custom_field->addCustomField($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->data += $this->language->load('sale/custom_field');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/custom_field');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_custom_field->editCustomField($this->request->get['custom_field_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->data += $this->language->load('sale/custom_field');

		$this->document->setTitle($this->language->get('heading_title'));
 		
		$this->load->model('sale/custom_field');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $custom_field_id) {
				$this->model_sale_custom_field->deleteCustomField($custom_field_id);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'cfd.name';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
			
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL')
   		);
		
		$this->data['insert'] = $this->url->link('sale/custom_field/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('sale/custom_field/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		 
		$this->data['custom_fields'] = array();
		
		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$custom_field_total = $this->model_sale_custom_field->getTotalCustomFields();
		
		$results = $this->model_sale_custom_field->getCustomFields($data);
		
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('sale/custom_field/update', 'token=' . $this->session->data['token'] . '&custom_field_id=' . $result['custom_field_id'] . $url, 'SSL')
			);
			
			$type = '';
			
			switch ($result['type']) {
				case 'select':
					$type = $this->language->get('text_select');
					break;
				case 'radio':
					$type = $this->language->get('text_radio');
					break;
				case 'checkbox':
					$type = $this->language->get('text_checkbox');
					break;
				case 'input':
					$type = $this->language->get('text_input');
					break;
				case 'text':
					$type = $this->language->get('text_text');
					break;
				case 'textarea':
					$type = $this->language->get('text_textarea');
					break;
				case 'file':
					$type = $this->language->get('text_file');
					break;
				case 'date':
					$type = $this->language->get('text_date');
					break;																														
				case 'datetime':
					$type = $this->language->get('text_datetime');
					break;	
				case 'time':
					$type = $this->language->get('text_time');
					break;																	
			}
			
			$location = '';
			
			switch ($result['location']) {
				case 'customer':
					$location = $this->language->get('text_customer');
					break;
				case 'address':
					$location = $this->language->get('text_address');
					break;
				case 'payment_address':
					$location = $this->language->get('text_payment_address');
					break;
				case 'shipping_address':
					$location = $this->language->get('text_shipping_address');
					break;										
			}			
		
			$this->data['custom_fields'][] = array(
				'custom_field_id' => $result['custom_field_id'],
				'name'            => $result['name'],
				'type'            => $type,
				'location'        => $location,
				'status'          => $result['status'],
				'sort_order'      => $result['sort_order'],
				'selected'        => isset($this->request->post['selected']) && in_array($result['custom_field_id'], $this->request->post['selected']),
				'action'          => $action
			);
		}	
 
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$this->data['sort_name'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cfd.name' . $url, 'SSL');
		$this->data['sort_type'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cf.type' . $url, 'SSL');
		$this->data['sort_location'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cf.name' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cf.status' . $url, 'SSL');
		$this->data['sort_sort_order'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . '&sort=cf.sort_order' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $custom_field_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'sale/custom_field_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function getForm() {	

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
		}	
				
 		if (isset($this->error['custom_field_value'])) {
			$this->data['error_custom_field_value'] = $this->error['custom_field_value'];
		} else {
			$this->data['error_custom_field_value'] = array();
		}	

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL')
   		);
		
		if (!isset($this->request->get['custom_field_id'])) {
			$this->data['action'] = $this->url->link('sale/custom_field/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else { 
			$this->data['action'] = $this->url->link('sale/custom_field/update', 'token=' . $this->session->data['token'] . '&custom_field_id=' . $this->request->get['custom_field_id'] . $url, 'SSL');
		}

		$this->data['cancel'] = $this->url->link('sale/custom_field', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['custom_field_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$custom_field_info = $this->model_sale_custom_field->getCustomField($this->request->get['custom_field_id']);
    	}
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['custom_field_description'])) {
			$this->data['custom_field_description'] = $this->request->post['custom_field_description'];
		} elseif (isset($this->request->get['custom_field_id'])) {
			$this->data['custom_field_description'] = $this->model_sale_custom_field->getCustomFieldDescriptions($this->request->get['custom_field_id']);
		} else {
			$this->data['custom_field_description'] = array();
		}	
						
		if (isset($this->request->post['type'])) {
			$this->data['type'] = $this->request->post['type'];
		} elseif (!empty($custom_field_info)) {
			$this->data['type'] = $custom_field_info['type'];
		} else {
			$this->data['type'] = '';
		}
		
		if (isset($this->request->post['value'])) {
			$this->data['value'] = $this->request->post['value'];
		} elseif (!empty($custom_field_info)) {
			$this->data['value'] = $custom_field_info['value'];
		} else {
			$this->data['value'] = '';
		}
				
		if (isset($this->request->post['custom_field_customer_group'])) {
			$custom_field_customer_groups = $this->request->post['custom_field_customer_group'];
		} elseif (isset($this->request->get['custom_field_id'])) {
			$custom_field_customer_groups = $this->model_sale_custom_field->getCustomFieldCustomerGroups($this->request->get['custom_field_id']);
		} else {
			$custom_field_customer_groups = array();
		}
		
		$this->data['custom_field_customer_group'] = array();
		
		foreach ($custom_field_customer_groups as $custom_field_customer_group) {
			if (isset($custom_field_customer_group['customer_group_id'])) {
				$this->data['custom_field_customer_group'][] = $custom_field_customer_group['customer_group_id'];
			}
		}
		
		$this->data['custom_field_required'] = array();
		
		foreach ($custom_field_customer_groups as $custom_field_customer_group) {
			if (isset($custom_field_customer_group['required'])) {
				$this->data['custom_field_required'][] = $custom_field_customer_group['required'];
			}
		}
		
		$this->load->model('sale/customer_group');
		
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();	
								
		if (isset($this->request->post['location'])) {
			$this->data['location'] = $this->request->post['location'];
		} elseif (!empty($custom_field_info)) {
			$this->data['location'] = $custom_field_info['location'];
		} else {
			$this->data['location'] = '';
		}
		
		if (isset($this->request->post['position'])) {
			$this->data['position'] = $this->request->post['position'];
		} elseif (!empty($custom_field_info)) {
			$this->data['position'] = $custom_field_info['position'];
		} else {
			$this->data['position'] = '';
		}
			
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($custom_field_info)) {
			$this->data['status'] = $custom_field_info['status'];
		} else {
			$this->data['status'] = '';
		}
					
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($custom_field_info)) {
			$this->data['sort_order'] = $custom_field_info['sort_order'];
		} else {
			$this->data['sort_order'] = '';
		}
		
		if (isset($this->request->post['custom_field_value'])) {
			$custom_field_values = $this->request->post['custom_field_value'];
		} elseif (isset($this->request->get['custom_field_id'])) {
			$custom_field_values = $this->model_sale_custom_field->getCustomFieldValueDescriptions($this->request->get['custom_field_id']);
		} else {
			$custom_field_values = array();
		}
		
		$this->data['custom_field_values'] = array();
		 
		foreach ($custom_field_values as $custom_field_value) {
			$this->data['custom_field_values'][] = array(
				'custom_field_value_id'          => $custom_field_value['custom_field_value_id'],
				'custom_field_value_description' => $custom_field_value['custom_field_value_description'],
				'sort_order'                     => $custom_field_value['sort_order']
			);
		}

		$this->template = 'sale/custom_field_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'sale/custom_field')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['custom_field_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 128)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if (($this->request->post['type'] == 'select' || $this->request->post['type'] == 'radio' || $this->request->post['type'] == 'checkbox') && !isset($this->request->post['custom_field_value'])) {
			$this->error['warning'] = $this->language->get('error_type');
		}

		if (isset($this->request->post['custom_field_value'])) {
			foreach ($this->request->post['custom_field_value'] as $custom_field_value_id => $custom_field_value) {
				foreach ($custom_field_value['custom_field_value_description'] as $language_id => $custom_field_value_description) {
					if ((utf8_strlen($custom_field_value_description['name']) < 1) || (utf8_strlen($custom_field_value_description['name']) > 128)) {
						$this->error['custom_field_value'][$custom_field_value_id][$language_id] = $this->language->get('error_custom_value'); 
					}					
				}
			}	
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/custom_field')) {
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