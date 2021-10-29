<?php

    /**
     * 그누보드 스케쥴링 플러그인 v1.0.6
     * Date : 2021-10-28
     * Author : dinist (https://github.com/devdinist)
     */

use Jaybizzle\CrawlerDetect\CrawlerDetect;

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

    $schedule_list_sql = " select * from g5_auto_schedule where status = 1 ";
    $schedule_list = sql_query($schedule_list_sql);

    if(!$schedule_list){
        unset($schedule_list_sql);
        unset($schedule_list);
    }else{
        $schedule_file = array();

        while($r = sql_fetch_array($schedule_list)):
            $php_preg_result = false;

            if (preg_match("/(\.php)$/i", $r['schedule_file'])) $php_preg_result = true;

            $schedule_file[$r['idx']] = array(
                "file" => $r['schedule_file'],
                "exec_time" => $r['execute_time'],
                "last_run_time" => $r['last_running_time'],
                "loop_type" => $r['loop_type'],
                "loop_number" => $r['loop_number'],
                "allow_robot" => $r['allow_robot'],
                "is_php" => $php_preg_result
            );
        endwhile;

        $log_sql = " insert into g5_auto_schedule_log(schedule_idx,schedule_log,log_time) values (";
        

        if(!empty($schedule_file) && is_array($schedule_file)) {

            foreach($schedule_file as $idx=>$info) {
                $log_sql_value = array();
                $log_sql_value['idx'] = $idx;

                date_default_timezone_set('Asia/Seoul');

                $a = strtotime($info['last_run_time']);
                $b = time();

                $time_set = 1;
                switch($info['loop_type']){
                    case "0":
                        $time_set *= 60;
                        break;
                    case "1":
                        $time_set *= (60 * 60);
                        break;
                    case "2":
                        $time_set *= (60 * 60 * 24);
                        break;
                    case "3":
                        $time_set *= (60 * 60 * 24 * 30);
                        break;
                }

                if(!$info['allow_robot']){
                    include_once(G5_PLUGIN_PATH."/Crawler_Detect/src/autoload.php");

                    $crawler_detect = new CrawlerDetect();

                    if($crawler_detect->isCrawler()) continue;
                }

                if($b-$a > $info['loop_number'] * $time_set || $info['exec_time'] == 0){
                    if(file_exists(G5_PATH.$info['file']) && $info['is_php']){
                        include_once(G5_PATH.$info['file']);
                        $log_sql_value['log'] = "'성공'";
                        $log_sql_value['last_running_time'] = "'".G5_TIME_YMDHIS."'";
                    }
                    else if(!file_exists(G5_PATH.$info['file'])){
                        $log_sql_value['log'] = "'실패 (파일이 존재하지 않습니다.)'";
                        $log_sql_value['last_running_time'] = "'".G5_TIME_YMDHIS."'";
                    }
                    else if(!$info['is_php']){
                        $log_sql_value['log'] = "'실패 (이 파일은 php 파일이 아닙니다.)'";
                        $log_sql_value['last_running_time'] = "'".G5_TIME_YMDHIS."'";
                    }
                    $execute_time = $info['exec_time']+1;

                
                    sql_fetch($log_sql.implode(",",$log_sql_value).")");
                    sql_fetch(" update g5_auto_schedule set execute_time = {$execute_time}, last_running_time = {$log_sql_value['last_running_time']} where idx = {$idx} ");
                }
            }
            unset($file);
        }
        unset($schedule_file);
    }