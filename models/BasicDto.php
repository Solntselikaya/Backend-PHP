<?php
include_once 'helpers/headers.php';

class BasicDto {
    public function getContents() {
        $r = array();
        foreach(get_object_vars($this) as $key => $value) {
            $r[$key] = $value;
        }

        return $r;
    }
    
    public function echoContents() {
        setHTTPStatus(200);
        $r = array();
        foreach(get_object_vars($this) as $key => $value) {
            $r[$key] = $value;
        }

        echo json_encode($r);
    } 
}
?>