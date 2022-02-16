<?php
    include_once('./_common.php');

    if(!sql_query(" DESCRIBE g5_auto_schedule_log ", false)) {
        sql_query("CREATE TABLE `g5_auto_schedule_log` (
            `idx` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'INDEX',
            `schedule_idx` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '스케쥴 idx',
            `schedule_log` text NOT NULL DEFAULT '' COMMENT '스케쥴 로그',
            `log_time` datetime NOT NULL DEFAULT current_timestamp() COMMENT '스케쥴 로그 작성 시간',
            PRIMARY KEY (`idx`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",true);
    }

    if(!sql_query(" DESCRIBE g5_auto_schedule ", false)) {
        sql_query("CREATE TABLE `g5_auto_schedule` (
            `idx` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'INDEX',
            `schedule_name` varchar(255) NOT NULL DEFAULT '' COMMENT '스케쥴명',
            `schedule_file` varchar(255) NOT NULL DEFAULT '' COMMENT '스케쥴 파일',
            `loop_type` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '0 : 분 1 : 시간 2 : 일 3 : 월',
            `loop_number` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '분,시,일,월에 입력 될 0 ~ 59 사이의 숫자 ',
            `status` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '0 : 비활성화 1 : 활성화',
            `is_first` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '1번 이상 실행 여부',
            `last_running_time` datetime NOT NULL DEFAULT current_timestamp() COMMENT '마지막 실행시간',
            `allow_robot` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '0 : 비허용 1 : 허용',
            `log_del_type` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '0 : 분 1 : 시간 2 : 일 3 : 월',
            `log_del_number` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '분,시,일,월에 입력 될 1 ~ 59 사이의 숫자 0 입력시 동작 안함',
            `exec_available_time_start` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '실행 가능 시간 start',
            `exec_available_time_end` smallint(5) unsigned NOT NULL DEFAULT 23 COMMENT '실행 가능 시간 end',
            `exec_day` varchar(100) NOT NULL DEFAULT '0|1|2|3|4|5|6' COMMENT '실행 가능 요일 설정 파이프라인 기준으로 구분',
            PRIMARY KEY (`idx`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",true);
    }