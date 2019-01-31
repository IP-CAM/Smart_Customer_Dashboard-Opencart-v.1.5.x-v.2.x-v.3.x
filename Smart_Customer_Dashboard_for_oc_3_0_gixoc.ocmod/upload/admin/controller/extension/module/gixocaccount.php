<?php
class ControllerExtensionModuleGixOCAccount extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/gixocaccount');

		$this->document->setTitle($this->language->get('text_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('gixocaccount', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_title'),
			'href' => $this->url->link('extension/module/gixocaccount', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/gixocaccount', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['gixocaccount_status'])) {
			$data['gixocaccount_status'] = $this->request->post['gixocaccount_status'];
		} else {
			$data['gixocaccount_status'] = $this->config->get('gixocaccount_status');
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		// Groups customer
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && !isset($this->request->post['gixocaccount_groups_customer'])) {
			$data['gixocaccount_groups_customer'] = array();
		} elseif ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['gixocaccount_groups_customer'])) {
			$data['gixocaccount_groups_customer'] = $this->request->post['gixocaccount_groups_customer'];
		} elseif ($this->config->get('gixocaccount_groups_customer')) {
			$data['gixocaccount_groups_customer'] = $this->config->get('gixocaccount_groups_customer');
		} else {
			$data['gixocaccount_groups_customer'] = array();
		}
		// End groups customer

		// El customer
		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 70, 70);

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && !isset($this->request->post['gixocaccount_el_customer'])) {
			$data['gixocaccount_el_customer'] = array();
		} elseif ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['gixocaccount_el_customer'])) {
			$results = $this->request->post['gixocaccount_el_customer'];

			foreach ($results as $result) {
				if (is_file(DIR_IMAGE . $result['image'])) {
					$image = $result['image'];
					$thumb = $this->model_tool_image->resize($result['image'], 70, 70);
				} else {
					$image = 'no_image.png';
					$thumb = $this->model_tool_image->resize('no_image.png', 70, 70);
				};

				$data['gixocaccount_el_customer'][] = array(
					'customer_group_id'  => $result['customer_group_id'],
					'name'               => $result['name'],
					'sort_order'         => $result['sort_order'],
					'url'                => $result['url'],
					'image'              => $image,
					'image_width'        => $result['image_width'],
					'image_height'       => $result['image_height'],
					'thumb'              => $thumb,
					'class_el'           => $result['class_el'],
					'color'              => $result['color'],
					'color_hover'        => $result['color_hover'],
					'font'               => $result['font'],
					'font_hover'         => $result['font_hover'],
					'status'             => $result['status']
				);
			}
		} elseif ($this->config->get('gixocaccount_el_customer')) {
			$results = $this->config->get('gixocaccount_el_customer');

			foreach ($results as $result) {
				if (is_file(DIR_IMAGE . $result['image'])) {
					$image = $result['image'];
					$thumb = $this->model_tool_image->resize($result['image'], 70, 70);
				} else {
					$image = 'no_image.png';
					$thumb = $this->model_tool_image->resize('no_image.png', 70, 70);
				};

				$data['gixocaccount_el_customer'][] = array(
					'customer_group_id'  => $result['customer_group_id'],
					'name'               => $result['name'],
					'sort_order'         => $result['sort_order'],
					'url'                => $result['url'],
					'image'              => $image,
					'image_width'        => $result['image_width'],
					'image_height'       => $result['image_height'],
					'thumb'              => $thumb,
					'class_el'           => $result['class_el'],
					'color'              => $result['color'],
					'color_hover'        => $result['color_hover'],
					'font'               => $result['font'],
					'font_hover'         => $result['font_hover'],
					'status'             => $result['status']
				);
			}
		} else {
			$data['gixocaccount_el_customer'] = array();
		}
		// End el customer

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$data['domain'] = HTTPS_CATALOG;
		} else {
			$data['domain'] = HTTP_CATALOG;
		}

		$data['language_id'] = $this->config->get('config_language_id');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/gixocaccount', $data));
	}

	public function install(){
		$this->load->model('setting/setting');

		$gixocaccount_groups_customer = json_decode('[{"id":"0","name":{"1":"\u041c\u043e\u044f \u0443\u0447\u0435\u0442\u043d\u0430\u044f \u0437\u0430\u043f\u0438\u0441\u044c        ","2":"My Account        "},"class_el":"h2","sort_order":"1","color":"#000000","color_hover":"#000000","status":"1","font":"27","font_hover":"28"},{"id":"1","name":{"1":"\u041c\u043e\u0438 \u0437\u0430\u043a\u0430\u0437\u044b        ","2":"My Orders        "},"class_el":"h2","sort_order":"2","color":"#000000","color_hover":"#000000","status":"1","font":"27","font_hover":"28"},{"id":"2","name":{"1":"\u041f\u043e\u0434\u043f\u0438\u0441\u043a\u0430        ","2":"Newsletter        "},"class_el":"h2","sort_order":"4","color":"#000000","color_hover":"#000000","status":"1","font":"27","font_hover":"28"},{"id":"3","name":{"1":"\u041c\u043e\u0439 \u043f\u0430\u0440\u0442\u043d\u0435\u0440\u0441\u043a\u0438\u0439 \u0430\u043a\u043a\u0430\u0443\u043d\u0442     ","2":"My Affiliate Account     "},"class_el":"h2","sort_order":"3","color":"#000000","color_hover":"#000000","status":"0","font":"27","font_hover":"28"}]');
		$gixocaccount_el_customer = json_decode('[{"id":"0","name":{"1":"\u0418\u0441\u0442\u043e\u0440\u0438\u044f \u0437\u0430\u043a\u0430\u0437\u043e\u0432        ","2":"View your order history        "},"customer_group_id":"1","sort_order":"1","image":"catalog\/gixocscd\/skin1\/order.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/order","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"1","name":{"1":"\u0424\u0430\u0439\u043b\u044b \u0434\u043b\u044f \u0441\u043a\u0430\u0447\u0438\u0432\u0430\u043d\u0438\u044f        ","2":"Downloads        "},"customer_group_id":"1","sort_order":"2","image":"catalog\/gixocscd\/skin1\/download.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/download","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"2","name":{"1":"\u0411\u043e\u043d\u0443\u0441\u043d\u044b\u0435 \u0431\u0430\u043b\u043b\u044b        ","2":"Your Reward Points        "},"customer_group_id":"1","sort_order":"3","image":"catalog\/gixocscd\/skin1\/reward.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/reward","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"3","name":{"1":"\u0417\u0430\u044f\u0432\u043a\u0438 \u043d\u0430 \u0432\u043e\u0437\u0432\u0440\u0430\u0442        ","2":"View your return requests        "},"customer_group_id":"1","sort_order":"4","image":"catalog\/gixocscd\/skin1\/return.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/return","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"4","name":{"1":"\u0418\u0441\u0442\u043e\u0440\u0438\u044f \u043f\u043b\u0430\u0442\u0435\u0436\u0435\u0439        ","2":"Your Transactions        "},"customer_group_id":"1","sort_order":"5","image":"catalog\/gixocscd\/skin1\/transaction.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/transaction","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"5","name":{"1":"\u0420\u0435\u0433\u0443\u043b\u044f\u0440\u043d\u044b\u0435 \u043f\u043b\u0430\u0442\u0435\u0436\u0438        ","2":"Recurring payments        "},"customer_group_id":"1","sort_order":"6","image":"catalog\/gixocscd\/skin1\/recurring.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/recurring","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"6","name":{"1":"\u041e\u0441\u043d\u043e\u0432\u043d\u044b\u0435 \u0434\u0430\u043d\u043d\u044b\u0435        ","2":"Edit your account information        "},"customer_group_id":"0","sort_order":"1","image":"catalog\/gixocscd\/skin1\/edit.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/edit","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"7","name":{"1":"\u0418\u0437\u043c\u0435\u043d\u0438\u0442\u044c \u0441\u0432\u043e\u0439 \u043f\u0430\u0440\u043e\u043b\u044c        ","2":"Change your password        "},"customer_group_id":"0","sort_order":"2","image":"catalog\/gixocscd\/skin1\/password.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/password","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"8","name":{"1":"\u0418\u0437\u043c\u0435\u043d\u0438\u0442\u044c \u043c\u043e\u0438 \u0430\u0434\u0440\u0435\u0441\u0430        ","2":"Modify your address book entries        "},"customer_group_id":"0","sort_order":"3","image":"catalog\/gixocscd\/skin1\/address.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/address","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"9","name":{"1":"\u0418\u0437\u043c\u0435\u043d\u0438\u0442\u044c \u0437\u0430\u043a\u043b\u0430\u0434\u043a\u0438        ","2":"Modify your wish list        "},"customer_group_id":"0","sort_order":"4","image":"catalog\/gixocscd\/skin1\/wishlist.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/wishlist","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"10","name":{"1":"\u0420\u0435\u0434\u0430\u043a\u0442\u0438\u0440\u043e\u0432\u0430\u0442\u044c \u043f\u043e\u0434\u043f\u0438\u0441\u043a\u0443        ","2":"Subscribe \/ unsubscribe to newsletter        "},"customer_group_id":"2","sort_order":"1","image":"catalog\/gixocscd\/skin1\/newsletter.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/newsletter","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"11","name":{"1":"\u0420\u0435\u0433\u0438\u0441\u0442\u0440\u0430\u0446\u0438\u044f \u043f\u0430\u0440\u0442\u043d\u0435\u0440\u0441\u043a\u043e\u0433\u043e \u0430\u043a\u043a\u0430\u0443\u043d\u0442\u0430    ","2":"Register for an affiliate account    "},"customer_group_id":"3","sort_order":"1","image":"catalog\/gixocscd\/skin1\/password.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/affiliate\/add","color_hover":"#000000","status":"0","font":"14","font_hover":"15"},{"id":"12","name":{"1":"\u0418\u0437\u043c\u0435\u043d\u0438\u0442\u044c \u043f\u0430\u0440\u0442\u043d\u0435\u0440\u0441\u043a\u0443\u044e \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0438\u044e   ","2":"Edit your affiliate information   "},"customer_group_id":"3","sort_order":"1","image":"catalog\/gixocscd\/skin1\/edit.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/affiliate\/edit","color_hover":"#000000","status":"1","font":"14","font_hover":"15"},{"id":"13","name":{"1":"\u041a\u043e\u0434 \u043e\u0442\u0441\u043b\u0435\u0436\u0438\u0432\u0430\u043d\u0438\u044f \u043f\u0430\u0440\u0442\u043d\u0435\u0440\u0441\u043a\u0438\u0445 \u043e\u0442\u043d\u043e\u0448\u0435\u043d\u0438\u0439  ","2":"Custom Affiliate Tracking Code  "},"customer_group_id":"3","sort_order":"2","image":"catalog\/gixocscd\/skin1\/transaction.png","image_width":"64","image_height":"64","class_el":"col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center","color":"#000000","url":"index.php?route=account\/tracking","color_hover":"#000000","status":"1","font":"14","font_hover":"15"}]');

		$setting = array(
			'gixocaccount_status'           => 0,
			'gixocaccount_groups_customer'  => $gixocaccount_groups_customer,
			'gixocaccount_el_customer'      => $gixocaccount_el_customer
		);
		$this->model_setting_setting->editSetting('gixocaccount', $setting, 0);
		
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/gixocaccount');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/gixocaccount');
	}

	public function uninstall(){
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('gixocaccount');
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/gixocaccount')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	} 
}