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
			redirect('/dashboard');
		}
	}
    public function login() {
		$data = array(
			'title' => "Login"
		);
		$this->load->view('pages/auth-login', $data);
	}
	private function check_login() {
		$data = array(
			'title' => "Dashboard"
		);
		if(!$this->session->has_userdata('username')){
			redirect('/login', 'refresh');
		}
	}
	private function check_login_api() {
		if(!$this->session->has_userdata('username')){
			return false;
		}
		return true;
	}
	/** PAGE ORG - START */
	public function dashboard() {
		$data = array(
			"title" 	=> "Dashboard",
			"menu"		=> "dashboard"
		);
		$this->load->view('pages/dashboard', $data);
	}

	/** PAGE ORG - START */
	public function view_org_list() {
		$this->check_login();
		$this->load->model('organization_model');
		$orgs = $this->organization_model->get_organizations();

		$data = array(
			"title" 	=> "Daftar Organisasi",
			"menu"		=> "org",
			"data"		=> $orgs
		);
		$this->load->view('pages/org-view', $data);
	}

	public function view_org_add() {
		$this->check_login();
		$data = array(
			"title" 		=> "Add Organization",
			"menu"			=> "org"
		);
		$this->load->view('pages/org-add', $data);
	}

	public function process_org_add(){
		$this->check_login();
		$this->load->model('organization_model');

		$name 		= $_POST['name'];
		$hierarchy 	= $_POST['hierarchy'];
		$created = $_SESSION['username'];
		$data = $this->organization_model->add_org($name, $hierarchy,$created);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/org');
		}else{
			redirect('/org/add');
		}
	}

	public function view_org_edit($id){
		$this->check_login();
		$this->load->model('organization_model');

		$org = $this->organization_model->get_organization($id);
		$data = array(
			"title" 		=> "Edit Organisasi",
			"menu"			=> "org",
			"data" 			=> $org
		);
		$this->load->view('pages/org-edit', $data);
	}

	public function process_org_edit(){
		$this->check_login();
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
		$this->check_login();
		$this->load->model('organization_model');
		$data = $this->organization_model->delete_org($id);
		$this->session->set_flashdata('message', $data->message);
		redirect('/org');
	}
	/** PAGE ORG - END */

	/** PAGE USER - START */
	public function view_user_list() {
		$this->check_login();
		$this->load->model('user_model');
		$users = $this->user_model->view_user();

		$data = array(
			"title" 	=> "Daftar User",
			"menu"		=> "user",
			"user_data"	=> $users
		);
		$this->load->view('pages/user-view', $data);
	}

	public function view_user_add() {
		$this->check_login();
		$this->load->model('role_model');
		$this->load->model('organization_model');

		$roles = $this->role_model->get_roles();
		$organizations = $this->organization_model->get_organizations();

		$data = array(
			"title" 		=> "Tambah User",
			"menu"			=> "user",
			"roles" 		=> $roles,
			"organizations" => $organizations
		);
		$this->load->view('pages/user-add', $data);
	}

	public function process_user_add(){
		$this->check_login();
		$this->load->model('user_model');

		$name 		= $_POST['name'];
		$address 	= $_POST['address'];
		$email 		= $_POST['email'];
		$username 	= $_POST['username'];
		$password 	= $_POST['password'];
		$org_id 	= $_POST['organization'];
    	$role_id 	= $_POST['role'];

		$created = $_SESSION['username'];

		$data = $this->user_model->add_user($name, $address, $email, $username, md5($password), $org_id, $role_id);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/user');
		}else{
			redirect('/user/add');
		}
	}

	public function view_user_edit($id){
		$this->check_login();
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
		$this->check_login();
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
		$this->check_login();
		$this->load->model('user_model');
		$data = $this->user_model->delete_user($id);
		$this->session->set_flashdata('message', $data->message);
		redirect('/user');
	}
	/** PAGE USER - END */


	/** PAGE INDICATOR - START */
	public function view_period_list() {
		$this->check_login();
		$this->load->model('period_model');
		$periods = $this->period_model->get_period();

		$data = array(
			"title" 		=> "Daftar Periode",
			"menu"			=> "period",
			"period_data"	=> $periods
		);
		$this->load->view('pages/period-view', $data);
	}

	public function view_period_add() {
		$this->check_login();
		$this->load->model('period_model');
		$this->load->model('draft_model');
		$drafts = $this->draft_model->get_approved_draft();
		$data = array(
			"title" 		=> "Tambah Periode",
			"menu"			=> "period",
			"draft_data"	=> $drafts
		);
		$this->load->view('pages/period-add', $data);
	}

	public function process_period_add(){
		$this->check_login();
		$this->load->model('period_model');

		$period_from= $_POST['period_from'];
		$period_to 	= $_POST['period_to'];
		$status 	= $_POST['status'];
		$draft 	= $_POST['draft'];
		$created    = $_SESSION['username'];

		$data = $this->period_model->add_period($period_from, $period_to, $status, $created, $draft);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/period');
		}else{
			redirect('/period/add');
		}
	}

	public function view_period_edit($period_id) {
		$this->check_login();
		$this->load->model('period_model');
		$perioddata = $this->period_model->get_period_by_id($period_id);
		$this->load->model('draft_model');
		$drafts = $this->draft_model->get_approved_draft();
		$data = array(
			"title" 		=> "Edit Period",
			"menu"			=> "period",
			"perioddata"	=> $perioddata,
			"draft_data"	=> $drafts
		);
		$this->load->view('pages/period-edit', $data);
	}

	public function process_period_edit(){
		$this->check_login();
		$this->load->model('period_model');

		$period_id 	= $_POST['id'];
		$period_from= $_POST['period_from'];
		$period_to 	= $_POST['period_to'];
		$draft 		= $_POST['draft'];
		$status 	= $_POST['status'];

		$data = $this->period_model->edit_period($period_id,$period_from, $period_to, $status, $draft);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/period');
		}else{
			redirect('/period/edit');
		}
	}
	/** PAGE INDICATOR - END */

	/** PAGE DRAFT - START */
	public function view_draft_list() {
		$this->check_login();
		$this->load->model('draft_model');

		$drafts = $this->draft_model->get_draft();
		//var_dump($drafts);
		$data = array(
			"title" 		=> "Daftar Draft",
			"menu"			=> "draft",
			"draft_data"	=> $drafts
		);
		$this->load->view('pages/draft-view', $data);
	}
	public function view_draft_add() {
		$this->check_login();
		$this->load->model('draft_model');

		$data = array(
			"title" 		=> "Tambah Draft",
			"menu"			=> "draft"
		);
		$this->load->view('pages/draft-add', $data);
	}
	public function process_draft_add(){
		if(!$this->check_login_api()){
			return false;
		}
		$this->load->model('draft_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$draft_name = $obj->name;
		$missions = $obj->details;
		$data = $this->draft_model->add_draft($draft_name, $missions);
		echo json_encode($data);
		//var_dump($test);
		//exit();
	}
	public function view_draft_edit($draft_id) {
		$this->check_login();
		$this->load->model('draft_model');

		$draft_data = $this->draft_model->get_draft_by_id($draft_id);

		$data = array(
			"title" 		=> "Daftar Draft",
			"menu"			=> "draft",
			"draft_data"	=> $draft_data
		);
		$this->load->view('pages/draft-edit', $data);
	}
	public function process_draft_edit(){
		if(!$this->check_login_api()){
			return false;
		}
		$this->load->model('draft_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$draft_name = $obj->name;
		$draft_id = $obj->id;
		$missions = $obj->details;
		$data = $this->draft_model->edit_draft($draft_id, $draft_name, $missions);
		echo json_encode($data);
		//var_dump($test);
		//exit();
	}
	public function process_draft_rfa($id){
		$this->check_login();
		$this->load->model('draft_model');
		
		$data = $this->draft_model->rfa_draft($id);
		$this->session->set_flashdata('message', $data->message);
		//var_dump($data);
		redirect('/draft');
	}

	/** DRAFT APPROVAL */
	public function view_draft_approval_list() {
		$this->check_login();
		$this->load->model('draft_model');
		
		$drafts = $this->draft_model->get_draft_approval();
		//var_dump($drafts);
		$data = array(
			"title" 		=> "Daftar Pengajuan Draft KPI",
			"menu"			=> "draft-approval",
			"draft_data"	=> $drafts
		);
		$this->load->view('pages/appr-draft-view', $data);
	}
	public function view_draft_approval_edit($draft_id) {
		$this->check_login();
		$this->load->model('draft_model');

		$draft_data = $this->draft_model->get_draft_by_id($draft_id);

		$data = array(
			"title" 		=> "Daftar Draft",
			"menu"			=> "draft-approval",
			"draft_data"	=> $draft_data
		);
		$this->load->view('pages/appr-draft-edit', $data);
	}
	public function process_draft_approval_edit(){
		if(!$this->check_login_api()){
			return false;
		}
		$this->load->model('draft_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$draft_id = $obj->id;
		$action = $obj->action;
		$remarks = $obj->remarks;
		$data = $this->draft_model->process_draft($draft_id, $action, $remarks);
		echo json_encode($data);
		//var_dump($test);
		//exit();
	}
	public function cancel_draft_approval($id){
		$this->check_login();
		$this->load->model('draft_model');
		
		$data = $this->draft_model->cancel_draft_approval($id);
		$this->session->set_flashdata('message', $data->message);
		//var_dump($data);
		redirect('/draft-approval');
	}
	/** PAGE DRAFT - END */



	/** PAGE INDICATOR - START */
	public function view_indicator_list() {
		$this->check_login();
		$this->check_login();
		$this->load->model('user_model');
		$this->load->model('indicator_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		//var_dump($user);
		$periods = $this->indicator_model->get_org_active_period_indicators($user->org_id);
		//var_dump($periods);
		$data = array(
			"title" 		=> "Manajemen Indikator (".$user->org_name.")",
			"menu"			=> "indicator",
			"periods"		=> $periods
		);
		$this->load->view('pages/indicator-view', $data);
	}

	public function view_indicator_edit($draft_id) {
		$this->check_login();
		$this->load->model('user_model');
		$this->load->model('indicator_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		$indicator = $this->indicator_model->get_indicator_by_draft_org($draft_id,$user->org_id);
		//var_dump(json_encode($indicator));
		$data = array(
			"title" 		=> "Manajemen Indikator (".$user->org_name.")",
			"menu"			=> "indicator",
			"indicator"		=> $indicator
		);
		$this->load->view('pages/indicator-edit', $data);
	}
	
	public function get_program_by_target(){
		if(!$this->check_login_api()){
			return false;
		}
		$this->load->model('indicator_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$target_id = $obj->target_id;
		
		$data = $this->indicator_model->get_program_by_target($target_id);
		echo json_encode($data);
	}

	public function process_indicator_add(){
		if(!$this->check_login_api()){
			return false;
		}
		$this->load->model('indicator_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$this->load->model('user_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
    
		$data = $this->indicator_model->add_indicator($user,$obj);
		
		
		//exit();
	}
	/** PAGE INDICATOR - END */
}
