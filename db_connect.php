<?php
$servername = "localhost"; // sau adresa serverului tău de baze de date
$username = "root"; // numele de utilizator pentru baza de date
$password = "npm_am19"; // parola pentru baza de date
$dbname = "route_rover_db"; // numele bazei de date

// Crearea conexiunii
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//echo "Connected successfully";
?>