<?php

/**
 * 그누보드 스케쥴링 플러그인 v1.0
 * Date : 2021-10-28
 * Author : dinist (https://github.com/devdinist)
 */

$sub_menu = "600100";
include_once("./_common.php");

if ($w == 'u')
    check_demo();

auth_check_menu($auth, $sub_menu, 'w');

check_admin_token();

$idx = isset($_POST['idx']) && is_numeric($_POST['idx']) ? intval($_POST['idx']) : '';
$schedule_name = isset($_POST['schedule_name']) ? html_purifier(trim($_POST['schedule_name'])) : '';
$schedule_file = isset($_POST['schedule_file']) ? preg_replace("/\.\./","",html_purifier(trim($_POST['schedule_file']))) : '';
$loop_type = isset($_POST['loop_type']) && is_numeric($_POST['loop_type']) ? intval($_POST['loop_type']) : '';
$loop_number = isset($_POST['loop_number']) && is_numeric($_POST['loop_number']) ? intval($_POST['loop_number']) : '';
$status = isset($_POST['status']) && is_numeric($_POST['status']) ? intval($_POST['status']) : '';
$allow_robot = isset($_POST['allow_robot']) && is_numeric($_POST['allow_robot']) ? intval($_POST['allow_robot']) : '';

if($w == "u" && strlen($idx) == 0) alert("idx 값이 없습니다.");

// 스케쥴 파일 확장자 체크
if(!preg_match("/\.(php)$/i",$schedule_file)) alert("php 파일만 지정할 수 있습니다.");

$sql_common = "  schedule_name = '{$schedule_name}',
                 schedule_file = '{$schedule_file}',
                 loop_type = '{$loop_type}',
                 loop_number = '{$loop_number}',
                 status = '{$status}',
                 allow_robot = '{$allow_robot}'
                  ";

$sql_insert_common = " '{$schedule_name}', '{$schedule_file}', '{$loop_type}', '{$loop_number}', '{$status}', '{$allow_robot}' ";

if (strlen($schedule_name) == 0)
    alert('스케쥴명이 입력되지 않았거나 잘못된 값이 입력되었습니다.');

if (strlen($schedule_file) == 0)
    alert('스케쥴 파일이 입력되지 않았거나 잘못된 값이 입력되었습니다.');

if(!in_array($loop_type,array(0,1,2,3)))
    alert('실행주기 값 설정이 잘못되었습니다.');

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
    $sql = " update g5_auto_schedule
                    set {$sql_common}
                    where idx = '{$idx}' ";
    sql_query($sql);
else:
    $sql = " insert into g5_auto_schedule (schedule_name, schedule_file, loop_type, loop_number, status, allow_robot) values ({$sql_insert_common}) ";
    sql_query($sql);
    $idx = sql_insert_id();
endif;

goto_url('./schedule_form.php?'.$qstr.'&amp;w=u&amp;idx='.$idx, false);