<?php

function logHit($method = null,$actual_link = null,$inputJson = null,$stsResponse = null,$jsonResponse = null)
{
    $id_log = getPk();
    

    $sqlLogHit = "
    insert into `easet`.`log_hit` values
    (
        '$id_log',
        now(),
        '$method',
        '$actual_link',
        '$inputJson',
        '$stsResponse',
        '$jsonResponse'
    );
    ";
    //echo $sqlLogHit; die();
    $resultLogHit = DB::query($sqlLogHit);
    if ($resultLogHit) {

    } else {
        jsonResp(400, "Log gagal disimpan","");
        return;
    }
}
?>