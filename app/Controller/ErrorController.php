<?php namespace App\Controller;
use Core\Controller;

class ErrorController extends Controller
{
   public function errorPage()
   {
      $this->view->render('errors/errorPage');
   }
   public function errorMethod()
   {
      $this->view->render('errors/errorMethod');
   }

}