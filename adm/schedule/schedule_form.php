<?php

/**
 * 그누보드 스케쥴링 플러그인 v1.0.9
 * Date : 2021-10-28
 * Author : dinist (https://github.com/devdinist)
 */

$sub_menu = "600100";
include_once('./_common.php');
include_once('./schedule_init.php');

$idx = isset($_REQUEST['idx']) ? intval(addslashes(clean_xss_tags(clean_xss_attributes($_REQUEST['idx']),1,1))) : 0;

$sound_only = '';
$required_mb_id_class = '';

if ($w == '')
{
    $required_schedule_name = 'required';
    $required_schedule_name_class = 'required alnum_';
    $sound_only = '<strong class="sound_only">필수</strong>';

}
else if ($w == 'u')
{
    $schedule = sql_fetch(" select * from g5_auto_schedule where idx = '{$idx}'");
    if (!$schedule['idx'])
        alert('존재하지 않는 자료입니다.');

}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');

if($w == 'u' && !$idx) alert("idx 값이 설정되지 않았습니다.");


$add_ment = $w == "u" ? "수정" : "등록";
$g5['title'] = '스케쥴'.$add_ment;

include_once(G5_ADMIN_PATH.'/admin.head.php');

?>
<style>
    input[name$='_number']{width: 35px;}
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }
    input[type=number] {
    -moz-appearance: textfield;
    }
