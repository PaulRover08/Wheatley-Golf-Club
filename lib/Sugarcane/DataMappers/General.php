<?phpclass Sugarcane_DataMappers_General extends Sugarcane_DataMappers_Abstract {        public function getPages($parent = '', $published = '') {        $sql = "SELECT page_id, page_title, menu_text, permalink, type, published, parent, last_update FROM pages WHERE %s ORDER BY parent, ordering ASC";                $where = array();        if($parent == '0' || $parent) {            $where[] = sprintf('parent = %d', $parent);        }                if($published) {            $where[] = sprintf('published = %d', $published);        }                $whereClaus = count($where) ? implode(' AND ', $where) : '1';        $sql = sprintf($sql, $whereClaus);                return $this->db->fetchAll($sql);    }        public function getPage($page_id) {        $sql = sprintf("SELECT * FROM pages WHERE page_id = %d", $page_id);                return $this->db->fetchRow($sql);    }        public function getPagesByParentId($parent) {        $sql = sprintf("SELECT * FROM pages WHERE parent = %d ORDER BY ordering ASC", $parent);                return $this->db->fetchAll($sql);    }        public function getPageByPermalink($permalink, $published='') {        $sql = sprintf("SELECT * FROM pages WHERE permalink = '%s'", $permalink);                if($published != '') {            $sql .= ' AND published = 1';        }                return $this->db->fetchRow($sql);    }        public function getLastPage($parent) {        $sql = sprintf("SELECT * FROM pages WHERE parent = %d ORDER BY ordering DESC LIMIT 0,1", $parent);                return $this->db->fetchRow($sql);    }        public function updatePageOrder($page_id, $page_order, $page_parent) {        $sql = sprintf("UPDATE pages SET ordering = %d, parent = %d WHERE page_id = %d", $page_order, $page_parent, $page_id);                $this->db->query($sql);    }        public function getPagesMetaData($page_id='') {        $sql = sprintf("SELECT * FROM metadata WHERE page_id = %d", $page_id);        $metadata = $this->db->fetchRow($sql);                if(!$metadata) {            $sql = "SELECT * FROM cms_settings WHERE setting_title LIKE 'meta_%'";            $metaArray = $this->db->fetchAll($sql);                        $metadata = array();            foreach($metaArray as $metatag) {                $metadata[$metatag['setting_title']] = $metatag['setting_value'];            }        }                return $metadata;    }        public function getBlogInfo($blog_id) {        $sql = "SELECT * FROM blogs WHERE blog_id = " . $blog_id;                return $this->db->fetchRow($sql);    }        public function getBlogPosts($options) {        $sql = "SELECT * FROM blogposts WHERE %s ORDER BY date_added %s LIMIT 0,10";                $order = isset($options['order']) ? $options['order'] : 'DESC';                $where = array();        if(isset($options['filter']['approved'])) {            $where[] = "published = '" . $options['filter']['approved'] . "'";        }                if(isset($options['filter']['blog_id'])) {            $where[] = "blog_id = '" . $options['filter']['blog_id'] . "'";        }                $whereClaus = count($where) ? implode(' AND ', $where) : '1';        $sql = sprintf($sql, $whereClaus, $order);                return $this->db->fetchAll($sql);    }        public function getArchiveBlogPosts($options) {        $sql = "SELECT * FROM blogposts WHERE %s ORDER BY date_added %s LIMIT 10,1000";                $order = isset($options['order']) ? $options['order'] : 'DESC';                $where = array();        if(isset($options['filter']['approved'])) {            $where[] = "published = '" . $options['filter']['approved'] . "'";        }                if(isset($options['filter']['blog_id'])) {            $where[] = "blog_id = '" . $options['filter']['blog_id'] . "'";        }                $whereClaus = count($where) ? implode(' AND ', $where) : '1';        $sql = sprintf($sql, $whereClaus, $order);                return $this->db->fetchAll($sql);    }        public function getBlogPost($post_id) {        $sql = sprintf("SELECT * FROM blogposts WHERE post_id = %d", $post_id);                return $this->db->fetchRow($sql);    }        public function getLatestBlogPosts($numberOfItems) {        $sql = sprintf("SELECT * FROM blogposts WHERE published = 'Y' ORDER BY date_added DESC LIMIT 0, %d", $numberOfItems);                return $this->db->fetchAll($sql);    }        public function getPostComments($post_id) {        $sql = sprintf("SELECT * FROM blogcomments WHERE post_id = %d AND approved = 'Y'", $post_id);                return $this->db->fetchAll($sql);    }        public function getAllPostComments($post_id) {        $sql = sprintf("SELECT * FROM blogcomments WHERE post_id = %d", $post_id);                return $this->db->fetchAll($sql);    }        public function getEditableRegion($region_id) {        $sql = sprintf("SELECT * FROM editable_regions WHERE content_id = %d", $region_id);                return $this->db->fetchRow($sql);    }        public function getEditableRegionBySlug($slug) {        $sql = sprintf("SELECT * FROM editable_regions WHERE slug = '%s'", $slug);        $region = $this->db->fetchRow($sql);                return $region['content'];    }        /* News Related Functions */    public function getLatestNewsItems($numberOfItems) {        $sql = sprintf("SELECT * FROM news WHERE published = 1 ORDER BY newsdate DESC LIMIT 0, %d", $numberOfItems);                return $this->db->fetchAll($sql);    }        public function getNewsItem($news_id) {        $sql = sprintf("SELECT * FROM news WHERE news_id = %d AND published = 1", $news_id);                return $this->db->fetchRow($sql);    }        /* Header Images Multiple */    public function getHeaderImages() {        $sql = "SELECT * FROM header_images ORDER BY ordering ASC LIMIT 0,5";                return $this->db->fetchAll($sql);    }        /* Header Images Single  */    public function getPagesHeader($page_id='') {        $sql = sprintf("SELECT * FROM page_headers WHERE page_id = %d", $page_id);        $header = $this->db->fetchRow($sql);                if(!$header) {            $sql = "SELECT * FROM cms_settings WHERE setting_title LIKE 'page_header%'";            $headerArray = $this->db->fetchAll($sql);                        $header = array();            foreach($headerArray as $headerVar) {                $field = ($headerVar['setting_title'] == 'page_header') ? 'picture' : 'caption';                $header[$field] = $headerVar['setting_value'];            }        }                return $header;    }        /* Forms Specific */    public function locateApplicationFormByCredentials($email, $passcode) {        $sql = sprintf("SELECT form_id FROM initial_enquiry WHERE email = '%s' AND authKey = '%s' AND approved = 'Y'", $email, $passcode);                return $this->db->fetchRow($sql);    }        public function locateApplicationFormById($form_id, $approved = 'Y') {        $sql = sprintf("SELECT * FROM initial_enquiry WHERE form_id = %d AND approved = '%s'", $form_id, $approved);                return $this->db->fetchRow($sql);    }            // DO NOT EDIT BLOW THIS LINE        /*     * Save a record, pass in the data to be saved, the table to save to, and the primary key     */    public function saveRecord($data, $table, $primary_key) {        $pk_id = '';        if(isset($data[$primary_key])) {            $pk_id = $data[$primary_key];            unset($data[$primary_key]);        }                if(!$pk_id) {            $insert = $this->db->insert($table, $data);            return $this->db->lastInsertId();        } else {            $this->db->update($table, $data, $primary_key . ' = ' . $pk_id);            return $pk_id;        }    }        /*     * Delete a record from the database     */    public function deleteRecord($table, $key, $value) {        $removeSyntax = sprintf('%s = %d', $key, $value);                return $this->db->delete($table, $removeSyntax);    }        /*     * Delete children rows from a database     */    public function deleteChildren($table, $field, $value) {        $removeSyntax = sprintf('%s = %d', $field, $value);                return $this->db->delete($table, $removeSyntax);    }}