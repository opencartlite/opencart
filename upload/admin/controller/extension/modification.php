<?php
class ControllerExtensionModification extends Controller {
	private $error = array();

	public function index() {
		$this->data += $this->language->load('extension/modification');
	
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/modification');
		
		$this->getList();
	}
		
	public function insert() {
		$this->data += $this->language->load('extension/modification');
	
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/modification');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_modification->addModification($this->request->post);
		  	
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
						
			$this->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
	
		$this->getForm();
	}

	public function update() {
		$this->data += $this->language->load('extension/modification');
	
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/modification');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
	  		$this->model_setting_modification->editModification($this->request->get['modification_id'], $this->request->post);
			
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
			
			$this->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
	
		$this->getForm();
	}

	public function delete() {
		$this->data += $this->language->load('extension/modification');
	
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/modification');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $modification_id) {
				$this->model_setting_modification->deleteModification($modification_id);
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
			
			$this->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
	
		$this->getList();
	}
	
	public function install() {
		$this->data += $this->language->load('extension/payment');
		
		if (!$this->user->hasPermission('modify', 'extension/payment')) {
			$this->session->data['error'] = $this->language->get('error_permission');
			
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		} else {
			$this->load->model('setting/extension');
		
			$this->model_setting_extension->install('payment', $this->request->get['extension']);

			$this->load->model('user/user_group');
		
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'payment/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'payment/' . $this->request->get['extension']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
	
	public function uninstall() {
		$this->data += $this->language->load('extension/payment');
		
		if (!$this->user->hasPermission('modify', 'extension/payment')) {
			$this->session->data['error'] = $this->language->get('error_permission');
			
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		} else {
			$this->load->model('setting/extension');
			$this->load->model('setting/setting');
				
			$this->model_setting_extension->uninstall('payment', $this->request->get['extension']);
		
			$this->model_setting_setting->deleteSetting($this->request->get['extension']);
		
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
	
	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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
						
		$this->data += $this->language->load('extension/modification');
		 
		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
		);

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/modification', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
				
		$this->data['insert'] = $this->url->link('extension/modification/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('extension/modification/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['modifications'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$modification_total = $this->model_setting_modification->getTotalModifications();
	
		$results = $this->model_setting_modification->getModifications($data);

		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('extension/modification/update', 'token=' . $this->session->data['token'] . '&modification_id=' . $result['modification_id'] . $url, 'SSL')
			);
						
			$this->data['modifications'][] = array(
				'modification_id' => $result['modification_id'],
				'name'            => $result['name'],
				'author'          => $result['author'],
				'date_added'      => $result['date_added'],
				'date_modified'   => $result['date_modified'],
				'selected'        => isset($this->request->post['selected']) && in_array($result['modification_id'], $this->request->post['selected']),
				'action'          => $action
			);
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];
		
			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
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
		
		$this->data['sort_name'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$this->data['sort_author'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'] . '&sort=author' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');
		$this->data['sort_date_modified'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'] . '&sort=date_modified' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $modification_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('extension/modification', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		
		$this->template = 'extension/modification_list.tpl';
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
			'href' => $this->url->link('catalog/attribute', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		if (!isset($this->request->get['modification_id'])) {
			$this->data['action'] = $this->url->link('extension/modification/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('extension/modification/update', 'token=' . $this->session->data['token'] . '&modification_id=' . $this->request->get['modification_id'] . $url, 'SSL');
		}
			
		$this->data['cancel'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['modification_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$modification_info = $this->model_extension_modification->getModification($this->request->get['modification_id']);
		}
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->template = 'extension/modification_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/modification')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/modification')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
	}
			
	public function upload() {
/*
New XML Modifcation Standard

<modification>
	<id><![CDATA[Test]]></id>
	<version><![CDATA[1.0]]></version>
	<author><![CDATA[http://www.opencart.com]]></author>
	<file name="catalog/controller/product/product.php" error="log|skip|abort">
		<operation>
			<search index="1" error="log|skip|abort"><![CDATA[
			
			code
			
			]]></search>
			
			<add position="replace|before|after"><![CDATA[
			
			code
			
			]]></add>
		</operation>
	</file>
</modification>
*/
		
		$this->data += $this->language->load('extension/manager');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'extension/manager')) {
			$json['error'] = $this->language->get('error_permission') . "\n";
		}
		
		if (!empty($this->request->files['file']['name'])) {
			if (strrchr($this->request->files['file']['name'], '.') != '.zip') {
				$json['error'] = $this->language->get('error_filetype');
			}
					
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}
	
		if (!isset($json['error']) && is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
			// Unzip the files
			$file = $this->request->files['file']['tmp_name'];
			$directory = dirname($this->request->files['file']['tmp_name']) . '/' . basename($this->request->files['file']['name'], '.zip') . '/';
	
			$zip = new ZipArchive();
			$zip->open($file);
			$zip->extractTo($directory);
			$zip->close();
			
			// Remove Zip
			unlink($file);
			
			// Get a list of files ready to upload
			$files = array();
			
			$path = array($directory . '*');
			
			while(count($path) != 0) {
				$next = array_shift($path);
		
				foreach(glob($next) as $file) {
					if (is_dir($file)) {
						$path[] = $file . '/*';
					}
					
					$files[] = $file;
				}
			}
			
			sort($files);
					
			// Connect to the site via FTP
			$connection = ftp_connect($this->config->get('config_ftp_host'), $this->config->get('config_ftp_port'));
	
			if (!$connection) {
				exit($this->language->get('error_ftp_connection') . $this->config->get('config_ftp_host') . ':' . $this->config->get('config_ftp_port')) ;
			}
			
			$login = ftp_login($connection, $this->config->get('config_ftp_username'), $this->config->get('config_ftp_password'));
			
			if (!$login) {
				exit('Couldn\'t connect as ' . $this->config->get('config_ftp_username'));
			}
			
			if ($this->config->get('config_ftp_root')) {
				$root = ftp_chdir($connection, $this->config->get('config_ftp_root'));
				
				if (!$root) {
					exit('Couldn\'t change to directory ' . $this->config->get('config_ftp_root'));
				}
			}
		
			foreach ($files as $file) {
				// Upload everything in the upload directory
				if (substr(substr($file, strlen($directory)), 0, 7) == 'upload/') {
					$destination = substr(substr($file, strlen($directory)), 7);
					
					if (is_dir($file)) {
						$list = ftp_nlist($connection, substr($destination, 0, strrpos($destination, '/')));
						
						if (!in_array($destination, $list)) {
							if (ftp_mkdir($connection, $destination)) {
								echo 'Made directory ' . $destination . '<br />';
							}
						}
					}
					
					if (is_file($file)) {
						if (ftp_put($connection, $destination, $file, FTP_ASCII)) {
							echo 'Successfully uploaded ' . $file . '<br />';
						}
					}
				} elseif (strrchr(basename($file), '.') == '.sql') {
					$sql = file_get_contents($file);
				
				
				} elseif (strrchr(basename($file), '.') == '.xml') {
					$xml = file_get_contents($file);
					
				}
			}
			
			ftp_close($connection);
			
			rsort($files);
						
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				} elseif (is_dir($file)) {
					rmdir($file);
				}
			}
			
			if (file_exists($directory)) {
				rmdir($directory);
			}
						
			$json['success'] = $this->language->get('text_success');
		}
		
		
		if (file_exists($file)) {
			$xml = file_get_contents($file);
			
			$this->parse($xml);
		} else {
			trigger_error('Error: Could not load modification ' . $file . '!');
			exit();
		}
		

					
		$this->response->setOutput(json_encode($json));
	}
	
	public function sql() {
		$query = '';

		foreach($lines as $line) {
			if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
				$query .= $line;

				if (preg_match('/;\s*$/', $line)) {
					$query = str_replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" . $data['db_prefix'], $query);
					$query = str_replace("CREATE TABLE `oc_", "CREATE TABLE `" . $data['db_prefix'], $query);
					$query = str_replace("INSERT INTO `oc_", "INSERT INTO `" . $data['db_prefix'], $query);
					
					$result = mysql_query($query, $connection);

					if (!$result) {
						die(mysql_error());
					}

					$query = '';
				}
			}
		}
	}
	
	public function xml() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXml($xml);
		
		$files = array();
		
		$files = $dom->getElementsByTagName('modification')->item(0)->getElementsByTagName('file');
		

		
		foreach ($files as $file) {
		//	foreach () {
		//		$files = glob($file);
			
		//		if ($files) {
			
		//		}
		//	}

			$filename = $file->getAttribute('name');
		
			if (!isset($this->data[$filename])) {
				if (file_exists($filename)) {
					$content = file_get_contents($filename);
				} else {
					trigger_error('Error: Could not load language ' . $filename . '!');
					exit();
				}
			} else {
				$content = $this->data[$filename];
			}

			$operations = $file->getElementsByTagName('operation');
		
			foreach ($operations as $operation) {
				$search = $operation->getElementsByTagName('search')->item(0)->nodeValue;
				$index = $operation->getElementsByTagName('search')->item(0)->getAttribute('index');
				$add = $operation->getElementsByTagName('add')->item(0)->nodeValue;
				$position = $operation->getElementsByTagName('add')->item(0)->getAttribute('position');
					
				if (!$index) {
					$index = 1;
				}
				
				switch ($position) {
					default:
					case 'replace':
						$replace = $add;
						break;
					case 'before':
						$replace = $add . $search;
						break;
					case 'after':
						$replace = $search . $add;
						break;
				}

				$i = 0;
				$pos = -1;
				$result = array();

				while (($pos = strpos($content, $search, $pos + 1)) !== false) {
					$result[$i++] = $pos;
				}
				
				// Only replace the occurance of the string that is equal to the index
				if (isset($result[$index - 1])) {
					$content = substr_replace($content, $replace, $result[$index - 1], strlen($search));
				}
			}

			echo '<pre>';
			print_r($result);
			echo $content;
			echo '</pre>';
		}
	}
}
?>