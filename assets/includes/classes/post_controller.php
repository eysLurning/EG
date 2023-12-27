<?php

// TODO: check user params
class Post_controller{

    public function __construct(Array $post = null) {
        global $_SESSION;
        //detection of the type of post and db
        foreach($post as $key=>$val){
            $feald = explode('_',$key);
            if($feald[0]=== 'new'){
                $this->post_type = 'new';
                $this->for = $feald[1];
                unset($post[$key]);
                break;
            }
            if($feald[0]=== 'edit'){
                $this->post_type = 'update';
                $this->for = $feald[1];
                unset($post[$key]);
                break;
            }
        }
        $this->post = $post;

        //add the user id for spesific transactions
        if(isset($this->needed_fealds[$this->for])){
            foreach($this->needed_fealds[$this->for] as $key=>$value){
                $this->post[$key] = '';
            }
        }



        // if no detector feald is present, throw an err
        if(empty($this->for)||empty($this->post_type)){
            $this->errors[17] = 'missing CSRF toke'; 
            throw new Exception($this->errors[17]);
        }
        
        /**
         * validate erturns true only if all fealds are valid
         */
        $this->validate();
        //TODO: check for errors and act uppon it

        /**
         * check_exsists returns true only if donst exsist
         */
        $this->check_exsists();

        if($this->post_type === 'new' && empty($this->errors)){
            $this->insert_post();
        }
        if(!empty($this->errors)){
            echo '<pre>';
            var_dump( $this->errors);
            echo '</pre>';
        }


    }
    /**
     * gets the hidden feald and sets the targeted entry to add/update/delete
     */
    private  $for = null;
    /**
     * is set to the type of opperation 
     * avalable types are 
     *      new
     *      update
     */
    private  $post_type = null;

    /**
     * Error codes
     *   1 -  99 basic errors
     * 100 - 199 validation err 
     * 200 - 299 sql err
     * 300 - 399 
     * 
     * errors are a key => val paid with the key being a code and the val is a err msg.
     */
    public array $errors = array();
    /**
     * 
     */
    public array $post = array();
    public array $valid_post = array();
    private array $needed_fealds = array(
        'customers'=>['letter_id'=>'','by'=>'','type'=>'']
        ,'vendors'=>['letter_id'=>'','by'=>'','type'=>'']
        ,'forworders'=>['letter_id'=>'','by'=>'','type'=>'']
        ,'items'=>''
        ,'company'=>''
        ,'purchases'=>''
        ,'sales'=>''
        ,'freight'=>''
        ,'brand'=>''
        ,'file'=>''
        ,'location'=>''
        ,'status'=>''
        ,'fx'=>''
        ,'delivary_address'=>''
        ,'catagory'=>''

    );



    /**
     * global controller to set error codes 
     * ---Must rest to 0 on end of oporation---
     */
    private  $errnum = 0;

    /**
     *  Avalable constarints
     * 
     *     MUST   = not empty
     *     ISNUM  = is numaric (id's ect)
     *     ISEMAIL  = is a valid email
     *     MIN3  = has at least 3 chars
     *     HASNUM  = has at least 1 number
     *     SKIP  = no validation rqwired used for CSRF and identifiers
     * 
     *      key =>value pair of all possible post names to be validated.
     *      if the key is not on this list it shuld be ignored
     */
    private array $fealds = array( 
        'name'=>'MUST'
        ,'address'=>'MUST' 
        ,'city'=>'MUST' 
        ,'zip'=>'MUST,HASNUM' 
        ,'vat'=>'HASNUM' 
        ,'email'=>'ISEMAIL' 
        ,'country'=>'MUST' 
        ,'tel'=>'HASNUM' 
        ,'url'=>'' 
        ,'contact'=>'' 
        ,'letter_id'=>'GETID,INT' 
        ,'currency'=>'ISNUM,MUST' 
        ,'company_id'=>'ISNUM,MUST,INT' 
        ,'type'=>'GETTYPE' 
        ,'color'=>'' 
        ,'brand'=>'ISNUM' 
        ,'shot_name'=>'MUST' 
        ,'by'=>'GETID,INT' 
        ,'ean'=>'' 
        ,'pn'=>'' 
        ,'description'=>'MUST' 
        ,'pi_num'=>'ISNUM' 
        ,'inv_num'=>'ISNUM' 
        ,'po_num'=>'ISNUM' 
        ,'date'=>'ISDATE' 
        ,'vendors_num'=>'ISNUM' 
        ,'customers_num'=>'ISNUM' 
        ,'qty'=>'ISNUM,MUST' 
        ,'item'=>'ISNUM,MUST' 
        ,'fx'=>'ISNUM,MUST' 
        ,'fx_rate'=>'ISNUM,MUST'
        ,'new_customers'=>'SKIP'
        ,'new_vendors'=>'SKIP'
        ,'new_forworders'=>'SKIP'
        ,'new_items'=>'SKIP'
        ,'new_company'=>'SKIP'
        ,'new_purchases'=>'SKIP'
        ,'new_sales'=>'SKIP'
        ,'new_freight'=>'SKIP'
        ,'new_brand'=>'SKIP'
        ,'new_users'=>'SKIP'
        ,'new_setting'=>'SKIP'
        ,'new_files'=>'SKIP'
        ,'new_locations'=>'SKIP'
        ,'new_status'=>'SKIP'
        ,'new_fx'=>'SKIP'
        ,'new_delivary_address'=>'SKIP'
        ,'new_catagorys'=>'SKIP'
        ,'edit_customers'=>'SKIP'
        ,'edit_vendors'=>'SKIP'
        ,'edit_forworders'=>'SKIP'
        ,'edit_items'=>'SKIP'
        ,'edit_company'=>'SKIP'
        ,'edit_purchases'=>'SKIP'
        ,'edit_sales'=>'SKIP'
        ,'edit_freight'=>'SKIP'
        ,'edit_brand'=>'SKIP'
        ,'edit_users'=>'SKIP'
        ,'edit_setting'=>'SKIP'
        ,'edit_files'=>'SKIP'
        ,'edit_locations'=>'SKIP'
        ,'edit_status'=>'SKIP'
        ,'edit_fx'=>'SKIP'
        ,'edit_delivary_address'=>'SKIP'
        ,'edit_catagorys'=>'SKIP'
    ); 

