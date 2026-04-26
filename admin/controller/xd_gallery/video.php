<?php
class ControllerXdGalleryVideo extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('xd_gallery/video');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('xd_gallery/video');

        $this->getList();
    }

    public function insert()
    {
        $this->load->language('xd_gallery/video');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('xd_gallery/video');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_xd_gallery_video->addVideo($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('xd_gallery/video', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit()
    {
        $this->load->language('xd_gallery/video');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('xd_gallery/video');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_xd_gallery_video->editVideo($this->request->get['video_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('xd_gallery/video', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->load->language('xd_gallery/video');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('xd_gallery/video');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $video_id) {
                $this->model_xd_gallery_video->deleteVideo($video_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('xd_gallery/video', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList()
    {
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('page_title'),
            'href'      => $this->url->link('xd_gallery/video', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'separator' => ' :: '
        );

        $data['xd_gallery_manager'] = $this->url->link('extension/module/xd_gallery_manager', 'user_token=' . $this->session->data['user_token'], true);

        $data['insert'] = $this->url->link('xd_gallery/video/insert', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('xd_gallery/video/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['videos'] = array();

        $data_videos = array(
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $video_total = $this->model_xd_gallery_video->getTotalVideos();

        $results = $this->model_xd_gallery_video->getVideos($data_videos);

        $this->load->model('tool/image');

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('xd_gallery/video/edit', 'user_token=' . $this->session->data['user_token'] . '&video_id=' . $result['video_id'] . $url, true)
            );

            if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 80, 80);
            } else {
                $image = $this->model_tool_image->resize('no_image.jpg', 80, 80);
            }

            $data['videos'][] = array(
                'video_id' => $result['video_id'],
                'name'        => $result['name'],
                'image'        => $image,
                'sort_order'  => $result['sort_order'],
                'selected'    => isset($this->request->post['selected']) && in_array($result['video_id'], $this->request->post['selected']),
                'edit'        => $this->url->link('xd_gallery/video/edit', 'user_token=' . $this->session->data['user_token'] . '&video_id=' . $result['video_id'] . $url, true),
                'delete'      => $this->url->link('xd_gallery/video/delete', 'user_token=' . $this->session->data['user_token'] . '&video_id=' . $result['video_id'] . $url, true)
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['column_name'] = $this->language->get('column_name');
        $data['column_image'] = $this->language->get('column_image');
        $data['column_sort_order'] = $this->language->get('column_sort_order');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_edit'] = $this->language->get('button_edit');

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

        $url = '';

        $pagination = new Pagination();
        $pagination->total = $video_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('xd_gallery/video', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($video_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($video_total - $this->config->get('config_limit_admin'))) ? $video_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $video_total, ceil($video_total / $this->config->get('config_limit_admin')));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('xd_gallery/video_list', $data));
    }

    protected function getForm()
    {
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form'] = !isset($this->request->get['video_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_image_manager'] = $this->language->get('text_image_manager');
        $data['text_browse'] = $this->language->get('text_browse');
        $data['text_clear'] = $this->language->get('text_clear');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_percent'] = $this->language->get('text_percent');
        $data['text_amount'] = $this->language->get('text_amount');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_keyword'] = $this->language->get('entry_keyword');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_data'] = $this->language->get('tab_data');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('page_title'),
            'href'      => $this->url->link('xd_gallery/video', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['video_id'])) {
            $data['action'] = $this->url->link('xd_gallery/video/insert', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('xd_gallery/video/edit', 'user_token=' . $this->session->data['user_token'] . '&video_id=' . $this->request->get['video_id'], true);
        }

        $data['cancel'] = $this->url->link('xd_gallery/video', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->request->get['video_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $video_info = $this->model_xd_gallery_video->getVideo($this->request->get['video_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['video_description'])) {
            $data['video_description'] = $this->request->post['video_description'];
        } elseif (isset($this->request->get['video_id'])) {
            $data['video_description'] = $this->model_xd_gallery_video->getVideoDescriptions($this->request->get['video_id']);
        } else {
            $data['video_description'] = array();
        }

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['video_store'])) {
            $data['video_store'] = $this->request->post['video_store'];
        } elseif (isset($this->request->get['video_id'])) {
            $data['video_store'] = $this->model_xd_gallery_video->getVideoStores($this->request->get['video_id']);
        } else {
            $data['video_store'] = array(0);
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($video_info)) {
            $data['image'] = $video_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($video_info) && is_file(DIR_IMAGE . $video_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($video_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $this->load->model('xd_gallery/video');

        if (isset($this->request->post['gallery_seo_url'])) {
            $data['gallery_seo_url'] = $this->request->post['gallery_seo_url'];
        } elseif (isset($this->request->get['video_id'])) {
            $data['gallery_seo_url'] = $this->model_xd_gallery_video->getGallerySeoUrls($this->request->get['video_id']);
        } else {
            $data['gallery_seo_url'] = array();
        }

        if (isset($this->request->post['video'])) {
            $data['video'] = $this->request->post['video'];
        } elseif (!empty($video_info)) {
            $data['video'] = $video_info['video'];
        } else {
            $data['video'] = '';
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($video_info)) {
            $data['sort_order'] = $video_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($video_info)) {
            $data['status'] = $video_info['status'];
        } else {
            $data['status'] = 1;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('xd_gallery/video_form', $data));
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'xd_gallery/video')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['video_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
                $this->error['name'][$language_id] = $this->language->get('error_name');
            }
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

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'xd_gallery/video')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('xd_gallery/video');

            $data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 20
            );

            $results = $this->model_xd_gallery_video->getVideos($data);

            foreach ($results as $result) {
                $json[] = array(
                    'video_id' => $result['video_id'],
                    'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->setOutput(json_encode($json));
    }
}
