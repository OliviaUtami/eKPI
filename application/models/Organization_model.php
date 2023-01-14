<?php
class organization_model extends CI_Model {
  public function get_organizations(){
    return $this->db->query("select * from organization where is_active = ? order by hierarchy asc", array(1))->result();
  }
  public function add_org($name, $hierarchy, $created){
    $message = ""; $ok = 1;
    $exist = $this->db->query("SELECT COUNT(1) count FROM organization WHERE hierarchy = ? and is_active = 1",array($hierarchy))->row();

    if((int) $exist->count > 0){
      $message = "Organization with same hierarchy exists";
    }else{
      $sql = "INSERT INTO organization
            (org_name, hierarchy, is_active, created_by)
            VALUES (?, ?, ?, ?)";
      $this->db->query($sql, array($name, $hierarchy, 1, $created));
      $message = "Organization succesfully added";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }
  public function edit_org($id, $name, $hierarchy){
    $message = ""; $ok = 1;
    $exist = $this->db->query("SELECT COUNT(1) count FROM organization WHERE hierarchy = ? and is_active = 1 and org_id <> ?",array($hierarchy, $id))->row();

    if((int) $exist->count > 0){
      $ok = 0;
      $message = "Organization with same hierarchy exists";
    }else{
      $sql = "UPDATE organization
              SET
                org_name = ?,
                hierarchy = ?
              WHERE org_id = ?";
      $this->db->query($sql, array($name, $hierarchy, $id));
      $message = "Organization succesfully updated";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }
  public function get_organization($id){
    $sql = "SELECT * FROM organization WHERE org_id = ?";
    return $this->db->query($sql,array($id))->row();
  }
  public function delete_org($id){
    $message = ""; $ok = 1;
    $sql = "UPDATE organization
            SET
                is_active = ?
            WHERE
                org_id   = ?";
    $result = $this->db->query($sql, array(0, $id));
    if($result>0){
      $message = "Organization succesfully deleted";
    }else{
      $message = "Failed to delete organization";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }
}
