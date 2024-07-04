<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_log_model extends MY_Model {

    public function __construct() {
        parent::__construct('admin_log');
    }

    /////////////////////////////////////// begin public function section //////////////////////////////////////////////
    public function get_admin_log_list($condition, $page=0, $order=array(), $fields='*') {
        $this->fields($fields)->where($condition);
        if(!empty($order)) {
            $this->order_by($order);
        }
        $page = $page <= 0 ? $this->rows_per_page : $page;
        return $this->paginate($page, NULL, $this->page_number);
    }

    public function add($admin_info, $content, $action) {
        $data = array(
            'admin_id' => $admin_info['id'],
            'admin_name' => $admin_info['name'],
            'content' => $content,
            'action' => $action,
        );

        return $this->insert($data);
    }
    /////////////////////////////////////// end public function section //////////////////////////////////////////////

    protected function _before_create($data) {
        if(!isset($data['create_time']))
            $data['create_time'] = time();
        if(!isset($data['ip']))
            $data['ip'] = $this->input->ip_address();
        return $data;
    }

}