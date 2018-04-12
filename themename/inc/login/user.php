<?php 

class User{

    var $api;
    var $user;
    var $pass;
    var $api_return;
    // usuario=admin&senha=1234;

    function __construct(){
        // this is a api test json
        $this->api = get_bloginfo('stylesheet_directory')."/inc/login/api-teste.php?ambiente=p&hash_cron=1234";
    }

    function setUser($user) {
        $this->user = $user;
    }
    function getUser() {
        return $this->user;
    }

    function setPass($pass) {
        $this->pass = $pass;
    }
    function getPass() {
        return $this->pass;
    }

    function getUserData(){
        $json = file_get_contents($this->api.'&usuario='.$this->user.'&senha='.$this->pass);
        $obj = json_decode($json);
        $return = false;
        if($obj->acesso_permitido == true){
            $this->api_return = $obj;
            $return = $obj;
            if (!session_id()){
                session_start();
            }
            $_SESSION['loginapi'] = $obj;            
        }
        return $return;
    }

    function login(){
        $obj = $this->getUserData();
        $return = false;
        if($obj){
            $this->api_return = $obj;
            $return = $this->loginwp();
        }
        return $return;
    }

    function loginwp(){
        $user_id = get_user_by( 'email', $this->api_return->email );
        $user_id = ($user_id)? $user_id->ID : '';
        $return = false; 
        $name = $this->api_return->nome;
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );
        $userdata = array(
            'ID'             =>  $user_id,
            'user_login'     =>  $this->user,
            'user_pass'      =>  $this->pass,            
            'user_email'     =>  $this->api_return->email,
            'nickname'       =>  $this->api_return->nome,
            'first_name'     =>  $first_name,
            'last_name'       => $last_name
        );
        if($user_id){
            // faz login
            unset($userdata['ID']);
            $user_id = wp_update_user($userdata);
            unset($userdata['user_pass']);
            $userdata['user_password'] = $this->pass;
            if($user_id){
                $user_signon = wp_signon($userdata, false);
                if( ! is_wp_error($user_signon) ){
                    $this->update_courses($user_signon->ID);
                    $return = true;    
                }   
            }        
        }else{
            // cria user inexistente
            $user_id = wp_insert_user( $userdata ) ;
            if($user_id){
                unset($userdata['user_pass']);
                $userdata['user_password'] = $this->pass;
                $user_signon = wp_signon($userdata, false);
                if( ! is_wp_error($user_signon) ){
                    $this->update_courses($user_signon->ID);
                    $return = true; 
                }
            }
        }
        return $return;
    }

    function update_courses($user_id){
        // add order with courses
        wp_set_current_user($user_id);
        $apicourses = $this->api_return->cursos;
        $addcourses = array();
        if($apicourses){
            foreach ($apicourses as $apicourse) {
                $args = array(
                  'title'        => $apicourse->curso,
                  'post_type'   => 'lp_course',
                  'post_status' => 'publish',
                  'numberposts' => 1
                );
                $lp_courses = get_posts($args);
                if( $lp_courses ){
                    $lp_course = $lp_courses[0];
                    if(!learn_press_get_user_course_status(wp_get_current_user(),$lp_course->ID)){
                        $addcourses[] = array(
                            'item_id' => $lp_course->ID,
                            'order_item_name' => $lp_course->post_title,
                            'quantity' => 1,
                            'subtotal' => 0,
                            'total' => 0
                        );
                    }
                }
            }
            if(!empty($addcourses)){
                $order_data = array();
                $order_data['status'] = 'completed';
                $order_data['created_via'] = 'loginapi';
                $order = learn_press_create_order($order_data);
                foreach ($addcourses as $addcourse) {
                    $order->add_item($addcourse);
                }
            }
        }
    }


}

?>