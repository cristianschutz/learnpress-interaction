<?php 

// shortcode
function login_func( $atts ) {
    if(!is_user_logged_in()){
?>
    <form id="login" action="login" method="post">
        <p class="alert alert-secondary status"></p>
        <div class="elementor-form-fields-wrapper">
            <div class="elementor-field-type-text elementor-field-group elementor-column elementor-col-100 elementor-field-required" style="margin-bottom: 20px;">
                <label for="username">Usuário</label>
                <input type="text" name="username" id="username" placeholder="Usuário" class="elementor-field elementor-field-textual elementor-size-sm"></div>
            <div class="elementor-field-type-text elementor-field-group elementor-column elementor-col-100 elementor-field-required" style="margin-bottom: 20px;">
                <label for="password">Senha</label>
                <input size="1" type="password" name="password" id="password" placeholder="Senha" class="elementor-field elementor-field-textual elementor-size-sm">
            </div>
            <div class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-100">
                <button type="submit" class="elementor-size-sm elementor-button">
                    <span class="elementor-button-text">ENTRAR</span>
                </button>
            </div>
        </div>
        <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
    </form>
<?php
    }else{
        echo '<p>Você já está logado!</p>';
    }
}
add_shortcode( 'login', 'login_func' );





function ajax_login_init(){

    wp_register_script('ajax-login-script', get_template_directory_uri() . '/inc/login/ajax-login-script.js', array('jquery') ); 
    wp_enqueue_script('ajax-login-script');

    wp_localize_script( 'ajax-login-script', 'ajax_login_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => home_url(),
        'loadingmessage' => __('Enviando, aguarde...')
    ));

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
}

// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
    add_action('init', 'ajax_login_init');
}

if (!session_id()){
    session_start();
}
$GLOBALS['loginapi'] = $_SESSION['loginapi'];

function ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    $login = new User();
    $login->setUser($_POST['username']);
    $login->setPass($_POST['password']);
    $logged = $login->login();

    if($logged){
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login efetuado! Redirecionando...')));
    }else{
        echo json_encode(array('loggedin'=>false, 'message'=>__('Usuário ou senha errados!')));
    }

    die();

}

?>