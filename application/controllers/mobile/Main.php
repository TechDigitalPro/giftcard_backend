<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/paypal/paypal.php';
require_once APPPATH . 'third_party/stripe-php/init.php';
include_once 'Mobile_controller.php';

class Main extends Mobile_controller
{
    public $total_match = array();
    public $total_match_count = 0;
    public $tmp_match_one = array();

    public $round_result = array();

    public $group = array();
    public $group_count = 0;

    public $tmp_one_f_result = array();
    
    public $final_count = 0;
    public $final_result_arr = array();

    

    // public function __construct()
    // {
    //     parent::__construct();
    //     $this->load->model('user_model');
    //     $this->load->model('story_model');
    //     $this->load->model('story_description_model');
    //     $this->load->model('addiopizzo_model');
    //     $this->load->model('configuration_model');
    //     $this->load->model('francese_model');
    //     $this->load->model('project_model');
    //     $this->load->model('story_detail_model');

    // }

    public function test()
    {
        $teamIds = array('Team1', 'Team2', 'Team3', 'Team4', 'Team5', 'Team6');
        $this->getAllMatchs($teamIds, 1, 0);
        // print_r($this->total_match);
        $this->makeRounds($teamIds, 0, 0);
    //    print_r($this->group);
        $tmp_ok = $this->total_match_count / (sizeof($teamIds) / 2);
        $this->final_result($teamIds, $tmp_ok, 0, 0);
        print_r($this->final_result_arr);
    }
    private function final_result($teamIds, $tmp_ok, $start_point, $ok)
    {
        
        if ($this->final_count == 1) {
            return;
        }
        if ($ok == $tmp_ok) {
            $fflag = array();
            for ($ii = 0; $ii < $this->total_match_count; $ii++) {
                $fflag[] = 0;
            }
            for ($ii = 0; $ii < $ok; $ii++) {
                for ($j = 0; $j < sizeof($teamIds) / 2; $j ++ ) {
                    $fflag[$this->group[$this->tmp_one_f_result[$ii]][$j]] = 1;
                }
            }
            for ($ii = 0; $ii < $this->total_match_count; $ii++) {
                if ($fflag[$ii] == 0) {
                    return;
                }
            }
            for ($ii = 0; $ii < $ok; $ii++) {
                $tmp_result = array();
                for ($j = 0; $j < sizeof($teamIds) / 2; $j ++ ) {
                    $tmp_result[] = $this->total_match[$this->group[$this->tmp_one_f_result[$ii]][$j]][0].":".$this->total_match[$this->group[$this->tmp_one_f_result[$ii]][$j]][1];
                }
                $this->final_result_arr[] = $tmp_result;
            }
            $this->final_count = $this->final_count + 1;
            return;
        }
        for ($i = $start_point; $i < $this->group_count; $i++) {
            $this->tmp_one_f_result[$ok] = $i;
            $this->final_result($teamIds, $tmp_ok, $i + 1, $ok + 1);
        }
    }
    private function getAllMatchs($teamIds, $start_point, $ok)
    {
        if ($ok == 2) {
            $this->total_match[$this->total_match_count] = array($teamIds[$this->tmp_match_one[0] - 1], $teamIds[$this->tmp_match_one[1] - 1]);
            $this->total_match_count++;
        }
        for ($i = $start_point; $i <= sizeof($teamIds); $i++) {
            $this->tmp_match_one[$ok] = $i; //$teamIds[$i];
            $this->getAllMatchs($teamIds, $i + 1, $ok + 1);
        }
    }
    private function makeRounds($teamIds, $start_point, $ok)
    {
        if (sizeof($teamIds) / 2 == $ok) {
            for ($i = 0; $i < $ok; $i++) {
                for ($j = $i + 1; $j < $ok; $j++) {
                    if ($this->total_match[$this->round_result[$i]][0] == $this->total_match[$this->round_result[$j]][0] || 
                    $this->total_match[$this->round_result[$i]][1] == $this->total_match[$this->round_result[$j]][1] || 
                    $this->total_match[$this->round_result[$i]][0] == $this->total_match[$this->round_result[$j]][1] ||
                    $this->total_match[$this->round_result[$i]][1] == $this->total_match[$this->round_result[$j]][0]) {
                        return;
                    }
                }
            }
            for ($i = 0; $i < $ok; $i++) {
                $this->group[$this->group_count][] = $this->round_result[$i];
            }
            $this->group_count++;
            return;
        }
        for ($i = $start_point; $i < $this->total_match_count; $i++) {
            $this->round_result[$ok] = $i;
            $this->makeRounds($teamIds, $i + 1, $ok + 1);
        }
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
        foreach ($result as $match) {
            $matches[] = $match[0] . ":" . $match[1];
        }
        $matches = array_reverse($matches, false);
        $no_repeat_matches = sizeof($arr) / 2;
        // $games = $this->get_match_arr($matches, 3);
        // $games = array_reverse($games, false);
        print_r($matches);
    }

