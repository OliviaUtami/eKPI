<?php
class period_model extends CI_Model {
  public function get_period(){
    $sql = "SELECT
              period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.name period_name, p.status, coalesce(d.name,'Belum Diset') draft, p.created_by, p.created_at, p.updated_by, p.updated_at
            FROM `period` p
            LEFT JOIN `draft` d on p.draft_id = d.draft_id 
            ORDER BY period_from DESC;";
    return $this->db->query($sql)->result();
  }

  public function get_period_by_id($period_id){
    $sql = "SELECT
              period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.name period_name, p.status, coalesce(d.name,'Belum Diset') draft, d.draft_id, p.created_by, p.created_at, p.updated_by, p.updated_at
            FROM `period` p
            LEFT JOIN `draft` d on p.draft_id = d.draft_id 
            WHERE period_id = ?
            ORDER BY p.period_from DESC;";
    return $this->db->query($sql,array($period_id))->row();
  }

  public function add_period($period_from, $period_to, $name, $status, $created, $draft_id){
    var_dump($period_from);
    try {
      $this->db->trans_start();
      $message = ""; $ok = 1;
      
      if($status=="Aktif"&&$draft_id==""){
        $ok = 0;
        $message = "Silahkan pilih Draft KPI untuk dapat mengaktifkan periode pengisian";
      }else if($status=="Aktif"){
        $draft_kembar = $this->db->query("SELECT COUNT(1) count FROM period WHERE draft_id = ? and status = 'Aktif'",array($draft_id))->row();
        if($draft_kembar!==null&&$draft_kembar->count>0){
          $ok = 0;
          $message = "Draft sudah pernah digunakan pada periode lain, silahkan gunakan draft lain untuk periode ini";
        }
      }
      if($ok==1){
        if($draft_id=="")
          $draft_id=null;
        $sql = "INSERT INTO period
                  (period_from, period_to, name, status, created_by, draft_id)
                VALUES (STR_TO_DATE(?, '%d/%m/%Y'), STR_TO_DATE(?, '%d/%m/%Y'), ?, ?, ?, ?)";
        $this->db->query($sql, array($period_from, $period_to, $name, $status, $created, $draft_id));
        $message = "Periode berhasil ditambahkan";
        
      }
      if($ok==1){
        $this->db->trans_commit();
      }else{
        $this->db->trans_rollback();
      }
      $data = (object) [
        "ok"      => $ok,
        "message" => $message
      ];
      return $data;
    }catch (Exception $e) {
      $this->db->trans_rollback();
      $data = (object) [
        "ok"      => $ok,
        "message" => '%s : %s : Transaction failed. Error no: %s, Error msg:%s', __CLASS__, __FUNCTION__, $e->getCode(), $e->getMessage()
      ];
     } 
  }

  public function edit_period($period_id, $period_from, $period_to, $name, $status, $draft_id){
    $message = ""; $ok = 1;
    if($status=="Aktif"&&$draft_id==""){
      $ok = 0;
      $message = "Silahkan pilih Draft KPI untuk dapat mengaktifkan periode pengisian";
    }else if($status=="Aktif"){
      $draft_kembar = $this->db->query("SELECT COUNT(1) count FROM period WHERE draft_id = ? and status = 'Aktif' and period_id <> ?",array($draft_id,$period_id))->row();
      if($draft_kembar!==null&&$draft_kembar->count>0){
        $ok = 0;
        $message = "Draft sudah pernah digunakan pada periode lain, silahkan gunakan draft lain untuk periode ini";
      }
    }
    if($ok==1){
      if($draft_id=="")
        $draft_id=null;
      $sql = "UPDATE period
              SET
                  period_from      = STR_TO_DATE(?, '%d/%m/%Y'),
                  period_to        = STR_TO_DATE(?, '%d/%m/%Y'),
                  name             = ?,
                  status           = ?,
                  draft_id         = ?
              WHERE
                  period_id          = ?";
      $this->db->query($sql, array($period_from, $period_to, $name, $status, $draft_id, $period_id));
      $message = "Periode berhasil diperbarui";
    }

    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

}
