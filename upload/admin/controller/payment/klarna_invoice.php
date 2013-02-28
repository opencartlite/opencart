<?php
class ControllerPaymentKlarnaInvoice extends Controller {
    private $error = array();

    public function index() {
		$this->data += $this->language->load('payment/klarna_invoice');
        
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$status = false;
			
			foreach ($this->request->post['klarna_invoice'] as $klarna_invoice) {
				if ($klarna_invoice['status']) {
					$status = true;
					
					break;
				}
			}			
			
			$data = array(
				'klarna_invoice_pclasses' => $this->pclasses,
				'klarna_invoice_status'   => $status
			);
			
			$this->model_setting_setting->editSetting('klarna_invoice', array_merge($this->request->post, $data));
			
			$this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }			
				       
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
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/klarna_invoice', 'token=' . $this->session->data['token'], 'SSL')
        );
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
        $this->data['action'] = $this->url->link('payment/klarna_invoice', 'token=' . $this->session->data['token'], 'SSL');
       
	    $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['countries'] = array();
		
		$this->data['countries'][] = array(
			'name' => $this->language->get('text_germany'),
			'code' => 'DEU'
		);
		
		$this->data['countries'][] = array(
			'name' => $this->language->get('text_netherlands'),
			'code' => 'NLD'
		);
		
		$this->data['countries'][] = array(
			'name' => $this->language->get('text_denmark'),
			'code' => 'DNK'
		);
		
		$this->data['countries'][] = array(
			'name' => $this->language->get('text_sweden'),
			'code' => 'SWE'
		);
		
		$this->data['countries'][] = array(
			'name' => $this->language->get('text_norway'),
			'code' => 'NOR'
		);
		
		$this->data['countries'][] = array(
			'name' => $this->language->get('text_finland'),
			'code' => 'FIN'
		);

		if (isset($this->request->post['klarna_invoice'])) {
			$this->data['klarna_invoice'] = $this->request->post['klarna_invoice'];
		} else {
			$this->data['klarna_invoice'] = $this->config->get('klarna_invoice');
		}
		
		$this->load->model('localisation/geo_zone');
			
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$this->load->model('localisation/order_status');
			
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        
		$file = DIR_LOGS . 'klarna_invoice.log';
        
        if (file_exists($file)) {
            $this->data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
        } else {
            $this->data['log'] = '';
        }
        
        $this->data['clear'] = $this->url->link('payment/klarna_invoice/clear', 'token=' . $this->session->data['token'], 'SSL'); 

        $this->template = 'payment/klarna_invoice.tpl';
        $this->children = array(
            'common/header',
            'common/footer',
        );

        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/klarna_invoice')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
				
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
    
    private function parseResponse($node, $document) {
        $child = $node;

        switch ($child->nodeName) {
            case 'string':
                $value = $child->nodeValue;
                break;

            case 'boolean':
                $value = (string) $child->nodeValue;

                if ($value == '0') {
                    $value = false;
                } elseif ($value == '1') {
                    $value = true;
                } else {
                    $value = null;
                }

                break;

            case 'integer':
            case 'int':
            case 'i4':
            case 'i8':
                $value = (int) $child->nodeValue;
                break;

            case 'array':
                $value = array();
                
                $xpath = new DOMXPath($document);
                $entries = $xpath->query('.//array/data/value', $child);
                
                for ($i = 0; $i < $entries->length; $i++) {
                    $value[] = $this->parseResponse($entries->item($i)->firstChild, $document);
                }

                break;

            default:
                $value = null;
        }

        return $value;
    }
	
    public function clear() {
        $this->data += $this->language->load('payment/klarna_invoice');
		
		$file = DIR_LOGS . 'klarna_invoice.log';
		
		$handle = fopen($file, 'w+'); 
				
		fclose($handle); 
				
		$this->session->data['success'] = $this->language->get('text_success');
        
        $this->redirect($this->url->link('payment/klarna_invoice', 'token=' . $this->session->data['token'], 'SSL'));
    }    
}
?>