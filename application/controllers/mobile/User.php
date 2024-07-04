<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/qrlib/qrlib.php';
include_once 'Mobile_controller.php';

class User extends Mobile_member_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->check_login();
        $this->load->model('user_model');
        $this->load->model('User_RecyclableItem_model');
        $this->load->model('recyclableitem_model');
        $this->load->model('recyclehistory_model');
        $this->load->model('support_model');
        $this->load->model('referral_model');
        $this->load->model('level_model');
        $this->load->model('merchant_model');
        $this->load->model('merchant_item_model');
    }
    public function send_qr_chain()
    {
        $user_info = $this->user_info;
        $upload_path = UPLOADS_PATH . "/qrTmp/";
        if (!file_exists($upload_path) || !is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }
        // $codeContents = 'zimbo://me?'.$user_info['user_name']."_".preg_split("#@#", $user_info['user_email'])[0];
        $codeContents = 'zimbo://me?' . $user_info['user_name'] . "_" . preg_split("#@#", $user_info['user_email'])[0];

        $fileName = TIMESTAMP . '.png';

        $pngAbsoluteFilePath = $upload_path . $fileName;

        QRcode::png($codeContents, $pngAbsoluteFilePath);

        $subject = "KeyChain";
        $message_template = get_message_template(5);
        $message_template = str_replace("%%qr_link%%", base_url() . $pngAbsoluteFilePath, $message_template);
        $message_template = str_replace("%%white_logo%%", base_url() . "assets/global/img/ic_logo.png", $message_template);
        $rslt = $this->send_email($user_info['user_email'], $subject, $message_template);
        output_data(1);
    }

    public function get_recyclable_list()
    {
        $sql = "SELECT * from " . RECYCLABLE_ITEM_TB . " WHERE root_id = 0";
        $query = $this->db->query($sql);
        $recyclable_item_list = $query->result_array();
        if (empty($recyclable_item_list)) {
            $recyclable_item_list = array();
        } else {
            foreach ($recyclable_item_list as $key => $row) {
                if (!empty($row['image'])) {
                    $recyclable_item_list[$key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $row['image'];
                }
                $recyclable_item_list[$key]['user_recyclable_image'] = "";
                $sub_list = array();
                if ($row['has_sub'] == 1) {
                    $sub_sql = "SELECT * from ".RECYCLABLE_ITEM_TB." where root_id = " . $row["id"];
                    $sub_query = $this->db->query($sub_sql);
                    $sub_list = $sub_query->result_array();
                    if (empty($sub_list)) {
                        $sub_list = array();
                    } else {
                        foreach ($sub_list as $sub_key => $sub_row) {
                            if (!empty($sub_row['image'])) {
                                $sub_list[$sub_key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $sub_row['image'];
                            }
                            $sub_list[$sub_key]['user_recyclable_image'] = "";
                            $sub_list[$sub_key]['sub_list'] = array();
                        }
                    }
                }
                $recyclable_item_list[$key]['sub_list'] = $sub_list;
            }
        }
        output_data(array('recyclable_list' => $recyclable_item_list));
    }
    public function get_available_recyclable_list()
    {
        $user_info = $this->user_info;
        $sql = "SELECT * from " . RECYCLABLE_ITEM_TB . " WHERE id not in ( SELECT recyclable_item_id from " . USER_RECYCLABLE_ITEM_TB . " WHERE status = 1 and user_id = " . $user_info['id'] . ") and root_id = 0";
        $query = $this->db->query($sql);
        $recyclable_item_list = $query->result_array();
        if (empty($recyclable_item_list)) {
            $recyclable_item_list = array();
        } else {
            foreach ($recyclable_item_list as $key => $row) {
                if (!empty($row['image'])) {
                    $recyclable_item_list[$key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $row['image'];
                }
                $recyclable_item_list[$key]['user_recyclable_image'] = "";

                $sub_list = array();
                if ($row['has_sub'] == 1) {
                    $sub_sql = "SELECT * from ".RECYCLABLE_ITEM_TB." where root_id = " . $row["id"];
                    // $sub_sql = "SELECT * from ".RECYCLABLE_ITEM_TB." where id not in ( SELECT recyclable_item_id from " . USER_RECYCLABLE_ITEM_TB . " WHERE status = 1 and user_id = " . $user_info['id'] . ") and  root_id = " . $row["id"];
                    $sub_query = $this->db->query($sub_sql);
                    $sub_list = $sub_query->result_array();
                    if (empty($sub_list)) {
                        $sub_list = array();
                    } else {
                        foreach ($sub_list as $sub_key => $sub_row) {
                            if (!empty($sub_row['image'])) {
                                $sub_list[$sub_key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $sub_row['image'];
                            }
                            $sub_list[$sub_key]['user_recyclable_image'] = "";
                            $sub_list[$sub_key]['sub_list'] = array();
                        }
                    }
                    // if (sizeof($sub_list) == 0) {
                    //     unset($recyclable_item_list[$key]);
                    // } else {
                    //     $recyclable_item_list[$key]['sub_list'] = $sub_list;
                    // }
                    $recyclable_item_list[$key]['sub_list'] = $sub_list;
                } else {
                    $recyclable_item_list[$key]['sub_list'] = $sub_list;
                }

            }
        }
        // $recyclable_item_list = array_values($recyclable_item_list);
        output_data(array('recyclable_list' => $recyclable_item_list));
    }
    public function add_user_recyclable_items()
    {
        $user_info = $this->user_info;
        $list = get_param_from_request('recyclable_items');
        if (empty($list)) {
            output_error("Please select recyclable items");
        }

        foreach ($list as $key => $row) {
            $upload_location = "uploads/" . USER_RECYCLE_ITEM_IMAGE_PATH . "/";
            $item_image_path = "";

            if (isset($_FILES['recyclable_items']) && isset($_FILES['recyclable_items']['name'][$key]) && sizeof($_FILES['recyclable_items']['name'][$key]) != 0 && $_FILES['recyclable_items']['name'][$key]["image"] != '') {
                $filename = $_FILES['recyclable_items']['name'][$key]["image"];
                $type = explode(".", $filename)[1];
                $new_name = $this->generateRandomString();
                $new_name .= "." . $type;
                $path = $upload_location . $new_name;
                if (move_uploaded_file($_FILES['recyclable_items']['tmp_name'][$key]["image"], $path)) {
                    $item_image_path = base_url() . $path;
                }
            }

            $item_data = array(
                "user_id" => $user_info["id"],
                "recyclable_item_id" => $row['id'],
                "count" => 0,
                "user_recyclable_image" => $item_image_path,
                "timestamp" => TIMESTAMP,
            );
            $condition_arr = array(
                "user_id" => $user_info["id"],
                "recyclable_item_id" => $row['id'],
                "status" => 1,
            );
            $user_recyclable_item = $this->User_RecyclableItem_model->get_all($condition_arr);
            if (empty($user_recyclable_item)) {
                $this->User_RecyclableItem_model->insert($item_data);
            } else {
                $this->User_RecyclableItem_model->update($item_data, $condition_arr);
            }
        }
        $user_recyclable_item_list = $this->get_my_recyclable_list();

        output_data(array("user_recyclable_item_list" => $user_recyclable_item_list));
    }
    public function get_user_recyclable_list()
    {
        $user_recyclable_item_list = $this->get_my_recyclable_list();

        $user_info = $this->user_info;
        $sql = "SELECT * from " . RECYCLABLE_ITEM_TB . " WHERE id not in ( SELECT recyclable_item_id from " . USER_RECYCLABLE_ITEM_TB . " WHERE status = 1 and user_id = " . $user_info['id'] . ") and root_id = 0";
        $query = $this->db->query($sql);
        $available_item_list = $query->result_array();
        if (empty($available_item_list)) {
            $available_item_list = array();
        } else {
            foreach ($available_item_list as $key => $row) {
                if (!empty($row['image'])) {
                    $available_item_list[$key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $row['image'];
                }
                $available_item_list[$key]['user_recyclable_image'] = "";

                $sub_list = array();
                if ($row['has_sub'] == 1) {
                    $sub_sql = "SELECT * from ".RECYCLABLE_ITEM_TB." where root_id = " . $row["id"];
                    // $sub_sql = "SELECT * from ".RECYCLABLE_ITEM_TB." where id not in ( SELECT recyclable_item_id from " . USER_RECYCLABLE_ITEM_TB . " WHERE status = 1 and user_id = " . $user_info['id'] . ") and  root_id = " . $row["id"];
                    $sub_query = $this->db->query($sub_sql);
                    $sub_list = $sub_query->result_array();
                    if (empty($sub_list)) {
                        $sub_list = array();
                    } else {
                        foreach ($sub_list as $sub_key => $sub_row) {
                            if (!empty($sub_row['image'])) {
                                $sub_list[$sub_key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $sub_row['image'];
                            }
                            $sub_list[$sub_key]['user_recyclable_image'] = "";
                            $sub_list[$sub_key]['sub_list'] = array();
                        }
                    }
                    // if (sizeof($sub_list) == 0) {
                    //     unset($recyclable_item_list[$key]);
                    // } else {
                    //     $recyclable_item_list[$key]['sub_list'] = $sub_list;
                    // }
                    $recyclable_item_list[$key]['sub_list'] = $sub_list;
                } else {
                    $available_item_list[$key]['sub_list'] = $sub_list;
                }
            }
        }
        $available_item_list = array_values($available_item_list);
        output_data(array("user_recyclable_item_list" => $user_recyclable_item_list, "available_recyclable_list" => $available_item_list));
    }
    public function get_merchant_recyclable_list()
    {
        $merchant_id = get_param_from_request('merchant_id');
        if (empty($merchant_id)) {
            output_error("Invalid param.");
        }

        $merchant_info = $this->merchant_model->get(array("unique_id" => $merchant_id));

        if (empty($merchant_info)) {
            output_error("Merchant doesn't exist.");
        }

        $user_info = $this->user_info;
        $sql = "SELECT * from " . RECYCLABLE_ITEM_TB . " WHERE id in (" . $merchant_info['recycle_items'] . ")";
        $query = $this->db->query($sql);
        $recyclable_item_list = $query->result_array();
        if (empty($recyclable_item_list)) {
            $recyclable_item_list = array();
        } else {
            foreach ($recyclable_item_list as $key => $row) {
                if (!empty($row['image'])) {
                    $recyclable_item_list[$key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $row['image'];
                }
                $recyclable_item_list[$key]['user_recyclable_image'] = "";
                $sub_list = array();
                if ($row['has_sub'] == 1) {
                    $sub_sql = "SELECT * from ".RECYCLABLE_ITEM_TB." where root_id = " . $row["id"];
                    // $sub_sql = "SELECT * from ".RECYCLABLE_ITEM_TB." where id not in ( SELECT recyclable_item_id from " . USER_RECYCLABLE_ITEM_TB . " WHERE status = 1 and user_id = " . $user_info['id'] . ") and  root_id = " . $row["id"];
                    $sub_query = $this->db->query($sub_sql);
                    $sub_list = $sub_query->result_array();
                    if (empty($sub_list)) {
                        $sub_list = array();
                    } else {
                        foreach ($sub_list as $sub_key => $sub_row) {
                            if (!empty($sub_row['image'])) {
                                $sub_list[$sub_key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $sub_row['image'];
                            }
                            $sub_list[$sub_key]['user_recyclable_image'] = "";
                            $sub_list[$sub_key]['sub_list'] = array();
                        }
                    }
                    // if (sizeof($sub_list) == 0) {
                    //     unset($recyclable_item_list[$key]);
                    // } else {
                    //     $recyclable_item_list[$key]['sub_list'] = $sub_list;
                    // }
                    $recyclable_item_list[$key]['sub_list'] = $sub_list;
                } else {
                    $recyclable_item_list[$key]['sub_list'] = $sub_list;
                }
            }
        }

        output_data(array("user_recyclable_item_list" => $recyclable_item_list));
    }
    public function add_user_score()
    {
        $user_info = $this->user_info;
        $list = get_param_from_request('recyclable_ids');
        if (empty($list)) {
            output_error("Please select recyclable items");
        }
        $merchant_flag = get_param_from_request('merchant');
        if (empty($merchant_flag)) {
            $merchant_flag = false;
        }

        $merchant_id = get_param_from_request('merchant_id');
        if (empty($merchant_id)) {
            $merchant_id = '';
        }

        $merchant_used_count = 0;
        // $list = json_decode($list);
        foreach ($list as $key => $row) {
            $recyclable_id = $row['id'];
            $count = $row['count'];
            // $recyclable_id = $row->id;
            // $count = $row->count;
            $condition = array(
                "user_id" => $user_info["id"],
                "recyclable_item_id" => $recyclable_id,
                "status" => 1,
            );
            for ($i = 0; $i < $count; $i++) {
                $item_data = array(
                    "user_id" => $user_info["id"],
                    "recyclable_item_id" => $recyclable_id,
                    "scan_type" => $merchant_flag ? "merchant" : "keychain",
                    "merchant_id" => $merchant_id,
                    "timestamp" => TIMESTAMP,
                );
                $insert_id = $this->recyclehistory_model->insert($item_data);
                if (empty($insert_id)) {
                    output_error("Score add failed");
                }

            }
            if ($merchant_flag == true) {
                $merchant_used_count += intval($count);

                $merchant_info = $this->merchant_model->get(array('unique_id' => $merchant_id, 'status' => 1));
                if (empty($merchant_info)) {
                    output_error("Merchant does not exist");
                } else {
                    $check_arr = array('merchant_id' => $merchant_info['id'], 'recyclable_item_id' => $recyclable_id);
                    $merchant_recyclable_item = $this->merchant_item_model->get($check_arr);
                    $used_count = $merchant_recyclable_item["used_count"];
                    $used_count = intval($count) + intval($used_count);
                    $this->merchant_item_model->update(array("used_count" => $used_count), $check_arr);
                }
            } else {
                $user_recyclable_item = $this->User_RecyclableItem_model->get($condition);
                if (empty($user_recyclable_item)) {
                    output_error("Recyclable item does not exist");
                } else {
                    $used_count = $user_recyclable_item["used_count"];
                    $used_count = intval($count) + intval($used_count);
                    $this->User_RecyclableItem_model->update(array("used_count" => $used_count), $condition);
                }
            }
        }
        if ($merchant_flag == true) {
            $my_info = $this->user_model->get(array("id" => $user_info['id']));
            $merchant_count = $my_info['merchant_using'];
            $merchant_count = $merchant_count + $merchant_used_count;
            $this->user_model->update(array("merchant_using" => $merchant_count), array("id" => $user_info['id']));
        }

        $user_info = $this->adjust_user_info($user_info);
        output_data(array('user_info' => $user_info));
    }

    public function get_home_detail()
    {
        $user_info = $this->user_info;

        $insight_type = get_param_from_request('type');

        $carbon_offset_value = 0;
        $savings_value = 0;
        $reuse_value = 0;
        $tree_value = 0;

        $carbon_sql = "select a.*, b.name, b.carbon_offset, b.savings, b.image, count(a.recyclable_item_id) as sum_count from " . RECYCLE_HISTORY_TB . " a left join " . RECYCLABLE_ITEM_TB . " b on a.recyclable_item_id = b.id where a.user_id = '" . $user_info['id'] . "' GROUP BY a.recyclable_item_id";
        $carbon_query = $this->db->query($carbon_sql);
        $carbon_list = $carbon_query->result_array();
        $tree_value = 0;
        foreach ($carbon_list as $key => $item) {
            $carbon_list[$key]['sub_carbon_offset'] = strval(round($item['sum_count'] * $item['carbon_offset'], 2));
            $carbon_list[$key]['sub_savings'] = strval(round($item['sum_count'] * $item['savings'], 2));
            $reuse_value += $item['sum_count'];
            $carbon_offset_value += $item['sum_count'] * $item['carbon_offset'];
            $savings_value += $item['sum_count'] * $item['savings'];
            $tree_value += number_format((float) round($item['sum_count'] * $item['carbon_offset'] / 22, 2), 2, '.', '');
        }
        // $tree_value = $carbon_offset_value / 22;

        $total = "";
        $title = "";
        if ($insight_type == "carbon_offset") {
            $total = strval(number_format((float) round($carbon_offset_value, 2), 2, '.', '')) . "kg";
            $title = "Carbon Offset";
        } else if ($insight_type == "savings") {
            $total = "$" . strval(number_format((float) round($savings_value, 2), 2, '.', ''));
            $title = "Savings";
        } else if ($insight_type == "reuse") {
            $total = strval($reuse_value);
            $title = "Reuse";
        } else if ($insight_type == "tree") {
            $total = strval(number_format((float) round($tree_value, 2), 2, '.', ''));
            $title = "Trees";
        }
        $summary_list = array();
        foreach ($carbon_list as $key => $item) {
            $summary_list[$key]['name'] = $item['name'];
            $summary_list[$key]['source'] = "";
            $summary_list[$key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $item['image'];
            if ($insight_type == "carbon_offset") {
                $summary_list[$key]['value'] = strval(number_format((float) round($item['sum_count'] * $item['carbon_offset'], 2), 2, '.', '')) . "kg CO2e";
                $summary_list[$key]['source'] = strval(number_format((float) round($item['carbon_offset'], 2), 2, '.', ''));
            } else if ($insight_type == "savings") {
                if ($item['savings'] == "0") {
                    unset($summary_list[$key]);
                } else {
                    $summary_list[$key]['value'] = "$" . strval(number_format((float) round($item['sum_count'] * $item['savings'], 2), 2, '.', ''));
                    $summary_list[$key]['source'] = "$" . strval(number_format((float) round($item['savings'], 2), 2, '.', ''));
                }
            } else if ($insight_type == "reuse") {
                $summary_list[$key]['value'] = strval($item['sum_count']);
            } else if ($insight_type == "tree") {
                // $summary_list[$key]['value'] = strval(round($item['sum_count'] * $item['carbon_offset'] / 22, 2));
                $summary_list[$key]['value'] = strval(number_format((float) round($item['sum_count'] * $item['carbon_offset'] / 22, 2), 2, '.', '')) . " trees";
            }
        }
        if ($insight_type == "savings") {
            $summary_list = array_values($summary_list);
        }
        output_data(array('title' => $title, 'total' => $total, 'summary_list' => $summary_list));
    }
    public function get_home_profile()
    {
        $user_info = $this->user_info;
        $user_info = $this->adjust_user_info($user_info);
        $available_status = "0";
        $score_value = $user_info['user_score'];

        $sql = "SELECT * from " . USER_RECYCLABLE_ITEM_TB . " where status = 1 and user_id = " . $user_info["id"];
        $query = $this->db->query($sql);
        $recyclable_item_list = $query->result_array();

        if (empty($recyclable_item_list)) {
            $score_value = 0;
        } else {
            foreach ($recyclable_item_list as $key => $row) {
                if ($key == 0) {
                    if (TIMESTAMP - $row['timestamp'] < 30 * 24 * 60 * 60) {
                        $available_status = "0";
                    } else {
                        $available_status = "1";
                    }
                }
            }
        }

        $level_list = $this->level_model->get_all();
        foreach ($level_list as $key => $item) {
            $current_flag = "0";
            $level_min = $item['score_min'];
            $level_max = $item['score_max'];
            $score_range = "";
            if ($level_max == 0) {
                if ($score_value >= $level_min) {
                    $current_flag = "1";
                } else {
                    $current_flag = "0";
                }
                $score_range = $level_min . "+";
            } else {
                if ($score_value >= $level_min && $score_value <= $level_max) {
                    $current_flag = "1";
                } else {
                    $current_flag = "0";
                }
                $score_range = $level_min . " ~ " . $level_max;
            }

            $level_list[$key]['current'] = $current_flag;
            $level_list[$key]['range'] = $score_range;
            $level_list[$key]['score_max'] = $item['score_max'] == null ? "" : $item['score_max'];
        }

        $carbon_offset_value = 0;
        $savings_value = 0;
        $reuse_value = 0;
        $tree_value = 0;

        $carbon_sql = "select a.*, b.name, b.carbon_offset, b.savings, count(a.recyclable_item_id) as sum_count from " . RECYCLE_HISTORY_TB . " a left join " . RECYCLABLE_ITEM_TB . " b on a.recyclable_item_id = b.id where a.user_id = '" . $user_info['id'] . "' GROUP BY a.recyclable_item_id";
        $carbon_query = $this->db->query($carbon_sql);
        $carbon_list = $carbon_query->result_array();
        foreach ($carbon_list as $key => $item) {
            $carbon_list[$key]['sub_carbon_offset'] = strval(round($item['sum_count'] * $item['carbon_offset'], 2));
            $carbon_list[$key]['sub_savings'] = strval(round($item['sum_count'] * $item['savings'], 2));
            $reuse_value += $item['sum_count'];
            $carbon_offset_value += $item['sum_count'] * $item['carbon_offset'];
            $savings_value += $item['sum_count'] * $item['savings'];
        }
        $tree_value = $carbon_offset_value / 22;

        $insight_list = array(
            "carbon_offset" => strval(number_format((float) round($carbon_offset_value, 2), 2, '.', '')) . "kg CO2e",
            "savings" => "$" . strval(number_format((float) round($savings_value, 2), 2, '.', '')),
            "reuse" => strval($reuse_value),
            "tree" => strval(number_format((float) round($tree_value, 2), 2, '.', '')),
        );

        output_data(array('available' => $available_status, 'level_list' => $level_list, 'insight_list' => $insight_list, 'user_info' => $user_info));
    }
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function delete_user_recyclable_item()
    {
        $user_info = $this->user_info;
        $recyclable_id = get_param_from_request('recyclable_id');
        $condition = array(
            "user_id" => $user_info["id"],
            "recyclable_item_id" => $recyclable_id,
            "status" => 1,
        );
        $user_recyclable_item = $this->User_RecyclableItem_model->get($condition);
        if (empty($user_recyclable_item)) {
            output_error("Recyclable item does not exist");
        } else {
            $item_data = array(
                "user_id" => $user_info["id"],
                "recyclable_item_id" => $recyclable_id,
                "status" => 0,
            );
            $this->User_RecyclableItem_model->update($item_data, $condition);
            $this->recyclehistory_model->update(array("status" => 0), $condition);
        }
        $user_info = $this->adjust_user_info($user_info);
        output_data(array('user_info' => $user_info));
    }
    public function update_user_recyclable_count()
    {
        $user_info = $this->user_info;
        $recyclable_id = get_param_from_request('recyclable_id');
        $count = get_param_from_request('count');
        $condition = array(
            "user_id" => $user_info["id"],
            "recyclable_item_id" => $recyclable_id,
            "status" => 1,
        );
        $user_recyclable_item = $this->User_RecyclableItem_model->get($condition);
        if (empty($user_recyclable_item)) {
            output_error("Recyclable item does not exist");
        } else {
            $item_data = array(
                "user_id" => $user_info["id"],
                "recyclable_item_id" => $recyclable_id,
                "count" => $count,
            );
            $this->User_RecyclableItem_model->update($item_data, $condition);
        }
        $user_info = $this->adjust_user_info($user_info);
        output_data(array('user_info' => $user_info));
    }

    public function get_point_history()
    {
        $user_info = $this->user_info;
        $sql = "SELECT recyclable_item_id as id, `timestamp` from " . RECYCLE_HISTORY_TB . " where user_id = " . $user_info["id"] . " and status = 1 order by timestamp desc;";
        $query = $this->db->query($sql);
        $history_list = $query->result_array();
        if (empty($history_list)) {
            $history_list = array();
        } else {
            foreach ($history_list as $key => $row) {
                $item_info = $this->get_recycle_item_info($row['id']);
                $recyclable_item_info = $this->get_user_recycle_item_info($row['id']);
                if (empty($item_info) || empty($recyclable_item_info)) {
                    unset($history_list[$key]);
                } else {
                    if (!empty($item_info['image'])) {
                        $history_list[$key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $item_info['image'];
                    }
                    $history_list[$key]['name'] = $item_info['name'];
                    $history_list[$key]['count'] = $recyclable_item_info['count'];
                    $history_list[$key]['used_count'] = $recyclable_item_info['used_count'];
                    $history_list[$key]['user_recyclable_image'] = $recyclable_item_info['user_recyclable_image'];
                    $history_list[$key]['is_checked'] = "1";
                }
            }
        }
        $history_list = array_values($history_list);
        output_data(array("list" => $history_list));
    }

    public function set_shopping_day()
    {
        $user_info = $this->user_info;
        $specific_day = get_param_from_request('specific_day');
        $day_info = get_param_from_request('day_info');
        if (!isset($specific_day) || !isset($day_info)) {
            output_error("Param does not exist");
        } else {
            $item_data = array(
                "specific_day" => $specific_day,
                "day_info" => $day_info,
            );
            $this->user_model->update($item_data, array("id" => $user_info['id']));
        }
        output_data(1);
    }
    private function get_recycle_item_info($item_id)
    {
        $user_info = $this->user_info;
        $condition = array(
            "id" => $item_id,
        );
        $user_recyclable_item = $this->recyclableitem_model->get($condition);
        if (empty($user_recyclable_item)) {
            return "";
        } else {
            return $user_recyclable_item;
        }
    }
    private function get_user_recycle_item_info($item_id)
    {
        $user_info = $this->user_info;
        $condition = array(
            "recyclable_item_id" => $item_id,
            "user_id" => $user_info['id'],
        );
        $user_recyclable_item = $this->User_RecyclableItem_model->get($condition);
        if (empty($user_recyclable_item)) {
            return "";
        } else {
            return $user_recyclable_item;
        }
    }
    private function get_my_recyclable_list()
    {
        $user_info = $this->user_info;
        $sql = "SELECT a.recyclable_item_id as id, a.count, a.user_recyclable_image, a.used_count, b.is_checked, b.name, b.image, b.is_multiple, b.description, b.root_id, b.has_sub from " . USER_RECYCLABLE_ITEM_TB . " a LEFT JOIN " . RECYCLABLE_ITEM_TB . " b ON a.recyclable_item_id = b.id WHERE a.status = 1 and a.user_id = " . $user_info["id"];
        $query = $this->db->query($sql);
        $recyclable_item_list = $query->result_array();
        if (empty($recyclable_item_list)) {
            $recyclable_item_list = array();
        } else {
            foreach ($recyclable_item_list as $key => $row) {
                if (!empty($row['image'])) {
                    $recyclable_item_list[$key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $row['image'];
                }
                $recyclable_item_list[$key]['is_checked'] = "1";
                $sub_list = array();
                if ($row['has_sub'] == 1) {
                    $sub_sql = "SELECT * from ".RECYCLABLE_ITEM_TB." where root_id = " . $row["id"];
                    // $sub_sql = "SELECT a.recyclable_item_id as id, a.count, a.user_recyclable_image, a.used_count, b.is_checked, b.name, b.image, b.is_multiple, b.description, b.root_id, b.has_sub from " . USER_RECYCLABLE_ITEM_TB . " a LEFT JOIN " . RECYCLABLE_ITEM_TB . " b ON a.recyclable_item_id = b.id WHERE a.status = 1 and b.root_id = " . $row["id"];
                    $sub_query = $this->db->query($sub_sql);
                    $sub_list = $sub_query->result_array();
                    if (empty($sub_list)) {
                        $sub_list = array();
                    } else {
                        foreach ($sub_list as $sub_key => $sub_row) {
                            if (!empty($sub_row['image'])) {
                                $sub_list[$sub_key]['image'] = base_url() . UPLOADS_PATH . "/" . RECYCLABLE_ITEM_PATH . "/" . $sub_row['image'];
                            }
                            $sub_list[$sub_key]['is_checked'] = "1";
                            $sub_list[$sub_key]['sub_list'] = array();
                        }
                    }
                }
                $recyclable_item_list[$key]['sub_list'] = $sub_list;
            }
        }
        return $recyclable_item_list;
    }

    public function profile()
    {
        $user_info = $this->user_info;
        $user_info = $this->adjust_user_info($user_info);
        output_data(array('user_info' => $user_info));
    }
    public function add_postal_code()
    {
        $user_info = $this->user_info;
        $update_data = array();

        if (isset($_POST['first_name'])) {
            $update_data['user_first_name'] = $_POST['first_name'];
        } else {
            output_error("first name is required");
        }
        if (isset($_POST['last_name'])) {
            $update_data['user_last_name'] = $_POST['last_name'];
        } else {
            output_error("last name is required");
        }
        if (isset($_POST['street'])) {
            $update_data['street'] = $_POST['street'];
        } else {
            output_error("street is required");
        }
        if (isset($_POST['apt'])) {
            $update_data['apt'] = $_POST['apt'];
        }
        if (isset($_POST['city'])) {
            $update_data['city'] = $_POST['city'];
        } else {
            output_error("city is required");
        }
        if (isset($_POST['state'])) {
            $update_data['state'] = $_POST['state'];
        } else {
            output_error("state is required");
        }
        if (isset($_POST['postcode'])) {
            $update_data['postal_code'] = $_POST['postcode'];
        } else {
            output_error("postal code is required");
        }
        if (isset($_POST['country'])) {
            $update_data['country'] = $_POST['country'];
        } else {
            output_error("country is required");
        }
        $this->user_model->update($update_data, array('id' => $user_info['id']));

        $subject = "New User - Postal address";
        $message_template = isset($_POST['apt']) && !empty($_POST['apt']) ? get_message_template(7) : get_message_template(6);
        $message_template = str_replace("%%user_name%%", $_POST['first_name'] . " " . $_POST['last_name'], $message_template);
        $message_template = str_replace("%%street%%", $_POST['street'], $message_template);
        $message_template = str_replace("%%apt%%", $_POST['apt'], $message_template);
        $message_template = str_replace("%%city%%", $_POST['city'], $message_template);
        $message_template = str_replace("%%state%%", $_POST['state'], $message_template);
        $message_template = str_replace("%%postal_code%%", $_POST['postcode'], $message_template);
        $message_template = str_replace("%%country%%", $_POST['country'], $message_template);
        $message_template = str_replace("%%white_logo%%", base_url() . "assets/global/img/ic_logo.png", $message_template);
        $rslt = $this->send_email(SUPPORT_EMAIL, $subject, $message_template);

        //generate_qr_code
        // $upload_path = UPLOADS_PATH."/qrTmp/";
        // if(!file_exists($upload_path) || !is_dir($upload_path)) {
        //     mkdir($upload_path, 0755, true);
        // }
        // $val = $user_info['user_name']."_".preg_split("#@#", $user_info['user_email'])[0];
        // // $codeContents = 'https://zimbo.me?'.base64_encode($val);
        // $codeContents = 'https://zimbo.me';

        // $fileName = TIMESTAMP.'.png';

        // $pngAbsoluteFilePath = $upload_path . $fileName;

        // QRcode::png($codeContents, $pngAbsoluteFilePath);

        // $subject = "KeyChain";
        // $message_template = get_message_template(5);
        // $message_template = str_replace("%%qr_link%%", base_url().$pngAbsoluteFilePath, $message_template);
        // $message_template = str_replace("%%white_logo%%", base_url()."assets/global/img/ic_logo.png", $message_template);
        // $rslt = $this->send_email($_POST['postal_code'], $subject, $message_template);
        output_data("1");
    }
    public function get_referral_code()
    {
        $user_info = $this->user_info;
        $referral_code = random(6);
        $this->referral_model->insert(array('user_id' => $user_info['id'], 'referral_code' => $referral_code));
        output_data(array('referral_code' => $referral_code));
    }
    public function update_profile()
    {
        $user_info = $this->user_info;
        $update_data = array();

        if (isset($_POST['user_name'])) {
            $update_data['user_name'] = $_POST['user_name'];
        }
        if (isset($_POST['first_name'])) {
            $update_data['user_first_name'] = $_POST['first_name'];
        }
        if (isset($_POST['last_name'])) {
            $update_data['user_last_name'] = $_POST['last_name'];
        }
        if (isset($_POST['country'])) {
            $update_data['country'] = $_POST['country'];
        }
        if (isset($_POST['address'])) {
            $update_data['address'] = $_POST['address'];
        }
        if (isset($_POST['user_phone']) && !empty($_POST['user_phone'])) {
            $user_phone = get_param_from_request('user_phone');
            $update_data['user_phone'] = $user_phone;
        }

        if (isset($_FILES['user_image'])) {
            $user_image = $this->upload_file(PROFILE_IMAGE_PATH, "user_image");
            $update_data['user_image'] = $user_image;
        }

        $user_email = get_param_from_request('user_email');
        if (!empty($user_email)) {
            $user_email = is_valid_email($user_email);
            if (empty($user_email)) {
                output_error("Invalid email address");
            }
            $update_data['user_email'] = $user_email;
        }

        $user_pass = get_param_from_request("password");
        if (!empty($user_pass)) {
            $update_data['user_password'] = md5($user_pass);
        }
        $update_data['id'] = $user_info['id'];
        list($rslt, $message) = $this->user_model->check_duplicate_account($update_data);
        if ($rslt) {
            output_error($message);
        }
        unset($update_data['id']);

        if (empty($update_data)) {
            output_error("Empty request");
        }
        $this->user_model->update($update_data, array('id' => $user_info['id']));
        $user_info = $this->user_model->get(array('id' => $user_info['id']));
        // $update_data_1['user_name'] = $user_info['user_first_name']." ".$user_info['user_last_name'];
        // $this->user_model->update($update_data_1, array('id'=>$user_info['id']));
        $user_info = $this->user_model->get(array('id' => $user_info['id']));
        $user_info = $this->adjust_user_info($user_info);

        output_data(array('user_info' => $user_info));
    }
    public function reset_score()
    {
        $user_info = $this->user_info;
        $update_data = array();
        $this->recyclehistory_model->update(array("status" => 0), array("user_id" => $user_info["id"]));
        output_data("1");
    }
    public function post_support()
    {
        $user_info = $this->user_info;
        $update_data = array();

        if (isset($_POST['content'])) {
            $update_data['content'] = $_POST['content'];
        } else {
            output_error("Content is empty");
        }
        $attach_flag = false;
        if (isset($_FILES['attachment'])) {
            $attachment = $this->upload_file("tmp", "attachment");
            $update_data['attachment'] = $attachment;
            $attach_flag = true;
        }
        $id = $this->support_model->insert($update_data);
        if (empty($id)) {
            output_error("Internal server failed");
        }
        $subject = "Support";
        $message_template = $attach_flag ? get_message_template(3) : get_message_template(4);
        if ($attach_flag) {
            $attachment_link = base_url() . "uploads/tmp/" . $attachment;
            $message_template = str_replace("%%image_url%%", $attachment_link, $message_template);
        }
        $message_template = str_replace("%%content%%", $update_data['content'], $message_template);
        $message_template = str_replace("%%white_logo%%", base_url() . "assets/global/img/ic_logo.png", $message_template);
        $rslt = $this->send_email(SUPPORT_EMAIL, $subject, $message_template);
        if ($rslt) {
            output_data("1");
        } else {
            output_error("Can not send email");
        }

    }
    public function post_share()
    {
        $user_info = $this->user_info;
        $update_data = array();

        if (isset($_POST['content'])) {
            $update_data['content'] = $_POST['content'];
        } else {
            output_error("Content is empty");
        }
        $attach_flag = false;
        if (isset($_FILES['attachment'])) {
            $attachment = $this->upload_file("tmp", "attachment");
            $update_data['attachment'] = $attachment;
            $attach_flag = true;
        }
        $id = $this->support_model->insert($update_data);
        if (empty($id)) {
            output_error("Internal server failed");
        }
        $subject = "Support";

        $message_template = $attach_flag ? get_message_template(3) : get_message_template(4);
        if ($attach_flag) {
            $attachment_link = base_url() . "uploads/tmp/" . $attachment;
            $message_template = str_replace("%%image_url%%", $attachment_link, $message_template);
        }
        $message_template = str_replace("%%content%%", $update_data['content'], $message_template);
        $message_template = str_replace("%%white_logo%%", base_url() . "assets/global/img/ic_logo.png", $message_template);
        $rslt = $this->send_email(SUPPORT_EMAIL, $subject, $message_template);
        if ($rslt) {
            output_data("1");
        } else {
            output_error("Can not send email");
        }

    }
    public function delete_profile()
    {
        $user_info = $this->user_info;
        $condition = array("user_id" => $user_info['id']);
        $this->user_model->update(array("is_deleted" => 1), array("id" => $user_info['id']));
        $this->User_RecyclableItem_model->update(array("status" => 0), $condition);
        $this->recyclehistory_model->update(array("status" => 0), $condition);
        $this->mb_token_model->delete($condition);
        output_data('1');
    }
    public function logout()
    {
        $this->load->model('mb_token_model');
        $token = get_param_from_request('token', "GET");
        if (empty($token)) {
            output_error("Parameter error");
        }

        $where = array('token' => $token);
        $token_info = $this->mb_token_model->get($where);
        if (empty($token_info)) {
            output_error("You already logged out");
        }

        $this->mb_token_model->delete($where);
        $id = $token_info['user_id'];
        $condition = array('id' => $id);
        $update_data = array('login_status' => '0');
        $result = $this->user_model->update($update_data, $condition);
        output_data('1');
    }

    public function update_profile_image()
    {
        $user_info = $this->user_info;
        $update_data = array();

        $update_data['id'] = $user_info['id'];
        list($rslt, $message) = $this->user_model->check_duplicate_account($update_data);
        if ($rslt) {
            output_error($message);
        }
        unset($update_data['id']);

        if (isset($_FILES['image'])) {
            $user_image = $this->upload_file(PROFILE_IMAGE_PATH, "image");
            if (!empty($user_image)) {
                $old_image = $user_info['user_image'];
                $image_link = base_url() . UPLOADS_PATH . "/" . PROFILE_IMAGE_PATH . "/" . $old_image;
                delete_files($image_link);
                $update_data['user_image'] = base_url() . UPLOADS_PATH . "/" . PROFILE_IMAGE_PATH . "/" . $user_image;
            }

        }
        if (isset($_POST['image'])) {
            $update_data['user_image'] = $_POST['image'];
        }

        if (empty($update_data)) {
            output_error("Empty request");
        }
        $this->user_model->update($update_data, array('id' => $user_info['id']));
        $user_info = $this->user_model->get(array('id' => $user_info['id']));
        $user_info = $this->adjust_user_info($user_info);
        output_data(array('user_info' => $user_info));
    }
    public function update_user_settings()
    {
        $user_info = $this->user_info;
        $setting_info = $_POST;
        $this->user_settings_model->update_user_setting($setting_info, $user_info['id']);
        $setting_info = U($user_info['id']);
        output_data(array('setting_info' => $setting_info));
    }
    public function update_user_setting()
    {
        $user_info = $this->user_info;
        $setting_info = array();
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        $setting_info[$type] = $value;
        $this->user_settings_model->update_user_setting($setting_info, $user_info['id']);
        $setting_info = U($user_info['id']);
        output_data(array('setting_info' => $setting_info));
    }

    public function send_welcome_message($user_id)
    {
        $user_info = $this->user_model->get(array('id' => $user_id));
        if (empty($user_info)) {
            return false;
        }

        if (empty($user_info['user_type'])) {
            return false;
        }

        $message_templage = get_message_template(2);
        $message = str_replace("%%first_name%%", $user_info['user_first_name'], $message_templage);
        $subject = "Welcome to Zimbo!";
        $this->send_email($user_info['user_email'], $subject, $message);
    }
    public function get_membership_status()
    {
        $user_info = $this->user_info;
        $condition = array(
            'receiver_id' => $user_info['id'],
            'status' => 0,
        );
        $membership_plan = $this->membership_model->getAll();
        $unread_count = $this->notification_model->countAll($condition);
        $membership_require = true;
        $membership_paid = true;
        if ($user_info['membership_type'] == 0) {
            if ($user_info['membership_time'] == 0) {
                $membership_require = false;
                $membership_paid = false;
            } else {
                if ($user_info['membership_time'] + 604800 >= TIMESTAMP) {
                    $membership_require = false;
                    $membership_paid = true;
                } else {
                    $membership_require = true;
                    $membership_paid = false;
                }
            }
        } else if ($user_info['membership_type'] == 1) {
            if ($user_info['membership_time'] + 2592000 >= TIMESTAMP) {
                $membership_require = false;
                $membership_paid = true;
            } else {
                $membership_require = true;
                $membership_paid = false;
            }
        } else if ($user_info['membership_type'] == 2) {
            if ($user_info['membership_time'] + 15552000 >= TIMESTAMP) {
                $membership_require = false;
                $membership_paid = true;
            } else {
                $membership_require = true;
                $membership_paid = false;
            }
        } else if ($user_info['membership_type'] == 3) {
            if ($user_info['membership_time'] + 31104000 >= TIMESTAMP) {
                $membership_require = false;
                $membership_paid = true;
            } else {
                $membership_require = true;
                $membership_paid = false;
            }
        }
        $data = array(
            'balance' => $user_info['balance'],
            'unread_count' => $unread_count . "",
            'membership_type' => $user_info['membership_type'],
            'membership_require' => $membership_require,
            'membership_paid' => $membership_paid,
            'membership_plan' => $membership_plan,
        );
        output_data($data);
    }

}