    public function test1()
    {

        $blackout_days = "01-16-2023";
        $tmp_start_date = "01-04-2023";
        $start_date = explode("-", $tmp_start_date)[1] . "-" . explode("-", $tmp_start_date)[0] . "-" . explode("-", $tmp_start_date)[2];
        $max_game = 1;
        $length = 55;
        $between = 5;
        $games_team = 1;
        $start_Dayofweek = date('w', strtotime($start_date));
        // print_r($start_Dayofweek);
        $start_day = explode("-", $start_date)[1];
        $start_month = explode("-", $start_date)[0];
        $start_year = explode("-", $start_date)[2];

        $days_times = "1-12:00 pm,3-01:00 pm";
        $location_ids = "25-22,25";
        $location_names = "location2-test location1,location2";
        $start_day_of_week = $start_Dayofweek;
        if (strpos($days_times, $start_Dayofweek . "-") == false) {
            $start_day_of_week = explode("-", $days_times)[0];
        }

        $days_times_list = explode(",", $days_times);
        $location_id_list = explode(",", $location_ids);
        $location_name_list = explode(",", $location_names);
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
            $tmpOneDay['location_id'] = $location_id_list[$key];
            $tmpOneDay['location_name'] = $location_name_list[$key];
            $tmpOneDay['location_count'] = sizeof(explode("-", $location_id_list[$key]));
            $DayTimes[] = $tmpOneDay;
        }
        $start_day_of_week_index = 0;
        foreach ($DayTimes as $tmpOneDay) {
            if (intval($tmpOneDay['day_of_week']) == intval($start_day_of_week)) {
                $start_day_of_week_index = $tmpOneDay['key'];
                break;
            }
        }
        
