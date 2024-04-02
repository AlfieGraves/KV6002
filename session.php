<?php
//Session starts
session_start();

//if user is already logged in redirect to "My Events" page
if(isset($_SESSION["userid"]) && $_SESSION["userid"] === true) {
    header("location: eventManament.php");
    exit;
}