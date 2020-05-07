<?php namespace App\Controller;

use Core\Controller;
use App\Helper\Helper;
use App\Model\StatModel as Stat;

class CatController extends Controller
{
    private $cookie_name = 'sender-cats';
    
    public function form_validation()
    {
        unset($_SESSION['validation']);
        if (empty($_GET['N'])) {
            $_SESSION['validation'] = 'Enter a number between 1 and 1000000';
            return false;
        }elseif(!is_numeric($_GET['N'])) {
            $_SESSION['validation'] = 'Only numbers accepted';
            return false;
        }
        if (!isset($_GET)) {
            return false;
        }
        if (isset($_GET) && $_GET['N'] > 1000000) {
            $_SESSION['validation'] = 'Enter a number between 1 and 1000000';
            return false;
        }
        if (isset($_GET) && $_GET['N'] <= 0) {
            $_SESSION['validation'] = 'Enter a number between 1 and 1000000';
            return false;
        }
        return true;
    }
    
    public function index()
    {
        if(!isset($_COOKIE[$this->cookie_name])) {
            $this->setCookie();
        } 
        
        $validator = $this->form_validation(($_GET));
        if ($validator == false) {
            $final_count_results = $this->show_count_n_result();
            $this->view->count_n_final = $final_count_results;
            return $this->view->render('cats/index');
        }else {
            $number = $_GET['N'];
        }
        #Saving the numbers
        $initial_request = time();
        if (!isset($_SESSION['_number'])) {
            #Cats send to the view
            $choosen_cats = $this->take_cats();
            $display_cats = $this->cat_text($choosen_cats);
            $_SESSION['_number'] = [$number => [
                'time' => $initial_request,
                'cats' => $display_cats,
                'visitor' => $_COOKIE['sender-cats'],
            ]];
            $this->countN($number, $display_cats);
            $_SESSION['cats'] = $display_cats;
            $this->view->cats = $display_cats;
            $visits = $this->countAll();
            $final_count_results = $this->show_count_n_result();
            $this->write_json($number, $display_cats, $visits, $final_count_results, $initial_request);
            $this->checking_cache_status($number, $initial_request);
        }else {
            $this->checking_cache_status($number, $initial_request);
        }
        #Show the count_n results
        $final_count_results = $this->show_count_n_result();
        $this->view->count_n_final = $final_count_results;
        $visits = $this->countAll();
        $this->view->visits = $visits;
        $this->view->render('cats/index');
    }
        
    public function checking_cache_status($number, $initial_request)
    {
        if ($this->clearCache($number) !== 'removed') {
            if (array_key_exists($number, $_SESSION['_number'])) {
                $display_cats = $this->clearCache($number);
                $_SESSION['cats'] = $display_cats;
                $this->view->cats = $display_cats;  
                $visits = $this->countAll();
                $final_count_results = $this->show_count_n_result();
            }else {
                $display_cats = $this->display_cats($number, $initial_request);
                $_SESSION['cats'] = $display_cats;
                $this->view->cats = $display_cats;
                $visits = $this->countAll();
                $final_count_results = $this->show_count_n_result();
                $this->write_json($number, $display_cats, $visits, $final_count_results, $initial_request);
            } 
        }else{
            $display_cats = $this->display_cats($number, $initial_request);
            $this->view->cats = $display_cats;
            $_SESSION['cats'] = $display_cats;
            $visits = $this->countAll();
                
            $final_count_results = $this->show_count_n_result();
            $this->write_json($number, $display_cats, $visits, $final_count_results, $initial_request);
        }
    }  
        
    public function clearCache($number)
    {
        #Clearing the cache ended and showing an active cat_list
        $display_cats = [];
        $current = time();
        $disable_stat = new Stat();
        $previously_saved = $disable_stat->search($number);
        if (isset($_SESSION['_number'][$number])) {
            foreach ($_SESSION['_number'][$number] as $key => $value) {
                if ($key == 'time') {
                    if ($current - $value >= 60) {
                        unset($_SESSION['_number'][$number]);
                        if (!empty($previously_saved)) 
                        {
                            $disable_stat->setActive(0);
                            $disable_stat->update_status($previously_saved->id);
                        }
                        return 'removed';
                    }else{
                        return $_SESSION['_number'][$number]['cats'];
                    }
                }elseif ($key == 'visitor') {
                    $disable_stat = new Stat();
                    $previously_saved = $disable_stat->search($number);
                    if ($value !== $_COOKIE['sender-cats']) {
                        $disable_stat->setActive(0);
                        $disable_stat->update_status($previously_saved->id);
                        return 'removed';
                    }
                        
                }
                elseif($key == 'remove') {
                    return 'removed';
                }
            }
        }    
        return $display_cats;
    } 
        
