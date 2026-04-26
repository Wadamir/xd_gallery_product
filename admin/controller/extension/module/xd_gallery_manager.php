<?php
class ControllerExtensionModuleXdGalleryManager extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->model('xd_gallery/image');
        $this->load->language('extension/module/xd_gallery_manager');

        $this->document->setTitle($this->language->get('page_title'));

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
            'href'      => $this->url->link('extension/module/xd_gallery_manager', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );

        $data['setting'] = $this->url->link('xd_gallery/setting', 'user_token=' . $this->session->data['user_token'], true);
        $data['image_manager'] = $this->url->link('xd_gallery/album', 'user_token=' . $this->session->data['user_token'], true);
        $data['album_manager'] = $this->url->link('xd_gallery/album', 'user_token=' . $this->session->data['user_token'], true);
        $data['video_manager'] = $this->url->link('xd_gallery/video', 'user_token=' . $this->session->data['user_token'], true);


        $data['action'] = $this->url->link('extension/module/xd_gallery_manager', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['modules'] = array();

        if (isset($this->request->post['xd_gallery_manager_module'])) {
            $data['modules'] = $this->request->post['xd_gallery_manager_module'];
        } elseif ($this->config->get('xd_gallery_manager_module')) {
            $data['modules'] = $this->config->get('xd_gallery_manager_module');
        }

        $this->load->model('xd_gallery/image');
        $data['total_album'] = $this->model_xd_gallery_image->getTotalAlbums();

        $this->load->model('xd_gallery/video');
        $data['total_video'] = $this->model_xd_gallery_video->getTotalVideos();

        $data['albums'] = array();

        $data_album = array(
            'start' => 0,
            'limit' => 6,
            'sort' => 'a.viewed',
            'order' => 'DESC',
        );

        $results = $this->model_xd_gallery_image->getAlbums($data_album);

        foreach ($results as $result) {

            $data['albums'][] = array(
                'name'        => $result['name'],
                'viewed'      => $result['viewed'],
                'href' => $this->url->link('xd_gallery/album/edit', 'user_token=' . $this->session->data['user_token'] . '&album_id=' . $result['album_id'], true)
            );
        }

        $data['videos'] = array();

        $data_video = array(
            'start' => 0,
            'limit' => 6,
            'sort' => 'v.viewed',
            'order' => 'DESC',
        );

        $results = $this->model_xd_gallery_video->getVideos($data_video);

        foreach ($results as $result) {

            $data['videos'][] = array(
                'name'        => $result['name'],
                'viewed'      => $result['viewed'],
                'href' => $this->url->link('xd_gallery/video/edit', 'user_token=' . $this->session->data['user_token'] . '&video_id=' . $result['video_id'], true)
            );
        }

        $this->load->model('design/layout');

        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/xd_gallery_manager', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/xd_gallery_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function install()
    {

        $this->load->model('xd_gallery/image');
        $this->model_xd_gallery_image->CreateDB();

        $this->load->model('design/layout');
        $layout_data = array(
            'name'            => $this->language->get('button_album_manager'),
            'layout_route'    => array(
                array(
                    'store_id'    => 0,
                    'route'        => 'gallery/album'
                )
            )
        );
        $this->model_design_layout->addLayout($layout_data);
        $layout_data2 = array(
            'name'            => $this->language->get('button_video_manager'),
            'layout_route'    => array(
                array(
                    'store_id'    => 0,
                    'route'        => 'gallery/video'
                )
            )
        );
        $this->model_design_layout->addLayout($layout_data2);

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_xd_gallery_manager', array('module_xd_gallery_manager_status' => 1));

        $this->db->query("
			INSERT INTO " . DB_PREFIX . "seo_url (`store_id`, `language_id`, `query`, `keyword`) VALUES
			(0, 1, 'gallery/album', 'photogallery'),
			(0, 1, 'gallery/video', 'videogallery'),
			(0, 2, 'gallery/album', 'photogallery2'),
			(0, 2, 'gallery/video', 'videogallery2'),
			(0, 3, 'gallery/album', 'photogallery3'),
			(0, 3, 'gallery/video', 'videogallery3');
			");
    }

    public function uninstall()
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query`='gallery/album'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query`='gallery/video'");
    }
}
