<?php
class user_model extends CI_Model {
  public function query_check_login($username,$password){
    $sql = "SELECT *
            FROM users u
            WHERE u.username = '$username' AND u.password='$password'";
    // echo("Hasil : ".$this->db->query($sql)->result());
    return $this->db->query($sql)->result();
  }

  public function add_user($name, $address, $email, $username, $password, $org_id, $role_id, $created){
    $message = ""; $ok = 1;
    $exist = $this->db->query("SELECT COUNT(1) count FROM users WHERE username = ? and is_active = 1",array($username))->row();

    if((int) $exist->count > 0){
      $message = "User with same user name exists";
    }else{
      $sql = "INSERT INTO users
            (name, address, email, username, password, org_id, role_id, is_active, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $this->db->query($sql, array($name, $address, $email, $username, $password, $org_id, $role_id, 1, $created));
      $message = "User succesfully added";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function view_user(){
    $sql = "SELECT u.*, o.org_name, r.role_name
            FROM users u
            JOIN organizations o on u.org_id = o.org_id
            JOIN roles r on u.role_id = r.role_id
            WHERE u.is_active = ?
            ORDER BY u.name ASC";
    return $this->db->query($sql,array(1))->result();
  }

  public function get_user($id){
    $sql = "SELECT * FROM users WHERE user_id = ?";
    return $this->db->query($sql,array($id))->row();
  }

  public function edit_user($user_id, $name, $address, $email, $username, $password, $org_id, $role_id){
    $message = ""; $ok = 1;
    $exist = $this->db->query("SELECT COUNT(1) count FROM users WHERE username = ? and is_active = 1 and user_id <> ?",array($username, $user_id))->row();
    if((int) $exist->count > 0){
      $message = "User with same user name exists";
      $ok = 0;
    }else{
      $sql = "UPDATE users
              SET
                  name      = ?,
                  address   = ?,
                  email     = ?,
                  username  = ?,
                  org_id    = ?,
                  role_id   = ?
              WHERE
                  user_id   = ?";
      $this->db->query($sql, array($name, $address, $email, $username, $org_id, $role_id, $user_id));
      if($password!==""){
        $sql = "UPDATE users
              SET
                  password  = ?
              WHERE
                  user_id   = ?";
        $this->db->query($sql, array(md5($password), $user_id));
      }
      $message = "User succesfully updated";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function delete_user($id){
    $message = ""; $ok = 1;
    $sql = "UPDATE users
            SET
                is_active = ?
            WHERE
                user_id   = ?";
    $result = $this->db->query($sql, array(0, $id));
    if($result>0){
      $message = "User succesfully deleted";
    }else{
      $message = "Failed to delete user";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }
}
