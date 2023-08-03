<?php

status_timeout();

function status_timeout()
{
    $data = file_get_contents('php://input');
    #log the response
    $logFile = "result.json";
    $log = fopen($logFile, "a");
    fwrite($log, $data);
    fclose($log);
}
