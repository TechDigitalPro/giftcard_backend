<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once 'Admin_controller.php';

class Pizzo extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->check_login();
        $this->load->model('addiopizzo_model');
        $this->load->model('story_description_model');
        $this->load->model('story_detail_model');
    }

    public function get_pizzo_list()
    {
        $type = "addiopizzo";
        // $data_list = $this->addiopizzo_model->get_all(array('status' => 1));

        $sql = "select * from ".ADDIOPIZZO_TB." where status = 1 order by id desc";
        $query = $this->db->query($sql);
        $data_list = $query->result_array();
        if (empty($data_list)) {
            $data_list = array();
        }

        if (!empty($data_list)) {
            foreach ($data_list as $key => $item) {
                $data_list[$key]['location'] = json_decode($item['location']);

                $cat_info = $this->story_description_model->get(array('key' => $item['cat'], 'type' => $type));
                $cat_en = "";
                $cat_it = "";
                if (!empty($cat_info)) {
                    $cat_en = $cat_info['des_en'];
                    $cat_it = $cat_info['des_it'];
                }
                if ($cat_en == null) {
                    $cat_en = "";
                }
                if ($cat_it == null) {
                    $cat_it = "";
                }
                $data_list[$key]['cat'] = array(
                    "en" => $cat_en,
                    "it" => $cat_it,
                );

                $subcat_info = $this->story_description_model->get(array('key' => $item['subcat'], 'type' => $type));
                $subcat_en = "";
                $subcat_it = "";
                if (!empty($subcat_info)) {
                    $subcat_en = $subcat_info['des_en'];
                    $subcat_it = $subcat_info['des_it'];
                }
                if ($subcat_en == null) {
                    $subcat_en = "";
                }
                if ($subcat_it == null) {
                    $subcat_it = "";
                }
                $data_list[$key]['subcat'] = array(
                    "en" => $subcat_en,
                    "it" => $subcat_it,
                );
                $data_list[$key]['subcat_key'] = $item['subcat'];
                unset($data_list[$key]['type']);
                unset($data_list[$key]['index']);
            }
        }

        $detail_item = $this->story_detail_model->get(array("type" => $type));
        $detail_info = array();
        if (!empty($detail_item)) {

            $detail_description_info = $this->story_description_model->get(array('key' => $detail_item['description'], 'type' => $type . "_detail"));
            $detail_description_en = "";
            $detail_description_it = "";
            if (!empty($detail_description_info)) {
                $detail_description_en = $detail_description_info['des_en'];
                $detail_description_it = $detail_description_info['des_it'];
            }
            if ($detail_description_en == null) {
                $detail_description_en = "";
            }
            if ($detail_description_it == null) {
                $detail_description_it = "";
            }

            $detail_info['description'] = array(
                "en" => $detail_description_en,
                "it" => $detail_description_it,
            );

            $detail_content_info = $this->story_description_model->get(array('key' => $detail_item['bodyguard_title'], 'type' => $type . "_detail"));
            $detail_content_en = "";
            $detail_content_it = "";
            if (!empty($detail_content_info)) {
                $detail_content_en = $detail_content_info['des_en'];
                $detail_content_it = $detail_content_info['des_it'];
            }
            if ($detail_content_en == null) {
                $detail_content_en = "";
            }
            if ($detail_content_it == null) {
                $detail_content_it = "";
            }

            $detail_info['bodyguard_title'] = array(
                "en" => $detail_content_en,
                "it" => $detail_content_it,
            );

            $detail_info['name'] = empty($detail_item['name']) ? "" : $detail_item['name'];
            $detail_info['short_name'] = empty($detail_item['short_name']) ? "" : $detail_item['short_name'];
            $detail_info['profile_icon'] = empty($detail_item['profile_icon']) ? "" : $detail_item['profile_icon'];
            $detail_info['speaker_icon'] = empty($detail_item['speaker_icon']) ? "" : $detail_item['speaker_icon'];
            $detail_info['augmented_sound_url'] = empty($detail_item['augmented_sound_url']) ? "" : $detail_item['augmented_sound_url'];
            $detail_info['resources_base_url'] = $detail_item['resources_base_url'];
        }

        output_data(array("list" => $data_list, "detail" => $detail_info));
    }
    public function update_pizzo()
    {
        $type = "addiopizzo";

        $id = get_param_from_request('id');
        $name = get_param_from_request('name');
        $description = get_param_from_request('description');
        $cat_en = get_param_from_request('cat_en');
        $cat_it = get_param_from_request('cat_it');
        $subcat_en = get_param_from_request('subcat_en');
        $subcat_it = get_param_from_request('subcat_it');
        $phone = get_param_from_request('phone');
        $web = get_param_from_request('web');
        $cat = get_param_from_request('cat');
        $cat_type = get_param_from_request('cat_type');
        $subcat = get_param_from_request('subcat');
        $lat = get_param_from_request('lat');
        $lng = get_param_from_request('lng');
        $city = get_param_from_request('city');
        $street = get_param_from_request('street');
        $country = get_param_from_request('country');

        $storyItem = $this->addiopizzo_model->get(array("id" => $id));
        if (empty($storyItem)) {
            output_error("Story doesn't exist");
        } else {
            $cat_key = $storyItem['cat'];
            $this->story_description_model->update(
                array('des_en' => $cat_en, 'des_it' => $cat_it),
                array('key' => $cat_key, 'type' => $type)
            );
            $subcat_key = $storyItem['subcat'];
            $this->story_description_model->update(
                array('des_en' => $subcat_en, 'des_it' => $subcat_it),
                array('key' => $subcat_key, 'type' => $type)
            );
            $location_arr = array();
            $address_arr = array();
            $address_arr['city'] = $city;
            $address_arr['street'] = $street;
            $address_arr['country'] = $country;
            $location_arr['address'] = $address_arr;
            $position_arr = array();
            $position_arr['latitude'] = $lat;
            $position_arr['longitude'] = $lng;
            $location_arr['position'] = $position_arr;

            $this->addiopizzo_model->update(
                array(
                    'name' => $name,
                    'description' => $description,
                    'phone' => $phone,
                    'web' => $web,
                    'cat' => $cat,
                    'cat_type' => $cat_type,
                    'subcat' => $subcat,
                    'location' => json_encode($location_arr)
                ),
                array('id' => $id));
            output_data(1);
        }

    }
    public function add_pizzo()
    {
        $type = "addiopizzo";

        $id = get_param_from_request('id');
        $name = get_param_from_request('name');
        $description = get_param_from_request('description');
        $cat_en = get_param_from_request('cat_en');
        $cat_it = get_param_from_request('cat_it');
        $subcat_en = get_param_from_request('subcat_en');
        $subcat_it = get_param_from_request('subcat_it');
        $phone = get_param_from_request('phone');
        $web = get_param_from_request('web');
        $cat = get_param_from_request('cat');
        $cat_type = get_param_from_request('cat_type');
        $subcat = get_param_from_request('subcat');
        $lat = get_param_from_request('lat');
        $lng = get_param_from_request('lng');
        $city = get_param_from_request('city');
        $street = get_param_from_request('street');
        $country = get_param_from_request('country');
        $this->story_description_model->update(
            array('des_en' => $cat_en, 'des_it' => $cat_it),
            array('key' => $cat, 'type' => $type)
        );
        $this->story_description_model->update(
            array('des_en' => $subcat_en, 'des_it' => $subcat_it),
            array('key' => $subcat, 'type' => $type)
        );
        if (!empty($name)) {
            $location_arr = array();
            $address_arr = array();
            $address_arr['city'] = $city;
            $address_arr['street'] = $street;
            $address_arr['country'] = $country;
            $location_arr['address'] = $address_arr;
            $position_arr = array();
            $position_arr['latitude'] = $lat;
            $position_arr['longitude'] = $lng;
            $location_arr['position'] = $position_arr;

            $this->addiopizzo_model->insert(
                array(
                    'name' => $name,
                    'description' => $description,
                    'phone' => $phone,
                    'web' => $web,
                    'cat' => $cat,
                    'cat_type' => $cat_type,
                    'subcat' => $subcat,
                    'location' => json_encode($location_arr),
                    'timestamp' => TIMESTAMP,
                ));
        }
        output_data(1);
    }
    public function get_pizzo_cat_list()
    {
        $cat_list = array();
        $sql = "SELECT * from " . ADDIOPIZZO_TB . " group by cat_type";
        $query = $this->db->query($sql);
        $parentList = $query->result_array();
        if (empty($parentList)) {
            $parentList = array();
        } else {
            foreach ($parentList as $key => $row) {
                $cat_list[] = array(
                    "name" => $row['cat'],
                    "cat" => $row['cat'],
                    "cat_type" => $row['cat_type'],
                    "subcat" => $row['subcat'],
                    "parent" => true,
                );
                $sql_child = "SELECT * from " . ADDIOPIZZO_TB . " where cat_type = '" . $row['cat_type'] . "' group by subcat";
                $query_child = $this->db->query($sql_child);
                $childList = $query_child->result_array();
                if (!empty($childList)) {
                    foreach ($childList as $ckey => $cRow) {
                        $cat_list[] = array(
                            "name" => $cRow['subcat'],
                            "cat" => $cRow['cat'],
                            "cat_type" => $cRow['cat_type'],
                            "subcat" => $cRow['subcat'],
                            "parent" => false,
                        );
                    }
                }
            }
        }
        output_data($cat_list);
    }

    public function delete_pizzo()
    {
        
        $id = get_param_from_request('id');
       
        $this->addiopizzo_model->update(
            array(
                'status' => 0,
            ), 
            array("id" => $id)
        );
        output_data(1);
    }
}
