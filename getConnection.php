<?php
//This function returns a conncetion to the database using PDO
//The function returns the connectin object (or exits on error)

function getConnection () {
    //This assumes the database is in a folder called "db"
    $dbName = "db/eventManager.db";

    try {
        //Use PDO to create a connection to the database
        $connection = new PDO('sqlite:' .$dbName);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    } catch (PDOException $e) {
        //If there is an error, return a message in JSON format
        $error['error'] = "Database Connection Error";
        $error['message'] = $e->getMessage();
        echo json_encode($error);
        exit();
    }

}
