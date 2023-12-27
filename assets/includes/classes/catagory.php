<?php 


class Catagory {


    public $catagorys = array();
    public $catagory;
    public $err = array();
    public $sucsess = array();


    public function add_catagory($cat){
        global $database;


        $this->catagory = $cat['catagory'];

        if(empty(trim($this->catagory))){
            $this->err[]= "Must provide a catagory";
            return false;
        }
        $catagorys = $database->con->query('SELECT * FROM catagorys')->fetchAll(PDO::FETCH_OBJ);

        foreach ($catagorys as $catagory) {
            // $this->$catagorys[] =  $catagory;
            if(strtolower($catagory->name) == strtolower($this->catagory)){
                $this->err[] = "Catagory alredy exsists";
                return false;
            }
        }
        $catagory_name = $this->catagory;
        $new_cat = $database->con->prepare("INSERT INTO catagorys (name) VALUES (?)");
        $new_cat->bindParam(1,$catagory_name);
        try{
            if(!$new_cat->execute()){
                $this->err[] = "an Error ocured"; 
                return false;
            }else{
                $this->sucsess[] = "Catagory Added Sucssesfuly";
                return true;
            }
        }catch (Exception $e){
            $this->err[] = "an Error ocured " . $e->getMessage();
            return false; 
        }
    }


}