<?php
class kpi_model extends CI_Model {
  private $allowed = array("image/jpeg", "image/jpg", "image/png", "application/pdf", "application/msword", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.oasis.opendocument.spreadsheet","application/wps-office.doc");

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
    foreach ($data->details as $det){
      $sql = "SELECT file_id id, file_name filename, file_path file
              FROM indicator_user_det_file f
              WHERE ind_user_det_id = ? ORDER BY FILE_ID ASC ";
      $dokumen = $this->db->query($sql,array($det->ind_user_det_id))->result();
      $det->dokumen = $dokumen;
      if($det->tipe_indikator=="Pilihan Kustom"){
        $sql   = "SELECT custval_id, nama, nilai FROM indicator_custom_value WHERE ind_det_id = ? ORDER BY custval_id ASC";
        $pilihan = $this->db->query($sql,array($ind->ind_det_id))->result();
        $det->pilihan = $pilihan;
      }else{
        $det->pilihan = array();
      }
    }
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
        $existing = $this->db->query("SELECT ind_user_id FROM indicator_user WHERE user_id = ? and indicator_id = ?",array($user->user_id, $indikator_id))->row();        
        $ind_user_id = $existing->ind_user_id;
        $sql = "UPDATE indicator_user SET
                  updated_by = ?, 
                  updated_at = now(),
                  status = ?
                WHERE ind_user_id = ?";
        $this->db->query($sql, array($user->username, "Draft", $ind_user_id));

        $sql = "UPDATE indicator_user_detail SET
                  edit = 1
                WHERE ind_user_id = ?";
        $this->db->query($sql, array($ind_user_id));

        for($i=0;$i<count($indikator);$i++){
          $obj = $indikator[$i];
          if($obj->ind_user_det_id==-1){
            $sql = "INSERT INTO indicator_user_detail
                      (ind_user_id, ind_det_id, realisasi, target, nilai, created_by)
                    VALUES (?, ?, ?, ?, ?, ?)";

            //hitung nilai
            $nilai = $this->calc_nilai($obj->tipe_indikator, $obj->target_indikator, $obj->target_indikator_value, $obj->realisasi);

            $this->db->query($sql, array($ind_user_id, $obj->ind_det_id, $obj->realisasi, $obj->target_indikator, $nilai, $user->username));
            $ind_user_det_id = $this->db->insert_id();

          }else{
            $sql = "SELECT ind_user_det_id FROM indicator_user_detail
                    WHERE ind_det_id = ? and ind_user_id = ? ";
            $det = $this->db->query($sql, array($obj->ind_det_id, $ind_user_id))->row();
            $ind_user_det_id = $det->ind_user_det_id;
            $nilai = $this->calc_nilai($obj->tipe_indikator, $obj->target_indikator, $obj->target_indikator_value, $obj->realisasi);

            $sql = "UPDATE indicator_user_detail SET
                      edit = 0, 
                      realisasi = ?, 
                      target = ?, 
                      nilai = ?, 
                      updated_by = ?,
                      updated_at = now()
                    WHERE ind_user_det_id = ?";
            $this->db->query($sql, array($obj->realisasi, $obj->target_indikator, $nilai, $user->username, $ind_user_det_id));           
          }
          
          $sql = "UPDATE indicator_user_det_file SET
                    edit = 1
                  WHERE ind_user_det_id = ?";
          $this->db->query($sql, array($ind_user_det_id));

          foreach($obj->dokumen as $dokumen){
            if($dokumen->id==-1){
              $dirUpload = "assets/documents/";
              
              $filename = explode(".",$dokumen->filename);
              $filepath = $dirUpload.str_replace('.', ' ', $obj->kode_indikator."-".$user->user_id."-".uniqid());
              $terupload = file_put_contents($filepath,base64_decode($dokumen->file));
              $sql = "INSERT INTO indicator_user_det_file
                      (ind_user_det_id, file_name, file_path, created_by)
                      VALUES (?, ?, ?, ?) ";
              $this->db->query($sql, array($ind_user_det_id, $dokumen->filename, $filepath, $user->username));
            }else{
              $sql = "UPDATE indicator_user_det_file SET
                        edit = 0
                      WHERE file_id = ?";
              $this->db->query($sql, array($dokumen->id));
            }
          }
          $sql = "SELECT file_path FROM indicator_user_det_file WHERE edit = 1 and ind_user_det_id = ?";
          $delete = $this->db->query($sql, array($ind_user_det_id))->result();
          foreach($delete as $del){
            if(file_exists($del->file_path))
            {
                unlink($del->file_path);
            }
          }

          $sql = "DELETE FROM indicator_user_det_file WHERE edit = 1 and ind_user_det_id = ?";
          $this->db->query($sql, array($ind_user_det_id));
        }
        
        $sql = "DELETE FROM indicator_user_detail WHERE edit = 1 and ind_user_id = ?";
        $this->db->query($sql, array($ind_user_id));

        $message = "Indikator berhasil disimpan";
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
          $ind_user_det_id = $this->db->insert_id();

          foreach($obj->dokumen as $dokumen){
            $dirUpload = "assets/documents/";
            
            $filename = explode(".",$dokumen->filename);
            $filepath = $dirUpload.str_replace('.', ' ', $obj->kode_indikator."-".$user->user_id."-".uniqid());
            $terupload = file_put_contents($filepath,base64_decode($dokumen->file));
            $sql = "INSERT INTO indicator_user_det_file
                    (ind_user_det_id, file_name, file_path, created_by)
                    VALUES (?, ?, ?, ?) ";
            $this->db->query($sql, array($ind_user_det_id, $dokumen->filename, $filepath, $user->username));
          }
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