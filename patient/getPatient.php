<?php 
    include_once('../conn.php');

    if(isset($_POST['updateid'])){
        $id = $_POST['updateid'];
        $sql = "SELECT * FROM patients WHERE patient_id='$id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo json_encode($row);
      }
?>