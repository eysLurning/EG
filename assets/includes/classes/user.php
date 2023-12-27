<?php 

require_once 'session.php';
class User extends Session{
    // propertys
   
    public $google_client;
    public $token;
    public $user_id;
    public $user_info;
    public $preferences;
    public $defaults;
    public $auth_level = 2;
    public $err = [];
    function __construct(){
        $google_client = new Google_Client();
        $google_client->setClientId(CID);
        $google_client->setClientSecret(CC);
        $google_client->setRedirectUri(GOOGLE_REDIRECT);
        $google_client->addScope("email");
        $google_client->addScope("profile");
        $this->google_client = $google_client;
    }


    function name(){}

    function login($google_code){
        
        global $database;
        global $session;

        $this->token = $this->google_client->fetchAccessTokenWithAuthCode($google_code);
        if(isset($this->token['error'])){
          header('Location: ../login');
          exit;
        }
        $this->google_client->setAccessToken($this->token);
        $google_oauth = new Google_Service_Oauth2($this->google_client);
        $this->user_info = $google_oauth->userinfo->get();

        // Check if the user is not from withing the domain //infinityplusltd.com
        if(empty($this->user_info->hd) || $this->user_info->hd !== DOMAIN){
          $this->set_err('login','Unregisterd email');
          header('Location: ../login');
          exit;
        }

        // TODO: add chack if user exsists
        

        // $this->get_other_user_info();

        if ($this->auth_level === 0){
            $this->set_err('login','unverifyd user, Please let admin know');
            header('Location: ../login');
            exit;
        }
        //TODO: get the user id here 
        $this->user_id = 1;
        $this->set_user_session();
        // get the user info from db here and preferences

        // rediect, can add last location or user preferences
        header('Location: ../home');
        exit;
    }
    function logout(){

        global $database;

        # Revoking the google access token
        $this->google_client->revokeToken();

        # Deleting the session that we stored
        $this->delete_all_sessions();
        
        header("Location: ../login");
        exit;
    }
    function get_other_user_info(){
        global $database;
        $email = $this->user_info->email;
        $user = $database->con->prepare("SELECT * from users WHERE email = ? LIMIT 1 ");
        $user->bindParam(1,$email);
        if(!$user->execute()){
            $this->err[] =  "SQL err (getting user)";
            return false;
        }
        $user = $user->fetch(PDO::FETCH_OBJ);
        $this->preferences = $user->preferences;
        $this->auth_level = $user->auth_level;
        $this->defaults = $user->defaults;

        return true;
    }
    function set_user_session(){

        $current_user = array();
        $current_user['first_name'] = $this->user_info->givenName;
        $current_user['last_name'] = $this->user_info->familyName;
        $current_user['user_id'] = $this->user_id;
        $current_user['full_name'] = $this->user_info->name;
        $current_user['picture'] = $this->user_info->picture;
        $current_user['email'] = $this->user_info->email;
        $current_user['oauth_id'] = $this->user_info->id;

        $current_user['preferences'] = $this->preferences;
        $current_user['defaults'] = $this->defaults;


        $token = $this->token;
        $auth_level = $this->auth_level;
        $_SESSION['token'] = $token;
        $_SESSION['auth'] = $auth_level;
        $_SESSION['user'] = $current_user;
        // $msg = json_encode($_SESSION['auth']);




    }
    





   
}
$user = new User();
