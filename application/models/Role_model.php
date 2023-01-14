<?php
class role_model extends CI_Model {
  public function get_roles(){
    return $this->db->query("select * from role where is_active = ?", array(1))->result();
  }
}
