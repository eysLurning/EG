<?php

//  require_once '../conf.php';
class Global_server {
    public $name;
    public $title;
    public $metadata;
    public $side_nav;
    public $top_nav;

   
    function __construct(){
        // Initialize default values
        $this->initializeDefaults();     
        // add metadata here
        $this->addDefaultMetadata();
        // add default side tabes here
        $this->addDefaultSideTabs();
        // add default top tabes here
        $this->addDefaultTopTabs();
        // Get the user prefrences
        $this->getUserPrefrences();
    }

    private function initializeDefaults() {
        // Initialize default values for properties
        $this->name = APP_NAME;
        $this->title = APP_TITLE;
        $this->metadata = [];
        $this->side_nav = [];
        $this->top_nav = [];
    }
    private function addDefaultSideTabs() {
        $this->side_nav[] = (object) array('name'=>'home','display_name'=>'Home','order'=>0,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'bx-home','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'home','display_name'=>'Dashboard','order'=>1,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'bx-grid-alt','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'files','display_name'=>'Files','order'=>3,'auth'=>0,'active'=>true,'children'=>array(
            (object) array('name'=>'upload_files','display_name'=>'Upload Files','order'=>0,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'','bdge_class'=>'','bdge'=>''),
            (object) array('name'=>'temp_files','display_name'=>'Temp Files','order'=>0,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'','bdge_class'=>'','bdge'=>''),
            (object) array('name'=>'other','display_name'=>'Other','order'=>0,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'','bdge_class'=>'','bdge'=>'')
        ),'icon'=>'bx-folder','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'fs','display_name'=>'FS OLD','order'=>4,'auth'=>0,'active'=>true,'children'=>array(
        ),'icon'=>'bx-folder','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'todos','display_name'=>'Todo','order'=>5,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'bx-task','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'test','display_name'=>'Test','order'=>6,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'bx-data','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'ui','display_name'=>'UI','order'=>7,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'bx-box','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'deals','display_name'=>'Deals','order'=>8,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'bx-cart','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'lists','display_name'=>'Lists','order'=>9,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'bx-cart','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'profile.php#user_settings','display_name'=>'Setting','order'=>10,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'bx-cog','bdge_class'=>'','bdge'=>'');
        $this->side_nav[] = (object) array('name'=>'admin','display_name'=>'Admin','order'=>10,'auth'=>0,'active'=>true,'children'=>false,'icon'=>'bx-shield','bdge_class'=>'','bdge'=>'');
    }
    private function addDefaultTopTabs() {
        $this->top_nav[0]= ['name'=>'Home','order'=>0,'auth'=>0,'active'=>true];
        $this->top_nav[1]= ['name'=>'Home','order'=>0,'auth'=>0,'active'=>true];
    }
    private function addDefaultMetadata() {
        $this->metadata[0]= ['name'=>'Home','content'=>"0"];
    }



    public function getUserPrefrences(){
        global $database;        
        try{
            // $sql = "SELECT * FROM user";
            // $server = $database->con->query($sql);
            // $server = $server->fetch(PDO::FETCH_OBJ);
            // if($server){
            //     $this->name = $server->name ? $server->name:'';
            //     $this->title = $server->title ? $server->title :'';
            //     $this->side_nav = json_decode($server->side_nav);
            //     $this->top_nav = json_decode($server->top_nav);
            //     $this->metadata = json_decode($server->metadata);
            // }
        }catch(Exception $e){
            die("Faild to load company info: ". $e->getMessage());
        }
    }


}
$glob = new Global_server();



