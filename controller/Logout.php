<?php

if(isset($_POST['logout'])){
    
    session_start();
    session_destroy();
    echo json_encode(array('status' => 'success', 'message' => 'Logout success'));
}