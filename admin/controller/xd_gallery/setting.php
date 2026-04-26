<?php
class ControllerXdGallerySetting extends Controller
{
    private $error = array();

    public function index()
    {

        $this->load->language('xd_gallery/setting');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting('og', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('xd_gallery/setting', 'user_token=' . $this->session->data['user_token'], true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form'] = $this->language->get('heading_title');

        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_image_manager'] = $this->language->get('text_image_manager');
        $data['text_browse'] = $this->language->get('text_browse');
        $data['text_clear'] = $this->language->get('text_clear');

        $data['entry_meta_length'] = $this->language->get('entry_meta_length');
        $data['entry_picture_type'] = $this->language->get('entry_picture_type');
        $data['entry_column_width'] = $this->language->get('entry_column_width');
        $data['entry_column_height'] = $this->language->get('entry_column_height');
        $data['entry_limit_category'] = $this->language->get('entry_limit_category');
        $data['entry_limit_per_row'] = $this->language->get('entry_limit_per_row');

        $data['text_rectangular'] = $this->language->get('text_rectangular');
        $data['text_square'] = $this->language->get('text_square');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_setting'] = $this->language->get('tab_setting');
        $data['tab_newspage'] = $this->language->get('tab_newspage');
        $data['tab_newscatpage'] = $this->language->get('tab_newscatpage');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('page_title'),
            'href'      => $this->url->link('xd_gallery/setting', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['xd_gallery_manager'] = $this->url->link('extension/module/xd_gallery_manager', 'user_token=' . $this->session->data['user_token'], true);

        $data['action'] = $this->url->link('xd_gallery/setting', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('extension/module/xd_gallery_manager', 'user_token=' . $this->session->data['user_token'], true);

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['og_sitemap'])) {
            $data['og_sitemap'] = $this->request->post['og_sitemap'];
        } elseif ($this->config->get('og_sitemap')) {
            $data['og_sitemap'] = $this->config->get('og_sitemap');
        } else {
            $data['og_sitemap'] = '0';
        }

        if (isset($this->request->post['og_heading_title_font'])) {
            $data['og_heading_title_font'] = $this->request->post['og_heading_title_font'];
        } elseif ($this->config->get('og_heading_title_font')) {
            $data['og_heading_title_font'] = $this->config->get('og_heading_title_font');
        } else {
            $data['og_heading_title_font'] = '27';
        }

        if (isset($this->request->post['og_heading_title_size'])) {
            $data['og_heading_title_size'] = $this->request->post['og_heading_title_size'];
        } elseif ($this->config->get('og_heading_title_size')) {
            $data['og_heading_title_size'] = $this->config->get('og_heading_title_size');
        } else {
            $data['og_heading_title_size'] = '18';
        }

        if (isset($this->request->post['og_heading_title_line'])) {
            $data['og_heading_title_line'] = $this->request->post['og_heading_title_line'];
        } elseif ($this->config->get('og_heading_title_line')) {
            $data['og_heading_title_line'] = $this->config->get('og_heading_title_line');
        } else {
            $data['og_heading_title_line'] = '5';
        }

        if (isset($this->request->post['og_title_font'])) {
            $data['og_title_font'] = $this->request->post['og_title_font'];
        } elseif ($this->config->get('og_heading_title_font')) {
            $data['og_title_font'] = $this->config->get('og_title_font');
        } else {
            $data['og_title_font'] = '27';
        }

        if (isset($this->request->post['og_title_size'])) {
            $data['og_title_size'] = $this->request->post['og_title_size'];
        } elseif ($this->config->get('og_title_size')) {
            $data['og_title_size'] = $this->config->get('og_title_size');
        } else {
            $data['og_title_size'] = '12';
        }

        if (isset($this->request->post['og_title_font_weight'])) {
            $data['og_title_font_weight'] = $this->request->post['og_title_font_weight'];
        } else {
            $data['og_title_font_weight'] = $this->config->get('og_title_font_weight');
        }

        if (isset($this->request->post['og_show_title'])) {
            $data['og_show_title'] = $this->request->post['og_show_title'];
        } elseif ($this->config->has('og_show_title')) {
            $data['og_show_title'] = $this->config->get('og_show_title');
        } else {
            $data['og_show_title'] = '1';
        }

        if (isset($this->request->post['og_title_position'])) {
            $data['og_title_position'] = $this->request->post['og_title_position'];
        } elseif ($this->config->has('og_title_position')) {
            $data['og_title_position'] = $this->config->get('og_title_position');
        } else {
            $data['og_title_position'] = 'center';
        }

        if (isset($this->request->post['og_show_media_title'])) {
            $data['og_show_media_title'] = $this->request->post['og_show_media_title'];
        } elseif ($this->config->has('og_show_media_title')) {
            $data['og_show_media_title'] = $this->config->get('og_show_media_title');
        } else {
            $data['og_show_media_title'] = '0';
        }

        if (isset($this->request->post['og_media_title_position'])) {
            $data['og_media_title_position'] = $this->request->post['og_media_title_position'];
        } elseif ($this->config->has('og_media_title_position')) {
            $data['og_media_title_position'] = $this->config->get('og_media_title_position');
        } else {
            $data['og_media_title_position'] = 'center';
        }

        if (isset($this->request->post['og_media_title_link'])) {
            $data['og_media_title_link'] = $this->request->post['og_media_title_link'];
        } elseif ($this->config->has('og_media_title_link')) {
            $data['og_media_title_link'] = $this->config->get('og_media_title_link');
        } else {
            $data['og_media_title_link'] = '0';
        }

        if (isset($this->request->post['og_album_menu'])) {
            $data['og_album_menu'] = $this->request->post['og_album_menu'];
        } elseif ($this->config->get('og_album_menu')) {
            $data['og_album_menu'] = $this->config->get('og_album_menu');
        } else {
            $data['og_album_menu'] = '0';
        }

        if (isset($this->request->post['og_album_carousel'])) {
            $data['og_album_carousel'] = $this->request->post['og_album_carousel'];
        } elseif ($this->config->has('og_album_carousel')) {
            $data['og_album_carousel'] = $this->config->get('og_album_carousel');
        } else {
            $data['og_album_carousel'] = '0';
        }

        if (isset($this->request->post['og_album_carousel_pagination'])) {
            $data['og_album_carousel_pagination'] = $this->request->post['og_album_carousel_pagination'];
        } elseif ($this->config->has('og_album_carousel_pagination')) {
            $data['og_album_carousel_pagination'] = $this->config->get('og_album_carousel_pagination');
        } else {
            $data['og_album_carousel_pagination'] = '1';
        }

        if (isset($this->request->post['og_album_carousel_loop'])) {
            $data['og_album_carousel_loop'] = $this->request->post['og_album_carousel_loop'];
        } elseif ($this->config->has('og_album_carousel_loop')) {
            $data['og_album_carousel_loop'] = $this->config->get('og_album_carousel_loop');
        } else {
            $data['og_album_carousel_loop'] = '1';
        }

        if (isset($this->request->post['og_album_carousel_space'])) {
            $data['og_album_carousel_space'] = $this->request->post['og_album_carousel_space'];
        } elseif ($this->config->has('og_album_carousel_space')) {
            $data['og_album_carousel_space'] = $this->config->get('og_album_carousel_space');
        } else {
            $data['og_album_carousel_space'] = '10';
        }

        if (isset($this->request->post['og_album_carousel_autoplay'])) {
            $data['og_album_carousel_autoplay'] = $this->request->post['og_album_carousel_autoplay'];
        } elseif ($this->config->has('og_album_carousel_autoplay')) {
            $data['og_album_carousel_autoplay'] = $this->config->get('og_album_carousel_autoplay');
        } else {
            $data['og_album_carousel_autoplay'] = '3500';
        }

        if (isset($this->request->post['og_album_magnific_popup'])) {
            $data['og_album_magnific_popup'] = $this->request->post['og_album_magnific_popup'];
        } elseif ($this->config->has('og_album_magnific_popup')) {
            $data['og_album_magnific_popup'] = $this->config->get('og_album_magnific_popup');
        } else {
            $data['og_album_magnific_popup'] = '0';
        }

        if (isset($this->request->post['og_album_carousel_xs'])) {
            $data['og_album_carousel_xs'] = $this->request->post['og_album_carousel_xs'];
        } elseif ($this->config->has('og_album_carousel_xs')) {
            $data['og_album_carousel_xs'] = $this->config->get('og_album_carousel_xs');
        } else {
            $data['og_album_carousel_xs'] = '1';
        }

        if (isset($this->request->post['og_album_carousel_sm'])) {
            $data['og_album_carousel_sm'] = $this->request->post['og_album_carousel_sm'];
        } elseif ($this->config->has('og_album_carousel_sm')) {
            $data['og_album_carousel_sm'] = $this->config->get('og_album_carousel_sm');
        } else {
            $data['og_album_carousel_sm'] = '2';
        }

        if (isset($this->request->post['og_album_carousel_md'])) {
            $data['og_album_carousel_md'] = $this->request->post['og_album_carousel_md'];
        } elseif ($this->config->has('og_album_carousel_md')) {
            $data['og_album_carousel_md'] = $this->config->get('og_album_carousel_md');
        } else {
            $data['og_album_carousel_md'] = '4';
        }

        if (isset($this->request->post['og_album_carousel_lg'])) {
            $data['og_album_carousel_lg'] = $this->request->post['og_album_carousel_lg'];
        } elseif ($this->config->has('og_album_carousel_lg')) {
            $data['og_album_carousel_lg'] = $this->config->get('og_album_carousel_lg');
        } else {
            $data['og_album_carousel_lg'] = '6';
        }

        if (isset($this->request->post['og_album_carousel_xl'])) {
            $data['og_album_carousel_xl'] = $this->request->post['og_album_carousel_xl'];
        } elseif ($this->config->has('og_album_carousel_xl')) {
            $data['og_album_carousel_xl'] = $this->config->get('og_album_carousel_xl');
        } else {
            $data['og_album_carousel_xl'] = '8';
        }

        if (isset($this->request->post['og_album_carousel_xxl'])) {
            $data['og_album_carousel_xxl'] = $this->request->post['og_album_carousel_xxl'];
        } elseif ($this->config->has('og_album_carousel_xxl')) {
            $data['og_album_carousel_xxl'] = $this->config->get('og_album_carousel_xxl');
        } else {
            $data['og_album_carousel_xxl'] = '8';
        }

        if (isset($this->request->post['og_album_title'])) {
            $data['og_album_title'] = $this->request->post['og_album_title'];
        } elseif ($this->config->get('og_album_title')) {
            $data['og_album_title'] = $this->config->get('og_album_title');
        } else {
            $data['og_album_title'] = 'Фотогалерея';
        }

        if (isset($this->request->post['og_album_per_row'])) {
            $data['og_album_per_row'] = $this->request->post['og_album_per_row'];
        } elseif ($this->config->get('og_album_per_row')) {
            $data['og_album_per_row'] = $this->config->get('og_album_per_row');
        } else {
            $data['og_album_per_row'] = '4';
        }

        if (isset($this->request->post['og_album_per_page'])) {
            $data['og_album_per_page'] = $this->request->post['og_album_per_page'];
        } elseif ($this->config->get('og_album_per_page')) {
            $data['og_album_per_page'] = $this->config->get('og_album_per_page');
        } else {
            $data['og_album_per_page'] = '12';
        }

        if (isset($this->request->post['og_album_size'])) {
            $data['og_album_size'] = $this->request->post['og_album_size'];
        } elseif ($this->config->get('og_album_size')) {
            $data['og_album_size'] = $this->config->get('og_album_size');
        } else {
            $data['og_album_size'] = '2';
        }

        if (isset($this->request->post['og_album_thumb_width'])) {
            $data['og_album_thumb_width'] = $this->request->post['og_album_thumb_width'];
        } elseif ($this->config->has('og_album_thumb_width')) {
            $data['og_album_thumb_width'] = $this->config->get('og_album_thumb_width');
        } else {
            $data['og_album_thumb_width'] = '200';
        }

        if (isset($this->request->post['og_album_thumb_height'])) {
            $data['og_album_thumb_height'] = $this->request->post['og_album_thumb_height'];
        } elseif ($this->config->has('og_album_thumb_height')) {
            $data['og_album_thumb_height'] = $this->config->get('og_album_thumb_height');
        } else {
            $data['og_album_thumb_height'] = '150';
        }

        if (isset($this->request->post['og_image_pu_width'])) {
            $data['og_image_pu_width'] = $this->request->post['og_image_pu_width'];
        } elseif ($this->config->get('og_image_pu_width')) {
            $data['og_image_pu_width'] = $this->config->get('og_image_pu_width');
        } else {
            $data['og_image_pu_width'] = '800';
        }

        if (isset($this->request->post['og_image_pu_height'])) {
            $data['og_image_pu_height'] = $this->request->post['og_image_pu_height'];
        } elseif ($this->config->get('og_image_pu_height')) {
            $data['og_image_pu_height'] = $this->config->get('og_image_pu_height');
        } else {
            $data['og_image_pu_height'] = '600';
        }

        if (isset($this->request->post['og_video_menu'])) {
            $data['og_video_menu'] = $this->request->post['og_video_menu'];
        } elseif ($this->config->get('og_video_menu')) {
            $data['og_video_menu'] = $this->config->get('og_video_menu');
        } else {
            $data['og_video_menu'] = '0';
        }

        if (isset($this->request->post['og_video_title'])) {
            $data['og_video_title'] = $this->request->post['og_video_title'];
        } elseif ($this->config->get('og_video_title')) {
            $data['og_video_title'] = $this->config->get('og_video_title');
        } else {
            $data['og_video_title'] = 'Видеогалерея';
        }

        if (isset($this->request->post['og_video_btn'])) {
            $data['og_video_btn'] = $this->request->post['og_video_btn'];
        } elseif ($this->config->get('og_video_btn')) {
            $data['og_video_btn'] = $this->config->get('og_video_btn');
        } else {
            $data['og_video_btn'] = '1';
        }

        if (isset($this->request->post['og_video_per_row'])) {
            $data['og_video_per_row'] = $this->request->post['og_video_per_row'];
        } elseif ($this->config->get('og_video_per_row')) {
            $data['og_video_per_row'] = $this->config->get('og_video_per_row');
        } else {
            $data['og_video_per_row'] = '4';
        }

        if (isset($this->request->post['og_video_per_page'])) {
            $data['og_video_per_page'] = $this->request->post['og_video_per_page'];
        } elseif ($this->config->get('og_video_per_page')) {
            $data['og_video_per_page'] = $this->config->get('og_video_per_page');
        } else {
            $data['og_video_per_page'] = '12';
        }

        if (isset($this->request->post['og_video_list_size'])) {
            $data['og_video_list_size'] = $this->request->post['og_video_list_size'];
        } elseif ($this->config->get('og_video_list_size')) {
            $data['og_video_list_size'] = $this->config->get('og_video_list_size');
        } else {
            $data['og_video_list_size'] = '3';
        }

        if (isset($this->request->post['og_video_size'])) {
            $data['og_video_size'] = $this->request->post['og_video_size'];
        } elseif ($this->config->get('og_video_size')) {
            $data['og_video_size'] = $this->config->get('og_video_size');
        } else {
            $data['og_video_size'] = '8';
        }

        if (isset($this->request->post['og_video_height'])) {
            $data['og_video_height'] = $this->request->post['og_video_height'];
        } elseif ($this->config->get('og_video_height')) {
            $data['og_video_height'] = $this->config->get('og_video_height');
        } else {
            $data['og_video_height'] = '200';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('xd_gallery/setting', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'xd_gallery/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
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
}
