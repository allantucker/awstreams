<?php

class connection
{

    public function initilize_connection()
    {

        /* connect to MySQL database */
        $connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check connection
        if($connection === false){
            die("ERROR: Could not connect. " . $connection->connect_error);
        }

        return $connection;

    }
}