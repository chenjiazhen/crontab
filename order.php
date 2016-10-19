<?php

    $localhost      = '127.0.0.1';
    $my_user        = 'root';
    $my_password    = 'Di#qweasd';
    $world          = 'test';
    
    $mysqli = new mysqli($localhost, $my_user, $my_password, $world);

    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    
    $mysqli->set_charset('utf8');

    $res = $mysqli->query('SELECT * FROM `user`');
    print_r($res->fetch_all());exit;
    //$mysqli->query("INSERT INTO `user` (`username`, `phone`, `password`) VALUES ('谢艳婷', '13977128729', '123456')");
    //echo $mysqli->insert_id;
    $mysqli->close();
?>