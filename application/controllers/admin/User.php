<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once 'Admin_controller.php';

class User extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->check_login();
        $this->load->model('user_model');
        $this->load->model('mb_token_model');
    }
    public function index()
    {
        $data = $this->data;
        $limit = 10; //item count per page
        $segment_index = 4;
        $sql = "select * from " . USER_TB . " where is_deleted = 0";
        if (isset($_GET['keyword1']) && !empty($_GET['keyword1'])) {
            $keyword1 = "%" . $_GET['keyword1'] . "%";
            $sql .= " and (user_name like '" . $keyword1 . "' or user_email like '" . $keyword1 . "')";
        }
        if (isset($_GET['status'])) {
            $sql .= " and status = " . intval($_GET['status']);
        }
        $query_cnt = $this->db->query($sql);
        $total = $query_cnt->num_rows();
        $sql .= " order by id desc";

        $page = 1;
        if (@$this->uri->segment($segment_index)) {
            $page = (int) $this->uri->segment($segment_index);
        }
        $offset = ($page - 1) * $limit;
        $sql .= " limit " . $offset . "," . $limit;
        $query = $this->db->query($sql);
        $user_list = $query->result_array();
        if (empty($user_list)) {
            $user_list = array();
        }

        foreach ($user_list as $index => $row) {
            $carbon_sql = "select a.*, b.name, SUM(b.carbon_offset) as sum_offset, count(a.recyclable_item_id) as sum_count from " . RECYCLE_HISTORY_TB . " a left join " . RECYCLABLE_ITEM_TB . " b on a.recyclable_item_id = b.id where a.user_id = '" . $row['id'] . "' GROUP BY a.recyclable_item_id, a.scan_type";
            $carbon_query = $this->db->query($carbon_sql);
            $carbon_list = $carbon_query->result_array();
            $item_name = "";
            $variety_count = 0;
            $ab = 0;
            foreach ($carbon_list as $key => $item) {
                $tmp_ab = 0;
                if ($item['scan_type'] == 'merchant') {
                    $tmp_ab = $item['sum_offset'] * 10;
                } else {
                    $tmp_ab = $item['sum_offset'];
                }
                $carbon_list[$key]['ab'] = $tmp_ab;
                $ab += $tmp_ab;
                if ($item['name'] != $item_name) {
                    $variety_count++;
                    $item_name = $item['name'];
                }
            }

            $referal_count = $row['referral_using'];
            $c = 0;
            $d = $referal_count;
            $e = $variety_count;

            $tmp_total = $ab + ($c + $d) * $e;

            $tmp_carbon_offset_value = 0;
            $tmp_savings_value = 0;
            $tmp_reuse_value = 0;
            $tmp_tree_value = 0;

            $carbon_sql = "select a.*, b.name, b.carbon_offset, b.savings, count(a.recyclable_item_id) as sum_count from " . RECYCLE_HISTORY_TB . " a left join " . RECYCLABLE_ITEM_TB . " b on a.recyclable_item_id = b.id where a.user_id = '" . $row['id'] . "' GROUP BY a.recyclable_item_id";
            $carbon_query = $this->db->query($carbon_sql);
            $carbon_list = $carbon_query->result_array();
            foreach ($carbon_list as $key => $item) {
                $tmp_reuse_value += $item['sum_count'];
                $tmp_carbon_offset_value += $item['sum_count'] * $item['carbon_offset'];
                $tmp_savings_value += $item['sum_count'] * $item['savings'];
            }
            $tmp_tree_value = $tmp_carbon_offset_value / 22;


            $user_list[$index]['total'] = strval(number_format((float) round($tmp_total, 2), 2, '.', ''));
            $user_list[$index]['carbon_offset'] = strval(number_format((float)round($tmp_carbon_offset_value, 2), 2, '.', ''))."Kg CO2e";
            $user_list[$index]['savings'] = "$".strval(number_format((float)round($tmp_savings_value, 2), 2, '.', ''));
            $user_list[$index]['reuse'] = strval($tmp_reuse_value);
            $user_list[$index]['tree'] = strval(number_format((float)round($tmp_tree_value, 2), 2, '.', ''));
        }
        $config['base_url'] = base_url('admin/user/index');
        $config['total_rows'] = $total;
        $config['per_page'] = $limit;
        $config['uri_segment'] = $segment_index;

        $data['list'] = $user_list;
        $data['show_pagination'] = pagination($config);
        $data['page_title'] = "User Management";
        $this->page->showpage($data);
    }
    public function detail($id)
    {
        $data = $this->data;
        $data['page_title'] = "Edit user";
        $condition = array('id' => $id);
        $info = $this->user_model->get($condition);
        if (empty($info)) {
            $this->session->set_flashdata('error_message', "User does not exist");
            redirect($_SERVER['HTTP_REFERER']);
        }
        $data['user'] = $info;
        if ($this->check_submit()) {
            $update_data = array();
            $update_data['user_name'] = $_POST['name'];
            // $update_data['user_name'] = $update_data['user_first_name']." ".$update_data['user_last_name'];
            // $update_data['user_email'] = $_POST['email'];
            // $update_data['user_phone'] = $_POST['phone'];
            // $update_data['aboutme'] = $_POST['aboutme'];
            // $update_data['website'] = $_POST['website'];
            if (!empty($_POST['password'])) {
                $update_data['user_password'] = md5($_POST['password']);
            }
            if (!empty($_POST['status'])) {
                $update_data['status'] = 1;
            } else {
                $update_data['status'] = 0;
            }
            $user_info = $update_data;
            $user_info['id'] = $id;
            list($check, $msg) = $this->user_model->check_duplicate_account($user_info);
            if ($check) {
                $this->session->set_flashdata('error_message', $msg);
                redirect($_SERVER['HTTP_REFERER']);
                return true;
            }
            //print_r($_FILES); die;
            if (isset($_FILES['upload_file']) && !empty($_FILES['upload_file']['name'])) {
                $user_image = $this->upload_file(PROFILE_IMAGE_PATH, "image");
                if (!empty($user_image)) {
                    $old_image = $user_info['user_image'];
                    $image_link = base_url() . UPLOADS_PATH . "/" . PROFILE_IMAGE_PATH . "/" . $old_image;
                    delete_files($image_link);
                    $update_data['user_image'] = base_url() . UPLOADS_PATH . "/" . PROFILE_IMAGE_PATH . "/" . $user_image;
                }

            }
            $this->user_model->update($update_data, $condition);
            $this->session->set_flashdata('status_message', "User info has been updated successfully");
            redirect(base_url('admin/user/index'));
        } else {
            $this->page->showpage($data);
        }
    }
    public function add()
    {
        $data = $this->data;
        $id = 0;
        if ($this->check_submit()) {
            $update_data = array();
            $update_data['user_first_name'] = $_POST['user_first_name'];
            $update_data['user_last_name'] = $_POST['user_last_name'];
            $update_data['user_name'] = $update_data['user_first_name'] . " " . $update_data['user_last_name'];
            $update_data['user_email'] = $_POST['user_email'];
            $update_data['user_phone'] = $_POST['user_phone'];
            $update_data['user_kind'] = $_POST['user_kind'];
            $update_data['user_type'] = $_POST['user_type'];
            $update_data['lat'] = $_POST['lat'];
            $update_data['lon'] = $_POST['lon'];
            if (isset($_POST['is_nhs'])) {
                $update_data['is_nhs'] = intval($_POST['is_nhs']);
            } else {
                $update_data['is_nhs'] = 0;
            }
            if (!empty($_POST['user_password'])) {
                $update_data['user_password'] = md5($_POST['user_password']);
            }
            $user_info = $update_data;
            $user_info['id'] = $id;
            list($check, $msg) = $this->user_model->check_duplicate_account($user_info);
            if ($check) {
                $this->session->set_flashdata('error_message', $msg);
                redirect($_SERVER['HTTP_REFERER']);
                return true;
            }
            $upload_filename = $this->upload_file();
            if (!empty($upload_filename)) {
                if (!empty($info['user_image'])) {
                    @unlink(FCPATH . "/" . "uploads/" . PROFILE_IMAGE_PATH . "/" . $info['user_image']);
                }
                $update_data['user_image'] = $upload_filename;
            }
            $this->user_model->insert($update_data);
            $this->session->set_flashdata('status_message', "User has been added successfully");
            redirect(base_url('admin/user/index'));
        } else {
            $data['page_title'] = "Add user";
            $this->page->showpage($data, 'main', 'user/detail');
        }
    }
    public function delete($id)
    {
        /*$condition = array('id'=>$id);
        $info=$this->user_model->get($condition);
        $update_data = array('is_deleted'=>1);
        $this->user_model->update($update_data, $condition);
        $this->session->set_flashdata('status_message', 'User deleted successfully.');
        redirect($_SERVER['HTTP_REFERER']);*/

        $condition = array('id' => $id);
        $info = $this->user_model->get($condition);
        $this->user_model->delete($condition);
        $condition1 = array('user_id' => $id);
        $this->mb_token_model->delete($condition1);
        $this->session->set_flashdata('status_message', 'User deleted successfully.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function change_status()
    {
        $id = $this->input->post('user_id');
        $condition = array('id' => $id);
        $info = $this->user_model->get($condition);
        if (!empty($info)) {
            $update_data = array('status' => 1);
            $message = 'User has been activated.';
            if ($info['status'] == 1) {
                $update_data = array('status' => 0);
                $message = 'User has been blocked';
            }
            if (!empty($update_data)) {
                $this->user_model->update($update_data, $condition);
            }
            $this->json_output_data('1', $message);
        } else {
            $message = "User does not exist";
            $this->json_output_error($message);
        }
    }
    public function send_email_to_user($id)
    {
        $condition = array('id' => $id);
        $info = $this->user_model->get($condition);
        if (!empty($info)) {
            $message = $this->input->post('message');
            $this->send_email($info['user_email'], "Message From " . C('app_name') . " admin", $message);
            $message = "Email has been sent successfully";
            $this->json_output_data(1, $message);
        } else {
            $message = "User does not exist";
            $this->json_output_error($message);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function upload_file()
    { //upload image
        $this->load->library('upload');
        $save_path = PROFILE_IMAGE_PATH;
        list($result, $upload_file_name) = $this->upload->upload_to_local($save_path, 'upload_file');
        if (!$result) {
            return '';
        }

        //$pic_file = FCPATH.UPLOADS_PATH."/{$save_path}/{$upload_file_name}";
        return $upload_file_name;
    }

}