        $teamIds = array('Team1', 'Team2', 'Team3', 'Team4');
        $this->getAllMatchs($teamIds, 1, 0);
        $this->makeRounds($teamIds, 0, 0);
        $tmp_ok = $this->total_match_count / (sizeof($teamIds) / 2);
        $this->final_result($teamIds, $tmp_ok, 0, 0);
        $matches = $this->final_result_arr;
        //print_r($matches);
        $all_match_list = array();
        foreach ($matches as $round) {
            foreach ($round as $one_game) {
                $home_team = explode(":", $one_game)[0];
                $away_team = explode(":", $one_game)[1];
                if ($home_team != "Bye" && $away_team != "Bye") {
                    $tmp_match_list = array();
                    $tmp_match_list['home_team_id'] = $home_team;
                    $tmp_match_list['away_team_id'] = $away_team;
                    $tmp_match_list['round'] = $this->getRound($all_match_list, $home_team, $away_team, $max_game);
                    $all_match_list[] = $tmp_match_list;
                }
            }
        }
        // print_r($all_match_list);

        
        $latest_round = $all_match_list[sizeof($all_match_list) - 1]['round'];
        //print_r($start_Dayofweek);
        $result = array();
        $k = 0;
        $result_date_list = array();
        for ($i = 0; $i < $games_team; $i++) {

            $start_day = explode("-", $tmp_start_date)[1];
            $start_month = explode("-", $tmp_start_date)[0];
            $start_year = explode("-", $tmp_start_date)[2];

            $tmpResult = array();
            $result_date_value = $this->getStartDate($i, $k, $DayTimes, $start_day_of_week_index, $start_day, $start_month, $start_year, $start_Dayofweek, $blackout_days, $tmp_start_date, $result_date_list);
            $result_date = $result_date_value["result_date"];
            $dayList_index = $result_date_value["day_index"];
            $k = $result_date_value["k"];
            $blackout_days = $result_date_value["blackout_days"];
            $result_date_list[] = $result_date;

            $one_round_games = $this->getOneRoundGames($all_match_list, $i);
            for ($j = 0; $j < sizeof($one_round_games); $j++) {
                $tmpResult = $one_round_games[$j];
                $location_cnt = $DayTimes[$dayList_index]['location_count'];
                $tmpLocationIDs = $DayTimes[$dayList_index]['location_id'];
                $tmpLocationNames = $DayTimes[$dayList_index]['location_name'];
                
                $one_game_start_time = $DayTimes[$dayList_index]['time'];
                $one_game_start_time_hh = intval(explode(":", $one_game_start_time)[0]);
                $one_game_start_time_mm = intval(explode(":", $one_game_start_time)[1]);

                $minute = $one_game_start_time_hh * 60 + $one_game_start_time_mm;
                $delta = 0;
                if ($location_cnt == 1) {
                    $delta = $minute + $j * ($length + $between);
                    $tmpResult['location_id'] = $tmpLocationIDs;
                    $tmpResult['location_name'] = $tmpLocationNames;
                } else {
                    $location_available = $j % $location_cnt;
                    $tmpResult['location_id'] = explode("-", $tmpLocationIDs)[$location_available];
                    $tmpResult['location_name'] = explode("-", $tmpLocationNames)[$location_available];
                    if ($location_cnt >= $j + 1) {
                        $delta = $minute;
                    } else {
                        $delta = $minute + ($j - $location_cnt + 1) * ($length + $between);
                    }
                }
                
                $new_hh = "";
                $new_am_pm = "";
                if (intval($delta / 60) > 12) {
                    $new_hh = intval($delta / 60) - 12;
                    $new_am_pm = "pm";
                } else if (intval($delta / 60) == 12) {
                    $new_hh = intval($delta / 60);
                    $new_am_pm = "pm";
                } else {
                    $new_hh = intval($delta / 60);
                    $new_am_pm = "am";
                }
                if ($new_hh < 10) {
                    $new_hh = "0" . $new_hh;
                }

                $new_mm = "";
                if (intval($delta % 60) < 10) {
                    $new_mm = "0" . intval($delta % 60);
                } else {
                    $new_mm = intval($delta % 60);
                }
                $tmpResult['start_time'] = $new_hh . ":" . $new_mm;
                $tmpResult['start_date'] = $result_date;
                $tmpResult['game_start_date_time'] = $result_date . "T" . $new_hh . ":" . $new_mm . " " . $new_am_pm;
                $result[] = $tmpResult;
            }

        }
        print_r($result);
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
        $match_count = $match_list[sizeof($match_list) - 1]['round'] + 1;

        $tmpList = array();
        foreach ($match_list as $item) {
            if ($match_count > $i) {
                if ($item['round'] == $i) {
                    $tmpList[] = $item;
                }
            } else {
                $k = $i % $match_count;
                $repeat_k = intval($i/$match_count) % 2;
                if ($repeat_k == 0) {
                    if ($item['round'] == $k) {
                        $tmpItem['home_team_id'] = $item['home_team_id'];
                        $tmpItem['away_team_id'] = $item['away_team_id'];
                        $tmpItem['round'] = $i;
                        $tmpList[] = $tmpItem;
                    }
                } else {
                    if ($item['round'] == $k) {
                        $tmpItem = array();
                        $tmpItem['home_team_id'] = $item['away_team_id'];
                        $tmpItem['away_team_id'] = $item['home_team_id'];
                        $tmpItem['round'] = $i;
                        $tmpList[] = $tmpItem;
                    }
                }
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