</style>
<form name="fschedule" id="fschedule" action="./schedule_form_update.php" onsubmit="return fschedule_submit(this);" method="post">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="idx" value="<?php echo $idx ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="schedule_name">스케쥴명<?php echo $sound_only ?></label></th>
        <td>
            <input type="text" name="schedule_name" value="<?php echo $schedule['schedule_name'] ?>" id="schedule_name" required class="frm_input required" size="70">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="schedule_file">스케쥴 파일<?php echo $sound_only ?></label></th>
        <td>
            <?php echo help("최상위 디렉터리를 기준으로 절대경로로 입력해주세요"); ?>
            <input type="text" name="schedule_file" value="<?php echo $schedule['schedule_file'] ?>" id="schedule_file" required class="frm_input required" size="70">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="schedule_file">실행 주기<?php echo $sound_only ?></label></th>
        <td>
            <input type="number" name="loop_number" value="<?php echo $schedule['loop_number'] ?>" id="loop_number" <?php echo $required_schedule_name; ?> class="frm_input <?php echo $required_schedule_name_class; ?>" size="5"  maxlength="20">
            <select name="loop_type" id="loop_type">
                <option value="0"<?php echo get_selected($schedule['loop_type'], "0"); ?>>분</option>
                <option value="1"<?php echo get_selected($schedule['loop_type'], "1"); ?>>시간</option>
                <option value="2"<?php echo get_selected($schedule['loop_type'], "2"); ?>>일</option>
                <option value="3"<?php echo get_selected($schedule['loop_type'], "3"); ?>>월</option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="schedule_file">실행 가능 시간대 설정<?php echo $sound_only ?></label></th>
        <td>
            <select name="exec_available_time_start_number" id="exec_available_time_start_number" data-alias="eats_num" style="width: 40px;">
                <?php for($eatsn = 0; $eatsn <= 23; $eatsn++): 
                        $default_eats = 0;
                        if(strlen($schedule['exec_available_time_start']) > 0) $default_eats = intval($schedule['exec_available_time_start']);
                ?>
                        <option value="<?php echo $eatsn;?>" <?php echo get_selected($eatsn, $default_eats); ?>><?php echo sprintf("%02d",$eatsn);?></option>
                <?php endfor; ?>
            </select>
            <span>시 ~ </span>

            <select name="exec_available_time_end_number" id="exec_available_time_end_number" data-alias="eate_num" style="width: 40px;">
                <?php for($eaten = 0; $eaten <= 23; $eaten++): 
                        $default_eate = 23;
                        if(strlen($schedule['exec_available_time_end']) > 0) $default_eate = intval($schedule['exec_available_time_end']);
                ?>
                        <option value="<?php echo $eaten;?>" <?php echo get_selected($eaten, $default_eate); ?>><?php echo sprintf("%02d",$eaten);?></option>
                <?php endfor; ?>
            </select>
            <span>시</span>

            <?php echo help('<span id="eats_num">00</span>:00 ~ <span id="eate_num">23</span>:59분 까지'); ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="schedule_file">실행 가능 요일 설정<?php echo $sound_only ?></label></th>
        <td>
                    <div>
                    <button type="button" class="checkbox_checker" id="all" style="border: 1px solid black; background:black; color:white; padding: 2px 5px; font-weight:bold;">매일</button>
                    <button type="button" class="checkbox_checker" id="weekend" style="border: 1px solid black; background:black; color:white; padding: 2px 5px; font-weight:bold;">주말</button>
                    <button type="button" class="checkbox_checker" id="weekday" style="border: 1px solid black; background:black; color:white; padding: 2px 5px; font-weight:bold;">평일</button>
                    </div>
            <?php
                    $exec_day = strlen($schedule['exec_day']) > 0 ? explode("|",$schedule['exec_day']) : array();
                    $format = new IntlDateFormatter('ko_KR',IntlDateFormatter::FULL,IntlDateFormatter::NONE,'Asia/Seoul',IntlDateFormatter::GREGORIAN,'E');
                    for($i = 1; $i <= 7; $i++):
                        $date_stamp = $format->format(strtotime("+{$i} day", strtotime('2021-08-01'))); ?>
                        <input type="checkbox" name="exec_day[]" id="exec_day[<?php echo ($i-1); ?>]" value="<?php echo ($i-1); ?>" <?php echo in_array(($i-1),$exec_day) ? "checked" : ""; ?>>
                        <label for="exec_day[<?php echo ($i-1); ?>]"><?php echo $date_stamp."요일";?></label>
            <?php   
                    endfor; ?>
            <?php   echo help("활성화 된 요일에만 스케쥴 작업이 진행됩니다.");
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="schedule_file">로그 삭제 주기<?php echo $sound_only ?></label></th>
        <td>
            <?php echo help("현재시간을 기준으로 아래 지정한 기간이 지난 로그를 삭제합니다.
            값을 0으로 지정하는 경우 로그를 삭제하지 않습니다.
            로그를 삭제하지 않고 지속적으로 저장하면 DB용량 증가의 원인이 됩니다."); ?>
            <input type="number" name="log_del_number" value="<?php echo strlen($schedule['log_del_number']) > 0 ? $schedule['log_del_number'] : "0"; ?>" id="log_del_number" <?php echo $required_schedule_name; ?> class="frm_input <?php echo $required_schedule_name_class; ?>" size="5"  maxlength="20">
            <select name="log_del_type" id="log_del_type">
                <option value="0"<?php echo get_selected($schedule['log_del_type'], "0"); ?>>분</option>
                <option value="1"<?php echo get_selected($schedule['log_del_type'], "1"); ?>>시간</option>
                <option value="2"<?php echo get_selected($schedule['log_del_type'], "2"); ?>>일</option>
                <option value="3"<?php echo get_selected($schedule['log_del_type'], "3"); ?>>월</option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="allow_robot">로봇 방문시 실행 여부<?php echo $sound_only ?></label></th>
        <td>
            <select name="allow_robot" id="allow_robot">
                <option value="0"<?php echo get_selected($schedule['allow_robot'], "0"); ?>>비활성화</option>
                <option value="1"<?php echo get_selected($schedule['allow_robot'], "1"); ?>>활성화</option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="status">활성화 여부<?php echo $sound_only ?></label></th>
        <td>
            <select name="status" id="status">
                <option value="0"<?php echo get_selected($schedule['status'], "0"); ?>>비활성화</option>
                <option value="1"<?php echo get_selected($schedule['status'], "1"); ?>>활성화</option>
            </select>
        </td>
    </tr>
    

    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <a href="./schedule.php?<?php echo $qstr ?>" class="btn btn_02">목록</a>
    <input type="submit" name="submit_btn" value="확인" class="btn_submit btn" accesskey='s' onclick="document.pressed='확인'">
    <?php if($w == "u"): ?>
    <input type="submit" name="submit_btn" value="삭제" class="btn_submit btn" accesskey='s' onclick="document.pressed='삭제'">
    <?php endif; ?>
</div>
</form>

<script>

function sizetwo_zero_filler(number){
    return number.length < 2 ? new Array(number.length+1).join('0') + number : number;
}

var loop_number = $("input[name='loop_number']");

function value_range_checker(type){
    switch(type.val()){
        case "0":
            loop_number.attr("min","1");
            loop_number.attr("max","59");
            break;
        case "1":
            loop_number.attr("min","1");
            loop_number.attr("max","23");
            break;
        case "2":
            loop_number.attr("min","1");
            loop_number.attr("max","29");
            break;
        case "3":
            loop_number.attr("min","1");
            loop_number.attr("max","11");
            break;
    }
}

$("select[name='loop_type']").on("change",function(){
    value_range_checker($(this));
})

$(function(){
    $("select[name^='exec_available_time']").each(function(i,e){
        var span_id = $(this).attr("data-alias");
        $("span#"+span_id).text(sizetwo_zero_filler($(this).val()))
    })
})

$("select[name^='exec_available_time'").on("change",function(){
    var span_id = $(this).attr("data-alias");
    $("span#"+span_id).text(sizetwo_zero_filler($(this).val()))
})

$("button.checkbox_checker").on("click",function(){
    var eleid = $(this).attr("id");
    var day_obj = {
        "all" : [0,1,2,3,4,5,6],
        "weekend" : [5,6],
        "weekday" : [0,1,2,3,4]
    };

    $("input[name='exec_day[]']").prop("checked",false)

    for(const value of day_obj[eleid]){
        $("input[name='exec_day[]']").each(function(i,e){
            if($(this).val() == value){
                $(this).prop("checked",true)
            }
        })
    }
})

value_range_checker($("select[name='loop_type']"))

function fschedule_submit(f)
{
    if (!f.schedule_name.value) {
        alert('스케쥴명을 입력하세요.');
        return false;
    }

    if (!f.schedule_file.value) {
        alert('스케쥴 파일을 입력하세요.');
        return false;
    }

    if (!f.loop_number.value) {
        alert('실행주기 값을 입력하세요.');
        return false;
    }

    if (document.pressed == "삭제") {
        if(confirm("스케쥴을 삭제하시겠습니까? 진행 후에는 되돌릴 수 없습니다.")){
            f.w.value = "d";
        }else{
            return false;
        }
    }

    return true;
}
</script>

<?php


include_once(G5_ADMIN_PATH.'/admin.tail.php');