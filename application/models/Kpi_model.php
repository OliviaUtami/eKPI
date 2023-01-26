<?php
class kpi_model extends CI_Model {
  public function get_my_kpi($user_id){
    $sql = "SELECT 
              p.period_id, 
              DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              d.draft_id, i.indicator_id, i.org_id, 
              iu.ind_user_id, iu.user_id, coalesce(iu.status,'Belum Ada') status, 
              iu.remarks, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              u.user_id, u.username, o.org_name
            FROM period p
            JOIN draft d ON p.draft_id = d.draft_id
            JOIN indicator i ON i.draft_id = d.draft_id
            LEFT JOIN indicator_user iu ON i.indicator_id = iu.indicator_id
            JOIN user u ON i.org_id = u.org_id
            JOIN organization o ON u.org_id = o.org_id
            WHERE u.user_id = ? AND iu.ind_user_id IS NULL
            UNION
            SELECT 
              p.period_id, 
              DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              d.draft_id, i.indicator_id, i.org_id, 
              iu.ind_user_id, iu.user_id, coalesce(i.status,'Belum Ada') status, 
              iu.remarks, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              u.user_id, u.username, o.org_name
            FROM indicator_user iu
            JOIN indicator i ON iu.indicator_id = i.indicator_id
            JOIN draft d ON i.draft_id = d.draft_id
            JOIN period p  ON p.draft_id = d.draft_id
            JOIN user u ON iu.user_id = u.user_id
            JOIN organization o ON i.org_id = o.org_id
            WHERE u.user_id = ? 
            ORDER BY period_from DESC";
    $data = $this->db->query($sql, array($user_id,$user_id))->result();
    return $data;
  }

  public function get_kpi_indicator($indicator_id, $user){
    $sql = "SELECT 
              p.period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.draft_id, i.indicator_id, i.org_id, coalesce(iu.status,'Belum Ada') status, 
              iu.created_by, DATE_FORMAT(iu.created_at, '%d/%m/%Y %H:%i:%s') created_at
            FROM indicator i
            JOIN period p on p.draft_id = i.draft_id
            LEFT JOIN indicator_user iu ON i.indicator_id = iu.indicator_id AND iu.user_id = ?
            WHERE i.indicator_id = ? and i.status = 'Dipublikasi' ";
    $data = $this->db->query($sql,array($user->user_id, $indicator_id))->row();

    $sql = "SELECT 
                ud.ind_user_det_id, d.ind_det_id,
                t.code kode_sasaran, t.name nama_sasaran,
                d.kode kode_indikator, d.nama nama_indikator, d.satuan satuan_indikator, 
                d.target target_indikator, coalesce(cv.nilai, d.target) target_indikator_value,
                d.tipe tipe_indikator, realisasi
            FROM indicator h
            JOIN indicator_detail d ON h.indicator_id = d.indicator_id
            LEFT JOIN indicator_custom_value cv on d.ind_det_id = cv.ind_det_id
            JOIN program p ON d.program_id = p.program_id
            JOIN target t ON t.target_id = p.target_id
            JOIN (indicator_user_detail ud 
              JOIN indicator_user uh on ud.ind_user_id = uh.ind_user_id )
              ON d.ind_det_id = ud.ind_det_id and uh.user_id = ?
            WHERE h.status = 'Dipublikasi' AND h.org_id = ? AND h.indicator_id = ?
            ORDER BY d.kode ASC ";
    $data->details = $this->db->query($sql, array($user->user_id,$user->org_id,$indicator_id))->result();
    return $data;
  }


