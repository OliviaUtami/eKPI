<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {
	public function __construct()
	{
			parent::__construct();
			// Your own constructor code
			$this->load->library('session');
	}
	public function index() {
		$data = array(
			'title' => "Dashboard"
		);
		if(!$this->session->has_userdata('username')){
			redirect('/login', 'refresh');
		} else{
			redirect('/user');
		}
	}
    public function login() {
		$data = array(
			'title' => "Login"
		);
		$this->load->view('pages/auth-login', $data);
	}

	/** PAGE ORG - START */
	public function view_org_list() {
		$this->load->model('organization_model');
		$orgs = $this->organization_model->get_organizations();
		
		$data = array(
			"title" 	=> "Organization List",
			"menu"		=> "org",
			"data"		=> $orgs
		);
		$this->load->view('pages/org-view', $data);
	}

	public function view_org_add() {
		$data = array(
			"title" 		=> "Add Organization",
			"menu"			=> "org"
		);
		$this->load->view('pages/org-add', $data);
	}

	public function process_org_add(){
		$this->load->model('organization_model');

		$name 		= $_POST['name'];
		$hierarchy 	= $_POST['hierarchy'];
		
		$data = $this->organization_model->add_org($name, $hierarchy);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/org');
		}else{
			redirect('/org/add');
		}
	}

	public function view_org_edit($id){
		$this->load->model('organization_model');

		$org = $this->organization_model->get_organization($id);
		$data = array(
			"title" 		=> "Edit Organization",
			"menu"			=> "org",
			"data" 			=> $org
		);
		$this->load->view('pages/org-edit', $data);
	}
	
	public function process_org_edit(){
		$this->load->model('organization_model');

		$id 		= $_POST['id'];
		$name 		= $_POST['name'];
		$hierarchy 	= $_POST['hierarchy'];
		
		$data = $this->organization_model->edit_org($id, $name, $hierarchy);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/org');
		}else{
			redirect('/org/edit/'.$id);
		}
	}

	public function process_org_delete($id){
		$this->load->model('organization_model');
		$data = $this->organization_model->delete_org($id);
		$this->session->set_flashdata('message', $data->message);
		redirect('/org');
	}
	/** PAGE ORG - END */

	/** PAGE USER - START */
	public function view_user_list() {
		$this->load->model('user_model');
		$users = $this->user_model->view_user();
		
		$data = array(
			"title" 	=> "User List",
			"menu"		=> "user",
			"user_data"	=> $users
		);
		$this->load->view('pages/user-view', $data);
	}

	public function view_user_add() {
		$this->load->model('role_model');
		$this->load->model('organization_model');

		$roles = $this->role_model->get_roles();
		$organizations = $this->organization_model->get_organizations();
		
		$data = array(
			"title" 		=> "Add User",
			"menu"			=> "user",
			"roles" 		=> $roles,
			"organizations" => $organizations
		);
		$this->load->view('pages/user-add', $data);
	}

	public function process_user_add(){
		$this->load->model('user_model');

		$name 		= $_POST['name'];
		$address 	= $_POST['address'];
		$email 		= $_POST['email'];
		$username 	= $_POST['username'];
		$password 	= $_POST['password'];
		$org_id 	= $_POST['organization'];
    	$role_id 	= $_POST['role'];

		$data = $this->user_model->add_user($name, $address, $email, $username, md5($password), $org_id, $role_id);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/user');
		}else{
			redirect('/user/add');
		}
	}
	
	public function view_user_edit($id){
		$this->load->model('user_model');
		$this->load->model('role_model');
		$this->load->model('organization_model');

		$roles = $this->role_model->get_roles();
		$organizations = $this->organization_model->get_organizations();
		
		$user = $this->user_model->get_user($id);
		
		$data = array(
			"title" 		=> "Edit User",
			"menu"			=> "user",
			"user" 			=> $user,
			"roles" 		=> $roles,
			"organizations" => $organizations
		);
		$this->load->view('pages/user-edit', $data);
	}

	public function process_user_edit(){
		$user_id 	= $_POST['id'];
		$name 		= $_POST['name'];
		$address 	= $_POST['address'];
		$email 		= $_POST['email'];
		$username 	= $_POST['username'];
		$password 	= $_POST['password'];
		$org_id 	= $_POST['organization'];
    	$role_id 	= $_POST['role'];
		
		$this->load->model('user_model');
		$data = $this->user_model->edit_user($user_id, $name, $address, $email, $username, $password, $org_id, $role_id);
		//var_dump($data);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/user');
		}else{
			redirect('/user/edit/'.$user_id);
		}
	}

	public function process_user_delete($id){
		$this->load->model('user_model');
		$data = $this->user_model->delete_user($id);
		$this->session->set_flashdata('message', $data->message);
		redirect('/user');
	}
	/** PAGE USER - END */


	/** PAGE INDICATOR - START */
	public function view_period_list() {
		$this->load->model('indicator_model');
		$periods = $this->indicator_model->get_period();
		
		$data = array(
			"title" 		=> "Period List",
			"menu"			=> "period",
			"period_data"	=> $periods
		);
		$this->load->view('pages/period-view', $data);
	}

	public function view_period_add() {
		$this->load->model('indicator_model');
		
		$data = array(
			"title" 		=> "Add Period",
			"menu"			=> "indicator"
		);
		$this->load->view('pages/period-add', $data);
	}

	public function process_period_add(){
		$this->load->model('indicator_model');

		$period_from= $_POST['period_from'];
		$period_to 	= $_POST['period_to'];
		$status 	= $_POST['status'];
		
		$data = $this->indicator_model->add_period($period_from, $period_to, $status);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/indicator');
		}else{
			redirect('/period/add');
		}
	}

	public function view_period_edit($period_id) {
		$this->load->model('indicator_model');
		$perioddata = $this->indicator_model->get_period_by_id($period_id);
		$data = array(
			"title" 		=> "Edit Period",
			"menu"			=> "Period",
			"perioddata"	=> $perioddata
		);
		$this->load->view('pages/period-edit', $data);
	}

	public function process_period_edit(){
		$this->load->model('indicator_model');
		$period_id 	= $_POST['id'];
		$period_from= $_POST['period_from'];
		$period_to 	= $_POST['period_to'];
		$status 	= $_POST['status'];
		
		$data = $this->indicator_model->edit_period($period_id,$period_from, $period_to, $status);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/indicator');
		}else{
			redirect('/period/edit');
		}
	}
	/** PAGE INDICATOR - END */

	/** PAGE DRAFT - START */
	public function view_draft_list() {
		$this->load->model('draft_model');
		$drafts = $this->draft_model->get_draft();
		
		$data = array(
			"title" 		=> "Draft List",
			"menu"			=> "draft",
			"draft_data"	=> $drafts
		);
		$this->load->view('pages/draft-view', $data);
	}
	public function view_draft_add() {
		$this->load->model('draft_model');
		
		$data = array(
			"title" 		=> "Add Draft",
			"menu"			=> "draft"
		);
		$this->load->view('pages/draft-add', $data);
	}
	public function process_draft_add(){
		var_dump($_POST);
exit();
	}
	/** PAGE DRAFT - END */
}
