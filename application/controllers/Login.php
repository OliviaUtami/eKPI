<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
  public function check_login() {
    $this->load->library('session');
    // echo($_POST['username']);
    $this->load->model('user_model');
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $tampung = $this->user_model->query_check_login($username,$password);
    $menu = $this->user_model->get_menu($tampung->org_id, $tampung->role_id);
    // echo("Username : ".$username." Password : ".$password);
    // print_r($tampung); die();
    $user = "";
    $role = "";
    
    if($tampung !== null){
      $user_id    = $tampung->user_id;
      $username   = $tampung->username;
      $role_id    = $tampung->role_id;
      $org_id     = $tampung->org_id;
      $newdata = array(
              'user_id'  => $user_id,
              'username'  => $username,
              'role_id' => $role_id,
              'org_id' => $org_id,
              'logged_in_at' => time(),
              'menu' => $menu
      );
      $this->session->set_userdata($newdata);
      redirect('/', 'refresh');
    } else {
      redirect('/dist/auth_login', 'refresh');
    }
	}

  public function session_logout() {
    $this->load->library('session');

    $this->session->sess_destroy();
    redirect('/dist/auth_login', 'refresh');
	}

}
