<?php

    $localhost      = '192.168.10.88';
    $my_user        = 'root';
    $my_password    = '123456';
    $world          = 'shopmall';
    
    $mysqli = new mysqli($localhost, $my_user, $my_password, $world);

    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    $mysqli->set_charset('utf8');

    /*
     * @关闭支付超时订单
     * @return {none}
     * @example crontab: * * * * * /usr/local/bin/php /home/www/crontab/order.php
     */
    $time = time();
    $query = $mysqli->query("SELECT * FROM `lnsm_order` WHERE `add_time` + 3600 <= $time AND (`status` = 10 OR `status` = 31)");
    $orders = $query->fetch_all(1);

    if ($orders) {
        foreach ($orders as $order) {
            //关闭订单
            $mysqli->query("UPDATE `lnsm_order` SET `status` = 20 WHERE `id` = '" . $order['id'] . "'");

            //写入日志
            $mysqli->query("INSERT INTO `lnsm_order_oplog` (`order_id`, `order_op_status`, `op_id`, `op_type`, `op_name`, `remark`, `add_time`) VALUES (" . $order['id'] . ", 12, 0, 0, '系统', '陈近南的系统自动关闭超时订单', $time)");

            //返回库存
            /*
            $query = $mysqli->query("SELECT * FROM `lnsm_order_goods` WHERE `order_id` = " . $order['id']);
            $order_goods = $query->fetch_all();
            if ($order_goods) {
                foreach ($order_goods as $_order_goods) {
                    $mysqli->query("UPDATE `lnsm_goods` SET `stock_total` = `stock_total` + $_order_goods['goods_quantity'] WHERE `id` = '" . $_order_goods['goods_id']. "'");
                    echo 3;
                }
            }
            */

            //写入本地操作日志
            $myfile = fopen(dirname(__FILE__) . "/orders.log", "a") or die("Unable to open file!");
            //@example [2016-10-27 12:10] ■ ■ ■ ■ ■ POLLING_PROGRAM_CLOSE_ORDER >> [20161027000004];
            $txt = '[' . date('Y-m-d H:i', $time) . '] [POLLING_PROGRAM_CLOSE_ORDER] [' . $order['id'] . ']; ';
            fwrite($myfile, $txt);

        }
    }

    $mysqli->close();
    if ($myfile)
    fclose($myfile);
?>