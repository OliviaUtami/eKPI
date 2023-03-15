<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {
	private $allow_access = array("notification");
	public function __construct()
	{
			parent::__construct();
			$this->load->library('session');
			$this->load->model('user_model');
			if(isset($_SESSION["user_id"])){
				$notif = $this->user_model->get_notif($_SESSION["user_id"],0,true);
				$_SESSION['notif'] = $notif;
			}
			//var_dump($_SESSION['notif']);
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
	private function check_access() {
		$check = $this->user_model->check_access($this->uri->segment(1));
		if($check>0||in_array($this->uri->segment(1), $this->allow_access)){

		}else{
			header("HTTP/1.1 403 Forbidden");
			$data = array(
				"title" 	=> "Error 403 Forbidden",
				"menu"		=> ""
			);
			$this->load->view('pages/errors-403',$data);
			$this->output->_display();
			die;
		}
		//cari ditable access apakah user punya akses ke sana
	}
	private function check_access_api() {
		$check = $this->user_model->check_access($this->uri->segment(1));
		if($check>0){
			return true;
		}else{
			return false;
		}
		//cari ditable access apakah user punya akses ke sana
	}
	private function check_login_api() {
		if(!$this->session->has_userdata('username')){
			return false;
		}
		return true;
	}
	public function mark_as_read(){
		$this->load->model('user_model');
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$id = $obj->id;
		$data = $this->user_model->mark_as_read($id);
		$_SESSION["notif"]=[];
		echo json_encode($data);
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
		$this->check_access();
		$this->load->model('organization_model');
		$orgs = $this->organization_model->get_organizations();

		$data = array(
			"title" 	=> "Daftar Unit",
			"menu"		=> "org",
			"data"		=> $orgs
		);
		$this->load->view('pages/org-view', $data);
	}

	public function view_org_add() {
		$this->check_login();
		$this->check_access();
		$data = array(
			"title" 		=> "Tambah Unit",
			"menu"			=> "org"
		);
		$this->load->view('pages/org-add', $data);
	}

	public function process_org_add(){
		$this->check_login();
		$this->check_access();
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
		$this->check_access();
		$this->load->model('organization_model');

		$org = $this->organization_model->get_organization($id);
		$data = array(
			"title" 		=> "Edit Unit",
			"menu"			=> "org",
			"data" 			=> $org
		);
		$this->load->view('pages/org-edit', $data);
	}

	public function process_org_edit(){
		$this->check_login();
		$this->check_access();
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
		$this->check_access();
		$this->load->model('organization_model');
		$data = $this->organization_model->delete_org($id);
		$this->session->set_flashdata('message', $data->message);
		redirect('/org');
	}
	/** PAGE ORG - END */

	/** PAGE USER - START */
	public function view_user_list() {
		$this->check_login();
		$this->check_access();
		$this->load->model('user_model');
		$this->check_access();
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
		$this->check_access();
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
		$this->check_access();
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
		$this->check_access();
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
		$this->check_access();
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
		$this->check_access();
		$this->load->model('user_model');
		$data = $this->user_model->delete_user($id);
		$this->session->set_flashdata('message', $data->message);
		redirect('/user');
	}
	/** PAGE USER - END */


	/** PAGE INDICATOR - START */
	public function view_period_list() {
		$this->check_login();
		$this->check_access();
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
		$this->check_access();
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
		$this->check_access();
		$this->load->model('period_model');

		$period_from= $_POST['period_from'];
		$period_to 	= $_POST['period_to'];
		$name 	= $_POST['name'];
		$status 	= $_POST['status'];
		$draft 	= $_POST['draft'];
		$created    = $_SESSION['username'];

		$data = $this->period_model->add_period($period_from, $period_to, $name, $status, $created, $draft);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/period');
		}else{
			redirect('/period/add');
		}
	}

	public function view_period_edit($period_id) {
		$this->check_login();
		$this->check_access();
		$this->load->model('period_model');
		$perioddata = $this->period_model->get_period_by_id($period_id);
		$this->load->model('draft_model');
		$drafts = $this->draft_model->get_approved_draft();
		$data = array(
			"title" 		=> "Edit Periode",
			"menu"			=> "period",
			"perioddata"	=> $perioddata,
			"draft_data"	=> $drafts
		);
		$this->load->view('pages/period-edit', $data);
	}

	public function process_period_edit(){
		$this->check_login();
		$this->check_access();
		$this->load->model('period_model');

		$period_id 	= $_POST['id'];
		$period_from= $_POST['period_from'];
		$period_to 	= $_POST['period_to'];
		$name 		= $_POST['name'];
		$draft 		= $_POST['draft'];
		$status 	= $_POST['status'];

		$data = $this->period_model->edit_period($period_id, $period_from, $period_to, $name, $status, $draft);
		$this->session->set_flashdata('message', $data->message);
		if($data->ok == 1){
			redirect('/period');
		}else{
			redirect('/period/edit/'.$period_id);
		}
	}
	/** PAGE INDICATOR - END */

	/** PAGE DRAFT - START */
	public function view_draft_list() {
		$this->check_login();
		$this->check_access();
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
		$this->check_access();
		$this->load->model('draft_model');

		$data = array(
			"title" 		=> "Tambah Draft",
			"menu"			=> "draft"
		);
		$this->load->view('pages/draft-add', $data);
	}
	public function process_draft_add(){
		if(!$this->check_login_api()||!$this->check_access_api()){
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
		$this->check_access();
		$this->load->model('draft_model');

		$draft_data = $this->draft_model->get_draft_by_id($draft_id);

		$data = array(
			"title" 		=> "Edit Draft",
			"menu"			=> "draft",
			"draft_data"	=> $draft_data
		);
		$this->load->view('pages/draft-edit', $data);
	}
	public function process_draft_edit(){
		if(!$this->check_login_api()||!$this->check_access_api()){
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
	}

	public function process_draft_delete($id){
		$this->check_login();
		$this->check_access();
		$this->load->model('draft_model');
		$data = $this->draft_model->delete_draft($id);
		$this->session->set_flashdata('message', $data->message);
		redirect('/draft');
	}

	public function process_draft_rfa($id){
		$this->check_login();
		$this->check_access();
		$this->load->model('draft_model');
		
		$data = $this->draft_model->rfa_draft($id);
		$this->session->set_flashdata('message', $data->message);
		//var_dump($data);
		redirect('/draft');
	}

	public function process_draft_copy($id){
		$this->check_login();
		$this->check_access();
		$this->load->model('draft_model');
		
		$data = $this->draft_model->copy_draft($id);
		$this->session->set_flashdata('message', $data->message);
		redirect('/draft');
	}

	/** DRAFT APPROVAL */
	public function view_draft_approval_list() {
		$this->check_login();
		$this->check_access();
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
		$this->check_access();
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
		if(!$this->check_login_api()||!$this->check_access_api()){
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
		$this->check_access();
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
		$this->check_access();
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
		$this->check_access();
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
		if(!$this->check_login_api()||!$this->check_access_api()){
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
		if(!$this->check_login_api()||!$this->check_access_api()){
			return false;
		}
		$this->load->model('indicator_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$this->load->model('user_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
    
		$data = $this->indicator_model->add_indicator($user,$obj);
		//$this->session->set_flashdata('message', $data->message);
		echo json_encode($data);
		//exit();
	}

	public function process_indicator_rfa($id){
		$this->check_login();
		$this->check_access();
		$this->load->model('indicator_model');
		
		$data = $this->indicator_model->rfa_indicator($id);
		$this->session->set_flashdata('message', $data->message);
		//var_dump($data);
		redirect('/indicator');
	}

	public function view_indicator_approval_list() {
		$this->check_login();
		$this->check_access();
		$this->load->model('indicator_model');
		
		$periods = $this->indicator_model->get_indicator_approval();
		//var_dump($drafts);
		$data = array(
			"title" 		=> "Daftar Pengajuan Indikator KPI",
			"menu"			=> "indicator-approval",
			"periods"		=> $periods
		);
		$this->load->view('pages/appr-indicator-view', $data);
	}

	public function view_indicator_approval_edit($uid) {
		$this->check_login();
		$this->check_access();
		$this->load->model('user_model');
		$this->load->model('indicator_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		$indicator = $this->indicator_model->get_indicator_approval_by_uid($uid);
		//var_dump($indicator);
		$data = array(
			"title" 		=> "Manajemen Indikator (".$user->org_name.")",
			"menu"			=> "indicator",
			"indicator"		=> $indicator
		);
		$this->load->view('pages/appr-indicator-edit', $data);
	}
	public function process_indicator_approval_edit(){
		if(!$this->check_login_api()||!$this->check_access_api()){
			return false;
		}
		$this->load->model('indicator_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$indicator_id = $obj->id;
		$action = $obj->action;
		$remarks = $obj->remarks;
		
		$data = $this->indicator_model->publish_indicator($indicator_id, $action, $remarks);
		echo json_encode($data);
		//var_dump($test);
		//exit();
	}
	public function cancel_indicator_approval($id){
		$this->check_login();
		$this->check_access();
		$this->load->model('indicator_model');
		
		$data = $this->indicator_model->cancel_indicator_approval($id);
		$this->session->set_flashdata('message', $data->message);
		//var_dump($data);
		redirect('/indicator-approval');
	}
	/** PAGE INDICATOR - END */

	/** PAGE KPI - START */
	public function view_kpi_list() {
		$this->check_login();
		$this->check_access();
		$this->load->model('user_model');
		$this->load->model('kpi_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		//var_dump($user);
		$kpi = $this->kpi_model->get_my_kpi($user->user_id);
		//var_dump($periods);
		$data = array(
			"title" 		=> "KPI Saya",
			"menu"			=> "kpi",
			"kpi"			=> $kpi
		);
		$this->load->view('pages/kpi-view', $data);
	}

	public function view_kpi_add($indicator_id) {
		$this->check_login();
		$this->check_access();
		$this->load->model('user_model');
		$this->load->model('kpi_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		$indicator = $this->kpi_model->get_kpi_indicator($indicator_id,$user);
		//var_dump(json_encode($indicator));
		$data = array(
			"title" 		=> "KPI Saya",
			"menu"			=> "kpi",
			"indicator"		=> $indicator
		);
		$this->load->view('pages/kpi-edit', $data);
	}

	public function view_kpi_edit($uid) {
		$this->check_login();
		$this->check_access();
		$this->load->model('user_model');
		$this->load->model('kpi_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		$indicator = $this->kpi_model->get_kpi_indicator_by_uid($uid);
		//var_dump(json_encode($indicator));
		$data = array(
			"title" 		=> "KPI Saya",
			"menu"			=> "kpi",
			"indicator"		=> $indicator
		);
		$this->load->view('pages/kpi-edit', $data);
	}

	public function get_indicator(){
		if(!$this->check_login_api()||!$this->check_access_api()){
			return false;
		}
		$this->load->model('kpi_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$indicator_id = $obj->indicator_id;
		
		$data = $this->kpi_model->get_indicator($indicator_id);
		echo json_encode($data);
	}

	public function process_kpi_add(){
		if(!$this->check_login_api()||!$this->check_access_api()){
			return false;
		}
		$this->load->model('kpi_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$this->load->model('user_model');
		//echo var_dump($obj);
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		$data = $this->kpi_model->add_kpi($user,$obj);
		echo json_encode($data);
		//exit();
	}

	public function submit_kpi($id){
		$this->check_login();
		$this->check_access();
		$this->load->model('kpi_model');
		
		$data = $this->kpi_model->submit_kpi($id);
		$this->session->set_flashdata('message', $data->message);
		//echo $data->message;
		redirect('/kpi');
	}

	public function print_kpi($uid) {
		$this->check_login();
		$this->check_access();
		$this->load->model('user_model');
		$this->load->model('kpi_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		$indicator = $this->kpi_model->get_kpi_indicator_by_uid($uid);
		//var_dump(json_encode($indicator));
		$data = array(
			"title" 		=> "KPI Saya",
			"menu"			=> "kpi",
			"indicator"		=> $indicator
		);
		$this->load->view('pages/kpi-print', $data);
	}
	/** PAGE KPI - END */

	/** PAGE CHECK KPI - START */
	public function view_check_kpi_list() {
		$this->check_login();
		$this->check_access();
		$this->load->model('user_model');
		$this->load->model('kpi_model');
		$this->load->model('period_model');
		$this->load->model('organization_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		//var_dump($user);
		$kpi = $this->kpi_model->get_check_kpi_list($user->user_id);
		$allkpi = $this->kpi_model->get_all_kpi_list(null, null);
		$period = $this->period_model->get_period();
		$org = $this->organization_model->get_organizations();
		//var_dump($periods);
		$data = array(
			"title" 		=> "Daftar KPI Karyawan",
			"menu"			=> "check-kpi",
			"period"		=> $period,
			"org"			=> $org,
			"kpi"			=> $kpi,
			"allkpi"		=> $allkpi
		);
		$this->load->view('pages/check-kpi-view', $data);
	}

	public function get_kpi(){
		if(!$this->check_login_api()||!$this->check_access_api()){
			return false;
		}
		$this->load->model('kpi_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$period = $obj->period;
		$org = $obj->organization;
		
		$data = $this->kpi_model->get_all_kpi_list($period, $org);
		echo json_encode($data);
	}

	public function view_check_kpi_edit($uid) {
		$this->check_login();
		$this->check_access();
		$this->load->model('user_model');
		$this->load->model('kpi_model');
		$user = $this->user_model->get_user_by_username($_SESSION["username"]);
		$indicator = $this->kpi_model->get_kpi_indicator_by_uid($uid);
		//var_dump(json_encode($indicator));
		$data = array(
			"title" 		=> "Cek KPI",
			"menu"			=> "check-kpi",
			"indicator"		=> $indicator
		);
		$this->load->view('pages/check-kpi-edit', $data);
	}

	public function process_check_kpi_edit(){
		if(!$this->check_login_api()||!$this->check_access_api()){
			return false;
		}
		$this->load->model('kpi_model');
		
		$json = file_get_contents('php://input');
		$obj = json_decode($json);
		$ind_user_id = $obj->id;
		$action = $obj->action;
		$remarks = $obj->remarks;
		$data = $this->kpi_model->process_kpi($ind_user_id, $action, $remarks);
		echo json_encode($data);
		//var_dump($test);
		//exit();
	}

	public function view_notification_list() {
		$this->check_login();
		$this->check_access();
		$this->load->model('user_model');
		$notif = $this->user_model->get_notif($_SESSION["user_id"],"",false);
		
		$data = array(
			"title" 		=> "Notifikasi",
			"menu"			=> "notification",
			"notif"			=> $notif
		);
		$this->load->view('pages/notif-view', $data);
	}
}
