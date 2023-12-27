<?php 
require_once 'company.php';

class File {

    //temp vs normal
    public bool $isTempFile;


    //file props
    public string $file_name;
    public int $file_size;
    public string $file_type;
    public string $file_catagory;
    public string $file_catagory_id;
    public string $main_company;
    public string $company_name;
    public string $file_date;
    public string $file_deal;
    public string $file_tags;

    //temp props
    public string $temp_file_name;
    public string $temp_upload_file_path;
    public string $temp_id;
    public string $time_uploaded;
    public string $file_src;
    public string $tempDir = FILES.'tempFiles'.DS;


    //errs
    public array $errors = array();

    public array $upload_errors_array = array(
        UPLOAD_ERR_OK           => "There is no error",
        UPLOAD_ERR_INI_SIZE		=> "The uploaded file exceeds the upload_max_filesize directive in php.ini",
        UPLOAD_ERR_FORM_SIZE    => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
        UPLOAD_ERR_PARTIAL      => "The uploaded file was only partially uploaded.",
        UPLOAD_ERR_NO_FILE      => "No file was uploaded.",               
        UPLOAD_ERR_NO_TMP_DIR   => "Missing a temporary folder.",
        UPLOAD_ERR_CANT_WRITE   => "Failed to write file to disk.",
        UPLOAD_ERR_EXTENSION    => "A PHP extension stopped the file upload."					                                          
    );
    
        public function get_temp_file($id) {
            global $database;
            try{
                $file = $database->con->prepare("SELECT * FROM temp_files WHERE temp_id = ? LIMIT 1 ");
                $file->bindParam(1,$id);
                if(!$file->execute()){
                    $this->errors[] = "SQL err, Culd not retrive temp file";
                    return false;
                }
                $file = $file->fetch(PDO::FETCH_OBJ);
                $this->file_name = $file->name;
                $this->file_type = $file->type;
                $this->file_size = $file->size;
                $this->temp_id = $file->temp_id;
                $this->time_uploaded= $file->date_uploaded;
                $this->file_src = $file->temp_id.$file->name;
            }catch (Exception $e){
                $this->errors[] = '<br>Error is:' .$e ;
                echo json_encode($this->errors);
                return;
            }
            
        }
        public function set_temp_file($file, $id) {
    
        global $database;
        if(empty($file)|| !$file || !is_array($file) || empty($id) || !$id){
            $this->errors[] = "There was no file uploaded here";
            return false;
        }elseif($file['error'] !=0) {
            $this->errors[] = $this->upload_errors_array[$file['error']];
            return false;
        }else{
            $this->file_name =  basename($file['name']);
		    $this->temp_upload_file_path = $file['tmp_name'];
		    $this->file_type = $file['type'];
		    $this->file_size = $file['size'];
            $this->temp_id = $id;
            $this->file_src = $this->temp_id.$this->file_name;
        
            if(!$this->move_file_to_temp()){
                return false;
            }
            $file = $database->con->prepare("INSERT INTO temp_files (name , type , size , temp_id ) VALUES (?,?,?,?) ");
            $file->bindParam(1,$this->file_name);
            $file->bindParam(2,$this->file_type);
            $file->bindParam(3,$this->file_size);
            $file->bindParam(4,$this->temp_id);
            if(!$file->execute()){
                $this->errors[] = "SQL err, Culd not create temp file";
                return false;
            }
            $this->create_png_copy();
        }
    }


    public function move_file_to_temp(){
        if(move_uploaded_file($this->temp_upload_file_path, $this->tempDir.$this->temp_id.$this->file_name) ){
            return true;
        }else{
            $this->errors[] = "Culd not move file to tepm directory";
            return false;
        }
    }

