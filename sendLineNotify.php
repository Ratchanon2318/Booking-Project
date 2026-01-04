<?php

function sendLineNotify($message, $token) {
    // URL ของ API LINE Notify
    $url = 'https://notify-api.line.me/api/notify';
    
    // ข้อมูลที่จะส่งไปยัง LINE Notify
    $data = array('message' => $message);
    
    // ตั้งค่า header สำหรับการส่งข้อมูล
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $token
    );

    // ใช้ cURL เพื่อส่งข้อมูลไปยัง LINE Notify
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // สำหรับเซิร์ฟเวอร์ที่ใช้ SSL ที่ไม่ถูกต้อง
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

?>
