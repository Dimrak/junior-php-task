<?php namespace App\Model;

use Core\Database;
class StatModel
{
    private $id;
    private $cats;
    private $number;
    private $active;
    private $visitor;
    
    protected $db;
    
    public function __construct()
    {
        $this->db = new Database();
        return $this->db;
    }
      
    public function search($number)
    {
        $this->db->select()->from('count_n')->where('number', $number);
        return $this->db->getOne();
    }      
    public function save()
    {
        $this->create();
    }

    public function getResult($id)
    {
       $this->db->select()->from('count_n')->where('id', $id);
       return $this->db->getOne();
    }

    public function getResults()
    {
        $this->db->select()->from('count_n');
        return $this->db->getAll();
    }

    public function create()
    {
        $columns = 'number,  visitor, cats, active';
        $values = "'$this->number', '$this->visitor', '$this->cats', $this->active ";
        $this->db->insert('count_n', $columns, $values);
        $this->db->getOne();
    }

    public function update_status($id)
    {
        $setContent = "active = $this->active";
        $this->db->update('count_n', $setContent)->where('id',$id);
        $this->db->getOne();
    }

    public function update_status_all($active)
    {
        $this->db->update('count_n', 'active = '. $active .'');
        $this->db->getAll();
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNumber()
    {
        return $this->number;
    }
    
    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getVisitor()
    {
        return $this->visitor;
    }
    
    public function setVisitor($visitor)
    {
        $this->visitor = $visitor;
    }

    public function getCats()
    {
        return $this->cats;
    }
    
    public function setCats($cats)
    {
        $this->cats = $cats;
    }

    public function getActive()
    {
        return $this->active;
    }
    
    public function setActive($active)
    {
        $this->active = $active;
    }
    
}