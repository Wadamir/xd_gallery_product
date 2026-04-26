<?php
class ControllerXdGalleryVideo extends Controller
{
    public function index()
    {
        $this->load->language('xd_gallery/video');

        $this->load->model('xd_gallery/video');

        $this->load->model('tool/image');

        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/xd_gallery.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/xd_gallery.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/xd_gallery.css');
        }

        $data['heading_title_size'] = $this->config->get('og_heading_title_size');

        $heading_title_font = $this->config->get('og_heading_title_font');

        if ($heading_title_font == 1) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Open+Sans:400,800');
            $data['heading_title_font'] = "'Open Sans', sans-serif";
        } else if ($heading_title_font == 2) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Josefin+Slab:400,700');
            $data['heading_title_font'] = "'Josefin Slab', serif";
        } else if ($heading_title_font == 3) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Arvo:400,700');
            $data['heading_title_font'] = "'Arvo', serif";
        } else if ($heading_title_font == 6) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Ubuntu:400,700');
            $data['heading_title_font'] = "'Ubuntu', sans-serif";
        } else if ($heading_title_font == 7) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=PT+Sans:400,700');
            $data['heading_title_font'] = "'PT Sans', sans-serif";
        } else if ($heading_title_font == 8) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Old+Standard+TT:400,700');
            $data['heading_title_font'] = "'Old Standard TT', serif";
        } else if ($heading_title_font == 9) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Droid+Sans:400,700');
            $data['heading_title_font'] = "'Droid Sans', sans-serif";
        } else if ($heading_title_font == 10) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Oswald:400,700');
            $data['heading_title_font'] = "'Oswald', sans-serif";
        } else if ($heading_title_font == 11) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Lato:400,700');
            $data['heading_title_font'] = "'Lato', sans-serif";
        } else if ($heading_title_font == 12) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Lobster+Two:400,700');
            $data['heading_title_font'] = "'Lobster Two', cursive";
        } else if ($heading_title_font == 13) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Pacifico');
            $data['heading_title_font'] = "'Pacifico', cursive";
        } else if ($heading_title_font == 14) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Oleo+Script:400,700');
            $data['heading_title_font'] = "'Oleo Script', cursive";
        } else if ($heading_title_font == 21) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Montserrat:400,700');
            $data['heading_title_font'] = "'Montserrat', sans-serif";
        } else if ($heading_title_font == 24) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Inconsolata:400,700');
            $data['heading_title_font'] = "'Inconsolata'";
        } else if ($heading_title_font == 25) {
            $this->document->addStyle('http://fonts.googleapis.com/css?family=Roboto:400,700');
            $data['heading_title_font'] = "'Roboto', sans-serif";
        } else if ($heading_title_font == 27) {
            $data['heading_title_font'] = "Arial";
        } else if ($heading_title_font == 28) {
            $data['heading_title_font'] = "'Times New Roman'";
        } else if ($heading_title_font == 29) {
            $data['heading_title_font'] = "'Tahoma'";
        } else if ($heading_title_font == 30) {
            $data['heading_title_font'] = "'Verdana'";
        }

        $og_video_per_row = $this->config->get('og_video_per_row');

        if ($og_video_per_row == 1) {
            $data['apr'] = 'col-lg-12 col-md-12 col-sm-12';
        } else if ($og_video_per_row == 2) {
            $data['apr'] = 'col-lg-6 col-md-6 col-sm-6';
        } else if ($og_video_per_row == 3) {
            $data['apr'] = 'col-lg-4 col-md-4 col-sm-6';
        } else if ($og_video_per_row == 4) {
            $data['apr'] = 'col-lg-3 col-md-3 col-sm-6';
        } else if ($og_video_per_row == 6) {
            $data['apr'] = 'col-lg-2 col-md-2 col-sm-6';
        }

        $data['heading_title_line'] = $this->config->get('og_heading_title_line');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }

        // Set the last category breadcrumb		
        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_gallery_video'),
            'href'      => $this->url->link('xd_gallery/video', $url),
            'separator' => $this->language->get('text_separator')
        );

        if (isset($this->request->get['video_id'])) {
            $video_id = $this->request->get['video_id'];
        } else {
            $video_id = 0;
        }

        $video_info = $this->model_xd_gallery_video->getVideo($video_id);

        if ($video_info) {
            $this->document->setTitle($video_info['name']);
            $this->document->setDescription($video_info['meta_description']);
            $this->document->setKeywords($video_info['meta_keyword']);

            $data['heading_title'] = $video_info['name'];

            $data['text_empty'] = $this->language->get('text_empty');

            // Set the last category breadcrumb		
            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['breadcrumbs'][] = array(
                'text'      => $video_info['name'],
                'href'      => $this->url->link('xd_gallery/video', 'video_id=' . $this->request->get['video_id'], true),
                'separator' => $this->language->get('text_separator')
            );

            $data['video_id'] = $this->request->get['video_id'];

            if (strpos($video_info['video'], 'youtube') > 0) {

                $data['video_type'] = 'youtube';

                $parts = explode('v=', (string)$video_info['video']);

                $data['video_code'] = $parts['1'];
            } else if (strpos($video_info['video'], 'vimeo') > 0) {

                $data['video_type'] = 'vimeo';

                $parts = explode('com/', (string)$video_info['video']);

                $data['video_code'] = $parts['1'];
            }

            if ($this->config->get('og_video_size') == 1) {
                $data['v_width'] = 320;
                $data['v_height'] = 240;
            } else if ($this->config->get('og_video_size') == 2) {
                $data['v_width'] = 432;
                $data['v_height'] = 243;
            } else if ($this->config->get('og_video_size') == 3) {
                $data['v_width'] = 560;
                $data['v_height'] = 315;
            } else if ($this->config->get('og_video_size') == 4) {
                $data['v_width'] = 640;
                $data['v_height'] = 360;
            } else if ($this->config->get('og_video_size') == 5) {
                $data['v_width'] = 672;
                $data['v_height'] = 378;
            } else if ($this->config->get('og_video_size') == 6) {
                $data['v_width'] = 752;
                $data['v_height'] = 423;
            } else if ($this->config->get('og_video_size') == 7) {
                $data['v_width'] = 784;
                $data['v_height'] = 441;
            } else if ($this->config->get('og_video_size') == 8) {
                $data['v_width'] = '100%';
                $data['v_height'] = '100%';
            }

            $data['description'] = html_entity_decode($video_info['description'], ENT_QUOTES, 'UTF-8');

            $url = '';

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->model_xd_gallery_video->updateViewed($this->request->get['video_id']);

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');


            $this->response->setOutput($this->load->view('xd_gallery/video_info', $data));
        } else {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }


            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }



            if (isset($this->request->get['video_id'])) {

                $data['breadcrumbs'][] = array(
                    'text'      => $this->language->get('text_error'),
                    'href'      => $this->url->link('xd_gallery/video', $url),
                    'separator' => $this->language->get('text_separator')
                );

                $this->document->setTitle($this->language->get('text_error'));

                $data['heading_title'] = $this->language->get('text_error');
                $data['text_empty'] = $this->language->get('text_empty');
                $data['text_error'] = $this->language->get('text_error');

                $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');


                $this->response->setOutput($this->load->view('error/not_found', $data));
            } else {

                $this->document->setTitle($this->language->get('heading_title'));

                $data['heading_title'] = $this->language->get('heading_title');
                $data['text_empty'] = $this->language->get('text_empty');

                if (isset($this->request->get['page'])) {
                    $page = $this->request->get['page'];
                } else {
                    $page = 1;
                }

                if (isset($this->request->get['limit'])) {
                    $limit = $this->request->get['limit'];
                } else {
                    $limit = $this->config->get('og_video_per_page');
                }

                $url = '';

                if (isset($this->request->get['limit'])) {
                    $url .= '&limit=' . $this->request->get['limit'];
                }

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

                $data['video_btn'] = $this->config->get('og_video_btn');

                $data['videos'] = array();

                $data_videos = array(
                    'start'              => ($page - 1) * $limit,
                    'limit'              => $limit
                );

                $data['og_title_font_weight'] = $this->config->get('og_title_font_weight');
                $data['og_video_per_row'] = $this->config->get('og_video_per_row');
                $data['og_video_height'] = $this->config->get('og_video_height');

                $data['og_video_list_size'] = $this->config->get('og_video_list_size');

                if ($data['og_video_list_size'] == 1) {
                    $data['img_width'] = 128;
                    $data['img_height'] = 72;
                } else if ($data['og_video_list_size'] == 2) {
                    $data['img_width'] = 160;
                    $data['img_height'] = 90;
                } else if ($data['og_video_list_size'] == 3) {
                    $data['img_width'] = 192;
                    $data['img_height'] = 108;
                } else if ($data['og_video_list_size'] == 4) {
                    $data['img_width'] = 224;
                    $data['img_height'] = 126;
                } else if ($data['og_video_list_size'] == 5) {
                    $data['img_width'] = 256;
                    $data['img_height'] = 144;
                } else if ($data['og_video_list_size'] == 6) {
                    $data['img_width'] = 288;
                    $data['img_height'] = 162;
                } else if ($data['og_video_list_size'] == 7) {
                    $data['img_width'] = 320;
                    $data['img_height'] = 180;
                }

                $data['play_btn_top'] = (int)(($data['img_height'] - 50) / 2);
                $data['play_btn_left'] = (int)(($data['img_width'] - 50) / 2);
                $data['play_btn_icon'] = $this->config->get('og_video_btn');

                $video_total = $this->model_xd_gallery_video->getTotalVideos($data_videos);

                $results = $this->model_xd_gallery_video->getVideos($data_videos);

                foreach ($results as $result) {
                    if ($result['image']) {
                        $image = $this->model_tool_image->resize($result['image'], $data['img_width'], $data['img_height']);
                    } else {
                        $image = false;
                    }

                    $data['videos'][] = array(
                        'video_id'    => $result['video_id'],
                        'thumb'       => $image,
                        'name'        => $result['name'],
                        'href'        => $this->url->link('xd_gallery/video', '&video_id=' . $result['video_id'], true)
                    );
                }

                $url = '';

                if (isset($this->request->get['limit'])) {
                    $url .= '&limit=' . $this->request->get['limit'];
                }

                $data['limits'] = array();

                $limits = array_unique(array($this->config->get('og_video_per_page'), $this->config->get('og_video_per_page') * 2, $this->config->get('og_video_per_page') * 4, $this->config->get('og_video_per_page') * 8, $this->config->get('og_video_per_page') * 16));


                sort($limits);

                foreach ($limits as $value) {
                    $data['limits'][] = array(
                        'text'  => $value,
                        'value' => $value,
                        'href'  => $this->url->link('xd_gallery/video', $url . '&limit=' . $value, true)
                    );
                }

                $url = '';


                if (isset($this->request->get['limit'])) {
                    $url .= '&limit=' . $this->request->get['limit'];
                }

                $pagination = new Pagination();
                $pagination->total = $video_total;
                $pagination->page = $page;
                $pagination->limit =  $limit;
                $pagination->url = $this->url->link('xd_gallery/video', $url . '&page={page}', true);

                $data['pagination'] = $pagination->render();
                $data['results'] = sprintf($this->language->get('text_pagination'), ($video_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($video_total - $limit)) ? $video_total : ((($page - 1) * $limit) + $limit), $video_total, ceil($video_total / $limit));

                $data['limit'] = $limit;
            }

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');


            $this->response->setOutput($this->load->view('xd_gallery/video', $data));
        }
    }
}