  public function get_indicator($indicator_id){
    $data = new stdClass();
    $sql = "SELECT 
                t.target_id, t.code kode_sasaran, t.name nama_sasaran, 
                h.indicator_id, 
                d.ind_det_id id, CONCAT(d.kode,' - ',d.nama) text,
                d.ind_det_id, d.kode kode_indikator, d.nama nama_indikator, d.satuan satuan_indikator, 
                d.target target_indikator, d.tipe tipe_indikator
            FROM indicator h
            JOIN indicator_detail d ON h.indicator_id = d.indicator_id
            JOIN program p ON d.program_id = p.program_id
            JOIN target t ON t.target_id = p.target_id
            WHERE h.status = 'Dipublikasi' AND h.indicator_id = ?
            ORDER BY d.kode ASC ";
    $indicator = $this->db->query($sql,array($indicator_id))->result();
    foreach($indicator as $ind){
      if($ind->tipe_indikator=="Pilihan Kustom"){
        $sql   = "SELECT custval_id, nama, nilai FROM indicator_custom_value WHERE ind_det_id = ? ORDER BY custval_id ASC";
        $pilihan = $this->db->query($sql,array($ind->ind_det_id))->result();
        $ind->pilihan = $pilihan;
        $sql   = "SELECT nilai FROM indicator_custom_value WHERE ind_det_id = ? AND nama = ?";
        $pilihan = $this->db->query($sql,array($ind->ind_det_id, $ind->target_indikator))->row();
        $ind->target_indikator_val = $pilihan->nilai;
      }else{
        $ind->pilihan = array();
        $ind->target_indikator_val = $ind->target_indikator;
      }
    }
    $data->ok = 1;
    $data->indicator = $indicator;
    return $data;
  }

  public function add_kpi($user,$reqdata){
    try {
      $this->db->trans_start();
      $message = ""; $ok = 1;
      $indikator = $reqdata->data;
      $indikator_id = $reqdata->id;
      $used = $this->db->query("SELECT count(1) cnt FROM indicator_user WHERE user_id = ? and indicator_id = ?",array($user->user_id, $indikator_id))->row();
      if($used->cnt>0){//update
        $existing = $this->db->query("SELECT indicator_id FROM indicator_user WHERE user_id = ? and indicator_id = ?",array($user->user_id, $indikator_id))->row();
        
        $message = "Indikator berhasil disimpanss";
      }else{
        $sql = "INSERT INTO indicator_user 
                  (indicator_id, user_id, created_by, status)
                VALUES (?, ?, ?, ?)";
        $this->db->query($sql, array($indikator_id, $user->user_id, $user->username, "Draft"));
        $ind_user_id = $this->db->insert_id();
        for($i=0;$i<count($indikator);$i++){
          $obj = $indikator[$i];
          $sql = "INSERT INTO indicator_user_detail
                    (ind_user_id, ind_det_id, realisasi, target, nilai, created_by)
                  VALUES (?, ?, ?, ?, ?, ?)";

          //hitung nilai
          $nilai = $this->calc_nilai($obj->tipe_indikator, $obj->target_indikator, $obj->target_indikator_value, $obj->realisasi);

          $this->db->query($sql, array($ind_user_id, $obj->ind_det_id, $obj->realisasi, $obj->target_indikator, $nilai, $user->username));
          $in = $this->db->insert_id();
        }
        $message = "KPI berhasil disimpan";
      }
      $data = (object) [
        "ok"      => $ok,
        "message" => $message
      ];
      $this->db->trans_commit();
      return $data;
    }catch (Exception $e) {
      $this->db->trans_rollback();
      $data = (object) [
        "ok"      => $ok,
        "message" => '%s : %s : DB transaction failed. Error no: %s, Error msg:%s, Last query: %s', __CLASS__, __FUNCTION__, $e->getCode(), $e->getMessage(), print_r($this->main_db->last_query(), TRUE)
      ];
     } 
  }

  function calc_nilai($tipe, $target, $target_val, $realisasi){
    if($realisasi==null||$realisasi==""){
      return 0;
    }else{
      if($tipe=="Persentase"||$tipe=="Angka"){
        $nilai = round((float) ($realisasi/$target_val)*100,2);
      }else if($tipe=="Batas Persentase"||$tipe=="Batas Angka"){
        $nilai = round((float) ($target_val-$realisasi)/$target_val*100 ,2);
      }else if($tipe=="Pilihan Kustom"){
        $nilai = (int) ($realisasi);
      }
      return $nilai;
    }
  }
}