    private array $types = array(
        'cutomer'=> 'CONTACT'
        ,'vendor'=> 'CONTACT'
        ,'forworder'=> 'CONTACT'
        ,'item'=> 'ITEM'
    );
    private array $exsists_sql = array(
        'customers'=>["sql"=>"SELECT 1 FROM contacts WHERE name = ? AND company_id = ? LIMIT 1","params"=>[1=>'name',2=>'company_id']]
        ,'vendors'=>["sql"=>"SELECT 1 FROM contacts WHERE name = ? AND company_id = ? LIMIT 1","params"=>[1=>'name',2=>'company_id']]
        ,'forworders'=>["sql"=>"SELECT 1 FROM contacts WHERE name = ? AND company_id = ? LIMIT 1","params"=>[1=>'name',2=>'company_id']]
        ,'items'=>["sql"=>"SELECT 1 FROM items WHERE name = ? OR shot_name = ? LIMIT 1","params"=>[1=>'name',2=>'shot_name']]
        ,'company'=>["sql"=>"SELECT 1 FROM company WHERE name = ? LIMIT 1","params"=>[1=>'name']]
        ,'contacts'=>["sql"=>"SELECT 1 FROM contacts WHERE name = ? AND company_id = ? LIMIT 1","params"=>[1=>'name',2=>'company_id']]
        ,'purchases'=>["sql"=>"SELECT 1 FROM purchases WHERE po_num = ? AND company_id = ? LIMIT 1","params"=>[1=>'po_num',2=>'company_id']]
        ,'sales'=>["sql"=>"SELECT 1 FROM company WHERE name = ? AND company_id = ? LIMIT 1","params"=>[1=>'name',2=>'company_id']]
        ,'freight'=>["sql"=>"SELECT 1 FROM freight WHERE vendors_num = ?  AND company_id = ? LIMIT 1","params"=>[1=>'vendors_num',2=>'company_id']]
        ,'brand'=>["sql"=>"SELECT 1 FROM brand WHERE name = ? LIMIT 1","params"=>[1=>'name']]
        ,'users'=>["sql"=>"SELECT 1 FROM users WHERE email = ? LIMIT 1","params"=>[1=>'email']]
        ,'settings'=>["sql"=>"SELECT 1 FROM settings WHERE user_id = ? LIMIT 1","params"=>[1=>'user_id']]
        // ,'files'=>["sql"=>'SKIP',"params"=>[]]
        ,'locations'=>["sql"=>"SELECT 1 FROM locations WHERE name = ? LIMIT 1","params"=>[1=>'name']]
        ,'status'=>["sql"=>"SELECT 1 FROM status WHERE name = ? LIMIT 1","params"=>[1=>'name']]
        ,'fx'=>["sql"=>"SELECT 1 FROM fx WHERE currency = ? LIMIT 1","params"=>[1=>'currency']]
        ,'delivary_address'=>["sql"=>"SELECT 1 FROM delivary_address WHERE name = ? and customer_id = ? LIMIT 1","params"=>[1=>'name',2=>'customer_id']]
        ,'catagorys'=>["sql"=>"SELECT 1 FROM catagorys WHERE name = ? LIMIT 1","params"=>[1=>'name']]
    );
    private array $insert_sql = array(
        'customers'=>[
            0=>[
                "db"=>"contacts"
                ,"fealds"=>"name,address,city,country,zip,vat,email,tel,contact,letter_id,company_id,currency,url,type,`by`"
                ,"values"=>[ 1=>"name",2=>"address",3=>"city",4=>"country",5=>"zip",6=>"vat",7=>"email",8=>"tel",9=>"contact",10=>"letter_id",11=>"company_id",12=>"currency",13=>"url",14=>"type",15=>"by"]
                ]
            ,1=>[
                "db"=>"customers"
                ,"fealds"=>"contact"
                ,"values"=>["last_id"=>"last_id"]
            ]
        ]
        ,'vendors'=>[
            0=>[
                "db"=>"contacts"
                ,"fealds"=>"name,address,city,country,zip,vat,email,tel,contact,letter_id,company_id,currency,url,type,`by`"
                ,"values"=>[ 1=>"name",2=>"address",3=>"city",4=>"country",5=>"zip",6=>"vat",7=>"email",8=>"tel",9=>"contact",10=>"letter_id",11=>"company_id",12=>"currency",13=>"url",14=>"type",15=>"by"]
                ]
            ,1=>[
                "db"=>"vendors"
                ,"fealds"=>"contact"
                ,"values"=>["last_id"=>"last_id"]
            ]
        ]
        ,'forworders'=>[
            0=>[
                "db"=>"contacts"
                ,"fealds"=>"name,address,city,country,zip,vat,email,tel,contact,letter_id,company_id,currency,url,type,`by`"
                ,"values"=>[ 1=>"name",2=>"address",3=>"city",4=>"country",5=>"zip",6=>"vat",7=>"email",8=>"tel",9=>"contact",10=>"letter_id",11=>"company_id",12=>"currency",13=>"url",14=>"type",15=>"by"]
                ]
            ,1=>[
                "db"=>"forworders"
                ,"fealds"=>"contact"
                ,"values"=>["last_id"=>"last_id"]
            ]
        ]
        // ,'items'=>"INSERT INTO items (name, surname, sex) VALUES (?,?,?)"
        // ,'company'=>"INSERT INTO company (name, surname, sex) VALUES (?,?,?)"
        ,'contacts'=>[
            0=>[
            "db"=>"customers"
            ,"fealds"=>"name,address,city,country,zip,vat,email,tel,contact,letter_id,company_id,currency,url,type,`by`"
                ,"values"=>[ 1=>"name",2=>"address",3=>"city",4=>"country",5=>"zip",6=>"vat",7=>"email",8=>"tel",9=>"contact",10=>"letter_id",11=>"company_id",12=>"currency",13=>"url",14=>"type",15=>"by"]]]
        // ,'purchases'=>"INSERT INTO purchases (name, surname, sex) VALUES (?,?,?)"
        // ,'sales'=>"INSERT INTO company (name, surname, sex) VALUES (?,?,?)"
        // ,'freight'=>"INSERT INTO freight (name, surname, sex) VALUES (?,?,?)"
        // ,'brand'=>"INSERT INTO brand (name, surname, sex) VALUES (?,?,?)"
        // ,'users'=>"INSERT INTO users (name, surname, sex) VALUES (?,?,?)"
        // ,'settings'=>"INSERT INTO settings (name, surname, sex) VALUES (?,?,?)"
        // ,'files'=>"INSERT INTO files (name, surname, sex) VALUES (?,?,?)"
        // ,'locations'=>"INSERT INTO locations (name, surname, sex) VALUES (?,?,?)"
        // ,'status'=>"INSERT INTO status (name, surname, sex) VALUES (?,?,?)"
        // ,'fx'=>"INSERT INTO fx W(name, surname, sex) VALUES (?,?,?)"
        // ,'delivary_address'=>"INSERT INTO delivary_address (name, surname, sex) VALUES (?,?,?)"
        // ,'catagorys'=>"INSERT INTO catagorys (name, surname, sex) VALUES (?,?,?)"
    );


