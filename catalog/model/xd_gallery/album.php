<?php
class ModelXdGalleryAlbum extends Model
{
    public function getAlbumCategories($album_id)
    {
        $table_name = DB_PREFIX . 'xd_gallery_album_to_category';

        $table_query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape($table_name) . "'");

        if (!$table_query->num_rows) {
            return array();
        }

        $album_category_data = array();

        $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "xd_gallery_album_to_category WHERE album_id = '" . (int)$album_id . "'");

        foreach ($query->rows as $result) {
            $album_category_data[] = (int)$result['category_id'];
        }

        return $album_category_data;
    }

    public function updateViewed($album_id)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "xd_gallery_album SET viewed = (viewed + 1) WHERE album_id = '" . (int)$album_id . "'");
    }

    public function getAlbum($album_id)
    {

        $query = $this->db->query("SELECT DISTINCT *, ad.name AS name, a.image, a.sort_order FROM " . DB_PREFIX . "xd_gallery_album a LEFT JOIN " . DB_PREFIX . "xd_gallery_album_description ad ON (a.album_id = ad.album_id) LEFT JOIN " . DB_PREFIX . "xd_gallery_album_to_store a2s ON (a.album_id = a2s.album_id) WHERE a.album_id = '" . (int)$album_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND a.status = '1' AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

        if ($query->num_rows) {
            return array(
                'album_id'         => $query->row['album_id'],
                'name'             => $query->row['name'],
                'description'      => $query->row['description'],
                'meta_description' => $query->row['meta_description'],
                'meta_keyword'     => $query->row['meta_keyword'],
                'image'            => $query->row['image'],
                'sort_order'       => $query->row['sort_order'],
                'status'           => $query->row['status'],
                'date_added'       => $query->row['date_added'],
                'viewed'           => $query->row['viewed']
            );
        } else {
            return false;
        }
    }

    public function getAlbums($data = array())
    {

        $sql = "SELECT a.album_id ";


        $sql .= " FROM " . DB_PREFIX . "xd_gallery_album a";


        $sql .= " LEFT JOIN " . DB_PREFIX . "xd_gallery_album_description ad ON (a.album_id = ad.album_id) LEFT JOIN " . DB_PREFIX . "xd_gallery_album_to_store a2s ON (a.album_id = a2s.album_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND a.status = '1' AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " ORDER BY  a.sort_order DESC";
        } else {
            $sql .= " ORDER BY  a.sort_order ASC";
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

        $album_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $album_data[$result['album_id']] = $this->getAlbum($result['album_id']);
        }

        return $album_data;
    }

    public function getTotalAlbums($data = array())
    {

        $sql = "SELECT COUNT(DISTINCT a.album_id) AS total";

        $sql .= " FROM " . DB_PREFIX . "xd_gallery_album a";

        $sql .= " LEFT JOIN " . DB_PREFIX . "xd_gallery_album_description ad ON (a.album_id = ad.album_id) LEFT JOIN " . DB_PREFIX . "xd_gallery_album_to_store a2s ON (a.album_id = a2s.album_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND a.status = '1' AND a2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getAlbumImages($album_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "xd_gallery_image i WHERE i.album_id = '" . (int)$album_id . "' ORDER BY i.sort_order ASC");

        return $query->rows;
    }
}
