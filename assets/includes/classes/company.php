<?php

class Company {

    // <b>Exception:</b> SQLSTATE[HY093]: Invalid parameter number: number of bound variables does not match number of tokens

    public $company_name;
    public $company_address;
    public $company_letter;
    public $err = array();
    public $sucsess = array();

    public function add_company($post_global){
        global $database;

        $this->company_name = $post_global['name'];
        $this->company_address = $post_global['address'];

        if(empty(trim($this->company_name))){
            $this->err[] =  "Must Provide a valid name";
            return false;
        }
        if(empty(trim($this->company_address))){
            $this->err[] =  "Must Provide a valid address";
            return false;
        }
        // change name to a valid name
        $this->company_name = preg_replace('/(^([^a-zA-Z0-9])*|([^a-zA-Z0-9])*$)/', '', $this->company_name);
        // check if alredy exsists (without the iligal characters)
        try{
            $company_exsists = $database->con->prepare("SELECT * FROM inf_com WHERE name = ?");
            $company_exsists->bindParam(1,$this->company_name);
            if(!$company_exsists->execute()){
                $this->err[] =  "SQL err (checking ltrs)";
                return false;
            }
            $company_exsists = $company_exsists->fetchAll(PDO::FETCH_ASSOC);
            if($company_exsists){
                $this->err[] =  "Company Alredy exsists (The last character may not count)";
                return false;
            }
        }catch(PDOException $e){
            $this->err[] =  "SQL err (checking exsists)".$e;
            return false;
        }

        

        $this->company_letter =  strtolower(substr($this->company_name, 0, 1));
        try{
            $letters = $database->con->prepare("SELECT * from inf_letters WHERE letter = ? LIMIT 1 ");
            $the_company_letter = $this->company_letter;
            $letters->bindParam(1,$the_company_letter);
            if(!$letters->execute()){
                $this->err[] =  "SQL err (getting ltrs)";
                return false;
            }
            $letter = $letters->fetch(PDO::FETCH_OBJ);
            $letter_id = $letter->letter_id;
            $letter_sub = $letter->sub_com_ids;
            $company = $database->con->prepare("INSERT INTO inf_com (name , address, letter_id) VALUES (?,?,?) ");
            $the_company_name = $this->company_name;
            $the_company_address = $this->company_address;
            $company->bindParam(1,$the_company_name);
            $company->bindParam(2,$the_company_address);
            $company->bindParam(3,$letter_id);
            if(!$company->execute()){
                $this->err[] =  "SQL err (new comp)";
                return false;
            }
            $new_total_com_count = $letter->total_com ? $letter->total_com + 1 : 1;
            $com_id = $letter_sub . $database->con->lastInsertId().", ";
            $update_letters = $database->con->query("UPDATE inf_letters SET total_com = $new_total_com_count , sub_com_ids = '$com_id' WHERE letter_id = $letter_id");
            if($update_letters->execute()){
                $this->sucsess[]= "Company $this->company_name was added sucssessfuly";
                return true;
            }
        }catch(PDOException $e){
            $this->err[] =  "SQL err (getting ltrs)".$e;
            return false;
        }
        
    }
    public function get_company($company_name){
        global $database;
        $company = $database->con->prepare("SELECT * from inf_com WHERE name = ? LIMIT 1 ");
        $company->bindParam(1,$company_name);
        if(!$company->execute()){
            $this->err[] =  "SQL err (new comp)";
            return false;
        }
        $company = $company->fetch(PDO::FETCH_OBJ);
        return $company;
    }

}




?>