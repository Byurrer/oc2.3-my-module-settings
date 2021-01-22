<?php

class ControllerExtensionModuleMyModuleSettings extends Controller
{
    //страница настроек модуля
		public function index() 
		{
			//загрузка файла перевода модуля
			$this->load->language('extension/module/mymodulesettings');
			
			//установка title страницы
			$this->document->setTitle($this->language->get('doc_title'));
			
			//загрузка модели настроек
			$this->load->model('setting/setting');
			
			//создаем пустой массив, позже заполним его данными для шаблона
			$data = [];

			if($this->request->server['REQUEST_METHOD'] == 'POST')
			{
				//если валидация прошла успешно
				if($this->validate())
				{
					//записываем настройку "только для чтения" значением по умолчанию 
					$this->request->post["mymodulesettings_readonly"] = $this->config->get('mymodulesettings_readonly');

					//сохранение настроек модуля
					$this->model_setting_setting->editSetting('mymodulesettings', $this->request->post);
						
					//записываем в сессию статус успеха сохранения настроек
					$this->session->data['settings_success'] = $this->language->get('settings_success');
				}
				//валидация закончилась ошибкой, запишем информацию об этом в сессию
				else
					$this->session->data['settings_error'] = $this->m_aErrors;
		
				//перенаправляем на страницу настроек модуля 
				$this->response->redirect($this->url->link('extension/module/mymodulesettings', 'token=' . $this->session->data['token'] . '&type=module', true));
			}

			//если было успешное изменение настроек - показываем сообщение и удаляем из сессии чтобы больше не показывать
			if(array_key_exists("settings_success", $this->session->data))
			{
				$data['settings_success'] = $this->language->get('settings_success');
				unset($this->session->data["settings_success"]);
			}
			else
				$data['settings_success'] = false;
			
			//если есть ошибки - показываем и удаляем из сессии чтобы больше не показывать
			if(array_key_exists("settings_error", $this->session->data))
			{
				$data['error_warning'] = implode("<br/>", $this->session->data["settings_error"]);
				unset($this->session->data["settings_error"]);
			}
			else
				$data['error_warning'] = false;

			//загрузка представления головной части страницы
			$data['header'] = $this->load->controller('common/header');
			
			//загрузка сайдбара
			$data['column_left'] = $this->load->controller('common/column_left');
			
			//загрузка подвала админки
			$data['footer'] = $this->load->controller('common/footer');
			
			//заголовок h1 (но не title)
			$data['heading_title'] = $this->language->get('heading_title');
			
			$data['button_save'] = $this->language->get('button_save');
			$data['settings_edit'] = $this->language->get('settings_edit');

			//плейхолдеры для настроек
			$data['entry_setting1'] = $this->language->get('entry_setting1');
			$data['entry_setting2'] = $this->language->get('entry_setting2');

			//получаем массив настроек модуля (ранее мы загружали модель setting/setting)
			$aModuleInfo = $this->model_setting_setting->getSetting("mymodulesettings");
			
			$data['mymodulesettings_setting1'] = $aModuleInfo["mymodulesettings_setting1"];
			$data['mymodulesettings_setting2'] = $aModuleInfo["mymodulesettings_setting2"];

			$data['breadcrumbs'] = [];
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			];
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link('marketplace/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
			];
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/mymodulesettings', 'token=' . $this->session->data['token'], true)
			];

			if (!array_key_exists('module_id', $this->request->get)) {
				$data['action'] = $this->url->link('extension/module/mymodulesettings', 'token=' . $this->session->data['token'], true);
			} else {
				$data['action'] = $this->url->link('extension/module/mymodulesettings', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true);
			}

			$this->response->setOutput($this->load->view('extension/module/mymodulesettings', $data));
		}
     
    //установка модуля
		public function install() 
		{
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('mymodulesettings', [
				'mymodulesettings_setting1' => '',
				'mymodulesettings_setting2' => '',
				'mymodulesettings_readonly' => 'dsgf5'
			]);
		}
     
    //деинсталяция модуля
		public function uninstall() 
		{
			$this->load->model('setting/setting');
			$this->model_setting_setting->deleteSetting('mymodulesettings');
		}
     
    //валидация настроек модуля
		protected function validate() 
		{
			return true;
		}
     
    //линейный массив с ошибками
    private $m_aErrors = [];
};
