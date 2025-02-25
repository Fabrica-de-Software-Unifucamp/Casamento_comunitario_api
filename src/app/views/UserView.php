<?php

class UserView {

    public function showSuccess($message) {
        echo json_encode(["success" => $message]);
    }

    public function showError($message) {
        echo json_encode(["error" => $message]);
    }

    public function showValidationErrors($errors) {
        echo json_encode(["errors" => $errors]);
    }
}
?>