    public function show_count_n_result()
    {
        $results = new Stat();
        $table_count_n = $results->getResults();
        $final_count_results = [];
        foreach ($table_count_n as $key => $value) {
            $x = 1;
            if (isset($final_count_results[$value->number])) {
                $final_count_results[$value->number] = $final_count_results[$value->number] + $x;
            }else{
                $final_count_results[$value->number] = 1;
            }
        }
        return $final_count_results;
    }
        
    public function countN($number, $cats)
    {
        #Check if already in db
        $new_stat = new Stat();
        $previously_saved = $new_stat->search($number);
        if (empty($previously_saved)) {
            $new_stat = new Stat();
            $new_stat->setNumber($number);
            if (empty($_COOKIE)) {
                $this->setCookie();
            }
            $new_stat->setVisitor($_COOKIE[$this->cookie_name]);
            $new_stat->setCats($cats);
            $new_stat->setActive(1);
            $new_stat->save();
        }elseif($previously_saved->number == $number) {
            $new_stat = new Stat();
            $new_stat->setNumber($number);
            $new_stat->setVisitor($_COOKIE[$this->cookie_name]);
            $new_stat->setCats($cats);
            $new_stat->setActive(1);
            $new_stat->save();
        }  
    }
        
    public function countAll()
    {
        $stat_count = new Stat();
        $visits = count($stat_count->getResults());
        return $visits;
    }
        
    public function write_json($number, $cats, $visits, $final_count_results, $initial_request)
    {
        $date = date('Y-M-d H:i:s ', $initial_request);
        $myfile = fopen("log.json", "a+") or die("Unable to open file!");
        $json = 
        '{
            "datetime": "'. $date .'",
            "N": '. $number .',
            "Cats": '.json_encode($cats).',
            "countAll": '. $visits .',
            "countN": '. $final_count_results[$number] .'
        }';
        fwrite($myfile, $json);  
        fclose($myfile);     
    }
        
    public function display_cats($number, $initial_request)
    {
        $choosen_cats = $this->take_cats();
        $display_cats = $this->cat_text($choosen_cats);
        $_SESSION['_number'][$number] = [
            'time' => $initial_request,
            'cats' => $display_cats,
            'visitor' => $_COOKIE['sender-cats'],
        ];
        $this->countN($number, $display_cats);
        return $display_cats;
    }
        
    public function inserting_values_to_session()
    {
        $final_count_results = $this->show_count_n_result();
        foreach ($final_count_results as $key => $value) {
            $_SESSION['_number'][$key] = ['remove' => 'remove'];
            $this->removing_values_after_clearing_session();
        }
    }
        
    public function removing_values_after_clearing_session()
    {
        $active = 0;
        $disabling = new Stat();
        $disabling->update_status_all($active);
    }
        
    public function setCookie()
    {
        if(!isset($_SESSION)) {
            session_start();
        }
        $this->inserting_values_to_session();
        $cookie_value = Helper::generateToken();
        setcookie($this->cookie_name, $cookie_value, time() + (1800), "/"); // 1800 = 30 minutes
    }  
        
    public function file_display()
    {
        $data = file_get_contents('log.json'); 
        $this->view->log_data = $data;
        $this->view->render('cats/file');
        }
    //Returns 3 cats from cats.txt
    public function take_cats()
    {
        $cats_file = file('cats.txt');
        $random_cats = rand(1,count($cats_file));//12

        if ($random_cats + 3 > count($cats_file)) {
            $extra = ($random_cats + 3) - count($cats_file);
            $random_cats = $random_cats - $extra;
        }
        $take_three_cats = array_slice($cats_file, $random_cats, 3);
        return $take_three_cats;
    }
        
    public function cat_text($cats)
    {
        $cats_string = '';
        foreach ($cats as $key => $cat) {
            $cats_string .= 'Cat' . ($key + 1) . ' ' . $cat;
            if ($key <= 1) {
                $cats_string .= ',';
            }
        }
        return $cats_string;
    }  
}
    
    