<?php
class ControllerExtensionModuleXdGalleryVideo extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/xd_gallery_video');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('xd_gallery_video', $this->request->post);
            $this->model_setting_setting->editSetting('module_xd_gallery_video', array('module_xd_gallery_video_status' => $this->request->post['xd_gallery_video_status']));

            $this->cache->delete('product');

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['image'])) {
            $data['error_image'] = $this->error['image'];
        } else {
            $data['error_image'] = array();
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('page_title'),
            'href'      => $this->url->link('extension/module/xd_gallery_video', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('extension/module/xd_gallery_video', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('xd_gallery/video');

        if (isset($this->request->post['xd_gallery_video_featured'])) {
            $videos = explode(',', $this->request->post['xd_gallery_video_featured']);
        } else {
            $videos = explode(',', $this->config->get('xd_gallery_video_featured'));
        }

        $data['videos'] = array();

        foreach ($videos as $video_id) {
            $video_id = (int)$video_id;

            if ($video_id <= 0) {
                continue;
            }

            $video_info = $this->model_xd_gallery_video->getVideo($video_id);

            if ($video_info) {
                $data['videos'][] = array(
                    'video_id'   => $video_info['video_id'],
                    'name'       => $video_info['name']
                );
            }
        }

        if (isset($this->request->post['xd_gallery_video_status'])) {
            $data['xd_gallery_video_status'] = $this->request->post['xd_gallery_video_status'];
        } else {
            $data['xd_gallery_video_status'] = $this->config->get('xd_gallery_video_status');
        }

        if (isset($this->request->post['xd_gallery_video_module'])) {
            $module = $this->request->post['xd_gallery_video_module'];
        } elseif ($this->config->has('xd_gallery_video_module')) {
            $module = $this->config->get('xd_gallery_video_module');
        } else {
            $module = array('limit' => '3', 'apr' => '3', 'vs' => '1', 'sb' => '1');
        }


        $data['xd_gallery_video_module'] = array(
            'limit'  => $module['limit'],
            'apr'    => $module['apr'],
            'vs'    => $module['vs'],
            'sb'    => $module['sb'],
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/xd_gallery_video', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/xd_gallery_video')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
