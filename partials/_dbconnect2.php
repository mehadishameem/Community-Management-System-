<?php
        $servername = 'localhost';
        $username ='root';
        $password = '';
        $database = "community";
        $conn = mysqli_connect($servername,$username,$password,$database);

        if(!$conn){
            echo"Not conneted to database";
        }
       
?>