<?php
class ControllerSettingLocation extends Controller {
	private $error = array();
	
	public function index() {
		$this->data += $this->language->load('setting/location');
		
		$this->document->setTitle($this->language->get('heading_title'));  // Set the title and language
		
		$this->load->model('setting/location');
		
		$this->getList();
	}

	public function insert() {
		$this->data += $this->language->load('setting/location');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/location');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_location->addLocation($this->request->post);
			
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
			
			$this->redirect($this->url->link('setting/location', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		
		$this->getForm();
	}

	public function update() {
		$this->data += $this->language->load('setting/location');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/location');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_location->editLocation($this->request->get['location_id'], $this->request->post);
		
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
		
			$this->redirect($this->url->link('setting/location', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		
		$this->getForm();
	}

	public function delete() {
		$this->data += $this->language->load('setting/location');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/location');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach($this->request->post['selected'] as $location_id) {
				$this->model_setting_location->deleteLocation($location_id);
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
			
			$this->redirect($this->url->link('setting/location', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		
		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'l.name';
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
		
		$this->data['breadcrumbs'] =   array();
		
		$this->data['breadcrumbs'][] =   array(
			'text'      =>  $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
		);
		
		$this->data['breadcrumbs'][] =   array(
			'text'      =>  $this->language->get('heading_title'),
			'href'      =>  $this->url->link('setting/location', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		$this->data['insert'] = $this->url->link('setting/location/insert',  'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('setting/location/delete',  'token=' . $this->session->data['token'] . $url, 'SSL');
		
		$this->data['location'] = array();
		
		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
			
		$location_total = $this->model_setting_location->getTotalLocations();
		
		$results = $this->model_setting_location->getLocations($data); //  retrieve db information for locations from function in Model
		
		foreach($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('setting/location/update', 'token=' . $this->session->data['token'] . '&location_id=' . $result['location_id'] . $url, 'SSL')
			);
		
			$this->data['location'][] =   array(
				'location_id' => $result['location_id'],
				'name'        => $result['name'],
				'address_1'   => $result['address_1'],
				'zone'        => $result['zone'],
				'country'     => $result['country'],
				'selected'    => isset($this->request->post['selected']) && in_array($result['location_id'], $this->request->post['selected']),
				'action'      => $action
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
		
		$this->data['sort_name'] = $this->url->link('setting/location', 'token=' . $this->session->data['token'] . '&sort=l.name' . $url, 'SSL');
		$this->data['sort_address_1'] = $this->url->link('setting/location', 'token=' . $this->session->data['token'] . '&sort=l.address_1' . $url, 'SSL');
		$this->data['sort_zone'] = $this->url->link('setting/location', 'token=' . $this->session->data['token'] . '&sort=z.name' . $url, 'SSL');
		$this->data['sort_country'] = $this->url->link('setting/location', 'token=' . $this->session->data['token'] . '&sort=c.name' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $location_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('setting/location', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		
		$this->template = 'setting/location_list.tpl';
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
			$this->data['error_name'] = '';
		}
		
		if (isset($this->error['address_1'])) {
			$this->data['error_address_1'] = $this->error['address_1'];
		} else {
			$this->data['error_address_1'] = '';
		}
		
		if (isset($this->error['city'])) {
			$this->data['error_city'] = $this->error['city'];
		} else {
			$this->data['error_city'] = '';
		}
		
		if (isset($this->error['postcode'])) {
			$this->data['error_postcode'] = $this->error['postcode'];
		} else {
			$this->data['error_postcode'] = '';
		}
		
		if (isset($this->error['country'])) {
			$this->data['error_country'] = $this->error['country'];
		} else {
			$this->data['error_country'] = '';
		}
		
		if (isset($this->error['zone'])) {
			$this->data['error_zone'] = $this->error['zone'];
		} else {
			$this->data['error_zone'] = '';
		}
				
		if (isset($this->error['geocode'])) {
			$this->data['error_geocode'] = $this->error['geocode'];
		} else {
			$this->data['error_geocode'] = '';
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
			'href' => $this->url->link('setting/location', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		if (!isset($this->request->get['location_id'])) {
			$this->data['action'] = $this->url->link('setting/location/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('setting/location/update', 'token=' . $this->session->data['token'] .  '&location_id=' . $this->request->get['location_id'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('setting/location', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		if (isset($this->request->get['location_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$location_info = $this->model_setting_location->getLocation($this->request->get['location_id']);
		}
		
		$this->data['token'] = $this->session->data['token'];
		
		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} elseif (!empty($location_info)) {
			$this->data['name'] = $location_info['name'];
		} else {
			$this->data['name'] =   '';
		}
		
		if (isset($this->request->post['address_1'])) {
			$this->data['address_1'] = $this->request->post['address_1'];
		} elseif (!empty($location_info)) {
			$this->data['address_1'] = $location_info['address_1'];
		} else {
			$this->data['address_1'] = '';
		}
		
		if (isset($this->request->post['address_2'])) {
			$this->data['address_2'] = $this->request->post['address_2'];
		} elseif (!empty($location_info)) {
			$this->data['address_2'] = $location_info['address_2'];
		} else {
			$this->data['address_2'] = '';
		}
		
		if (isset($this->request->post['city'])) {
			$this->data['city'] = $this->request->post['city'];
		} elseif (!empty($location_info)) {
			$this->data['city'] = $location_info['city'];
		} else {
			$this->data['city'] = '';
		}
		
		if (isset($this->request->post['postcode'])) {
			$this->data['postcode'] = $this->request->post['postcode'];
		} elseif (!empty($location_info)) {
			$this->data['postcode'] = $location_info['postcode'];
		} else {
			$this->data['postcode'] = '';
		}
		
		if (isset($this->request->post['country_id'])) {
			$this->data['country_id'] = $this->request->post['country_id'];
		} elseif (!empty($location_info)) {
			$this->data['country_id'] = $location_info['country_id'];
		} else {
			$this->data['country_id'] = '';
		}
		
		$this->load->model('localisation/country');
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();
				
		if (isset($this->request->post['zone_id'])) {
			$this->data['zone_id'] = $this->request->post['zone_id'];
		} elseif (!empty($location_info)) {
			$this->data['zone_id'] = $location_info['zone_id'];
		} else {
			$this->data['zone_id'] = '';
		}
				
		if (isset($this->request->post['geocode'])) {
			$this->data['geocode'] = $this->request->post['geocode'];
		} elseif (!empty($location_info)) {
			$this->data['geocode'] = $location_info['geocode'];
		} else {
			$this->data['geocode'] = '';
		}
		
		if (isset($this->request->post['image'])) {
			$this->data['image'] = $this->request->post['image'];
		} elseif (!empty($location_info)) {
			$this->data['image'] = $location_info['image'];
		} else {
			$this->data['image'] = '';
		}
		
		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && file_exists(DIR_IMAGE . $this->request->post['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($location_info) && $location_info['image'] && file_exists(DIR_IMAGE . $location_info['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($location_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}
		
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
				
		if (isset($this->request->post['open'])) {
			$this->data['open'] = $this->request->post['open'];
		} elseif (!empty($location_info)) {
			$this->data['open']  =   $location_info['open'];
		} else {
			$this->data['open']    =   '';
		}
		
		if (isset($this->request->post['comment'])) {
			$this->data['comment'] = $this->request->post['comment'];
		} elseif (!empty($location_info)) {
			$this->data['comment'] = $location_info['comment'];
		} else {
			$this->data['comment'] = '';
		}
		
		$this->template = 'setting/location_form.tpl';   //  Remember to create this template!!
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'setting/location')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
			$this->error['address_1'] = $this->language->get('error_address_1');
		}
		
		if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
			$this->error['city'] = $this->language->get('city');
		}
		
		$this->load->model('localisation/country');
		
		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
		
		if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}
		
		if ($this->request->post['country_id'] == '') {
			$this->error['country'] = $this->language->get('error_country');
		}
		
		if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
			$this->error['zone'] = $this->language->get('error_zone');
		}
				
		if (!$this->request->post['geocode']) {
			$this->error['geocode'] = $this->language->get('error_geocode');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'setting/location')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function country() {
		$json = array();
		
		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);
		
		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>