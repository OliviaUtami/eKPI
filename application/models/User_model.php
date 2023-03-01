<?php
class user_model extends CI_Model {
  public function query_check_login($username,$password){
    $sql = "SELECT *
            FROM user u
            WHERE u.username = '$username' AND u.password='$password'";
    // echo("Hasil : ".$this->db->query($sql)->result());
    return $this->db->query($sql)->row();
  }

  public function get_menu($org_id, $role_id){
    $sql = "SELECT m.*  
            FROM access_menu a
            JOIN menu m ON a.menu_id = m.menu_id
            WHERE org_id = ? AND role_id = ?
            ORDER BY `order` ASC";
    $menu = $this->db->query($sql, array($org_id, $role_id))->result();
    return $menu;
  }

  public function add_user($name, $address, $email, $username, $password, $org_id, $role_id){
    $message = ""; $ok = 1;
    $exist = $this->db->query("SELECT COUNT(1) count FROM user WHERE username = ? and is_active = 1",array($username))->row();

    if((int) $exist->count > 0){
      $message = "User with same user name exists";
    }else{
      $sql = "INSERT INTO user
            (name, address, email, username, password, org_id, role_id, is_active, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $this->db->query($sql, array($name, $address, $email, $username, $password, $org_id, $role_id, 1, $_SESSION["username"]));
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
            FROM user u
            JOIN organization o on u.org_id = o.org_id
            JOIN role r on u.role_id = r.role_id
            WHERE u.is_active = ?
            ORDER BY u.name ASC";
    return $this->db->query($sql,array(1))->result();
  }

  public function get_user($id){
    $sql = "SELECT * FROM user WHERE user_id = ?";
    return $this->db->query($sql,array($id))->row();
  }

  public function get_user_by_username($username){
    $sql = "SELECT u.*,o.org_name FROM user u JOIN organization o ON u.org_id = o.org_id WHERE username = ?";
    return $this->db->query($sql,array($username))->row();
  }

  public function edit_user($user_id, $name, $address, $email, $username, $password, $org_id, $role_id){
    $message = ""; $ok = 1;
    $exist = $this->db->query("SELECT COUNT(1) count FROM user WHERE username = ? and is_active = 1 and user_id <> ?",array($username, $user_id))->row();
    if((int) $exist->count > 0){
      $message = "User with same user name exists";
      $ok = 0;
    }else{
      $sql = "UPDATE user
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
        $sql = "UPDATE user
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
    $sql = "UPDATE user
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

  public function get_notif($user_id, $read, $limit){
    $limitstr = "";
    if($limit){
      $limitstr = " limit 10 ";
    }
    if($read!==""){
      $sql = "select idnotification, userid, username, type, title, content, DATE_FORMAT(tstamp, '%d/%m/%Y %H:%i:%s') tstamp, byuser, isread, readon 
            from notification 
            where userid = ? and isread = ? order by tstamp desc ".$limitstr;
      $data = $this->db->query($sql, array($user_id,$read))->result();
    }else{
      $sql = "select idnotification, userid, username, type, title, content, tstamp, byuser, isread, readon 
            from notification 
            where userid = ? order by tstamp desc ".$limitstr;
      $data = $this->db->query($sql, array($user_id))->result();
    }
    return $data;
  }

  public function mark_as_read(){
    try {
      $this->db->trans_start();
      $this->db->query("update notification
                        set isread = 1, readon = now()
                        where userid = ? and isread = 0 and tstamp < now()", array($_SESSION["user_id"]));
      $this->db->trans_commit();
      
      $data = (object) [
        "ok"      => "ok",
        "message" => ""
      ];
      return $data;
    }catch (Exception $e) {
      $this->db->trans_rollback();
      $data = (object) [
        "ok"      => "not-ok",
        "message" => '%s : %s : Transaction failed. Error no: %s, Error msg:%s', __CLASS__, __FUNCTION__, $e->getCode(), $e->getMessage()
      ];
    } 
  }
}
