<?php
    include_once("./_common.php");

    $chk = isset($_POST['chk']) && is_array($_POST['chk']) ? array_map(function($v){
        return intval($v) >= 0 ? intval($v) : 0;
    },$_POST['chk']) : array();

    if(!count($chk)) alert("선택된 항목이 없습니다.",G5_ADMIN_URL."/schedule/schedule_log.php");

    // 배열 내 중복 값 제거
    $chk = array_unique($chk);

    // 오름차순 정렬
    sort($chk);

    // 오름차순 정렬 후 맨 첫번째 인덱스가 0인경우 제거
    if($chk[0] === 0) array_shift($chk);

    $sql_in = "(".implode(",",$chk).")";

    $sql_pre = " delete from g5_auto_schedule_log where idx IN {$sql_in} ";

    $res = sql_query($sql_pre);

    if($res) alert("선택한 로그의 삭제가 완료되었습니다.",G5_ADMIN_URL."/schedule/schedule_log.php");
    else alert("로그 삭제중 문제가 발생하였습니다.",G5_ADMIN_URL."/schedule/schedule_log.php");
