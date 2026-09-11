<?php

$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "unibook"
);

if ($conexion->connect_error) {
    die("Error de conexión");
}