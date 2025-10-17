<?php
header("Content-Type: application/json");
$response = ["success" => true, "message" => "Test form processed successfully"];
echo json_encode($response);
?>