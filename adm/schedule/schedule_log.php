<?php

/**
 * 그누보드 스케쥴링 플러그인 v1.0.4
 * Date : 2021-10-28
 * Author : dinist (https://github.com/devdinist)
 */

$sub_menu = '600200';
include_once('./_common.php');
include_once('./schedule_init.php');

auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = '스케쥴로그';
include_once(G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$colspan = 6;
$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'">처음</a>'; //페이지 처음으로 (초기화용도)
$sql_search = '';

if(isset($sfl) && $sfl && !in_array($sfl, array('schedule_idx','log_time')) ) {
    $sfl = '';
}
?>

<style>

    button.log_delete {
        display: inline-block;
        height: 30px;
        line-height: 30px;
        border: 0;
        border-radius: 5px;
        padding: 0 10px;
        font-weight: bold;
        font-size: 1.09em;
        vertical-align: middle;
    }

    div.del_btn{margin: 10px auto; text-align: center;}
</style>

<div class="local_desc02 local_desc">
        <p>
            · 스케쥴을 삭제하면 해당 스케쥴의 로그도 같이 삭제됩니다.
        </p>
    </div>

<div class="local_sch local_sch01">
    <form name="schedule_log" method="get" onsubmit="return schedule_log_submit(this);">
    <?php echo $listall?>
    <label for="sch_sort" class="sound_only">검색분류</label>
    <select name="sfl" id="sch_sort" class="search_sort">
        <option value="log_time"<?php echo get_selected($sfl, 'log_time'); ?>>시간</option>
        <option value="schedule_idx"<?php echo get_selected($sfl, 'schedule_idx'); ?>>인덱스번호</option>
    </select>
    <label for="sch_word" class="sound_only">검색어</label>
    <input type="text" name="stx" size="20" value="<?php echo stripslashes($stx); ?>" id="sch_word" class="frm_input" autocomplete="off">
    <input type="submit" value="검색" class="btn_submit">
    </form>
</div>

<form name="schedule_log" method="post" action="./schedule_log_delete.php">
<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col" id="schedule_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">스케쥴명</th>
        <th scope="col">스케쥴 파일</th>
        <th scope="col">로그</th>
        <th scope="col">시간</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql_common = " from g5_auto_schedule_log log join g5_auto_schedule schedule on log.schedule_idx = schedule.idx";
    if ($sfl) {

        if($sfl == "log_time") $stx = " between '{$stx} 00:00:00' and '{$stx} 23:59:59' ";
        else $stx = " = '{$stx}' ";

        $sql_search = " where log.$sfl {$stx} ";
    }
    $sql = " select count(*) as cnt
                {$sql_common}
                {$sql_search} ";
    $row = sql_fetch($sql);

    $total_count = $row['cnt'];

    $rows = $config['cf_page_rows'];
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    $sql = " select log.idx as idx, schedule.schedule_name as name,schedule.schedule_file as file ,log.schedule_log as log ,log_time as time
                {$sql_common}
                {$sql_search}
                order by log.idx desc
                limit {$from_record}, {$rows} ";
    $result = sql_query($sql);

    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
    <td headers="schedule_list_chk" class="td_chk">
            <label for="chk_<?php echo get_text($row['idx']); ?>" class="sound_only"><?php echo get_text($row['idx']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo get_text($row['idx']); ?>" id="chk_<?php echo get_text($row['idx']); ?>">
        </td>
        <td class="td_id"><span><?php echo $row['name']; ?></span></td>
        <td class="td_left"><span><?php echo $row['file']; ?></span></td>
        <td class="td_left"><span><?php echo $row['log']; ?></span></td>
        <td class="td_left"><span><?php echo $row['time']; ?></span></td>
    </tr>
    <?php } ?>
    <?php if ($i == 0) echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>'; ?>
    </tbody>
    </table>
    <?php if($i > 0): ?>
        <div class="del_btn">
            <button type="submit" class="log_delete btn_01">삭제</button>
        </div>
    <?php endif; ?>
</div>
</form>

<?php
$domain = isset($domain) ? $domain : '';
$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;page=');
if ($pagelist) {
    echo $pagelist;
}
?>

<script>
$(function(){
    $("#sch_sort").change(function(){ // select #sch_sort의 옵션이 바뀔때
        if($(this).val()=="log_time"){ // 해당 value 값이 vi_date이면
            $("#sch_word").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" }); // datepicker 실행
        }else{ // 아니라면
            $("#sch_word").datepicker("destroy"); // datepicker 미실행
        }
    });

    if($("#sch_sort option:selected").val()=="log_time"){ // select #sch_sort 의 옵션중 selected 된것의 값이 vi_date라면
        $("#sch_word").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" }); // datepicker 실행
    }
});

function schedule_log_submit(f)
{
    return true;
}

function schedule_log_delete(f){
    var chk = document.getElementsByName("chk[]");
    var checked = false;
    for(var i=0; i<chk.length; i++){
        if(chk[i].checked){
            checked = true;
            break;
        }
    }

    if(!checked){
        alert("선택된 항목이 없습니다.");
        return false;
    }

    if(confirm("선택하신 항목을 삭제하시겠습니까? 삭제 후에는 되돌릴 수 없습니다.")){
        return true;
    }else{
        return false;
    }

}
</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');

