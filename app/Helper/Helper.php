<?php namespace App\Helper;

class Helper
{
   public function getController($path)
   {
      $controller = strtolower($path);
      $controller = ucfirst($controller);
      $controller = "App\Controller\\" . $controller . "Controller";
      return $controller;
   } 

   public static function generateToken($length = 16)
   {
      $symbols = 'abcdefghijklmnopqrstuvwxyz1234567890';
      //Token characters
      $token = '';
      for ($i = 0; $i < $length; $i++){
          $token .= $symbols[mt_rand(0,strlen($symbols)-1)];
      }
      return $token;
  } 
     
}