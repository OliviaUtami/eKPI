<?php
class period_model extends CI_Model {
  public function get_period(){
    $sql = "SELECT
              period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, status, created_by, created_at, updated_by, updated_at
            FROM `period`
            ORDER BY period_from DESC;";
    return $this->db->query($sql)->result();
  }

  public function get_period_by_id($period_id){
    $sql = "SELECT
              period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, status, created_by, created_at, updated_by, updated_at
            FROM `period`
            WHERE period_id = ?
            ORDER BY period_from DESC;";
    return $this->db->query($sql,array($period_id))->row();
  }

  public function add_period($period_from, $period_to, $status, $created){
    $message = ""; $ok = 1;
    //$exist = $this->db->query("SELECT COUNT(1) count FROM period WHERE period_from = ? and is_active = 1",array($username))->row();
    //if((int) $exist->count > 0){
    if(1==0){
      $message = "User with same user name exists";
    }else{
      $sql = "INSERT INTO period
                (period_from, period_to, status, created_by)
              VALUES (STR_TO_DATE(?, '%d/%m/%Y'), STR_TO_DATE(?, '%d/%m/%Y'), ?, ?)";
      $this->db->query($sql, array($period_from, $period_to, $status, $created));
      $message = "Period succesfully added".$period_from.$period_to;
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function edit_period($period_id,$period_from, $period_to, $status){
    $message = ""; $ok = 1;
    //$exist = $this->db->query("SELECT COUNT(1) count FROM period WHERE period_from = ? and is_active = 1",array($username))->row();
    //if((int) $exist->count > 0){
    if(1==0){
      $message = "User with same user name exists";
    }else{
      $sql = "UPDATE period
              SET
                  period_from      = STR_TO_DATE(?, '%d/%m/%Y'),
                  period_to        = STR_TO_DATE(?, '%d/%m/%Y'),
                  status           = ?
              WHERE
                  period_id          = ?";
      $this->db->query($sql, array($period_from, $period_to, $status, $period_id));
      $message = "Period succesfully updated";
    }

    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

}
