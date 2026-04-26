<?php
class ModelXdGalleryVideo extends Model
{
    private $schema_initialized = false;

    private function ensureSchema()
    {
        if ($this->schema_initialized) {
            return;
        }

        $this->load->model('xd_gallery/image');
        $this->model_xd_gallery_image->CreateDB();

        $this->schema_initialized = true;
    }

    public function addVideo($data)
    {
        $this->ensureSchema();

        $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_video SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', video = '" . $data['video'] . "', date_added = NOW()");

        $video_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "xd_gallery_video SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE video_id = '" . (int)$video_id . "'");
        }

        foreach ($data['video_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_video_description SET video_id = '" . (int)$video_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "',  meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', description = '" . $this->db->escape($value['description']) . "'");
        }

        if (isset($data['video_store'])) {
            foreach ($data['video_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_video_to_store SET video_id = '" . (int)$video_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        if (isset($data['gallery_seo_url'])) {
            foreach ($data['gallery_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'video_id=" . (int)$video_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                }
            }
        }

        $this->cache->delete('video');
        $this->cache->delete('seo_pro');
    }

    public function editVideo($video_id, $data)
    {
        $this->ensureSchema();

        $this->db->query("UPDATE " . DB_PREFIX . "xd_gallery_video SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', video = '" . $data['video'] . "' WHERE video_id = '" . (int)$video_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "xd_gallery_video SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE video_id = '" . (int)$video_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_video_description WHERE video_id = '" . (int)$video_id . "'");

        foreach ($data['video_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_video_description SET video_id = '" . (int)$video_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "',  meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', description = '" . $this->db->escape($value['description']) . "'");
        }


        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_video_to_store WHERE video_id = '" . (int)$video_id . "'");

        if (isset($data['video_store'])) {
            foreach ($data['video_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "xd_gallery_video_to_store SET video_id = '" . (int)$video_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'video_id=" . (int)$video_id . "'");

        if (isset($data['gallery_seo_url'])) {
            foreach ($data['gallery_seo_url'] as $store_id => $language) {
                foreach ($language as $language_id => $keyword) {
                    if (!empty($keyword)) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'video_id=" . (int)$video_id . "', keyword = '" . $this->db->escape($keyword) . "'");
                    }
                }
            }
        }

        $this->cache->delete('video');
        $this->cache->delete('seo_pro');
    }

    public function deleteVideo($video_id)
    {
        $this->ensureSchema();


        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_video WHERE video_id = '" . (int)$video_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_video_description WHERE video_id = '" . (int)$video_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "xd_gallery_video_to_store WHERE video_id = '" . (int)$video_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'video_id=" . (int)$video_id . "'");

        $this->cache->delete('video');
        $this->cache->delete('seo_pro');
    }


    public function getVideo($video_id)
    {
        $this->ensureSchema();

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "xd_gallery_video v LEFT JOIN " . DB_PREFIX . "xd_gallery_video_description vd ON (v.video_id = vd.video_id) WHERE v.video_id = '" . (int)$video_id . "' AND vd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getVideos($data = array())
    {
        $this->ensureSchema();

        $sql = "SELECT * FROM " . DB_PREFIX . "xd_gallery_video v LEFT JOIN " . DB_PREFIX . "xd_gallery_video_description vd ON (v.video_id = vd.video_id)";

        $sql .= " WHERE vd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND vd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " GROUP BY v.video_id";

        $sort_data = array(
            'vd.name',
            'v.sort_order',
            'v.viewed',
        );


        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY vd.name";
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

    public function getTotalVideos()
    {
        $this->ensureSchema();

        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "xd_gallery_video");

        return $query->row['total'];
    }

    public function getVideoDescriptions($video_id)
    {
        $this->ensureSchema();

        $video_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "xd_gallery_video_description WHERE video_id = '" . (int)$video_id . "'");

        foreach ($query->rows as $result) {
            $video_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'description'      => $result['description'],
                'meta_keyword'     => $result['meta_keyword']
            );
        }

        return $video_description_data;
    }

    public function getVideoStores($video_id)
    {
        $this->ensureSchema();

        $video_store_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "xd_gallery_video_to_store WHERE video_id = '" . (int)$video_id . "'");

        foreach ($query->rows as $result) {
            $video_store_data[] = $result['store_id'];
        }

        return $video_store_data;
    }

    public function getGallerySeoUrls($gallery_id)
    {
        $this->ensureSchema();

        $gallery_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'video_id=" . (int)$gallery_id . "'");

        foreach ($query->rows as $result) {
            $gallery_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
        }

        return $gallery_seo_url_data;
    }
}
