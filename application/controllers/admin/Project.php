<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once 'Admin_controller.php';

class Project extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->check_login();
        $this->load->model('story_model');
        $this->load->model('project_model');
        $this->load->model('story_detail_model');
        $this->load->model('story_description_model');
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

            $detail_info['profile_icon'] = empty($detail_item['profile_icon']) ? "" : base_url() . "uploads/contents/v8" . $detail_item['resources_base_url']. $detail_item['profile_icon'];
            $detail_info['speaker_icon1'] = empty($detail_item['speaker_icon1']) ? "" : base_url() . "uploads/contents/v8" . $detail_item['resources_base_url'].$detail_item['speaker_icon1'];
            $detail_info['speaker_icon2'] = empty($detail_item['speaker_icon2']) ? "" : base_url() . "uploads/contents/v8" . $detail_item['resources_base_url'].$detail_item['speaker_icon2'];
            $detail_info['speaker_icon3'] = empty($detail_item['speaker_icon3']) ? "" : base_url() . "uploads/contents/v8" . $detail_item['resources_base_url'].$detail_item['speaker_icon3'];
            $detail_info['speaker_icon4'] = empty($detail_item['speaker_icon4']) ? "" : base_url() . "uploads/contents/v8" . $detail_item['resources_base_url'].$detail_item['speaker_icon4'];
            $detail_info['speaker_icon5'] = empty($detail_item['speaker_icon5']) ? "" : base_url() . "uploads/contents/v8" . $detail_item['resources_base_url'].$detail_item['speaker_icon5'];
            $detail_info['media'] = empty($detail_item['media']) ? "" : base_url() . "uploads/contents/v8" . $detail_item['resources_base_url'].$detail_item['media'];
            $detail_info['resources_base_url'] = $detail_item['resources_base_url'];
        }

        output_data(array("detail" => $detail_info));
    }
    public function update_project_detail() {
        $type = "project";
        $src_base_url = "/detail/";
        $id = 1;
        $title_en = get_param_from_request('title_en');
        $title_it = get_param_from_request('title_it');
        $description_en = get_param_from_request('description_en');
        $description_it = get_param_from_request('description_it');
        $content_en = get_param_from_request('content_en');
        $content_it = get_param_from_request('content_it');
        $credit1_en = get_param_from_request('credit1_en');
        $credit1_it = get_param_from_request('credit1_it');
        $credit2_en = get_param_from_request('credit2_en');
        $credit2_it = get_param_from_request('credit2_it');
        $credit3_en = get_param_from_request('credit3_en');
        $credit3_it = get_param_from_request('credit3_it');

        $detail_item = $this->project_model->get(array("id" => 1));
        if (empty($detail_item)) {
            output_error("Project info doesn't exist");
        } else {
            $this->story_description_model->update(
                array('des_en' => $title_en, 'des_it' => $title_it),
                array('key' => "title", 'type' => $type)
            );
            $this->story_description_model->update(
                array('des_en' => $description_en, 'des_it' => $description_it),
                array('key' => "description", 'type' => $type)
            );
            $this->story_description_model->update(
                array('des_en' => $content_en, 'des_it' => $content_it),
                array('key' => "content", 'type' => $type)
            );
            $this->story_description_model->update(
                array('des_en' => $credit1_en, 'des_it' => $credit1_it),
                array('key' => "credit1", 'type' => $type)
            );
            $this->story_description_model->update(
                array('des_en' => $credit2_en, 'des_it' => $credit2_it),
                array('key' => "credit2", 'type' => $type)
            );
            $this->story_description_model->update(
                array('des_en' => $credit3_en, 'des_it' => $credit3_it),
                array('key' => "credit3", 'type' => $type)
            );
            if (isset($_FILES['speakerFile1'])) {
                $speaker_image1 = $this->upload_file("contents/v8" . $src_base_url, "speakerFile1");
                $this->project_model->update(array('speaker_icon1' => $speaker_image1), array('id' => $id));
            }
            if (isset($_FILES['speakerFile2'])) {
                $speaker_image2 = $this->upload_file("contents/v8" . $src_base_url, "speakerFile2");
                $this->project_model->update(array('speaker_icon2' => $speaker_image2), array('id' => $id));
            }
            if (isset($_FILES['speakerFile3'])) {
                $speaker_image3 = $this->upload_file("contents/v8" . $src_base_url, "speakerFile3");
                $this->project_model->update(array('speaker_icon3' => $speaker_image3), array('id' => $id));
            }
            if (isset($_FILES['speakerFile4'])) {
                $speaker_image4 = $this->upload_file("contents/v8" . $src_base_url, "speakerFile4");
                $this->project_model->update(array('speaker_icon4' => $speaker_image4), array('id' => $id));
            }
            if (isset($_FILES['speakerFile5'])) {
                $speaker_image5 = $this->upload_file("contents/v8" . $src_base_url, "speakerFile5");
                $this->project_model->update(array('speaker_icon5' => $speaker_image5), array('id' => $id));
            }
            if (isset($_FILES['profileFile'])) {
                $profile_image = $this->upload_file("contents/v8" . $src_base_url, "profileFile");
                $this->project_model->update(array('profile_icon' => $profile_image), array('id' => $id));
            }
            if (isset($_FILES['mediaFile'])) {
                $media = $this->upload_file("contents/v8" . $src_base_url, "mediaFile");
                $this->project_model->update(array('media' => $media), array('id' => $id));
            }
            output_data(1);
        }

    }
}