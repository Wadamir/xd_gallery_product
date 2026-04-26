<?php
class ModelXdGalleryImage extends Model
{
    private $schema_initialized = false;

    private function ensureSchema()
    {
        if ($this->schema_initialized) {
            return;
        }

        $this->CreateDB();
        $this->schema_initialized = true;
    }

    public function CreateDB()
    {

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xd_gallery_album` (
			  `album_id` int(11) NOT NULL AUTO_INCREMENT,
			  `image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
			  `sort_order` int(3) NOT NULL DEFAULT '0',
			  `status` tinyint(1) NOT NULL,
			  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `viewed` int(5) NOT NULL,
			  PRIMARY KEY (`album_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xd_gallery_album_description` (
			  `album_id` int(11) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  `meta_description` varchar(255) NOT NULL,
			  `meta_keyword` varchar(255) NOT NULL,
			  PRIMARY KEY (`album_id`,`language_id`),
			  KEY `name` (`name`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xd_gallery_album_to_store` (
              `album_id` int(11) NOT NULL,
              `store_id` int(11) NOT NULL,
              PRIMARY KEY (`album_id`,`store_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xd_gallery_album_to_category` (
              `album_id` int(11) NOT NULL,
              `category_id` int(11) NOT NULL,
              PRIMARY KEY (`album_id`,`category_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ");

        $this->db->query("
                        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xd_gallery_album_to_product` (
                            `album_id` int(11) NOT NULL,
                            `product_id` int(11) NOT NULL,
                            PRIMARY KEY (`album_id`,`product_id`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
                ");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xd_gallery_image` (
			  `image_id` int(11) NOT NULL AUTO_INCREMENT,
			  `album_id` int(11) NOT NULL,
			  `name` varchar(255) COLLATE utf8_bin NOT NULL,
			  `image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
			  `sort_order` int(3) NOT NULL DEFAULT '0',
			  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`image_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xd_gallery_video` (
			  `video_id` int(11) NOT NULL AUTO_INCREMENT,
			  `image` varchar(255) COLLATE utf8_bin DEFAULT NULL,
			  `video` varchar(255) COLLATE utf8_bin NOT NULL,
			  `sort_order` int(3) NOT NULL DEFAULT '0',
			  `status` tinyint(1) NOT NULL,
			  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `viewed` int(5) NOT NULL,
			  PRIMARY KEY (`video_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xd_gallery_video_description` (
			  `video_id` int(11) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  `meta_description` varchar(255) NOT NULL,
			  `meta_keyword` varchar(255) NOT NULL,
			  PRIMARY KEY (`video_id`,`language_id`),
			  KEY `name` (`name`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "xd_gallery_video_to_store` (
			  `video_id` int(11) NOT NULL,
			  `store_id` int(11) NOT NULL,
			  PRIMARY KEY (`video_id`,`store_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
    }

    public function getTotalImages()
    {
        $this->ensureSchema();

        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "xd_gallery_image");

        return $query->row['total'];
    }

    public function getTotalAlbums()
    {
        $this->ensureSchema();

        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "xd_gallery_album");

        return $query->row['total'];
    }

    public function getAlbum($album_id)
    {
        $this->ensureSchema();

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "xd_gallery_album a LEFT JOIN " . DB_PREFIX . "xd_gallery_album_description ad ON (a.album_id = ad.album_id) WHERE a.album_id = '" . (int)$album_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getAlbums($data)
    {
        $this->ensureSchema();


        $sql = "SELECT * FROM " . DB_PREFIX . "xd_gallery_album a LEFT JOIN " . DB_PREFIX . "xd_gallery_album_description ad ON (a.album_id = ad.album_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";


        if (!empty($data['filter_name'])) {
            $sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }


        $sql .= " GROUP BY a.album_id";

        $sort_data = array(
            'a.viewed',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY ad.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }


        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function addAlbum($data)
    {
        $this->ensureSchema();

        $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_album SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

        $album_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "xd_gallery_album SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE album_id = '" . (int)$album_id . "'");
        }

        foreach ($data['album_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_album_description SET album_id = '" . (int)$album_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "' , meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "' , description = '" . $this->db->escape($value['description']) . "'");
        }

        if (isset($data['album_store'])) {
            foreach ($data['album_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_album_to_store SET album_id = '" . (int)$album_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        if (isset($data['album_category'])) {
            foreach ($data['album_category'] as $category_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_album_to_category SET album_id = '" . (int)$album_id . "', category_id = '" . (int)$category_id . "'");
            }
        }

        if (isset($data['album_product'])) {
            foreach ($data['album_product'] as $product_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_album_to_product SET album_id = '" . (int)$album_id . "', product_id = '" . (int)$product_id . "'");
            }
        }

        if (isset($data['album_image'])) {
            foreach ($data['album_image'] as $album_image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_image SET name = '" . $this->db->escape($album_image['name']) . "' , album_id = '" . (int)$album_id . "', image = '" . $this->db->escape($album_image['image']) . "', sort_order = '" . (int)$album_image['sort_order'] . "'");
            }
        }

        if (isset($data['gallery_seo_url'])) {
            foreach ($data['gallery_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'album_id=" . (int)$album_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                }
            }
        }

        $this->cache->delete('album');
        $this->cache->delete('seo_pro');
    }

    public function editAlbum($album_id, $data)
    {
        $this->ensureSchema();

        $this->db->query("UPDATE " . DB_PREFIX . "xd_gallery_album SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE album_id = '" . (int)$album_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "xd_gallery_album SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE album_id = '" . (int)$album_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_album_description WHERE album_id = '" . (int)$album_id . "'");

        foreach ($data['album_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_album_description SET album_id = '" . (int)$album_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "' , meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "' , description = '" . $this->db->escape($value['description']) . "'");
        }


        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_album_to_store WHERE album_id = '" . (int)$album_id . "'");

        if (isset($data['album_store'])) {
            foreach ($data['album_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_album_to_store SET album_id = '" . (int)$album_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_album_to_category WHERE album_id = '" . (int)$album_id . "'");

        if (isset($data['album_category'])) {
            foreach ($data['album_category'] as $category_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_album_to_category SET album_id = '" . (int)$album_id . "', category_id = '" . (int)$category_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_album_to_product WHERE album_id = '" . (int)$album_id . "'");

        if (isset($data['album_product'])) {
            foreach ($data['album_product'] as $product_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_album_to_product SET album_id = '" . (int)$album_id . "', product_id = '" . (int)$product_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_image WHERE album_id = '" . (int)$album_id . "'");

        if (isset($data['album_image'])) {
            foreach ($data['album_image'] as $album_image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_image SET name = '" . $this->db->escape($album_image['name']) . "' , album_id = '" . (int)$album_id . "', image = '" . $this->db->escape($album_image['image']) . "', sort_order = '" . (int)$album_image['sort_order'] . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'album_id=" . (int)$album_id . "'");

        if (isset($data['gallery_seo_url'])) {
            foreach ($data['gallery_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'album_id=" . (int)$album_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                }
            }
        }

        $this->cache->delete('album');
        $this->cache->delete('seo_pro');
    }

    public function deleteAlbum($album_id)
    {
        $this->ensureSchema();


        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_album WHERE album_id = '" . (int)$album_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_album_description WHERE album_id = '" . (int)$album_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_album_to_store WHERE album_id = '" . (int)$album_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_album_to_category WHERE album_id = '" . (int)$album_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_album_to_product WHERE album_id = '" . (int)$album_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_image WHERE album_id = '" . (int)$album_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'album_id=" . (int)$album_id . "'");

        $this->cache->delete('album');
        $this->cache->delete('seo_pro');
    }

    public function getAlbumDescriptions($album_id)
    {
        $this->ensureSchema();

        $album_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "xd_gallery_album_description WHERE album_id = '" . (int)$album_id . "'");

        foreach ($query->rows as $result) {
            $album_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'description'      => $result['description'],
                'meta_description' => $result['meta_description'],
                'meta_keyword'     => $result['meta_keyword'],
            );
        }

        return $album_description_data;
    }

    public function getAlbumStores($album_id)
    {
        $this->ensureSchema();

        $album_store_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "xd_gallery_album_to_store WHERE album_id = '" . (int)$album_id . "'");

        foreach ($query->rows as $result) {
            $album_store_data[] = $result['store_id'];
        }

        return $album_store_data;
    }

    public function getAlbumCategories($album_id)
    {
        $this->ensureSchema();

        $album_category_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "xd_gallery_album_to_category WHERE album_id = '" . (int)$album_id . "'");

        foreach ($query->rows as $result) {
            $album_category_data[] = $result['category_id'];
        }

        return $album_category_data;
    }

    public function getAlbumProducts($album_id)
    {
        $this->ensureSchema();

        $album_product_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "xd_gallery_album_to_product WHERE album_id = '" . (int)$album_id . "'");

        foreach ($query->rows as $result) {
            $album_product_data[] = $result['product_id'];
        }

        return $album_product_data;
    }

    public function getImageAlbum($album_id)
    {
        $this->ensureSchema();


        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "xd_gallery_image WHERE album_id = '" . (int)$album_id . "'");

        return $query->rows;
    }

    public function getGallerySeoUrls($gallery_id)
    {
        $this->ensureSchema();

        $gallery_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'album_id=" . (int)$gallery_id . "'");

        foreach ($query->rows as $result) {
            $gallery_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
        }

        return $gallery_seo_url_data;
    }
}
