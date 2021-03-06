<?php
$sub_menu = "600100";
include_once("./_common.php");

if ($w == 'u')
    check_demo();

check_admin_token();

$idx = isset($_POST['idx']) && is_numeric($_POST['idx']) ? intval($_POST['idx']) : '';
$schedule_name = isset($_POST['schedule_name']) ? html_purifier(trim($_POST['schedule_name'])) : '';
$schedule_file = isset($_POST['schedule_file']) ? preg_replace("/\.\./","",html_purifier(trim($_POST['schedule_file']))) : '';
$loop_type = isset($_POST['loop_type']) && is_numeric($_POST['loop_type']) ? intval($_POST['loop_type']) : '';
$loop_number = isset($_POST['loop_number']) && is_numeric($_POST['loop_number']) ? intval($_POST['loop_number']) : '';
$status = isset($_POST['status']) && is_numeric($_POST['status']) ? intval($_POST['status']) : '';
$allow_robot = isset($_POST['allow_robot']) && is_numeric($_POST['allow_robot']) ? intval($_POST['allow_robot']) : '';
$log_del_type = isset($_POST['log_del_type']) && is_numeric($_POST['log_del_type']) ? intval($_POST['log_del_type']) : '';
$log_del_number = isset($_POST['log_del_number']) && is_numeric($_POST['log_del_number']) ? intval($_POST['log_del_number']) : '';
$exec_available_time_start = isset($_POST['exec_available_time_start_number']) ? intval($_POST['exec_available_time_start_number']) : '';
$exec_available_time_end = isset($_POST['exec_available_time_end_number']) ? intval($_POST['exec_available_time_end_number']) : '';
$exec_day = isset($_POST['exec_day']) && is_array($_POST['exec_day']) ? array_map(function($v){return intval($v);},$_POST['exec_day']) : array();
$exec_day = implode("|",$exec_day);

if($w == "u" && strlen($idx) == 0) alert("idx 값이 없습니다.");

$sql_common = "  schedule_name = '{$schedule_name}',
                 schedule_file = '{$schedule_file}',
                 loop_type = '{$loop_type}',
                 loop_number = '{$loop_number}',
                 status = '{$status}',
                 allow_robot = '{$allow_robot}',
                 log_del_type = '{$log_del_type}',
                 log_del_number = '{$log_del_number}',
                 exec_available_time_start = '{$exec_available_time_start}',
                 exec_available_time_end = '{$exec_available_time_end}',
                 exec_day = '{$exec_day}'
                  ";

$sql_insert_common = " '{$schedule_name}', '{$schedule_file}', '{$loop_type}', '{$loop_number}', '{$status}', '{$allow_robot}', '{$log_del_type}', '{$log_del_number}', '{$exec_available_time_start}', '{$exec_available_time_end}', '{$exec_day}' ";

if (strlen($schedule_name) == 0)
    alert('스케쥴명이 입력되지 않았거나 잘못된 값이 입력되었습니다.');

if (strlen($schedule_file) == 0)
    alert('스케쥴 파일이 입력되지 않았거나 잘못된 값이 입력되었습니다.');

if(!in_array($loop_type,array(0,1,2,3)))
    alert('실행주기 값 설정이 잘못되었습니다.');

if($exec_available_time_start < 0 || $exec_available_time_start > 23) alert('실행 가능 시간 시작 값이 잘못되었습니다.');

if($exec_available_time_end < 0 || $exec_available_time_end > 23) alert('실행 가능 시간 종료 값이 잘못되었습니다.');
    
switch($loop_type){
    case "0":
        if($loop_number <= 0 || $loop_number > 59) alert("실행주기 값 범위를 벗어났습니다.");
        break;
    case "1":
        if($loop_number <= 0 || $loop_number > 23) alert("실행주기 값 범위를 벗어났습니다.");
        break;
    case "2":
        if($loop_number <= 0 || $loop_number > 29) alert("실행주기 값 범위를 벗어났습니다.");
        break;
    case "3":
        if($loop_number <= 0 || $loop_number > 11) alert("실행주기 값 범위를 벗어났습니다.");
        break;
}

if(!in_array($log_del_type,array(0,1,2,3)))
    alert('로그 삭제 주기 값 설정이 잘못되었습니다.');

switch($log_del_type){
    case "0":
        if($log_del_number < 0 || $log_del_number > 59) alert("로그 삭제 주기 값 범위를 벗어났습니다.");
        break;
    case "1":
        if($log_del_number < 0 || $log_del_number > 23) alert("로그 삭제 주기 값 범위를 벗어났습니다.");
        break;
    case "2":
        if($log_del_number < 0 || $log_del_number > 29) alert("로그 삭제 주기 값 범위를 벗어났습니다.");
        break;
    case "3":
        if($log_del_number < 0 || $log_del_number > 11) alert("로그 삭제 주기 값 범위를 벗어났습니다.");
        break;
}

if(!in_array($status,array(0,1)))
    alert('활성화 여부 지정이 잘못되었습니다.');

// 파일 존재 여부 확인
if( $w == '' || $w == 'u' ){

    $php_regex = "/(\.(php))$/i";

    if (!preg_match($php_regex, $schedule_file)) {
        alert($schedule_file . '은(는) php 파일이 아닙니다.');
    }
}

if($w == "u"):

    $sql_u = " update g5_auto_schedule
                    set {$sql_common}
                    where idx = '{$idx}' ";

    $res_u = sql_query($sql_u);

    if($res_u):
        alert("스케쥴 수정이 완료되었습니다.",G5_ADMIN_URL."/schedule/schedule.php");
    endif;

elseif($w == "d"):

    $sql = " delete from g5_auto_schedule where idx = '{$idx}' ";
    $sql2 = " delete from g5_auto_schedule_log where schedule_idx = '{$idx}' ";

    $res = sql_query($sql);
    $res2 = sql_query($sql2);

    if($res && $res2): alert("스케쥴 삭제가 완료되었습니다.",G5_ADMIN_URL."/schedule/schedule.php");
    else: alert("스케쥴 삭제중 문제가 발생하였습니다.",G5_ADMIN_URL."/schedule/schedule.php");
    endif;

else:

    $sql = " insert into g5_auto_schedule (schedule_name, schedule_file, loop_type, loop_number, status, allow_robot, log_del_type, log_del_number, exec_available_time_start, exec_available_time_end, exec_day ) values ({$sql_insert_common}) ";
    sql_query($sql);

    $idx = sql_insert_id();
    
    if($idx): alert("스케쥴 등록이 완료되었습니다.",G5_ADMIN_URL."/schedule/schedule.php");
    else: alert("스케쥴 등록중 문제가 발생하였습니다.",G5_ADMIN_URL."/schedule/schedule.php");
    endif;

endif;

goto_url('./schedule_form.php?'.$qstr.'&amp;w=u&amp;idx='.$idx, false);