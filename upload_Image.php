<?php
function uploadRoomImage($room_id, $file) {
    if (isset($file['room_image']) && $file['room_image']['name']) {
        $new_image_name = $room_id . '.jpg';

        // ลบรูปภาพเก่าถ้ามี
        if (file_exists('room_images/' . $new_image_name)) {
            unlink('room_images/' . $new_image_name);
        }

        $target_file = 'room_images/' . $new_image_name;
        if (move_uploaded_file($file['room_image']['tmp_name'], $target_file)) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}
?>
