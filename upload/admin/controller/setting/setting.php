<?php
class ControllerSettingSetting extends Controller {
	private $error = array();
 
	public function index() {
		$this->data += $this->language->load('setting/setting'); 

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		$settings = array(
			'name',
			'owner',
			'address',
			'email',
			'telephone',
			'fax',
			'title',
			'meta_description',
			'layout_id',
			'template',
			'country_id',
			'zone_id',
			'language',
			'admin_language',
			'currency',
			'currency_auto',
			'length_class_id',
			'weight_class_id',
			'catalog_limit',
			'list_description_limit',
			'admin_limit',
			'product_count',
			'review_status',
			'guest_review',
			'download',
			'voucher_min',
			'voucher_max',
			'tax',
			'vat',
			'tax_default',
			'tax_customer',
			'customer_online',
			'customer_group_id',
			'customer_price',
			'account_id',
			'cart_weight',
			'guest_checkout',
			'checkout_id',
			'order_status_id',
			'complete_status_id',
			'stock_display',
			'stock_warning',
			'stock_checkout',
			'stock_status_id',
			'affiliate_id',
			'return_id',
			'return_status_id',
			'logo',
			'icon',
			'image_category_width',
			'image_category_height',
			'image_thumb_width',
			'image_thumb_height',
			'image_popup_width',
			'image_popup_height',
			'image_product_width',
			'image_product_height',
			'image_additional_width',
			'image_additional_height',
			'image_related_width',
			'image_related_height',
			'image_compare_width',
			'image_compare_height',
			'image_wishlist_width',
			'image_wishlist_height',
			'image_cart_width',
			'image_cart_height',
			'ftp_username',
			'ftp_password',
			'ftp_root',
			'ftp_status',
			'mail_protocol',
			'mail_parameter',
			'smtp_host',
			'smtp_username',
			'smtp_password',
			'alert_mail',
			'account_mail',
			'review_mail',
			'alert_emails',
			'fraud_detection',
			'fraud_key',
			'fraud_score',
			'fraud_status_id',
			'secure',
			'shared',
			'robots',
			'seo_url',
			'file_extension_allowed',
			'file_mime_allowed',
			'maintenance',
			'password',
			'encryption',
			'compression',
			'error_display',
			'error_log',
			'error_filename',
			'google_analytics',
		);
		
		$errors = array(
			'warning',
			'name',
			'owner',
			'address',
			'email',
			'telephone',
			'title',
			'customer_group_display',
			'voucher_min',
			'voucher_max',
			'ftp_host',
			'ftp_port',
			'ftp_username',
			'ftp_password',
			'image_category',
			'image_thumb',
			'image_popup',
			'image_product',
			'image_additional',
			'image_related',
			'image_compare',
			'image_wishlist',
			'image_cart',
			'error_filename',
			'catalog_limit',
			'list_description_limit',
			'admin_limit',
			'encryption',
		);
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('config', $this->request->post);

			if ($this->config->get('config_currency_auto')) {
				$this->load->model('localisation/currency');
		
				$this->model_localisation_currency->updateCurrencies();
			}	
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('setting/store', 'token=' . $this->session->data['token'], 'SSL'));
		}																			

		foreach($errors as $error) {
			$this->data['error_' . $error] = isset($this->error[$error]) ? $this->error[$error] : '';
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL')
   		);
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->url->link('setting/setting', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('setting/store', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['token'] = $this->session->data['token'];
		
		foreach($settings as $setting) {
			$key = 'config_' . $setting;
			$this->data[$key] = isset($this->request->post[$key]) ? $this->request->post[$key] : $this->config->get($key);
		}
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->data['templates'] = array();

		$directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);
		
		foreach ($directories as $directory) {
			$this->data['templates'][] = basename($directory);
		}
		
		$this->load->model('localisation/country');
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();

		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->load->model('localisation/currency');
		
		$this->data['currencies'] = $this->model_localisation_currency->getCurrencies();
		
		$this->load->model('localisation/length_class');
		
		$this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
		
		$this->load->model('localisation/weight_class');
		
		$this->data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
		
		$this->load->model('sale/customer_group');
		
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

		if (isset($this->request->post['config_customer_group_display'])) {
			$this->data['config_customer_group_display'] = $this->request->post['config_customer_group_display'];
		} elseif ($this->config->get('config_customer_group_display')) {
			$this->data['config_customer_group_display'] = $this->config->get('config_customer_group_display');	
		} else {
			$this->data['config_customer_group_display'] = array();			
		}
				
		$this->load->model('catalog/information');
		
		$this->data['informations'] = $this->model_catalog_information->getInformations();

		if (isset($this->request->post['config_order_edit'])) {
			$this->data['config_order_edit'] = $this->request->post['config_order_edit'];
		} elseif ($this->config->get('config_order_edit')) {
			$this->data['config_order_edit'] = $this->config->get('config_order_edit');			
		} else {
			$this->data['config_order_edit'] = 7;
		}
									
		if (isset($this->request->post['config_invoice_prefix'])) {
			$this->data['config_invoice_prefix'] = $this->request->post['config_invoice_prefix'];
		} elseif ($this->config->get('config_invoice_prefix')) {
			$this->data['config_invoice_prefix'] = $this->config->get('config_invoice_prefix');			
		} else {
			$this->data['config_invoice_prefix'] = 'INV-' . date('Y') . '-00';
		}	
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$this->load->model('localisation/stock_status');
		
		$this->data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
		
		if (isset($this->request->post['config_commission'])) {
			$this->data['config_commission'] = $this->request->post['config_commission'];
		} elseif ($this->config->has('config_commission')) {
			$this->data['config_commission'] = $this->config->get('config_commission');		
		} else {
			$this->data['config_commission'] = '5.00';
		}
		
		$this->load->model('localisation/return_status');
		
		$this->data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();	
			
		$this->load->model('tool/image');

		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo')) && is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$this->data['logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 100, 100);		
		} else {
			$this->data['logo'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}
		
		if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon')) && is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->data['icon'] = $this->model_tool_image->resize($this->config->get('config_icon'), 100, 100);		
		} else {
			$this->data['icon'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}
		
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);		
				
		if (isset($this->request->post['config_ftp_host'])) {
			$this->data['config_ftp_host'] = $this->request->post['config_ftp_host'];
		} elseif ($this->config->get('config_ftp_host')) {
			$this->data['config_ftp_host'] = $this->config->get('config_ftp_host');		
		} else {
			$this->data['config_ftp_host'] = str_replace('www.', '', $this->request->server['HTTP_HOST']);
		}
		
		if (isset($this->request->post['config_ftp_port'])) {
			$this->data['config_ftp_port'] = $this->request->post['config_ftp_port'];
		} elseif ($this->config->get('config_ftp_port')) {
			$this->data['config_ftp_port'] = $this->config->get('config_ftp_port');
		} else {
			$this->data['config_ftp_port'] = 21;
		}	
		
		if (isset($this->request->post['config_smtp_port'])) {
			$this->data['config_smtp_port'] = $this->request->post['config_smtp_port'];
		} elseif ($this->config->get('config_smtp_port')) {
			$this->data['config_smtp_port'] = $this->config->get('config_smtp_port');
		} else {
			$this->data['config_smtp_port'] = 25;
		}	
		
		if (isset($this->request->post['config_smtp_timeout'])) {
			$this->data['config_smtp_timeout'] = $this->request->post['config_smtp_timeout'];
		} elseif ($this->config->get('config_smtp_timeout')) {
			$this->data['config_smtp_timeout'] = $this->config->get('config_smtp_timeout');
		} else {
			$this->data['config_smtp_timeout'] = 5;	
		}
		
		if (isset($this->request->post['config_image_file_size'])) {
			$this->data['config_image_file_size'] = $this->request->post['config_image_file_size'];
		} elseif ($this->config->get('config_image_file_size')) {
			$this->data['config_image_file_size'] = $this->config->get('config_image_file_size');
		} else {
			$this->data['config_image_file_size'] = 300000;	
		}
						
		$this->template = 'setting/setting.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'setting/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['config_name']) {
			$this->error['name'] = $this->language->get('error_name');
		}	
		
		if ((utf8_strlen($this->request->post['config_owner']) < 3) || (utf8_strlen($this->request->post['config_owner']) > 64)) {
			$this->error['owner'] = $this->language->get('error_owner');
		}

		if ((utf8_strlen($this->request->post['config_address']) < 3) || (utf8_strlen($this->request->post['config_address']) > 256)) {
			$this->error['address'] = $this->language->get('error_address');
		}
		
    	if ((utf8_strlen($this->request->post['config_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['config_email'])) {
      		$this->error['email'] = $this->language->get('error_email');
    	}

    	if ((utf8_strlen($this->request->post['config_telephone']) < 3) || (utf8_strlen($this->request->post['config_telephone']) > 32)) {
      		$this->error['telephone'] = $this->language->get('error_telephone');
    	}

		if (!$this->request->post['config_title']) {
			$this->error['title'] = $this->language->get('error_title');
		}	
		
		if (!empty($this->request->post['config_customer_group_display']) && !in_array($this->request->post['config_customer_group_id'], $this->request->post['config_customer_group_display'])) {
			$this->error['customer_group_display'] = $this->language->get('error_customer_group_display');
		}	
		
		if (!$this->request->post['config_voucher_min']) {
			$this->error['voucher_min'] = $this->language->get('error_voucher_min');
		}	
		
		if (!$this->request->post['config_voucher_max']) {
			$this->error['voucher_max'] = $this->language->get('error_voucher_max');
		}	
								
		if (!$this->request->post['config_image_category_width'] || !$this->request->post['config_image_category_height']) {
			$this->error['image_category'] = $this->language->get('error_image_category');
		} 
				
		if (!$this->request->post['config_image_thumb_width'] || !$this->request->post['config_image_thumb_height']) {
			$this->error['image_thumb'] = $this->language->get('error_image_thumb');
		}	
		
		if (!$this->request->post['config_image_popup_width'] || !$this->request->post['config_image_popup_height']) {
			$this->error['image_popup'] = $this->language->get('error_image_popup');
		}	
		
		if (!$this->request->post['config_image_product_width'] || !$this->request->post['config_image_product_height']) {
			$this->error['image_product'] = $this->language->get('error_image_product');
		}
				
		if (!$this->request->post['config_image_additional_width'] || !$this->request->post['config_image_additional_height']) {
			$this->error['image_additional'] = $this->language->get('error_image_additional');
		}
		
		if (!$this->request->post['config_image_related_width'] || !$this->request->post['config_image_related_height']) {
			$this->error['image_related'] = $this->language->get('error_image_related');
		}
		
		if (!$this->request->post['config_image_compare_width'] || !$this->request->post['config_image_compare_height']) {
			$this->error['image_compare'] = $this->language->get('error_image_compare');
		}
		
		if (!$this->request->post['config_image_wishlist_width'] || !$this->request->post['config_image_wishlist_height']) {
			$this->error['image_wishlist'] = $this->language->get('error_image_wishlist');
		}			
		
		if (!$this->request->post['config_image_cart_width'] || !$this->request->post['config_image_cart_height']) {
			$this->error['image_cart'] = $this->language->get('error_image_cart');
		}
		
		if ($this->request->post['config_ftp_status']) {
			if (!$this->request->post['config_ftp_host']) {
				$this->error['ftp_host'] = $this->language->get('error_ftp_host');
			}
			
			if (!$this->request->post['config_ftp_port']) {
				$this->error['ftp_port'] = $this->language->get('error_ftp_port');
			}
			
			if (!$this->request->post['config_ftp_username']) {
				$this->error['ftp_username'] = $this->language->get('error_ftp_username');
			}	
			
			if (!$this->request->post['config_ftp_password']) {
				$this->error['ftp_password'] = $this->language->get('error_ftp_password');
			}											
		}
	
		if (!$this->request->post['config_error_filename']) {
			$this->error['error_filename'] = $this->language->get('error_error_filename');
		}
		
		if (!$this->request->post['config_catalog_limit']) {
			$this->error['catalog_limit'] = $this->language->get('error_limit');
		}
		
		if (!$this->request->post['config_list_description_limit']) {
			$this->error['list_description_limit'] = $this->language->get('error_limit');
		}
		
		if (!$this->request->post['config_admin_limit']) {
			$this->error['admin_limit'] = $this->language->get('error_limit');
		}
				
		if ((utf8_strlen($this->request->post['config_encryption']) < 3) || (utf8_strlen($this->request->post['config_encryption']) > 32)) {
			$this->error['encryption'] = $this->language->get('error_encryption');
		}
				
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
			
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function template() {
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}
		
		if (file_exists(DIR_IMAGE . 'templates/' . basename($this->request->get['template']) . '.png')) {
			$image = $server . 'image/templates/' . basename($this->request->get['template']) . '.png';
		} else {
			$image = $server . 'image/no_image.jpg';
		}
		
		$this->response->setOutput('<img src="' . $image . '" alt="" title="" style="border: 1px solid #EEEEEE;" />');
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