<?php

function url($path)
{
    return APP_URL . $path;
}
function urlStyle($path)
{
    return APP_URL . $path;
}
//for dumping
function dump($data)
{
   echo '<pre>';
   print_r($data);
   
}
//for debugging
function dd($data)
{
   echo '<pre>';
   print_r($data);
   die();
}
//Redirect
function redirect($url, $statusCode = 303)
{
   header('Location: ' . $url, true, $statusCode);
}