    private function check_exsists(){
        global $database;
        $sql = $this->exsists_sql[$this->for];
        $exsists = $database->con->prepare($sql['sql']);
        foreach($sql['params'] as $index => $param){
            $exsists->bindParam($index,$this->valid_post[$param]);
        }
        $exsists->execute();
        if($exsists->fetchColumn()){
            $this->errors[18] = "$this->for Entry alrey exsists";
            return false;
        }
        return true;
    }

    private function insert_post(){
        global $database;
        if(!isset($this->insert_sql[$this->for])){
            $this->errors[99] = "Cannot add";
            return false;
        }
        $sql = $this->insert_sql[$this->for];
        $last_id = null;
        foreach($sql as $count => $entry){
            $db = $entry['db'];
            $fealds = $entry['fealds'];
            $values = substr(str_repeat('?,',count($entry['values'])),0,-1);
            $statement = "INSERT INTO $db ($fealds) VALUES ($values)";
            $insert = $database->con->prepare($statement);
            if(!isset($entry["values"]['last_id'])){
                foreach($entry['values'] as $index =>$param){
                    $inset_param =&$this->valid_post[$param];
                    $insert->bindParam($index,$inset_param);                    
                }                
            }
            if(isset($entry["values"]['last_id']) && isset($last_id)){
                $insert->bindParam(1,$last_id);
            }
            try{
                $insert->execute();
                $last_id = $database->con->lastInsertId();
            }catch(PDOException $e){
                $this->errors[301] = "SQL Error";
                $this->errors[302] = $e;
                return false;
            }
        }
        //todo: CHECK IF THE POST IS A MULTY STEP AND DO THEOTHER STEPS 
        // Exsample, create customer, needs first creating the contact, and thn the customer link

    }

    