    public function create_png_copy(){
        $imagick = new Imagick();
        $imagick->setResolution(300, 300);
        $imagick->readImage($this->tempDir.$this->temp_id.$this->file_name.'[0]');
        $imagick->setImageCompressionQuality(20);
        $imagick->setCompression(Imagick::COMPRESSION_JPEG);
        $imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);//remove transparant background 
        $imagick->setImageFormat("png");
        $imagick->writeImages($this->tempDir.$this->temp_id.$this->file_name.'.png',false);
    }



    public function upload_file($file){

        global $database;

        $this->temp_id = $file["id"];

        $tempfile = $database->con->prepare("SELECT * FROM temp_files WHERE temp_id = ? ");
        $tempfile->bindParam(1,$this->temp_id);
        $tempfile->execute();
        $tempfile = $tempfile->fetch(PDO::FETCH_OBJ);

        $this->file_size = $tempfile->size;
        $this->file_type = $tempfile->type;
        $this->file_name = $tempfile->name;
        $this->file_catagory = $file['catagory'];
        $this->main_company = $file['main_company'];
        $this->company_name = $file['company_name'];
        $this->file_date = $file['file_date'];
        $this->file_deal = $file['file_deal'];
        $this->file_tags = $file['file_tags'];

        if(!$this->validateCatagory($this->file_catagory) || !$this->validateCompany($this->company_name) || !$this->validateMainCompany($this->main_company)){
            echo json_encode($this->errors);
            return;
        }
        $this->file_catagory_id = $this->getCatagoryId($this->file_catagory);

        $company = $database->con->prepare("SELECT inf_com.*,  inf_letters.letter FROM inf_com INNER JOIN inf_letters ON inf_com.letter_id = inf_letters.letter_id WHERE inf_com.name = ?");
        $company->bindParam(1,$this->company_name);
        $company->execute();
        $company = $company->fetch(PDO::FETCH_OBJ);
        if(!$company){
            $this->errors[] = "Faild to find the provided company";
            echo json_encode($this->errors);
            return;
        }
        $base_path = FILES.$this->main_company.DS.$company->letter.DS.$company->name;
        try{
            if(!file_exists($base_path)){
                if(!mkdir($base_path)){
                    throw new Exception('failed to create folder in: '.$base_path);
                }
            }
            if(!file_exists($base_path.DS.$this->file_catagory)){
                if(!mkdir($base_path.DS.$this->file_catagory)){
                    throw new Exception('failed to create folder in: '.$base_path.DS.$this->file_catagory);
                }
            }
        }catch(Exception $e){
            $this->errors[] = "Culd not create folders for company/catagory";
            $this->errors[] = "Errors:" .$e;
            echo json_encode($this->errors);
            return ;
        }

        if(!rename(FILES.'tempFiles'.DS.$this->temp_id.$this->file_name, $base_path.DS.$this->file_catagory.DS.$this->file_name)){
            $this->errors[] = "Culd not move file from tepm directory1";
            echo json_encode($this->errors);
            return ;
        }
        if(!rename(FILES.'tempFiles'.DS.$this->temp_id.$this->file_name.'.png', $base_path.DS.$this->file_catagory.DS.$this->file_name.'.png') ){
            $this->errors[] = "Culd not move PNG file from tepm directory2";
            echo json_encode($this->errors);
            return ;
        }

        $Path = $this->main_company.DS.$company->letter.DS.$company->name;
        $uid = 12345656;
        $updat_history = '';

        
        

        try{
            $final_file = $database->con->prepare("INSERT INTO inf_files (name ,type,size,path,date,created_by,update_history,tags,com_id,catagory) VALUES (?,?,?,?,?,?,?,?,?,?) ");
            $final_file->bindParam(1,$this->file_name);
            $final_file->bindParam(2,$this->file_type);
            $final_file->bindParam(3,$this->file_size);
            $final_file->bindParam(4,$Path);
            $final_file->bindParam(5,$this->file_date);
            $final_file->bindParam(6,$uid);
            $final_file->bindParam(7,$updat_history);
            $final_file->bindParam(8,$this->file_tags);
            $final_file->bindParam(9,$company->company_id);
            $final_file->bindParam(10,$this->file_catagory);
            if($final_file->execute()){
                //update company and letter
                $new_catagorys = $this->get_new_company_catagorys($company->catagorys);
                $update_company = $database->con->prepare("UPDATE inf_com SET catagorys = '$new_catagorys', total_size = total_size+$this->file_size, total_files = total_files + 1 WHERE company_id = $company->company_id");
                $update_company->execute();
                $delet_temp_file = $database->con->prepare("DELETE FROM temp_files WHERE temp_id = ? ");
                $delet_temp_file->bindParam(1,$this->temp_id);
                $delet_temp_file->execute();
                if(empty($this->errors)){
                    echo 'sucsess';
                }else {
                    echo json_encode($this->errors);
                }
            }
        }catch (Exception $e){
            $this->errors[] = '<br>Error is:' .$e ;
            echo json_encode($this->errors);
            return;
        }

    }

    private function get_new_company_catagorys($catagory_list){
        //TODO FIX THE comma in the return felad
        if(empty($catagory_list)){
            return $this->file_catagory_id;
        }
        $company_catagorys_array = array_map('intval', explode(',', $catagory_list));
        if(in_array($this->file_catagory_id , $company_catagorys_array)){
            return $catagory_list;
        }
        $new_catagory_list = implode(', ', $company_catagorys_array).", $this->file_catagory_id";
        return $new_catagory_list;
    }

    private function validateCatagory($cat){
        global $database;
        if(empty($cat)){
            $this->errors[] = 'Catagory feald cannot be empty';
            return false;
        }
        $catagory = $database->con->prepare("SELECT * FROM catagorys WHERE name = ? ");
        $catagory->bindParam(1,$cat);
        $catagory->execute();
        $catagor = $catagory->fetch(PDO::FETCH_OBJ);
        if(!$catagory){
            $this->errors[] = 'Catagory provided did not match avalable catagorys';
            return false;
        }else{
            return true;
        }    
    }
    private function getCatagoryId($cat){
        global $database;
        if(empty($cat)){
            $this->errors[] = 'Catagory feald cannot be empty';
            return false;
        }
        $catagory = $database->con->prepare("SELECT * FROM catagorys WHERE name = ? ");
        $catagory->bindParam(1,$cat);
        $catagory->execute();
        $catagory = $catagory->fetch(PDO::FETCH_OBJ);
        if(!$catagory){
            $this->errors[] = 'Catagory provided did not match avalable catagorys';
            return false;
        }else{
            return $catagory->cat_id;
        }    
    }
   


    private function validateCompany($comp_name){
        global $database;
        if(empty($comp_name)){
            $this->errors[] = 'Company feald cannot be empty';
            return false;
        }
        $company = $database->con->prepare("SELECT * FROM inf_com WHERE name = ?");
        $company->bindParam(1,$comp_name);
        $company->execute();
        $company = $company->fetch(PDO::FETCH_OBJ);
        if(!$company){
            $this->errors[] = 'Company provided did not match exsistong companys';
            return false;
        }else{
            return true;
        }       
    
    }

    private function validateMainCompany($main_comp_name){
        if(empty($main_comp_name)){
            $this->errors[] = 'Main company feald cannot be empty';
            return false;
        }elseif(!$main_comp_name == 'infinity' || !$main_comp_name == 'cenexa' ){
            $this->errors[] = 'Main company provided dosnt exsist';
            return false;
        }else{
            return true;
        }        
    }

    // TDOD 
    public function get_files($path,$ds,$de){
        global $database;
        // $path = infinity/i/company/catagory/file?
        $paths = explode('/',$path);
        // validate 

        // get files
        if($path == '/' || $path == ''){
            $helper = 0;
            $object = new stdClass();
            $objectWrapper = new stdClass();
            $object->name = 'Infinity';
            $object->is = 'folder';
            $object->path = $path;
            $objectWrapper->$helper =  $object;
            return json_encode($objectWrapper);
        }
        if(count($paths)==1){
            if($paths[0]!== 'infinity'){
                $this->errors[] = $path.' is nut a valid path';
                return false;
            }
            // TODO return the folders that lead to the files
            // if count = 1 IE name
            // needed letters 
            $letters = $database->con->query('SELECT * FROM inf_letters WHERE total_com IS NOT NULL');
            $letters = $letters->fetchAll(PDO::FETCH_OBJ);
            $object = new stdClass();
            foreach($letters as $index => $letter){
                $lttr= new stdClass();
                $lttr->name = $letter->letter;
                $lttr->path = $path;
                $lttr->is = 'folder';
                $object->$index = $lttr;
            }
            return json_encode($object);
        }
        if(count($paths)==2){
            if(!preg_match('/^[a-z]{1}$/',$paths[1])){
                $this->errors[] = $path.' is nut a valid path';
                return false;
            }
            $letter = $paths[1];
            $letters = $database->con->prepare("SELECT * FROM inf_letters WHERE letter = ? LIMIT 1 ");
            // $letters->bindParam(1,$letter);
            $letters->execute([$letter]);
            $letters = $letters->fetch(PDO::FETCH_OBJ);
            $letter_id = $letters->letter_id;
            $companys = $database->con->query("SELECT * FROM inf_com WHERE letter_id = $letter_id ")->fetchAll(PDO::FETCH_OBJ);
            $object = new stdClass();
            foreach($companys as $index => $company){
                $com= new stdClass();
                 $com->name = $company->name;
                 $com->path = $path;
                 $com->is = 'folder';
                 $object->$index = $com;
                // $res .=  $this->get_folder_template($path,$company->name);
            }
            return json_encode($object);
            // return  $res;
        }
        if(count($paths)==3){
            $company_name = $paths[2];
            $company = $database->con->prepare("SELECT * FROM inf_com WHERE name = ? ");
            $company->execute([$company_name]);
            $company = $company->fetch(PDO::FETCH_OBJ);
            if(!$company){
                $this->errors[] = $path.' is nut a valid path';
                return false;
            }
            $has_cats = empty($company->catagorys)?false : true;
            if(!$has_cats){
                $this->errors[] ="Empty, No files found";
                return false;
            }
            $catagorys = explode(",",$company->catagorys);
            $res = '';
            $object = new stdClass();
            foreach($catagorys as $index=>$catagory){
                $cat= new stdClass();
                $catagory = $database->con->query("SELECT * FROM catagorys WHERE cat_id = $catagory")->fetch(PDO::FETCH_OBJ);
                $cat->name = $catagory->name;
                $cat->path = $path;
                $cat->is = 'folder';
                $object->$index = $cat;
            }
            return json_encode($object);
        }

        $company = new Company();
        $company =$company->get_company($paths[2]);
        $catagory = $paths[3];
        if(empty($catagory)){
            $this->errors[] =  'No catagory provided';
            return false;
        }
        $company_id = $company->company_id;
        try{
            $files = $database->con->prepare("SELECT * FROM inf_files WHERE com_id = $company_id and catagory = ? and date > ? and date < ? ");
        $files->bindParam(1,$catagory);
        $files->bindParam(2,$ds);
        $files->bindParam(3,$de);
        $files->execute();
        $files = $files->fetchAll(PDO::FETCH_OBJ);        
        if(!$files){
            $this->errors[] = 'No files founs';
            return false;
        }else{
            $object = new stdClass();
            $count = 0;
            foreach ($files as $file =>$file_val) {
                $object->$file = $file_val ;
                $object->$file->is = 'file' ;
                $object->$file->size = $this->formatBytes($object->$file->size);
            }
            return json_encode($object);
        } 
        }catch(PDOException $e){
            $this->errors[] = 'SQL err,'.$e;
            return false;
        }
        





    }


    


    private function file_template($data){
            $file_catagory = $data->catagory;
            $file_name = $data->name;
            $file_path =  str_replace(DS,'/','..'.DS.'uploads'.DS.$data->path.DS.$file_catagory.DS.$file_name);
            $image_path = str_replace(DS,'/','..'.DS.'uploads'.DS.$data->path.DS.$file_catagory.DS.$file_name.'.png');
            if(!$file_size = $this->formatBytes($data->size)){
              $this->errors[] = 'nable to create size to str';  
            }
            $file_type = explode('/',$data->type)[1];
            $file_date = $data->date;
            $file_id = $data->file_id;
            $file_tags = $data->tags;
        
       
        return "<a href='#' class='files-a files-a-img files-a-loaded' style='--ratio:1.5165876777251184' data-id='$file_id' data-name='$file_name'>
                    <img class='files-img' data-src='' width='320' height='211' src='$image_path'>
                    <div class='files-data'>
                        <span class='name' title='the FilesNAme'><?=$image_path?>$file_name</span>
                            <span class='icon'>
                                <svg viewBox='0 0 24 24' class='svg-icon svg-image'>
                                    <path class='svg-path-image' d='M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z'></path>
                                </svg>
                            </span>
                            <span class='dimensions'></span>
                            <span class='size'>$file_size</span>
                            <span class='ext'>
                                <span class='ext-inner'>$file_type</span>
                            </span>
                            <span class='date'>
                                <time datetime='2022-04-07T10:06:41+03:00' data-time='1649315201' data-format='L LT' title='Thursday, April 7, 2022 10:06 AM ~ a year ago' data-title-format='LLLL'>$file_date</time>
                            </span>
                            <span class='flex'></span>
                    </div>
                    <span class='context-button files-context' data-action='context'>
                        <svg viewBox='0 0 24 24' class='svg-icon svg-dots'>
                            <path class='svg-path-dots' d='M12,16A2,2 0 0,1 14,18A2,2 0 0,1 12,20A2,2 0 0,1 10,18A2,2 0 0,1 12,16M12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10M12,4A2,2 0 0,1 14,6A2,2 0 0,1 12,8A2,2 0 0,1 10,6A2,2 0 0,1 12,4Z'></path>
                            <path class='svg-path-minus' d='M19,13H5V11H19V13Z'></path>
                        </svg>
                    </span>
                </a>";
    }
    private function formatBytes($bytes, $precision = 2):string { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        $bytes /= (1 << (10 * $pow)); 
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    } 
    private function get_folder_template($path,$name){
        $path_with_name =  $path ? $path.'/'.$name : strtolower($name);

        return "<a href='#' target='_blank' class='files-a files-a-svg' data-name='$name' data-path='$path_with_name' >
        <svg viewBox='0 0 48 48' class='svg-folder files-svg'>
            <path class='svg-folder-bg' d='M40 12H22l-4-4H8c-2.2 0-4 1.8-4 4v8h40v-4c0-2.2-1.8-4-4-4z'></path>
            <path class='svg-folder-fg' d='M40 12H8c-2.2 0-4 1.8-4 4v20c0 2.2 1.8 4 4 4h32c2.2 0 4-1.8 4-4V16c0-2.2-1.8-4-4-4z'></path>
        </svg>
        <div class='files-data'>
            <span class='name' title=$name'>$name</span>
            <span class='icon'>
                <svg viewBox='0 0 48 48' class='svg-folder svg-icon'>
                    <path class='svg-folder-bg' d='M40 12H22l-4-4H8c-2.2 0-4 1.8-4 4v8h40v-4c0-2.2-1.8-4-4-4z'></path>
                    <path class='svg-folder-fg' d='M40 12H8c-2.2 0-4 1.8-4 4v20c0 2.2 1.8 4 4 4h32c2.2 0 4-1.8 4-4V16c0-2.2-1.8-4-4-4z'></path>
                </svg>
            </span>
            <span class='ext'></span>
            <span class='date'>
                <time datetime=' data-time=' data-format=' title=' data-title-format='></time>
            </span>
            <span class='flex'></span>
        </div>
        <span class='context-button files-context' data-action='context'>
            <svg viewBox='0 0 24 24' class='svg-icon svg-dots'>
                <path class='svg-path-dots' d='M12,16A2,2 0 0,1 14,18A2,2 0 0,1 12,20A2,2 0 0,1 10,18A2,2 0 0,1 12,16M12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10M12,4A2,2 0 0,1 14,6A2,2 0 0,1 12,8A2,2 0 0,1 10,6A2,2 0 0,1 12,4Z'></path>
                <path class='svg-path-minus' d='M19,13H5V11H19V13Z'></path>
            </svg>
        </span>
        <img data-src='' class='files-folder-preview files-lazy'>
        </a>";
    }

    public function get_single_file($id){
        global $database;
        if(empty($id)){
            return json_encode('No id supplyd');
        }
        $files = $database->con->prepare("SELECT * FROM inf_files WHERE file_id = ? LIMIT 1");
        $files->execute([(int)$id]);
        $files = $files->fetch(PDO::FETCH_OBJ);
        if(!$files){
            return json_encode("id: $id didnt match any file");
        }
        $company = $database->con->query("SELECT * FROM inf_com WHERE company_id = $files->com_id LIMIT 1")->fetch(PDO::FETCH_OBJ);
        $file = array();
        $file_catagory = $files->catagory;
        $file['file_name'] = $files->name;
        $file['file_id'] = $files->file_id;
        $file['file_tages'] = $files->tags;
        $file['file_catagory'] = $files->catagory;
        $file['file_company'] = $company->name;
        $file['file_date'] = $files->date;
        $file['file_last_update'] = $files->updated_at;
        $file_path_with_DS = '../assets/vendor/pdfjs-3.7.107/web/viewer.html?file=http://localhost/infinity/uploads'.DS.$files->path.DS.$file_catagory.DS.$files->name.'#pagemode=thumbs';
        $file_path = str_replace(DS,'/',$file_path_with_DS);
        $file_direct_path_with_ds = 'http://localhost/infinity/uploads/'.$files->path.DS.$file_catagory.DS.$files->name;
        $file_direct_path = str_replace(DS,'/',$file_direct_path_with_ds);
        $file['file_display_path'] = $file_path;
        $file['file_direct_path'] = $file_direct_path;
        return json_encode($file);
    }

}
