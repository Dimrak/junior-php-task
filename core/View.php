<?php namespace Core;
class View
{
   public $url;

   public function render($template = '')
   {
      $path = __DIR__;
      $path = str_replace('core', '/', $path);
      include $path.'views/parts/head.php';
      include $path . 'views/' . $template . '.php';
      include $path.'views/parts/footer.php';
    }
}