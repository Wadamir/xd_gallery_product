<?php
class ControllerExtensionModuleXdGalleryVideo extends Controller
{
    public function index()
    {
        $setting = $this->config->get('xd_gallery_video_module');
        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/xd_gallery.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/xd_gallery.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/xd_gallery.css');
        }

        $data['og_title_font_weight'] = $this->config->get('og_title_font_weight');
        $data['title_size'] = $this->config->get('og_title_size');

        $title_font = $this->config->get('og_title_font');

        if ($title_font == 1) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Open+Sans:400,800');
            $data['title_font'] = "'Open Sans', sans-serif";
        } else if ($title_font == 2) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Josefin+Slab:400,700');
            $data['title_font'] = "'Josefin Slab', serif";
        } else if ($title_font == 3) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Arvo:400,700');
            $data['title_font'] = "'Arvo', serif";
        } else if ($title_font == 6) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Ubuntu:400,700');
            $data['title_font'] = "'Ubuntu', sans-serif";
        } else if ($title_font == 7) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=PT+Sans:400,700');
            $data['title_font'] = "'PT Sans', sans-serif";
        } else if ($title_font == 8) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Old+Standard+TT:400,700');
            $data['title_font'] = "'Old Standard TT', serif";
        } else if ($title_font == 9) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Droid+Sans:400,700');
            $data['title_font'] = "'Droid Sans', sans-serif";
        } else if ($title_font == 10) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Oswald:400,700');
            $data['title_font'] = "'Oswald', sans-serif";
        } else if ($title_font == 11) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Lato:400,700');
            $data['title_font'] = "'Lato', sans-serif";
        } else if ($title_font == 12) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Lobster+Two:400,700');
            $data['title_font'] = "'Lobster Two', cursive";
        } else if ($title_font == 13) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Pacifico');
            $data['title_font'] = "'Pacifico', cursive";
        } else if ($title_font == 14) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Oleo+Script:400,700');
            $data['title_font'] = "'Oleo Script', cursive";
        } else if ($title_font == 21) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Montserrat:400,700');
            $data['title_font'] = "'Montserrat', sans-serif";
        } else if ($title_font == 24) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Inconsolata:400,700');
            $data['title_font'] = "'Inconsolata'";
        } else if ($title_font == 25) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Roboto:400,700');
            $data['title_font'] = "'Roboto', sans-serif";
        } else if ($title_font == 27) {
            $data['title_font'] = "Arial";
        } else if ($title_font == 28) {
            $data['title_font'] = "'Times New Roman'";
        } else if ($title_font == 29) {
            $data['title_font'] = "'Tahoma'";
        } else if ($title_font == 30) {
            $data['title_font'] = "'Verdana'";
        }

        $this->load->language('extension/module/xd_gallery_video');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['button_cart'] = $this->language->get('button_cart');

        $this->load->model('xd_gallery/video');

        $this->load->model('tool/image');

        if ($setting['apr'] == 1) {
            $data['apr'] = 'col-lg-12 col-md-12 col-sm-12';
        } else if ($setting['apr'] == 2) {
            $data['apr'] = 'col-lg-6 col-md-6 col-sm-6';
        } else if ($setting['apr'] == 3) {
            $data['apr'] = 'col-lg-4 col-md-4 col-sm-6';
        } else if ($setting['apr'] == 4) {
            $data['apr'] = 'col-lg-3 col-md-3 col-sm-6';
        } else if ($setting['apr'] == 6) {
            $data['apr'] = 'col-lg-2 col-md-2 col-sm-6';
        }

        $data['vs'] = $setting['vs'];
        $data['og_title_font_weight'] = $this->config->get('og_title_font_weight');

        if ($setting['vs'] == 1) {
            $data['img_width'] = 128;
            $data['img_height'] = 72;
            $data['m_height'] = 140;
        } else if ($setting['vs'] == 2) {
            $data['img_width'] = 160;
            $data['img_height'] = 90;
            $data['m_height'] = 160;
        } else if ($setting['vs'] == 3) {
            $data['img_width'] = 192;
            $data['img_height'] = 108;
            $data['m_height'] = 180;
        }

        $data['play_btn_top'] = (int)(($data['img_height'] - 50) / 2);
        $data['play_btn_left'] = (int)(($data['img_width'] - 50) / 2);

        $data['videos'] = array();

        if ($setting['sb'] == 6) {

            $videos = explode(',', $this->config->get('featured_video'));

            if (empty($setting['limit'])) {
                $setting['limit'] = 5;
            }

            $videos = array_slice($videos, 0, (int)$setting['limit']);

            foreach ($videos as $video_id) {
                $video_info = $this->model_xd_gallery_video->getVideo($video_id);

                if ($video_info) {
                    if ($video_info['image']) {
                        $image = $this->model_tool_image->resize($video_info['image'], $data['img_width'], $data['img_height']);
                    } else {
                        $image = false;
                    }

                    $data['videos'][] = array(
                        'video_id'   => $video_info['video_id'],
                        'thumb'        => $image,
                        'name'         => $video_info['name'],
                        'href'         => $this->url->link('xd_gallery/video', 'video_id=' . $video_info['video_id'])
                    );
                }
            }
        } else {

            if ($setting['sb'] == 1) {
                $sort = 'v.date_added';
                $order = 'DESC';
            } else if ($setting['sb'] == 2) {
                $sort = 'v.viewed';
                $order = 'DESC';
            } else if ($setting['sb'] == 4) {
                $sort = 'v.sort_order';
                $order = 'DESC';
            } else if ($setting['sb'] == 5) {
                $sort = 'v.name';
                $order = 'ASC';
            }

            $data_video = array(
                'sort'  => $sort,
                'order' => $order,
                'start' => 0,
                'limit' => $setting['limit']
            );

            $results = $this->model_xd_gallery_video->getVideos($data_video);

            foreach ($results as $result) {
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $data['img_width'], $data['img_height']);
                } else {
                    $image = false;
                }

                $data['videos'][] = array(
                    'video_id'   => $result['video_id'],
                    'thumb'        => $image,
                    'name'         => $result['name'],
                    'href'         => $this->url->link('xd_gallery/video', 'video_id=' . $result['video_id']),
                );
            }
        }

        if ($data['videos']) {

            return $this->load->view('extension/module/xd_gallery_video', $data);
        }
    }
}
