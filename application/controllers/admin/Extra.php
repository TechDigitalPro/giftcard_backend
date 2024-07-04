<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once 'Admin_controller.php';

class Extra extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->check_login();
        $this->load->model('story_model');
        $this->load->model('story_description_model');
    }

    public function update_sound()
    {
        $id = get_param_from_request('id');
        $type = get_param_from_request('type');
        $name = get_param_from_request('name');
        $short_name = get_param_from_request('short_name');

        $storyItem = $this->story_model->get(array("id" => $id));
        if (empty($storyItem)) {
            output_error("Story doesn't exist");
        } else {

            if (isset($_FILES['soundFile'])) {
                $sound_url = $this->upload_file("contents/v8" . $storyItem['resources_base_url'], "soundFile");
                $this->story_model->update(array('augmented_sound_url' => $sound_url), array('id' => $id));
            }
            $this->story_model->update(array('name' => $name, 'short_name' => $short_name), array('id' => $id));
            output_data(1);
        }

    }
    public function add_sound()
    {
        $type = get_param_from_request('type');
        $name = get_param_from_request('name');
        $short_name = get_param_from_request('short_name');
        $src_base_url = "/videonomafia/";
        if (isset($name) && !empty($name)) {
            $sound_url = "";
            if (isset($_FILES['soundFile'])) {
                $sound_url = $this->upload_file("contents/v8" . $src_base_url, "soundFile");
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
                'augmented_sound' => empty($sound_url) ? "no sound" : "sound",
                'augmented_sound_url' => $sound_url,
                'augmented_page' => "PAGE",
                'augmented_story' => "false",
                'augmented_eye' => "NOEYE",
                "available_story" => "false",
                "background" => 'url_to_file',
                "bodyguard_team" => "[]",
                "bodyguard_title" => "",
                "bundled_story" => "",
                "description" => "SansoneDescription",
                "facebook_photoID" => "https://www.facebook.com/NOmaluoghiestorienomafia/photos/a.495863130573082.1073741828.491407107685351/512852425540819/?type=3&theater",
                "geofenceAlert" => "GiulianoGeofenceAlert",
                "geofenceTitle" => "GiulianoGeofenceTitle",
                "icon" => "url_to_file",
                "index" => "story11",
                "link" => "script_giuliano.json",
                "map_icon" => "map_giuliano.jpg",
                "pinpoint_icon" => "storia_pinpoint_orangelight",
                'profile_icon' => "emanuelasansone.png",
                "share_icon" => "fb_giuliano1.jpg",
                "speakerDescription" => "SansoneSpeakerDescription",
                'speaker_icon' => "#pif2.png",
                "story_version" => "sv1.1",
                "admin_id" => 1,
                "status" => 1,
                "timestamp" => TIMESTAMP
            );
            $this->story_model->insert($insertArr);
            output_data(1);
        } else {
            output_data(0);
        }      

    }

}
