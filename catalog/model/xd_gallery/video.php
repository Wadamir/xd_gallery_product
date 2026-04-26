<?php
class ModelXdGalleryVideo extends Model
{
    private $image_to_video_table_exists = null;

    private function hasImageToVideoTable()
    {
        if ($this->image_to_video_table_exists === null) {
            $query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape(DB_PREFIX . "xd_gallery_image_to_video") . "'");
            $this->image_to_video_table_exists = (bool)$query->num_rows;
        }

        return $this->image_to_video_table_exists;
    }

    public function updateViewed($video_id)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "xd_gallery_video SET viewed = (viewed + 1) WHERE video_id = '" . (int)$video_id . "'");
    }

    public function getVideo($video_id)
    {

        $query = $this->db->query("SELECT DISTINCT *, vd.name AS name, v.image, v.sort_order FROM " . DB_PREFIX . "xd_gallery_video v LEFT JOIN " . DB_PREFIX . "xd_gallery_video_description vd ON (v.video_id = vd.video_id) LEFT JOIN " . DB_PREFIX . "xd_gallery_video_to_store v2s ON (v.video_id = v2s.video_id) WHERE v.video_id = '" . (int)$video_id . "' AND vd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND v.status = '1' AND v2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            return array(
                'video_id'         => $query->row['video_id'],
                'name'             => $query->row['name'],
                'image'             => $query->row['image'],
                'video'            => $query->row['video'],
                'description'      => $query->row['description'],
                'meta_description' => $query->row['meta_description'],
                'meta_keyword'     => $query->row['meta_keyword'],
                'sort_order'       => $query->row['sort_order'],
                'status'           => $query->row['status'],
                'date_added'       => $query->row['date_added'],
                'viewed'           => $query->row['viewed']
            );
        } else {
            return false;
        }
    }

    public function getVideos($data = array())
    {

        $sql = "SELECT v.video_id ";


        $sql .= " FROM " . DB_PREFIX . "xd_gallery_video v";


        $sql .= " LEFT JOIN " . DB_PREFIX . "xd_gallery_video_description vd ON (v.video_id = vd.video_id) LEFT JOIN " . DB_PREFIX . "xd_gallery_video_to_store v2s ON (v.video_id = v2s.video_id) WHERE vd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND v.status = '1' AND v2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "vd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                if (!empty($data['filter_description'])) {
                    $sql .= " OR vd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                }
            }

            $sql .= ")";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " ORDER BY  v.sort_order DESC";
        } else {
            $sql .= " ORDER BY  v.sort_order ASC";
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

        $video_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $video_data[$result['video_id']] = $this->getVideo($result['video_id']);
        }

        return $video_data;
    }

    public function getTotalVideos($data = array())
    {

        $sql = "SELECT COUNT(DISTINCT v.video_id) AS total";

        $sql .= " FROM " . DB_PREFIX . "xd_gallery_video v";

        $sql .= " LEFT JOIN " . DB_PREFIX . "xd_gallery_video_description vd ON (v.video_id = vd.video_id) LEFT JOIN " . DB_PREFIX . "xd_gallery_video_to_store v2s ON (v.video_id = v2s.video_id) WHERE vd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND v.status = '1' AND v2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";


        if (!empty($data['filter_name'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "vd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                if (!empty($data['filter_description'])) {
                    $sql .= " OR vd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                }
            }

            $sql .= ")";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getImage($image_id)
    {

        $query = $this->db->query("SELECT i.image_id, i.album_id, i.name, i.image, i.sort_order, i.date_added FROM " . DB_PREFIX . "xd_gallery_image i WHERE i.image_id = '" . (int)$image_id . "'");

        if ($query->num_rows) {
            return array(
                'image_id'       => $query->row['image_id'],
                'name'             => $query->row['name'],
                'image'            => $query->row['image'],
                'sort_order'       => $query->row['sort_order'],
                'album_id'         => $query->row['album_id'],
                'date_added'       => $query->row['date_added'],
            );
        } else {
            return false;
        }
    }

    public function getImages($data = array())
    {

        $sql = "SELECT i.image_id FROM " . DB_PREFIX . "xd_gallery_image i";

        if (!empty($data['filter_video_id']) && $this->hasImageToVideoTable()) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "xd_gallery_image_to_video i2v ON (i.image_id = i2v.image_id)";
        }

        $sql .= " WHERE 1 = 1";

        if (!empty($data['filter_video_id'])) {
            if ($this->hasImageToVideoTable()) {
                $sql .= " AND i2v.video_id = '" . (int)$data['filter_video_id'] . "'";
            } else {
                return array();
            }
        }

        if (!empty($data['filter_name'])) {
            $implode = array();

            $words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_name'])));

            foreach ($words as $word) {
                $implode[] = "i.name LIKE '%" . $this->db->escape($word) . "%'";
            }

            if ($implode) {
                $sql .= " AND " . implode(" AND ", $implode);
            }
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " ORDER BY i.sort_order DESC";
        } else {
            $sql .= " ORDER BY i.sort_order ASC";
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

        $image_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $image_data[$result['image_id']] = $this->getImage($result['image_id']);
        }

        return $image_data;
    }

    public function getAlbumImages($video_id)
    {
        if (!$this->hasImageToVideoTable()) {
            return array();
        }

        $query = $this->db->query("SELECT i.* FROM " . DB_PREFIX . "xd_gallery_image i LEFT JOIN " . DB_PREFIX . "xd_gallery_image_to_video i2v ON (i.image_id = i2v.image_id) WHERE i2v.video_id = '" . (int)$video_id . "' ORDER BY i.sort_order ASC");

        return $query->rows;
    }
}
