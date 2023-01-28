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
    // echo("Username : ".$username." Password : ".$password);
    // print_r($tampung); die();
    $user = "";
    $role = "";
    foreach ($tampung as $value) {
      // echo $value->user_id."<br>";
      // echo $value->username."<br>";
      // echo $value->role_id;
      $user_id    = $value->user_id;
      $username   = $value->username;
      $role_id    = $value->role_id;
      $org_id     = $value->org_id;
    }
    if(count($tampung)>0){
      $newdata = array(
              'user_id'  => $user_id,
              'username'  => $username,
              'role_id' => $role_id,
              'org_id' => $org_id
      );
      $this->session->set_userdata($newdata);
      redirect('/', 'refresh');
    } else {
      redirect('/dist/auth_login', 'refresh');
    }
    // echo(count($tampung));
    // $this->load->view('HomeView',$data);
		// $data = array(
		// 	'title' => "Login"
		// );
		// $this->load->view('dist/auth-login', $data);
	}

  public function session_logout() {
    $this->load->library('session');

    $this->session->sess_destroy();
    redirect('/dist/auth_login', 'refresh');
	}

}
