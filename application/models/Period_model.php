<?php
class period_model extends CI_Model {
  public function get_period(){
    $sql = "SELECT
              period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, p.status, coalesce(d.name,'Belum Diset') draft, p.created_by, p.created_at, p.updated_by, p.updated_at
            FROM `period` p
            LEFT JOIN `draft` d on p.draft_id = d.draft_id 
            ORDER BY period_from DESC;";
    return $this->db->query($sql)->result();
  }

  public function get_period_by_id($period_id){
    $sql = "SELECT
              period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, p.status, coalesce(d.name,'Belum Diset') draft, d.draft_id, p.created_by, p.created_at, p.updated_by, p.updated_at
            FROM `period` p
            LEFT JOIN `draft` d on p.draft_id = d.draft_id 
            WHERE period_id = ?
            ORDER BY p.period_from DESC;";
    return $this->db->query($sql,array($period_id))->row();
  }

  public function add_period($period_from, $period_to, $status, $created, $draft_id){
    $message = ""; $ok = 1;
    //$exist = $this->db->query("SELECT COUNT(1) count FROM period WHERE period_from = ? and is_active = 1",array($username))->row();
    //if((int) $exist->count > 0){
    if($status=="Aktif"&&$draft_id==""){
      $message = "Silahkan pilih Draft KPI untuk dapat mengaktifkan periode pengisian";
    }else{
      if($draft_id=="")
        $draft_id=null;
      $sql = "INSERT INTO period
                (period_from, period_to, status, created_by, draft_id)
              VALUES (STR_TO_DATE(?, '%d/%m/%Y'), STR_TO_DATE(?, '%d/%m/%Y'), ?, ?, ?)";
      $this->db->query($sql, array($period_from, $period_to, $status, $created, $draft_id));
      $message = "Periode berhasil ditambahkan";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function edit_period($period_id,$period_from, $period_to, $status, $draft_id){
    $message = ""; $ok = 1;
    //$exist = $this->db->query("SELECT COUNT(1) count FROM period WHERE period_from = ? and is_active = 1",array($username))->row();
    //if((int) $exist->count > 0){
    if($status=="Aktif"&&$draft_id==""){
      $message = "Silahkan pilih Draft KPI untuk dapat mengaktifkan periode pengisian";
    }else{
      if($draft_id=="")
        $draft_id=null;
      $sql = "UPDATE period
              SET
                  period_from      = STR_TO_DATE(?, '%d/%m/%Y'),
                  period_to        = STR_TO_DATE(?, '%d/%m/%Y'),
                  status           = ?,
                  draft_id         = ?
              WHERE
                  period_id          = ?";
      $this->db->query($sql, array($period_from, $period_to, $status, $draft_id, $period_id));
      $message = "Periode berhasil diperbarui";
    }

    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

}
