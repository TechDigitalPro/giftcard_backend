<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/paypal/paypal.php';
require_once APPPATH . 'third_party/stripe-php/init.php';
include_once 'Mobile_controller.php';

class Main extends Mobile_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('story_model');
        $this->load->model('story_description_model');
        $this->load->model('addiopizzo_model');
        $this->load->model('configuration_model');
        $this->load->model('francese_model');
        $this->load->model('project_model');
        $this->load->model('story_detail_model');
    }
    public function update_user_info()
    {
        $token = get_param_from_request('token');
        $email = get_param_from_request('email');
        if (empty($email)) {
            output_error("email is required");
        }
        $name = get_param_from_request('name');
        if (empty($name)) {
            output_error("name is required");
        }
        $dob = get_param_from_request('dob');
        if (empty($dob)) {
            output_error("birth date is required");
        }
        if (empty($token)) {
            $user_info = $this->user_model->get(array("email" => $email));
            if (empty($user_info)) {
                $insert_data = array();
                $insert_data['name'] = $name;
                $insert_data['dob'] = $dob;
                $token = md5($name) . md5($dob . strval(TIMESTAMP) . strval(rand(0, 999999)));
                $insert_data['token'] = $token;
                $insert_data['created'] = TIMESTAMP;
                $insert_data['updated'] = TIMESTAMP;
                $insert_id = $this->user_model->insert($insert_data);
            } else {
                output_error("you are already in");
            }
        } else {
            $user_info = $this->user_model->get(array("token" => $token, "status" => 1));
            if (!empty($user_info)) {
                $this->user_model->update(array("status" => 0), array("token" => $token));
            }
            $insert_data = array();
            $insert_data['name'] = $name;
            $insert_data['dob'] = $dob;
            $insert_data['token'] = $token;
            $insert_data['created'] = TIMESTAMP;
            $insert_data['updated'] = TIMESTAMP;
            $insert_id = $this->user_model->insert($insert_data);
        }
        $user_info = $this->user_model->get(array("id" => $insert_id));
        output_data(array("user_info" => $user_info));
    }
    public function get_story_list()
    {

        $type = get_param_from_request('type');
        if (empty($type)) {
            output_error("type is required");
        }

        $data_list = $this->story_model->get_all(array('type' => $type, 'status' => 1));
        if (empty($data_list)) {
            output_error("invalid type");
        }
        foreach ($data_list as $key => $item) {
            $data_list[$key]['address'] = json_decode($item['address']);
            $data_list[$key]['id'] = $item['index'];
            $data_list[$key]['bodyguard_team'] = json_decode($item['bodyguard_team']);
            $bList = json_decode($item['bodyguard_team']);
            if (sizeof($bList) > 0) {
                foreach ($bList as $keys => $items) {
                    $bodyguard_info = $this->story_description_model->get(array('key' => $items->bodyguard_description, 'type' => $type));
                    $bodyguard_en = "";
                    $bodyguard_it = "";
                    if (!empty($bodyguard_info)) {
                        $bodyguard_en = $bodyguard_info['des_en'];
                        $bodyguard_it = $bodyguard_info['des_it'];
                    }
                    if ($bodyguard_en == null) {
                        $bodyguard_en = "";
                    }
                    if ($bodyguard_it == null) {
                        $bodyguard_it = "";
                    }
                    $bList[$keys]->bodyguard_description = array(
                        "en" => $bodyguard_en,
                        "it" => $bodyguard_it,
                    );
                }
                $data_list[$key]['bodyguard_team'] = $bList;
            }
            $des_info = $this->story_description_model->get(array('key' => $item['description'], 'type' => $type));
            $des_en = "";
            $des_it = "";
            if (!empty($des_info)) {
                $des_en = $des_info['des_en'];
                $des_it = $des_info['des_it'];
            }
            if ($des_en == null) {
                $des_en = "";
            }
            if ($des_it == null) {
                $des_it = "";
            }
            $data_list[$key]['description'] = array(
                "en" => $des_en,
                "it" => $des_it,
            );
            $bodyguard_title_info = $this->story_description_model->get(array('key' => $item['bodyguard_title'], 'type' => $type));
            $bodyguard_title_en = "";
            $bodyguard_title_it = "";
            if (!empty($bodyguard_title_info)) {
                $bodyguard_title_en = $bodyguard_title_info['des_en'];
                $bodyguard_title_it = $bodyguard_title_info['des_it'];
            }
            if ($bodyguard_title_en == null) {
                $bodyguard_title_en = "";
            }
            if ($bodyguard_title_it == null) {
                $bodyguard_title_it = "";
            }
            $data_list[$key]['bodyguard_title'] = array(
                "en" => $bodyguard_title_en,
                "it" => $bodyguard_title_it,
            );
            $geofenceTitle_info = $this->story_description_model->get(array('key' => $item['geofenceTitle'], 'type' => $type));
            $geofenceTitle_en = "";
            $geofenceTitle_it = "";
            if (!empty($geofenceTitle_info)) {
                $geofenceTitle_en = $geofenceTitle_info['des_en'];
                $geofenceTitle_it = $geofenceTitle_info['des_it'];
            }
            if ($geofenceTitle_en == null) {
                $geofenceTitle_en = "";
            }
            if ($geofenceTitle_it == null) {
                $geofenceTitle_it = "";
            }
            $data_list[$key]['geofenceTitle'] = array(
                "en" => $geofenceTitle_en,
                "it" => $geofenceTitle_it,
            );
            $speakerDescription_info = $this->story_description_model->get(array('key' => $item['speakerDescription'], 'type' => $type));
            $speakerDescription_en = "";
            $speakerDescription_it = "";
            if (!empty($speakerDescription_info)) {
                $speakerDescription_en = $speakerDescription_info['des_en'];
                $speakerDescription_it = $speakerDescription_info['des_it'];
            }
            if ($speakerDescription_en == null) {
                $speakerDescription_en = "";
            }
            if ($speakerDescription_it == null) {
                $speakerDescription_it = "";
            }
            $data_list[$key]['speakerDescription'] = array(
                "en" => $speakerDescription_en,
                "it" => $speakerDescription_it,
            );

            $geofenceAlert_info = $this->story_description_model->get(array('key' => $item['geofenceAlert'], 'type' => $type));
            $geofenceAlert_en = "";
            $geofenceAlert_it = "";
            if (!empty($geofenceAlert_info)) {
                $geofenceAlert_en = $geofenceAlert_info['des_en'];
                $geofenceAlert_it = $geofenceAlert_info['des_it'];
            }
            if ($geofenceAlert_en == null) {
                $geofenceAlert_en = "";
            }
            if ($geofenceAlert_it == null) {
                $geofenceAlert_it = "";
            }
            $data_list[$key]['geofenceAlert'] = array(
                "en" => $geofenceAlert_en,
                "it" => $geofenceAlert_it,
            );

            unset($data_list[$key]['type']);
            unset($data_list[$key]['index']);
            unset($data_list[$key]['admin_id']);
            unset($data_list[$key]['status']);
            unset($data_list[$key]['timestamp']);
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
    public function get_story_detail()
    {
        $type = get_param_from_request('type');
        if (empty($type)) {
            output_error("paramter error");
        }
        $detail_item = $this->story_detail_model->get(array("type" => $type));
        if (empty($detail_item)) {
            output_error("Story doesn't exist");
        }

        $detail_info = array();
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

        output_data($detail_info);
    }
    public function get_francese()
    {
        $data_list = $this->francese_model->get_all(array('status' => 1));
        foreach ($data_list as $key => $item) {
            $data_list[$key]['cube_rotations'] = json_decode($item['cube_rotations']);
            $data_list[$key]['cube_textures'] = json_decode($item['cube_textures']);
            $data_list[$key]['cube_textures_low'] = json_decode($item['cube_textures_low']);
            $data_list[$key]['steps'] = json_decode($item['steps']);
            $data_list[$key]['supported_languages'] = json_decode($item['supported_languages']);
            unset($data_list[$key]['id']);
            unset($data_list[$key]['admin_id']);
            unset($data_list[$key]['status']);
            unset($data_list[$key]['timestamp']);
        }
        output_data($data_list[0]);
    }
    public function get_addiopizzo()
    {
        $data_list = $this->addiopizzo_model->get_all(array('status' => 1));
        foreach ($data_list as $key => $item) {

            $cat_info = $this->story_description_model->get(array('key' => $item['cat'], 'type' => "addiopizzo"));
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

            $subcat_info = $this->story_description_model->get(array('key' => $item['subcat'], 'type' => "addiopizzo"));
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
            $data_list[$key]['contacts'] = array(
                "phone" => $item['phone'],
                "web" => $item['web'],
            );
            $data_list[$key]['extra'] = array(
                "cat" => $data_list[$key]['cat'],
                "cat_type" => $data_list[$key]['cat_type'],
                "subcat" => $data_list[$key]['subcat'],
            );
            $data_list[$key]['location'] = json_decode($item['location']);
            unset($data_list[$key]['id']);
            unset($data_list[$key]['admin_id']);
            unset($data_list[$key]['status']);
            unset($data_list[$key]['timestamp']);
        }
        $type = "addiopizzo";
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

        output_data(array("entry" => $data_list, "detail" => $detail_info));
    }
    public function get_configuration()
    {
        $data_list = $this->configuration_model->get_all(array('status' => 1, 'type' => 'configuration'));
        foreach ($data_list as $key => $item) {
            $data_list[$key]['opengl_renderer_low'] = json_decode($item['opengl_renderer_low']);
            $data_list[$key]['server_base_url'] = $item['server_base_url'];
            unset($data_list[$key]['id']);
            unset($data_list[$key]['admin_id']);
            unset($data_list[$key]['status']);
            unset($data_list[$key]['type']);
            unset($data_list[$key]['timestamp']);
        }
        output_data($data_list[0]);
    }
    public function get_configuration_produzione()
    {
        $data_list = $this->configuration_model->get_all(array('status' => 1, 'type' => 'configuration_produzione'));
        foreach ($data_list as $key => $item) {
            $data_list[$key]['opengl_renderer_low'] = json_decode($item['opengl_renderer_low']);
            $data_list[$key]['server_base_url'] = $item['server_base_url'];
            unset($data_list[$key]['id']);
            unset($data_list[$key]['admin_id']);
            unset($data_list[$key]['status']);
            unset($data_list[$key]['type']);
            unset($data_list[$key]['timestamp']);
        }
        output_data($data_list[0]);
    }
    public function parse_francese()
    {
        $path = APPPATH . 'json/script_francese.json';
        $files = file_get_contents($path);
        $data_list = json_decode($files, true);

        $update_data = array();
        $update_data['cube_rotations'] = json_encode($data_list['cube_rotations']);
        $update_data['cube_textures'] = json_encode($data_list['cube_textures']);
        $update_data['cube_textures_low'] = json_encode($data_list['cube_textures_low']);
        $update_data['steps'] = json_encode($data_list['steps']);
        $update_data['supported_languages'] = json_encode($data_list['supported_languages']);
        $update_data['visual_dataset'] = $data_list['visual_dataset'];
        $update_data['visual_url'] = $data_list['visual_url'];

        $update_data['admin_id'] = 1;
        $update_data['timestamp'] = TIMESTAMP;

        $insert_id = $this->francese_model->insert($update_data);

        output_data(1);
    }
    public function parse_configuration()
    {
        $type = get_param_from_request('type');
        if (empty($type)) {
            output_error("type is required");
        }
        if ($type == "configuration") {
            $path = APPPATH . 'json/configuration.json';
        } else {
            $path = APPPATH . 'json/configuration_produzione.json';
        }

        $files = file_get_contents($path);
        $data_list = json_decode($files, true);

        $update_data = array();
        $update_data['opengl_renderer_low'] = json_encode($data_list['opengl_renderer_low']);
        $update_data['server_base_url'] = $data_list['server_base_url'];
        $update_data['type'] = $type;

        $update_data['admin_id'] = 1;
        $update_data['timestamp'] = TIMESTAMP;

        $insert_id = $this->configuration_model->insert($update_data);

        output_data(1);
    }
    public function parse_addiopizzo()
    {
        $path = APPPATH . 'json/addiopizzo.json';
        $files = file_get_contents($path);
        $data_list = json_decode($files, true);

        $update_data = array();
        $entry_list = json_encode($data_list['entry']);
        $update_data['entry'] = $entry_list;
        $update_data['info'] = json_encode($data_list['info']);
        $update_data['itemsPerPage'] = $data_list['itemsPerPage'];
        $update_data['startIndex'] = $data_list['startIndex'];
        $update_data['totalResults'] = $data_list['totalResults'];

        $update_data['admin_id'] = 1;
        $update_data['timestamp'] = TIMESTAMP;

        // $insert_id = $this->addiopizzo_model->insert($update_data);

        $english_des = $data_list['supported_languages']['en'];
        $italy_des = $data_list['supported_languages']['it'];

        $type = "addiopizzo";

        foreach ($data_list['entry'] as $key => $val) {
            $entry_item = array();
            $entry_item['name'] = $val['name'];
            $entry_item['description'] = $val['description'];
            $entry_item['cat'] = $val['extra']['cat'];
            $entry_item['cat_type'] = $val['extra']['cat_type'];
            $entry_item['subcat'] = $val['extra']['subcat'];
            $entry_item['location'] = json_encode($val['location']);
            $entry_item['phone'] = $val['contacts']['phone'];
            $entry_item['web'] = $val['contacts']['web'];
            $entry_item['timestamp'] = TIMESTAMP;
            $this->addiopizzo_model->insert($entry_item);
        }
        foreach ($english_des as $key => $val) {
            $update_data = array();
            $update_data['key'] = $key;
            $update_data['des_en'] = $val;
            $update_data['type'] = $type;

            $des_info = $this->story_description_model->get(array("key" => $key, "type" => $type));
            if (empty($des_info)) {
                $this->story_description_model->insert($update_data);
            } else {
                $this->story_description_model->update($update_data, array("key" => $key, "type" => $type));
            }
        }
        foreach ($italy_des as $key => $val) {
            $update_data = array();
            $update_data['key'] = $key;
            $update_data['des_it'] = $val;
            $update_data['type'] = $type;

            $des_info = $this->story_description_model->get(array("key" => $key, "type" => $type));
            if (empty($des_info)) {
                $this->story_description_model->insert($update_data);
            } else {
                $this->story_description_model->update($update_data, array("key" => $key, "type" => $type));
            }
        }

        output_data(1);
    }
    public function parse_json()
    {
        $type = get_param_from_request('type');
        if (empty($type)) {
            output_error("type is required");
        }
        $path = "";
        switch ($type) {
            case "story":
                $path = FCPATH . 'uploads\contents\v8\stories.json';
                break;
            case "isop":
                $path = FCPATH . 'uploads\contents\v8\isop.json';
                break;
            case "ipicciridi":
                $path = FCPATH . 'uploads\contents\v8\ipicciridi.json';
                break;
            case "ilegal":
                $path = FCPATH . 'uploads\contents\v8\ilegal.json';
                break;
            case "extra":
                $path = FCPATH . 'uploads\contents\v8\extra.json';
                break;
            case "canzoni":
                $path = FCPATH . 'uploads\contents\v8\canzoni.json';
                break;
        }
        if (empty($path)) {
            output_error("no json file");
        }

        $files = file_get_contents($path);
        $data_list = json_decode($files, true);
        $english_des = $data_list['supported_languages']['en'];
        $italy_des = $data_list['supported_languages']['it'];
        foreach ($english_des as $key => $val) {
            $update_data = array();
            $update_data['key'] = $key;
            $update_data['des_en'] = $val;
            $update_data['type'] = $type;

            $des_info = $this->story_description_model->get(array("key" => $key, "type" => $type));
            if (empty($des_info)) {
                $this->story_description_model->insert($update_data);
            } else {
                $this->story_description_model->update($update_data, array("key" => $key, "type" => $type));
            }
        }
        foreach ($italy_des as $key => $val) {
            $update_data = array();
            $update_data['key'] = $key;
            $update_data['des_it'] = $val;
            $update_data['type'] = $type;

            $des_info = $this->story_description_model->get(array("key" => $key, "type" => $type));
            if (empty($des_info)) {
                $this->story_description_model->insert($update_data);
            } else {
                $this->story_description_model->update($update_data, array("key" => $key, "type" => $type));
            }
        }

        $stories = $data_list['stories'];
        for ($i = 0; $i < sizeof($stories); $i++) {
            $update_data = array();
            $update_data['type'] = $type;
            $update_data['augmented_story'] = $stories[$i]['augmented_story'];

            $update_data['background'] = $stories[$i]['background'];
            $update_data['description'] = $stories[$i]['description'];
            $update_data['facebook_photoID'] = $stories[$i]['facebook_photoID'];
            $update_data['geofenceAlert'] = $stories[$i]['geofenceAlert'];
            $update_data['geofenceTitle'] = $stories[$i]['geofenceTitle'];
            $update_data['icon'] = $stories[$i]['icon'];
            $update_data['index'] = $stories[$i]['id'];
            $update_data['link'] = $stories[$i]['link'];
            $update_data['map_icon'] = $stories[$i]['map_icon'];
            $update_data['name'] = $stories[$i]['name'];
            $update_data['pinpoint_icon'] = $stories[$i]['pinpoint_icon'];
            $update_data['profile_icon'] = $stories[$i]['profile_icon'];
            $update_data['resources_base_url'] = $stories[$i]['resources_base_url'];
            $update_data['share_icon'] = $stories[$i]['share_icon'];
            $update_data['short_name'] = $stories[$i]['short_name'];
            $update_data['speakerDescription'] = $stories[$i]['speakerDescription'];
            $update_data['speaker_icon'] = $stories[$i]['speaker_icon'];
            $update_data['story_version'] = $stories[$i]['story_version'];
            $update_data['address'] = json_encode($stories[$i]['address']);
            if (isset($stories[$i]['bodyguard_team'])) {
                $update_data['bodyguard_team'] = json_encode($stories[$i]['bodyguard_team']);
            } else {
                $update_data['bodyguard_team'] = json_encode(array());
            }
            if (isset($stories[$i]['bodyguard_title'])) {
                $update_data['bodyguard_title'] = $stories[$i]['bodyguard_title'];
            } else {
                $update_data['bodyguard_title'] = "";
            }
            if (isset($stories[$i]['bundled_story'])) {
                $update_data['bundled_story'] = $stories[$i]['bundled_story'];
            } else {
                $update_data['bundled_story'] = "";
            }
            if (isset($stories[$i]['available_story'])) {
                $update_data['available_story'] = $stories[$i]['available_story'];
            } else {
                $update_data['available_story'] = "";
            }

            if (isset($stories[$i]['augmented_page'])) {
                $update_data['augmented_page'] = $stories[$i]['augmented_page'];
            } else {
                $update_data['augmented_page'] = "";
            }
            if (isset($stories[$i]['augmented_eye'])) {
                $update_data['augmented_eye'] = $stories[$i]['augmented_eye'];
            } else {
                $update_data['augmented_eye'] = "";
            }
            if (isset($stories[$i]['augmented_sound'])) {
                $update_data['augmented_sound'] = $stories[$i]['augmented_sound'];
            } else {
                $update_data['augmented_sound'] = "";
            }
            if (isset($stories[$i]['augmented_sound_url'])) {
                $update_data['augmented_sound_url'] = $stories[$i]['augmented_sound_url'];
            } else {
                $update_data['augmented_sound_url'] = "";
            }
            $update_data['admin_id'] = 1;
            $update_data['timestamp'] = TIMESTAMP;

            $story_id = $this->story_model->insert($update_data);

        }
        output_data(1);
    }
    public function parse_isop()
    {
        $path = APPPATH . 'json/isop.json';
        $files = file_get_contents($path);
        $data_list = json_decode($files, true);
        $english_des = $data_list['supported_languages']['en'];
        $italy_des = $data_list['supported_languages']['it'];

        foreach ($english_des as $key => $val) {
            $update_data = array();
            $update_data['key'] = $key;
            $update_data['des_en'] = $val;

            $des_info = $this->story_description_model->get(array("key" => $key));
            if (empty($des_info)) {
                $this->story_description_model->insert($update_data);
            } else {
                $this->story_description_model->update($update_data, array("key" => $key));
            }
        }
        foreach ($italy_des as $key => $val) {
            $update_data = array();
            $update_data['key'] = $key;
            $update_data['des_it'] = $val;

            $des_info = $this->story_description_model->get(array("key" => $key));
            if (empty($des_info)) {
                $this->isop_description_model->insert($update_data);
            } else {
                $this->isop_description_model->update($update_data, array("key" => $key));
            }
        }

        $stories = $data_list['stories'];
        for ($i = 0; $i < sizeof($stories); $i++) {
            $update_data = array();
            $update_data['augmented_story'] = $stories[$i]['augmented_story'];
            $update_data['available_story'] = $stories[$i]['available_story'];
            $update_data['background'] = $stories[$i]['background'];
            $update_data['description'] = $stories[$i]['description'];
            $update_data['facebook_photoID'] = $stories[$i]['facebook_photoID'];
            $update_data['geofenceAlert'] = $stories[$i]['geofenceAlert'];
            $update_data['geofenceTitle'] = $stories[$i]['geofenceTitle'];
            $update_data['icon'] = $stories[$i]['icon'];
            $update_data['index'] = $stories[$i]['id'];
            $update_data['link'] = $stories[$i]['link'];
            $update_data['map_icon'] = $stories[$i]['map_icon'];
            $update_data['name'] = $stories[$i]['name'];
            $update_data['pinpoint_icon'] = $stories[$i]['pinpoint_icon'];
            $update_data['profile_icon'] = $stories[$i]['profile_icon'];
            $update_data['resources_base_url'] = $stories[$i]['resources_base_url'];
            $update_data['share_icon'] = $stories[$i]['share_icon'];
            $update_data['short_name'] = $stories[$i]['short_name'];
            $update_data['speakerDescription'] = $stories[$i]['speakerDescription'];
            $update_data['speaker_icon'] = $stories[$i]['speaker_icon'];
            $update_data['story_version'] = $stories[$i]['story_version'];
            $update_data['address'] = json_encode($stories[$i]['address']);
            if (isset($stories[$i]['bodyguard_team'])) {
                $update_data['bodyguard_team'] = json_encode($stories[$i]['bodyguard_team']);
            } else {
                $update_data['bodyguard_team'] = json_encode(array());
            }
            if (isset($stories[$i]['bodyguard_title'])) {
                $update_data['bodyguard_title'] = $stories[$i]['bodyguard_title'];
            } else {
                $update_data['bodyguard_title'] = "";
            }
            if (isset($stories[$i]['bundled_story'])) {
                $update_data['bundled_story'] = $stories[$i]['bundled_story'];
            } else {
                $update_data['bundled_story'] = "";
            }
            $update_data['admin_id'] = 1;
            $update_data['timestamp'] = TIMESTAMP;

            $story_id = $this->isop_model->insert($update_data);

        }
        output_data(1);
    }
    public function get_project_detail()
    {
        $type = "project";
        $detail_item = $this->project_model->get(array("id" => 1));
        $detail_info = array();
        if (!empty($detail_item)) {

            $detail_title_info = $this->story_description_model->get(array('key' => "title", 'type' => $type));
            $detail_title_en = "";
            $detail_title_it = "";
            if (!empty($detail_title_info)) {
                $detail_title_en = $detail_title_info['des_en'];
                $detail_title_it = $detail_title_info['des_it'];
            }
            if ($detail_title_en == null) {
                $detail_title_en = "";
            }
            if ($detail_title_it == null) {
                $detail_title_it = "";
            }

            $detail_info['title'] = array(
                "en" => $detail_title_en,
                "it" => $detail_title_it,
            );

            $detail_description_info = $this->story_description_model->get(array('key' => "description", 'type' => $type));
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

            $detail_content_info = $this->story_description_model->get(array('key' => "content", 'type' => $type));
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

            $detail_info['content'] = array(
                "en" => $detail_content_en,
                "it" => $detail_content_it,
            );

            $detail_credit1_info = $this->story_description_model->get(array('key' => "credit1", 'type' => $type));
            $detail_credit1_en = "";
            $detail_credit1_it = "";
            if (!empty($detail_credit1_info)) {
                $detail_credit1_en = $detail_credit1_info['des_en'];
                $detail_credit1_it = $detail_credit1_info['des_it'];
            }
            if ($detail_credit1_en == null) {
                $detail_credit1_en = "";
            }
            if ($detail_credit1_it == null) {
                $detail_credit1_it = "";
            }

            $detail_info['credit1'] = array(
                "en" => $detail_credit1_en,
                "it" => $detail_credit1_it,
            );

            $detail_credit2_info = $this->story_description_model->get(array('key' => "credit2", 'type' => $type));
            $detail_credit2_en = "";
            $detail_credit2_it = "";
            if (!empty($detail_credit2_info)) {
                $detail_credit2_en = $detail_credit2_info['des_en'];
                $detail_credit2_it = $detail_credit2_info['des_it'];
            }
            if ($detail_credit2_en == null) {
                $detail_credit2_en = "";
            }
            if ($detail_credit2_it == null) {
                $detail_credit2_it = "";
            }

            $detail_info['credit2'] = array(
                "en" => $detail_credit2_en,
                "it" => $detail_credit2_it,
            );

            $detail_credit3_info = $this->story_description_model->get(array('key' => "credit3", 'type' => $type));
            $detail_credit3_en = "";
            $detail_credit3_it = "";
            if (!empty($detail_credit3_info)) {
                $detail_credit3_en = $detail_credit3_info['des_en'];
                $detail_credit3_it = $detail_credit3_info['des_it'];
            }
            if ($detail_credit3_en == null) {
                $detail_credit3_en = "";
            }
            if ($detail_credit3_it == null) {
                $detail_credit3_it = "";
            }

            $detail_info['credit3'] = array(
                "en" => $detail_credit3_en,
                "it" => $detail_credit3_it,
            );

            $detail_info['profile_icon'] = empty($detail_item['profile_icon']) ? "" : $detail_item['profile_icon'];
            $detail_info['speaker_icon1'] = empty($detail_item['speaker_icon1']) ? "" : $detail_item['speaker_icon1'];
            $detail_info['speaker_icon2'] = empty($detail_item['speaker_icon2']) ? "" : $detail_item['speaker_icon2'];
            $detail_info['speaker_icon3'] = empty($detail_item['speaker_icon3']) ? "" : $detail_item['speaker_icon3'];
            $detail_info['speaker_icon4'] = empty($detail_item['speaker_icon4']) ? "" : $detail_item['speaker_icon4'];
            $detail_info['speaker_icon5'] = empty($detail_item['speaker_icon5']) ? "" : $detail_item['speaker_icon5'];
            $detail_info['media'] = empty($detail_item['media']) ? "" : $detail_item['media'];
            $detail_info['resources_base_url'] = $detail_item['resources_base_url'];
        }

        output_data(array("detail" => $detail_info));
    }

    public function get_match_arr($numbers, $length)
    {
        $combinations = array();
        $stack = array();

        // every combinations can be ordered

        // startup
        array_push($stack, array(
            'store' => array(),
            'options' => $numbers,
        ));

        while (true) {
            // pop a item
            $item = array_pop($stack);

            // end of stack
            if (!$item) {
                break;
            }

            // valid store
            if ($length <= count($item['store'])) {
                $combinations[] = $item['store'];
                continue;
            }

            // bypass when options are not enough
            if (count($item['store']) + count($item['options']) < $length) {
                continue;
            }

            foreach ($item['options'] as $index => $n) {
                $newStore = $item['store'];
                $newStore[] = $n;

                // every combine can be ordered
                // so accept only options which is greater than store numbers
                $newOptions = array_slice($item['options'], $index + 1);

                // push new items
                array_push($stack, array(
                    'store' => $newStore,
                    'options' => $newOptions,
                ));
            }
        }

        return $combinations;
    }

    public function test2()
    {
        $arr = array('Team1', 'Team2', 'Team3', 'Team4', 'Team5', 'Team6');

        $result = $this->get_match_arr($arr, 2);
        $matches = array();
        foreach($result as $match){
            $matches[] = $match[0] . ":" . $match[1];
        }
        $matches = array_reverse($matches, false);
        $no_repeat_matches = sizeof($arr)/2;
        // $games = $this->get_match_arr($matches, 3);
        // $games = array_reverse($games, false);
        print_r($matches);
    }
    public function test() {
        $teamIds = array('Team1', 'Team2', 'Team3', 'Team4', 'Team5', 'Team6');
        $matches = $this->getAllMatch($teamIds, 1, 1, 0);
    }
    private function getAllMatch($teamIds, $match_count, $start_point, $ok) {
        if ($ok == 2) {
            total_match[total_match_count] = String.format("%d%d", aa[0], aa[1]);
            total_match_count++;
        }
        for ($i = $start_point; $i<=sizeof($teamIds);$i++) {
            $aa[$ok] = $teamIds[$i];
            getAllMatch($teamIds, $match_count, $i+1, $ok+1);
        }

    }
    public function test1()
    {

        $blackout_days = "";
        $tmp_start_date = "12-24-2022";
        $start_date = explode("-", $tmp_start_date)[1] . "-" . explode("-", $tmp_start_date)[0] . "-" . explode("-", $tmp_start_date)[2];
        $max_game = 1;
        $length = 55;
        $between = 5;
        $start_Dayofweek = date('w', strtotime($start_date));
        // print_r($start_Dayofweek);
        $start_day = explode("-", $start_date)[1];
        $start_month = explode("-", $start_date)[0];
        $start_year = explode("-", $start_date)[2];

        $days_times = "6-12:00 pm";

        $start_day_of_week = $start_Dayofweek;
        if (strpos($days_times, $start_Dayofweek . "-") == false) {
            $start_day_of_week = explode("-", $days_times)[0];
        }

        $days_times_list = explode(",", $days_times);
        $DayTimes = array();
        foreach ($days_times_list as $key => $item) {
            $tmpOneDay = array();
            $tmpOneDay['day_of_week'] = explode("-", $item)[0];
            $mng = explode(" ", explode("-", $item)[1])[1];
            $time = "";
            if ($mng == "pm") {
                $time = (explode(":", explode("-", $item)[1])[0] < 12 ? explode(":", explode("-", $item)[1])[0] + 12 : explode(":", explode("-", $item)[1])[0]) . ":" . explode(":", explode("-", $item)[1])[1];
            } else {
                $time = explode("-", $item)[1];
            }
            $tmpOneDay['time'] = explode(" ", $time)[0];
            $tmpOneDay['mng'] = explode(" ", $time)[1];
            $tmpOneDay['key'] = $key;
            $DayTimes[] = $tmpOneDay;
        }
        $start_day_of_week_index = 0;
        foreach ($DayTimes as $tmpOneDay) {
            if (intval($tmpOneDay['day_of_week']) == intval($start_day_of_week)) {
                $start_day_of_week_index = $tmpOneDay['key'];
                break;
            }
        }

        $teamIds = array('Team1', 'Team2', 'Team3', 'Team4', 'Team5', 'Team6');
        $matches = $this->sampling($teamIds, 5);
        print_r($matches);
        // $match_list = array();
        // foreach ($matches as $round) {
        //     foreach ($round as $one_game) {
        //         $home_team = explode(":", $one_game)[0];
        //         $away_team = explode(":", $one_game)[1];
        //         if ($home_team != "Bye" && $away_team != "Bye") {
        //             $tmp_match_list = array();
        //             $tmp_match_list['home_team_id'] = $home_team;
        //             $tmp_match_list['away_team_id'] = $away_team;
        //             $tmp_match_list['round'] = $this->getRound($match_list, $home_team, $away_team, $max_game);
        //             $match_list[] = $tmp_match_list;
        //         }
        //     }
        // }

        // $latest_round = $match_list[sizeof($match_list) - 1]['round'];
        // //print_r($start_Dayofweek);
        // $result = array();
        // $k = 0;

        // $result_date_list = array();
        // for ($i = 0; $i <= $latest_round; $i++) {

        //     $start_day = explode("-", $tmp_start_date)[1];
        //     $start_month = explode("-", $tmp_start_date)[0];
        //     $start_year = explode("-", $tmp_start_date)[2];

        //     $tmpResult = array();
        //     $result_date_value = $this->getStartDate($i, $k, $DayTimes, $start_day_of_week_index, $start_day, $start_month, $start_year, $start_Dayofweek, $blackout_days, $tmp_start_date, $result_date_list);
        //     $result_date = $result_date_value["result_date"];
        //     $dayList_index = $result_date_value["day_index"];
        //     $k = $result_date_value["k"];
        //     $blackout_days = $result_date_value["blackout_days"];
        //     $result_date_list[] = $result_date;
        //     $one_round_games = $this->getOneRoundGames($match_list, $i);
        //     for ($j = 0; $j < sizeof($one_round_games); $j++) {
        //         $tmpResult = $one_round_games[$j];
        //         $one_game_start_time = $DayTimes[$dayList_index]['time'];
        //         $one_game_start_time_hh = intval(explode(":", $one_game_start_time)[0]);
        //         $one_game_start_time_mm = intval(explode(":", $one_game_start_time)[1]);

        //         $minute = $one_game_start_time_hh * 60 + $one_game_start_time_mm;

        //         $delta = $minute + $j * ($length + $between);
        //         $new_hh = "";
        //         $new_am_pm = "";
        //         if (intval($delta / 60) > 12) {
        //             $new_hh = intval($delta / 60) - 12;
        //             $new_am_pm = "pm";
        //         } else {
        //             $new_hh = intval($delta / 60);
        //             $new_am_pm = "am";
        //         }
        //         if ($new_hh < 10) {
        //             $new_hh = "0" . $new_hh;
        //         }

        //         $new_mm = "";
        //         if (intval($delta % 60) < 10) {
        //             $new_mm = "0" . intval($delta % 60);
        //         } else {
        //             $new_mm = intval($delta % 60);
        //         }
        //         $tmpResult['start_time'] = $new_hh . ":" . $new_mm;
        //         $tmpResult['start_date'] = $result_date;
        //         $tmpResult['game_start_date_time'] = $result_date . "T" . $new_hh . ":" . $new_mm . " " . $new_am_pm;
        //         $result[] = $tmpResult;
        //     }

        // }
        // print_r($result);
        output_data(1);
    }
    private function getStartDate($i, $k, $DayTimes, $start_day_of_week_index, $start_day, $start_month, $start_year, $start_Dayofweek, $blackout_days, $tmp_start_date, $result_date_list)
    {
        $index = $i + $k;
        $dayList_index = $index % sizeof($DayTimes) + intval($start_day_of_week_index) >= sizeof($DayTimes) ? ($index % sizeof($DayTimes) + intval($start_day_of_week_index)) % sizeof($DayTimes) : $index % sizeof($DayTimes) + intval($start_day_of_week_index);
        $daysOfWeek = $DayTimes[$dayList_index]['day_of_week'];
        $delta_of_week = abs($start_Dayofweek - $daysOfWeek);

        $tmpStart_day = 0;
        if ($start_Dayofweek > $daysOfWeek) {
            $delta_week_index = $start_Dayofweek - $daysOfWeek;
            $tmpStart_day = $start_day + 7 - $delta_week_index;
        } else {
            $delta_week_index = $daysOfWeek - $start_Dayofweek;
            $tmpStart_day = $start_day + $delta_week_index;
        }
        $tmpStart_day = $tmpStart_day + (ceil(($index + 1) / sizeof($DayTimes)) - 1) * 7;

        $month_max_day = $this->getBigMonth(intval($start_month), intval($start_year));

        if ($tmpStart_day > $month_max_day) {
            if (intval($start_month) == 12) {
                $start_year = $start_year + 1;
                $start_month = 1;
            } else {
                $start_month = intval($start_month) + 1;
            }
            $tmpStart_day = $tmpStart_day - $month_max_day;
        }
        if (intval($tmpStart_day) > 31) {
            $start_month = intval($start_month) + 1;
            $tmpStart_day = $tmpStart_day - 31;
        }
        if (strlen($start_month) == 1 && intval($start_month) < 10) {
            $start_month = "0" . $start_month;
        }
        if (strlen($tmpStart_day) == 1 && intval($tmpStart_day) < 10) {
            $tmpStart_day = "0" . $tmpStart_day;
        }
        $result_date = $start_month . "-" . $tmpStart_day . "-" . $start_year;
        if (strpos($blackout_days, $result_date) !== false) {
            $b_days = explode(",", $blackout_days);
            $n_days = array();
            foreach ($b_days as $one_b_day) {
                if ($one_b_day != $result_date) {
                    $n_days[] = $one_b_day;
                }
            }
            $blackout_days = implode(",", $n_days);

            $start_day = explode("-", $tmp_start_date)[1];
            $start_month = explode("-", $tmp_start_date)[0];
            $start_year = explode("-", $tmp_start_date)[2];

            $result_arr_val = $this->getStartDate($i, $k + 1, $DayTimes, $start_day_of_week_index, $start_day, $start_month, $start_year, $start_Dayofweek, $blackout_days, $tmp_start_date, $result_date_list);
            $result_date = $result_arr_val["result_date"];
            $dayList_index = $result_arr_val["day_index"];
            $blackout_days = $result_arr_val["blackout_days"];

            $k = $result_arr_val["k"];
        }

        $out_month = explode("-", $result_date)[0];
        $out_day = explode("-", $result_date)[1];
        $out_year = explode("-", $result_date)[2];

        $out_month_max_day = $this->getBigMonth(intval($out_month), intval($out_year));
        if (intval($out_day) > $out_month_max_day) {
            $out_day = intval($out_day) - $out_month_max_day;
            $out_month = intval($out_month) + 1;
        }
        if (strlen($out_month) == 1 && intval($out_month) < 10) {
            $out_month = "0" . $out_month;
        }
        if (strlen($out_day) == 1 && intval($out_day) < 10) {
            $out_day = "0" . $out_day;
        }
        $result_date = $out_month . "-" . $out_day . "-" . $out_year;
        $result_arr = array("result_date" => $result_date, "k" => $k, "day_index" => $dayList_index, "blackout_days" => $blackout_days);

        return $result_arr;
    }
    private function getBigMonth($month, $year)
    {
        if (intval($month) == 2) {
            if ($this->year_check($year)) {
                return 29;
            } else {
                return 28;
            }

        } else {
            $months = ['1', '3', '5', '7', '8', '10', '12'];
            if (in_array($month, $months)) {
                return 31;
            } else {
                return 30;
            }
        }
    }
    private function year_check($my_year)
    {
        if ($my_year % 400 == 0) {
            return true;
        } else if ($my_year % 100 == 0) {
            return false;
        } else if ($my_year % 4 == 0) {
            return true;
        } else {
            return false;
        }

    }
    private function getOneRoundGames($match_list, $i)
    {
        $tmpList = array();
        foreach ($match_list as $item) {
            if ($item['round'] == $i) {
                $tmpList[] = $item;
            }
        }
        return $tmpList;
    }
    private function getRound($match_list, $home_team, $away_team, $max_game)
    {
        if (sizeof($match_list) == 0) {
            return 0;
        }
        $latest_round = $match_list[sizeof($match_list) - 1]['round'];
        $latest_matches = $this->getLatestTeams($match_list);
        $homeExists = count(array_keys($latest_matches, $home_team));
        $awayExists = count(array_keys($latest_matches, $away_team));
        if ($homeExists >= $max_game || $awayExists >= $max_game) {
            return intval($latest_round) + 1;
        } else {
            return $latest_round;
        }
    }
    private function getLatestTeams($match_list)
    {
        $tmpList = array();
        $latest_round = $match_list[sizeof($match_list) - 1]['round'];
        for ($i = 0; $i < sizeof($match_list); $i++) {
            if ($match_list[$i]['round'] == $latest_round) {
                $tmpList[] = $match_list[$i]['home_team_id'];
                $tmpList[] = $match_list[$i]['away_team_id'];
            }
        }
        return $tmpList;
    }

    private function sampling($homeArray, $gameCount)
    {

        if (empty($awayArray)) {
            $awayArray = $homeArray;
        }

        $size = 2;

        $match_count = sizeof($homeArray) / 2;
        $resultArr = array();
        $one_round = array();

        for ($k = 0; $k < $gameCount; $k++) {
            for ($i = 0; $i < sizeof($homeArray); $i++) {
                $home_team = $homeArray[$i];
                if ($this->checkTeam($one_round, $home_team)) {
                    continue;
                }

                for ($j = 0; $j < sizeof($awayArray); $j++) {
                    // if ($k == 0) {
                    //     $away_team = $awayArray[$j];
                    // } else if ($k == $gameCount - 1) {

                    // } else {
                    //     if (sizeof($one_round) == 0) {
                    //         $away_team = explode(":", $resultArr[$k - 1][count($resultArr[$k - 1]) - 1])[0];
                    //     } else {
                    //         $away_team = $awayArray[$j];
                    //     }
                    // }
                    $away_team = $awayArray[$j];
                    if ($home_team == $away_team) {
                        continue;
                    }
                    if ($this->checkTeam($one_round, $home_team)) {
                        continue;
                    }
                    if ($this->checkTeam($one_round, $away_team)) {
                        continue;
                    }
                    if ($k * 2 < sizeof($homeArray) && $this->checkMatch($resultArr, $home_team . ":" . $away_team)) {
                        continue;
                    }
                    $one_round[] = $home_team . ":" . $away_team;
                    break;
                }

            }
            $resultArr[] = $one_round;
            $one_round = array();
        }
        return $resultArr;

    }
    private function checkTeam($one_round, $teamName)
    {
        $include_flag = false;
        foreach ($one_round as $one_match) {
            if (strpos($one_match, $teamName) !== false) {
                $include_flag = true;
            }
        }
        return $include_flag;
    }
    private function checkMatch($rslArr, $teamName)
    {
        $include_flag = false;
        foreach ($rslArr as $one_round) {
            foreach ($one_round as $one_match) {
                if ($one_match == $teamName) {
                    $include_flag = true;
                }
            }
        }
        return $include_flag;
    }
}
