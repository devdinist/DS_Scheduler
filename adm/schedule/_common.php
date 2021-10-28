<?php
define('G5_IS_ADMIN', true);
include_once ('../../common.php');

if( isset($token) ){
    $token = @htmlspecialchars(strip_tags($token), ENT_QUOTES);
}

include_once(G5_ADMIN_PATH.'/admin.lib.php');