    /**
     * @return bool
     */
    public function validate(){
        $this->errnum = 100;
        $tripwire = 0;
        foreach($this->post as $key=>$value){
            $valid = $this->validators($key,$value,isset($this->fealds[$key])? $this->fealds[$key] : 'false');
            if($valid === true){
                $this->valid_post[$key] = empty($this->post[$key])? null : $this->post[$key];
            }else{
                $tripwire = 1;
            }
        }
        $this->errnum = 0;
        // // if(!empty($this->errors)){
        //     echo 'tripwires is '. $tripwire .'<br>';
        //     echo 'b4 validating Post is:<br>';
        //     echo '<pre>';
        //     var_dump($this->post);
        //     echo '</pre>';
        //     echo 'Valid Post is:<br>';
        //     echo '<pre>';
        //     var_dump( $this->valid_post);
        //     echo '</pre>';
        //     echo 'Validation errors are:<br>';
        //     echo '<pre>';
        //     var_dump( $this->errors);
        //     echo '</pre>';
        // // }
        if($tripwire === 1){
            return false;
        }

        return true;
    }


    private function validators($key,$val,$validators){
        global $database;
        $tripwire = 0;
        $validators = explode(',',$validators);
        foreach($validators as $validator){
            if($validator === 'false'){
                $this->errors[$this->errnum] = "Feald $key dosnt exsist, nice try";
                $this->errnum ++;
                return false;
            }
            if($validator === 'MUST' && empty($val)){
                $this->errors[$this->errnum] = "Feald $key cannot be empty";
                $this->errnum ++;
                $tripwire = 1;
            }
            if($validator === 'ISNUM' && !empty($val) && !is_numeric($val)){
                $this->errors[$this->errnum] = "Feald $key must be a number";
                $this->errnum ++;
                $tripwire = 1;
            }
            if($validator === 'ISEMAIL' && !empty($val) && !filter_var($val,FILTER_VALIDATE_EMAIL)){
                $this->errors[$this->errnum] = "Feald $key must be a valid email";
                $this->errnum ++;
                $tripwire = 1;
            }
            if($validator === 'MIN3' && !empty($val) && strlen($val) < 3){
                $this->errors[$this->errnum] = "Feald $key must be at least 3";
                $this->errnum ++;
                $tripwire = 1;
            }
            if($validator === 'HASNUM' && !empty($val) && !preg_match('~[0-9]+~', $val)){
                $this->errors[$this->errnum] = "Feald $key must contain numbers";
                $this->errnum ++;
                $tripwire = 1;
            }
            if($validator === 'SKIP'){
                return false;
            }
            if($validator === 'GETID'){
                switch ($key) {
                    case 'letter_id':
                        if(!isset($this->post['name']) || !isset($this->post['company_id'])){
                            $this->errors[$this->errnum ++] = 'Err culd not find letter id';
                            return false;
                        }
                        $name = strtolower(substr($this->post['name'], 0, 1));
                        $company_id = $this->post['company_id'];
                        $id = $database->con->prepare("SELECT * from letters WHERE letter = ? AND company_id = ? ");
                        $id->bindParam(1,$name);
                        $id->bindParam(2,$company_id);
                        try{
                            $id->execute();
                            $id = $id->fetch(PDO::FETCH_OBJ);
                            if($id){
                                $this->post['letter_id'] = "$id->id";
                            }
                            else{
                                $this->errors[$this->errnum] = "No such letter company combenation";
                                return false;
                            }
                        }catch(Exception $e){
                            $this->errors[301] = "SQL Error";
                            $this->errors[302] = $e;
                            return false;
                        }
                        break;
                    case 'by':
                        $this->post['by'] = '1';
                        break;
                    default:
                        # code...
                        break;
                }
            }
            
            if($validator === 'GETTYPE'){
                $this->post['type'] = $this->for;
            }
        }
        if($tripwire !== 0){
            return false;
        }
        return true;
    }
}
