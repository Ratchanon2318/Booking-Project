<?php
include_once('upload_image.php'); // รวมไฟล์ upload_image.php

function editRoomImage($room_id, $file) {
    return uploadRoomImage($room_id, $file);
}
?>
