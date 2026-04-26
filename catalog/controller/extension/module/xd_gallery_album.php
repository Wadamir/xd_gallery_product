<?php
class ControllerExtensionModuleXdGalleryAlbum extends Controller
{
    public function index($setting = array())
    {
        static $module = 0;

        if (!$setting) {
            $setting = $this->config->get('xd_gallery_album_module');
        }

        if (!$setting || !is_array($setting)) {
            return;
        }

        $data['module'] = $module++;

        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/xd_gallery.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/xd_gallery.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/xd_gallery.css');
        }

        if (!empty($this->config->get('og_album_carousel'))) {
            $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
            $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
            $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
        }

        if (!empty($this->config->get('og_album_magnific_popup'))) {
            $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
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

        $this->load->language('extension/module/xd_gallery_album');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['button_cart'] = $this->language->get('button_cart');

        $this->load->model('xd_gallery/album');
        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $current_product_category_ids = array();

        if (isset($this->request->get['product_id'])) {
            $product_categories = $this->model_catalog_product->getCategories((int)$this->request->get['product_id']);

            foreach ($product_categories as $product_category) {
                $current_product_category_ids[] = (int)$product_category['category_id'];
            }
        }

        $data['albums'] = array();

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

        $data['as'] = $setting['as'];

        $data['og_album_carousel'] = (int)$this->config->get('og_album_carousel');
        $data['og_album_carousel_xs'] = (int)$this->config->get('og_album_carousel_xs') ?: 1;
        $data['og_album_carousel_sm'] = (int)$this->config->get('og_album_carousel_sm') ?: 2;
        $data['og_album_carousel_md'] = (int)$this->config->get('og_album_carousel_md') ?: 4;
        $data['og_album_carousel_lg'] = (int)$this->config->get('og_album_carousel_lg') ?: 6;
        $data['og_album_carousel_xl'] = (int)$this->config->get('og_album_carousel_xl') ?: 8;
        $data['og_album_carousel_xxl'] = (int)$this->config->get('og_album_carousel_xxl') ?: 8;
        $data['og_album_carousel_pagination'] = (int)($this->config->get('og_album_carousel_pagination') ?? 1);
        $data['og_album_carousel_loop'] = (int)($this->config->get('og_album_carousel_loop') ?? 1);
        $data['og_album_carousel_space'] = (int)($this->config->get('og_album_carousel_space') ?? 10);
        $data['og_album_carousel_autoplay'] = (int)($this->config->get('og_album_carousel_autoplay') ?? 3500);
        $data['og_album_magnific_popup'] = (int)($this->config->get('og_album_magnific_popup') ?? 0);

        $carousel_width = (int)$this->config->get('og_image_pu_width');
        $carousel_height = (int)$this->config->get('og_image_pu_height');

        if ($carousel_width < 1) {
            $carousel_width = 900;
        }

        if ($carousel_height < 1) {
            $carousel_height = 500;
        }

        $custom_thumb_width = (int)$this->config->get('og_album_thumb_width');
        $custom_thumb_height = (int)$this->config->get('og_album_thumb_height');

        if ($setting['as'] == 1) {
            $image_width = 120;
            $image_height = 90;
        } else if ($setting['as'] == 2) {
            $image_width = 160;
            $image_height = 120;
        } else {
            $image_width = 200;
            $image_height = 150;
        }

        if ($custom_thumb_width <= 0) {
            $custom_thumb_width = 200;
        }

        if ($custom_thumb_height <= 0) {
            $custom_thumb_height = 150;
        }

        $image_width = $custom_thumb_width;
        $image_height = $custom_thumb_height;
        $frame_padding = 12;
        $data['og_album_thumb_width'] = $image_width;
        $data['og_album_thumb_height'] = $image_height;
        $data['og_album_frame_padding'] = $frame_padding;
        $data['og_album_frame_width'] = $image_width + ($frame_padding * 2);
        $data['og_album_frame_height'] = $image_height + ($frame_padding * 2);
        $data['mheight'] = ($data['og_album_frame_height'] + 55) . 'px';

        if ($setting['sb'] == 6) {

            $albums = explode(',', (string)$this->config->get('xd_gallery_album_featured'));

            if (empty($setting['limit'])) {
                $setting['limit'] = 5;
            }

            $albums = array_slice($albums, 0, (int)$setting['limit']);

            foreach ($albums as $album_id) {
                $album_info = $this->model_xd_gallery_album->getAlbum($album_id);

                if ($album_info) {
                    $album_category_ids = $this->model_xd_gallery_album->getAlbumCategories($album_info['album_id']);

                    if ($album_category_ids) {
                        if (!$current_product_category_ids || !array_intersect($album_category_ids, $current_product_category_ids)) {
                            continue;
                        }
                    }

                    if ($album_info['image']) {
                        $image = $this->model_tool_image->resize($album_info['image'], $image_width, $image_height);
                        $image_popup = $this->model_tool_image->resize($album_info['image'], $carousel_width, $carousel_height);
                    } else {
                        $image = false;
                        $image_popup = false;
                    }

                    $album_images = array();

                    if ($data['og_album_carousel']) {
                        if (!empty($album_info['image'])) {
                            $album_images[] = array(
                                'thumb' => $this->model_tool_image->resize($album_info['image'], $carousel_width, $carousel_height),
                                'popup' => $this->model_tool_image->resize($album_info['image'], $carousel_width, $carousel_height),
                                'name'  => $album_info['name']
                            );
                        }

                        $image_results = $this->model_xd_gallery_album->getAlbumImages($album_info['album_id']);

                        foreach ($image_results as $image_result) {
                            if (!empty($image_result['image'])) {
                                $album_images[] = array(
                                    'thumb' => $this->model_tool_image->resize($image_result['image'], $carousel_width, $carousel_height),
                                    'popup' => $this->model_tool_image->resize($image_result['image'], $carousel_width, $carousel_height),
                                    'name'  => $image_result['name']
                                );
                            }
                        }
                    }

                    $data['albums'][] = array(
                        'album_id'   => $album_info['album_id'],
                        'thumb'        => $image,
                        'popup'        => $image_popup,
                        'images'       => $album_images,
                        'name'         => $album_info['name'],
                        'href'         => $this->url->link('xd_gallery/album', 'album_id=' . $album_info['album_id'])
                    );
                }
            }
        } else {

            if ($setting['sb'] == 1) {
                $sort = 'a.date_added';
                $order = 'DESC';
            } else if ($setting['sb'] == 2) {
                $sort = 'a.viewed';
                $order = 'DESC';
            } else if ($setting['sb'] == 4) {
                $sort = 'a.sort_order';
                $order = 'DESC';
            } else if ($setting['sb'] == 5) {
                $sort = 'a.name';
                $order = 'ASC';
            }

            $data_album = array(
                'sort'  => $sort,
                'order' => $order,
                'start' => 0,
                'limit' => $setting['limit']
            );

            $results = $this->model_xd_gallery_album->getAlbums($data_album);

            foreach ($results as $result) {
                $album_category_ids = $this->model_xd_gallery_album->getAlbumCategories($result['album_id']);

                if ($album_category_ids) {
                    if (!$current_product_category_ids || !array_intersect($album_category_ids, $current_product_category_ids)) {
                        continue;
                    }
                }

                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $image_width, $image_height);
                    $image_popup = $this->model_tool_image->resize($result['image'], $carousel_width, $carousel_height);
                } else {
                    $image = false;
                    $image_popup = false;
                }

                $album_images = array();

                if ($data['og_album_carousel']) {
                    if (!empty($result['image'])) {
                        $album_images[] = array(
                            'thumb' => $this->model_tool_image->resize($result['image'], $carousel_width, $carousel_height),
                            'popup' => $this->model_tool_image->resize($result['image'], $carousel_width, $carousel_height),
                            'name'  => $result['name']
                        );
                    }

                    $image_results = $this->model_xd_gallery_album->getAlbumImages($result['album_id']);

                    foreach ($image_results as $image_result) {
                        if (!empty($image_result['image'])) {
                            $album_images[] = array(
                                'thumb' => $this->model_tool_image->resize($image_result['image'], $carousel_width, $carousel_height),
                                'popup' => $this->model_tool_image->resize($image_result['image'], $carousel_width, $carousel_height),
                                'name'  => $image_result['name']
                            );
                        }
                    }
                }

                $data['albums'][] = array(
                    'album_id'   => $result['album_id'],
                    'thumb'        => $image,
                    'popup'        => $image_popup,
                    'images'       => $album_images,
                    'name'         => $result['name'],
                    'href'         => $this->url->link('xd_gallery/album', 'album_id=' . $result['album_id']),
                );
            }
        }


        if ($data['albums']) {

            return $this->load->view('extension/module/xd_gallery_album', $data);
        }
    }
}
