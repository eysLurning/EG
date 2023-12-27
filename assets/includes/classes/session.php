<?php

class Session{

    function __construct() {
        session_start();
         // Initialize feedback array
        if (!isset($_SESSION['feedback'])) {
            $_SESSION['feedback'] = [];
        }
    }

    public function addFeedback($message, $type = 'info'){
        $_SESSION['feedback'][] = ['message' => $message, 'type' => $type];
    }

    public function getFeedback(){
        $feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : [];
        unset($_SESSION['feedback']); // Clear the feedback after retrieving it
        return $feedback;
    }

    public function set_err($name, $value){
        $_SESSION[$name.'_err'] = $value;
    }
    
    public function delete_all_sessions(){

        $_SESSION = array();

        // resets the ession coockie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    // Check if a Session Variable Exist
    public function has($name) {
        return isset($_SESSION[$name]);
    }

    // Get Session Variable:
    public function get($name) {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    // Remove a Specific Session Variable
    public function remove($name) {
        unset($_SESSION[$name]);
    }

    // Regenerate Session ID:
    public function regenerate_id() {
        session_regenerate_id(true);
    }

    //A message that is meant to be displayed once and then cleared):
    public function set_flash($name, $value) {
        $_SESSION[$name.'_flash'] = $value;
    }

    // a method to retrieve and clear flash messages:
    public function get_flash($name) {
        $value = isset($_SESSION[$name.'_flash']) ? $_SESSION[$name.'_flash'] : null;
        unset($_SESSION[$name.'_flash']);
        return $value;
    }

    // Check if the User is Authenticated:
    public function is_authenticated() {
        return isset($_SESSION['user_id']);
    }

    // Store User Authentication Information:
    public function set_user($user_id) {
        $_SESSION['user_id'] = $user_id;
    }

    // Get User ID:
    public function get_user_id() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }


}
$session = new Session();