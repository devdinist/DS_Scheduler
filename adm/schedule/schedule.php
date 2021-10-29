<?php

/**
 * 그누보드 스케쥴링 플러그인 v1.0.6
 * Date : 2021-10-28
 * Author : dinist (https://github.com/devdinist)
 */

include_once("./_common.php");
include_once('./schedule_init.php');
$sub_menu = '600100';

$g5['title'] = '스케쥴관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql_common = " from g5_auto_schedule ";

$sql_search = " where (1) ";
if ($stx && in_array($sfl,array("schedule_name","schedule_file"))) {
    $sql_search .= " and ( ";
    $sql_search .= " ({$sfl} like '%{$stx}%') ";
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst = "schedule_name";
    $sod = "desc";
}

$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$colspan=9;
?>

<style>
    form[name='flist']{display: inline-block;}
    div.schedule_add_btn{display: inline;float: right;margin: 10px 0;}
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
            · 사용자 또는 Robot이 본 사이트에 접속할때, 등록한 php 파일을 실행하도록 스케쥴을 관리할 수 있습니다.<br>
            · 실행주기를 지정할 수 있습니다. <b style="color:red;">(하지만 본 사이트 방문이 없을 경우 스케쥴을 실행 할 수 없습니다.)</b><br>
            · 등록된 스케쥴은 사이트 내 모든 페이지 접근시 마다 실행되므로 실행파일의 문제여부를 미리 확인 해주세요<br>
        </p>
    </div>

<form name="flist" class="local_sch01 local_sch">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="save_stx" value="<?php echo $stx; ?>">

    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <option value="schedule_name"<?php echo get_selected($sfl, "schedule_name", true); ?>>스케쥴명</option>
        <option value="schedule_file"<?php echo get_selected($sfl, "schedule_file", true); ?>>스케쥴 파일</option>
    </select>

    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" required class="required frm_input">
    <input type="submit" value="검색" class="btn_submit">
</form>

<div class="schedule_add_btn">
    <a href="./schedule_form.php" class="btn btn_01">추가</a>
</div>

<form name="schedule_list" method="post" action="./schedule_delete.php" onsubmit="return submit_checker(this);">
    <div class="tbl_head01 tbl_wrap">
        <table>
        <thead>
        <tr>
            <th scope="col" id="schedule_list_chk">
                <label for="chkall" class="sound_only">전체</label>
                <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
            </th>
            <th scope="col" id="schedule_name"><?php echo subject_sort_link('schedule_name', '', 'desc') ?>스케쥴명</a></th>
            <th scope="col" id="schedule_file"><?php echo subject_sort_link('schedule_file', '', 'desc') ?>스케쥴 파일</a></th>
            <th scope="col" id="schedule_type_number">실행주기</th>
            <th scope="col" id="schedule_status">실행여부</th>
            <th scope="col" id="schedule_allow_robot">봇 방문시 실행여부</th>
            <th scope="col" id="schedule_last_running_time">최근 실행 시각</th>
            <th scope="col" id="schedule_execute_time">총 실행 수</th>
            <th scope="col" rowspan="2" id="mb_list_mng">관리</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $row=sql_fetch_array($result); $i++) {

            
            $s_mod = '<a href="./schedule_form.php?'.$qstr.'&amp;w=u&amp;idx='.$row['idx'].'" class="btn btn_03">수정</a>';
            $s_grp = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'" class="btn btn_02">그룹</a>';

            $bg = 'bg'.($i%2);

            switch($row['loop_type']) {
                case '0':
                    $loop_type = '분';
                    break;
                case '1':
                    $loop_type = '시간';
                    break;
                case '2':
                    $loop_type = '일';
                    break;
                default:
                    $loop_type = '개월';
                    break;
            }

            $status = intval($row['status']) ? "활성화" : "비활성화";
            $allow_robot = intval($row['allow_robot']) ? "활성화" : "비활성화";
            $last_running_time = get_text($row['last_running_time']);
            $execute_time = intval($row['execute_time']);
        ?>

        <tr class="<?php echo $bg; ?>">
            <td headers="schedule_list_chk" class="td_chk">
                <label for="chk_<?php echo get_text($row['idx']); ?>" class="sound_only"><?php echo get_text($row['idx']); ?></label>
                <input type="checkbox" name="chk[]" value="<?php echo get_text($row['idx']); ?>" id="chk_<?php echo get_text($row['idx']); ?>">
            </td>
            <td headers="schedule_name">
                <span><?php echo get_text($row['schedule_name']);?></span>
            </td>
            <td headers="schedule_file">
                <span><?php echo get_text($row['schedule_file']);?></span>
            </td>
            <td headers="schedule_type_number">
                <span><?php echo get_text($row['loop_number']).$loop_type."마다"; ?></span>
            </td>
            <td headers="schedule_status">
                <span><?php echo $status; ?></span>
            </td>
            <td headers="allow_robot">
                <span><?php echo $allow_robot; ?></span>
            </td>
            <td headers="last_running_time">
                <span><?php echo $last_running_time; ?></span>
            </td>
            <td headers="execute_time">
                <span><?php echo $execute_time; ?></span>
            </td>
            <td headers="schedule_status">
                <span><?php echo $s_mod; ?></span>
            </td>
        </tr>

        <?php
        }
        if ($i == 0)
            echo "<tr><td colspan=\"".$colspan."\" class=\"empty_table\">자료가 없습니다.</td></tr>";
        ?>
        </tbody>
        </table>
    </div>
    <?php if($i > 0): ?>
        <div class="del_btn">
            <button type="submit" class="log_delete btn_01">삭제</button>
        </div>
    <?php endif; ?>
</form>

<script>
    function submit_checker(f){
        var a = document.getElementsByName("chk[]");
        var checked = false;
        for(var i = 0; i<a.length; i++){
            if(a[i].checked){
                checked = true;
                break;
            }
        }

        if(!checked){
            alert("선택된 항목이 없습니다.");
            return false;
        }

        if(!confirm("선택된 항목을 삭제하시겠습니까? 삭제후에는 되돌릴 수 없습니다.")){
            return false;
        }

        return true;
    }
</script>

<?php

    include_once (G5_ADMIN_PATH.'/admin.tail.php');