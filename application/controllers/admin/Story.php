<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once 'Admin_controller.php';

class Story extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->check_login();
        $this->load->model('story_model');
        $this->load->model('story_detail_model');
        $this->load->model('story_description_model');
    }

    public function get_story_list()
    {
        $type = get_param_from_request('type', 'GET');
        if (empty($type)) {
            output_error("paramter error");
        }
        $data_list = array();

        switch ($type) {
            case "1":
                $type = "story";
                break;
            case "2":
                $type = "isop";
                break;
            case "3":
                $type = "ipicciridi";
                break;
            case "4":
                $type = "ilegal";
                break;
            case "5":
                $type = "extra";
                break;
            case "6":
                $type = "canzoni";
                break;
        }
        //$data_list = $this->story_model->get_all(array('type' => $type, 'status' => 1));

        $sql = "select * from ".STORY_TB." where type = '".$type."' and status = 1 order by id desc";
        $query = $this->db->query($sql);
        $data_list = $query->result_array();
        if (empty($data_list)) {
            $data_list = array();
        }

        if (!empty($data_list)) {
            foreach ($data_list as $key => $item) {
                $data_list[$key]['address'] = json_decode($item['address']);
                $data_list[$key]['bodyguard_team'] = json_decode($item['bodyguard_team']);
                $data_list[$key]['resources_base_url'] = base_url() . "uploads/contents/v8" . $item['resources_base_url'];
                $data_list[$key]['profile_icon'] = base_url() . "uploads/contents/v8" . $item['resources_base_url'] . $item['profile_icon'];
                $data_list[$key]['speaker_icon'] = base_url() . "uploads/contents/v8" . $item['resources_base_url'] . $item['speaker_icon'];
                $base_url_link = $item['resources_base_url'];
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
                        $bList[$keys]->bodyguard_icon = base_url() . "uploads/contents/v8" . $base_url_link . $items->bodyguard_icon;
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
    public function update_story()
    {
        $id = get_param_from_request('id');
        $type = get_param_from_request('type');
        $name = get_param_from_request('name');
        $short_name = get_param_from_request('short_name');
        $description_en = get_param_from_request('description_en');
        $description_it = get_param_from_request('description_it');

        $storyItem = $this->story_model->get(array("id" => $id));
        if (empty($storyItem)) {
            output_error("Story doesn't exist");
        } else {
            $profile_image = $storyItem['profile_icon'];
            $speaker_image = $storyItem['speaker_icon'];
            $sound = $storyItem['augmented_sound_url'];
            $src_base_url = $storyItem['resources_base_url'];
            $desc_key = $storyItem['description'];
            $desc_info = $this->story_description_model->update(
                array('des_en' => $description_en, 'des_it' => $description_it),
                array('key' => $desc_key, 'type' => $type)
            );
            if (isset($_FILES['speaker'])) {
                $speaker_image = $this->upload_file("contents/v8" . $src_base_url, "speaker");
                $this->story_model->update(array('speaker_icon' => $speaker_image), array('id' => $id));
            }
            if (isset($_FILES['profile'])) {
                $profile_image = $this->upload_file("contents/v8" . $src_base_url, "profile");
                $this->story_model->update(array('profile_icon' => $profile_image), array('id' => $id));
            }
            if (isset($_FILES['soundFile'])) {
                $sound = $this->upload_file("contents/v8" . $src_base_url, "soundFile");
                $this->story_model->update(array('augmented_sound_url' => $sound), array('id' => $id));
            }
            if (!empty($name)) {
                $this->story_model->update(array('name' => $name), array('id' => $id));    
            }
            if (!empty($short_name)) {
                $this->story_model->update(array("short_name"=>$short_name), array('id' => $id));    
            }
            if ($type == "story") {
                $index_id = $storyItem['index'];
                $src_desc = $storyItem['description'];
                $src_speaker = $storyItem['speaker_icon'];
                $src_profile = $storyItem['profile_icon'];

                $path = FCPATH . 'uploads/contents/v8/stories.json';
                $files = $this->file_get_contents_utf8($path);
                $data_list = json_decode($files, true);
                
                $english_des = $data_list['supported_languages']['en'];
                $italy_des = $data_list['supported_languages']['it'];
                foreach ($english_des as $key => $val) {
                    if ($key == $src_desc) {
                        $english_des[$key] = $description_en;
                    }
                }
                foreach ($italy_des as $key => $val) {
                    if ($key == $src_desc) {
                        $italy_des[$key] = $description_it;
                    }
                }

                $stories = $data_list['stories'];
                foreach ($stories as $key => $val) {
                    if ($val['id'] == $index_id) {
                        if (isset($_FILES['profile'])) {
                            $stories[$key]['profile_icon'] = $profile_image;
                        }
                        if (isset($_FILES['speaker'])) {
                            $stories[$key]['speaker_icon'] = $speaker_image;
                        }
                        if (isset($_FILES['soundFile'])) {
                            $stories[$key]['speaker_icon'] = $sound;
                        }
                        if (isset($name)) {
                            $stories[$key]['name'] = $name;
                        }
                        if (isset($short_name)) {
                            $stories[$key]['short_name'] = $short_name;
                        }

                    }
                }

                $data_list['stories'] = $stories;
                $data_list['supported_languages'] = array(
                    "en" => $english_des,
                    "it" => $italy_des,
                );
                $newJsonString = json_encode($data_list);
                file_put_contents($path, $newJsonString);
            }

            output_data(1);
        }

    }
    private function file_get_contents_utf8($fn) {
        $content = file_get_contents($fn);
         return mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
   }
    public function update_story_detail()
    {
        $type = get_param_from_request('type');
        $name = get_param_from_request('name');
        $short_name = get_param_from_request('short_name');
        $content_en = get_param_from_request('bodyguard_title_en');
        $content_it = get_param_from_request('bodyguard_title_it');
        $description_en = get_param_from_request('description_en');
        $description_it = get_param_from_request('description_it');

        $storyItem = $this->story_detail_model->get(array("type" => $type));
        if (empty($storyItem)) {
            output_error("Story doesn't exist");
        } else {
            $this->story_detail_model->update(array('name' => $name, 'short_name' => $short_name), array('type' => $type));
            $this->story_description_model->update(
                array('des_en' => $content_en, 'des_it' => $content_it),
                array('key' => "content", 'type' => $type . "_detail")
            );
            $this->story_description_model->update(
                array('des_en' => $description_en, 'des_it' => $description_it),
                array('key' => "description", 'type' => $type . "_detail")
            );

            if (isset($_FILES['speakerFile'])) {
                $speaker_image = $this->upload_file("contents/v8" . $storyItem['resources_base_url'], "speakerFile");
                $this->story_detail_model->update(array('speaker_icon' => $speaker_image), array('type' => $type));
            }
            if (isset($_FILES['profileFile'])) {
                $profile_image = $this->upload_file("contents/v8" . $storyItem['resources_base_url'], "profileFile");
                $this->story_detail_model->update(array('profile_icon' => $profile_image), array('type' => $type));
            }
            if (isset($_FILES['soundFile'])) {
                $sound = $this->upload_file("contents/v8" . $storyItem['resources_base_url'], "soundFile");
                $this->story_detail_model->update(array('augmented_sound_url' => $sound), array('type' => $type));
            }
            output_data(1);
        }

    }
    public function get_story_detail()
    {
        $type = get_param_from_request('type', 'GET');
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
    public function delete_story()
    {

        $id = get_param_from_request('id');
        $this->story_model->update(
            array(
                'status' => 0,
            ),
            array("id" => $id)
        );
        output_data(1);
    }
    public function add_story()
    {
        $type = get_param_from_request('type');
        $name = get_param_from_request('name');
        $short_name = get_param_from_request('short_name');
        $description_en = get_param_from_request('description_en');
        $description_it = get_param_from_request('description_it');

        $profile_image = "";
        $speaker_image = "";
        $sound = "";
        $src_base_url = "";
        if ($type == "isop") {
            $src_base_url = "/Piciriddi/";
        } else if ($type == "ipicciridi") {
            $src_base_url = "/Piciriddi/";
        }
        $desc_key = $type . ":" . TIMESTAMP;
        $desc_info = $this->story_description_model->insert(
            array('des_en' => $description_en, 'des_it' => $description_it, 'key' => $desc_key, 'type' => $type)
        );

        if (isset($_FILES['speaker'])) {
            $speaker_image = $this->upload_file("contents/v8" . $src_base_url, "speaker");
        }

        if (isset($_FILES['profile'])) {
            $profile_image = $this->upload_file("contents/v8" . $src_base_url, "profile");
        }

        $address_arr = array();
        $address_arr['city'] = "Palermo";
        $address_arr['geofence_radius'] = 100;
        $address_arr['latitude'] = 38.1430026;
        $address_arr['longitude'] = 13.3547643;
        $address_arr['street'] = "insieme nella memoria, Carmelo Piraino e Massimo Scalici";
        
        $insertArr = array(
            'type' => $type,
            "name" => $name,
            "short_name" => $short_name,
            "resources_base_url" => $src_base_url,
            'address' => json_encode($address_arr),
            'augmented_sound' => empty($sound) ? "no sound" : "sound",
            'augmented_sound_url' => $sound,
            'augmented_page' => $type == "isop" ? "PAGE" : "",
            'augmented_eye' => "NOEYE",
            "available_story" => false,
            "background" => 'url_to_file',
            "bodyguard_team" => "[]",
            "bodyguard_title" => "",
            "bundled_story" => "",
            "description" => $desc_key,
            "facebook_photoID" => "https://www.facebook.com/NOmaluoghiestorienomafia/photos/a.495863130573082.1073741828.491407107685351/512852425540819/?type=3&theater",
            "geofenceAlert" => "GiulianoGeofenceAlert",
            "geofenceTitle" => "GiulianoGeofenceTitle",
            "icon" => "url_to_file",
            "index" => "story11",
            "link" => "script_giuliano.json",
            "map_icon" => "map_giuliano.jpg",
            "pinpoint_icon" => "storia_pinpoint_orangelight",
            'profile_icon' => $profile_image,
            "share_icon" => "fb_giuliano1.jpg",
            "speakerDescription" => "SansoneSpeakerDescription",
            'speaker_icon' => $speaker_image,
            "story_version" => "sv1.1",
            "admin_id" => 1,
            "status" => 1,
            "timestamp" => TIMESTAMP
        );
        $this->story_model->insert($insertArr);
        output_data(1);

    }
}

