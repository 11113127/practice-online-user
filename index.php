<?php
    $db_localhost = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "online_user";

    $conn = mysql_connect ($db_localhost, $db_username, $db_password) or die("Could not connect: " . mysql_error());
    mysql_select_db($db_name) or die("未選擇資料庫");
    mysql_query("SET NAMES UTF8");

    $timeoutseconds = 300; //存活時間，以秒為單位(5分鐘)
    $online_time = time(); //現在時間
    $timeout = $online_time-$timeoutseconds; //清除紀錄的時間差標準

    $check_select = "SELECT ip FROM online_user WHERE ip = '$_SERVER[REMOTE_ADDR]'";
    $check_nums = mysql_num_rows(mysql_query($check_select));

    if($check_nums < 1) { //驗證回傳是否為空
        $insert = "INSERT INTO online_user(online_time, ip) VALUES('$online_time','$_SERVER[REMOTE_ADDR]')";
        if(!mysql_query($insert)) {
            echo "ERROR：語法執行失敗，請檢查是否與資料庫連結或語法是否錯誤";
        }
    } else {
        //不為空則更新在線時間
        $update = "UPDATE online_user SET online_time = '$online_time' WHERE ip = '$_SERVER[REMOTE_ADDR]'";
        if(!mysql_query($update)) {
            echo "ERROR：語法執行失敗，請檢查是否與資料庫連結或語法是否錯誤";
        }
    }

    $delete = "DELETE FROM online_user WHERE online_time < $timeout"; //清除小於$timeout的值
    if(!mysql_query($delete)) {
        echo "ERROR：語法執行失敗，請檢查是否與資料庫連結或語法是否錯誤";
    }

    $select = "SELECT count(ip) as user_nums FROM online_user"; //搜尋所有現存ip，統計人數
    $result = mysql_query($select);
    if(!($result)) {
        echo "ERROR：語法執行失敗，請檢查是否與資料庫連結或語法是否錯誤";
    } else {
        $user_nums = mysql_fetch_array($result)["user_nums"];
    }

    mysql_close();

    echo("目前上線人數：$user_nums 人");
?>