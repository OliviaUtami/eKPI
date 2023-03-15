<?php
class kpi_model extends CI_Model {
  private $allowed = array("image/jpeg", "image/jpg", "image/png", "application/pdf", "application/msword", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.oasis.opendocument.spreadsheet","application/wps-office.doc");

  public function get_my_kpi($user_id){
    $sql = "SELECT 
              p.period_id, p.name period_name,
              DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              d.draft_id, i.indicator_id, i.org_id, 
              iu.ind_user_id, iu.user_id, coalesce(iu.status,'Belum Ada') status, 
              iu.remarks, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              u.user_id, u.username, o.org_name, iu.uid
            FROM period p
            JOIN draft d ON p.draft_id = d.draft_id
            JOIN indicator i ON i.draft_id = d.draft_id and i.status = 'Disetujui'
            JOIN user u ON i.org_id = u.org_id
            JOIN organization o ON u.org_id = o.org_id
            LEFT JOIN indicator_user iu ON i.indicator_id = iu.indicator_id and u.user_id = iu.user_id
            WHERE u.user_id = ? and iu.ind_user_id is null and p.status = 'Aktif'
            UNION
            SELECT 
              p.period_id, p.name period_name,
              DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              d.draft_id, i.indicator_id, i.org_id, 
              iu.ind_user_id, iu.user_id, coalesce(iu.status,'Belum Ada') status, 
              iu.remarks, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              u.user_id, u.username, o.org_name, iu.uid
            FROM indicator_user iu
            JOIN indicator i ON iu.indicator_id = i.indicator_id and i.status = 'Disetujui'
            JOIN draft d ON i.draft_id = d.draft_id
            JOIN period p  ON p.draft_id = d.draft_id
            JOIN user u ON iu.user_id = u.user_id
            JOIN organization o ON i.org_id = o.org_id
            WHERE u.user_id = ? and p.status = 'Aktif'
            ORDER BY period_from DESC";
    $data = $this->db->query($sql, array($user_id,$user_id))->result();
    return $data;
  }

  public function get_kpi_indicator($indicator_id, $user){
    $sql = "SELECT 
              p.name period_name,
              p.period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.draft_id, i.indicator_id, i.org_id, coalesce(iu.status,'Belum Ada') status, 
              iu.created_by, DATE_FORMAT(iu.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              o.org_name, u.name, iu.remarks, iu.uid
            FROM indicator i
            JOIN period p on p.draft_id = i.draft_id
            JOIN organization o on i.org_id = o.org_id
            LEFT JOIN indicator_user iu ON i.indicator_id = iu.indicator_id AND iu.user_id = ?
            LEFT JOIN user u on iu.user_id = u.user_id
            WHERE i.indicator_id = ? and i.status = 'Disetujui' ";
    $data = $this->db->query($sql,array($user->user_id, $indicator_id))->row();

    $sql = "SELECT 
                ud.ind_user_det_id, d.ind_det_id,
                t.code kode_sasaran, t.name nama_sasaran,
                d.kode kode_indikator, d.nama nama_indikator, d.satuan satuan_indikator, 
                d.target target_indikator, coalesce(cv.nilai, d.target) target_indikator_value,
                d.tipe tipe_indikator, realisasi
            FROM indicator h
            JOIN indicator_detail d ON h.indicator_id = d.indicator_id
            LEFT JOIN indicator_custom_value cv on d.ind_det_id = cv.ind_det_id and d.target = cv.nilai
            JOIN program p ON d.program_id = p.program_id
            JOIN target t ON t.target_id = p.target_id
            JOIN (indicator_user_detail ud 
              JOIN indicator_user uh on ud.ind_user_id = uh.ind_user_id )
              ON d.ind_det_id = ud.ind_det_id and uh.user_id = ?
            WHERE h.status = 'Disetujui' AND h.org_id = ? AND h.indicator_id = ?
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
        $pilihan = $this->db->query($sql,array($det->ind_det_id))->result();
        $det->pilihan = $pilihan;
      }else{
        $det->pilihan = array();
      }
    }
    return $data;
  }

  public function get_kpi_indicator_by_uid($uid){
    $sql = "SELECT 
              p.name period_name,
              p.period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.draft_id, i.indicator_id, i.org_id, coalesce(iu.status,'Belum Ada') status, 
              iu.created_by, DATE_FORMAT(iu.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              o.org_name, u.name, iu.remarks, iu.ind_user_id, iu.uid
            FROM indicator i
            JOIN period p on p.draft_id = i.draft_id
            JOIN organization o on i.org_id = o.org_id
            JOIN indicator_user iu ON i.indicator_id = iu.indicator_id
            JOIN user u on iu.user_id = u.user_id
            WHERE i.status = 'Disetujui'  AND iu.uid = ?";
    $data = $this->db->query($sql,array($uid))->row();
    //var_dump($data);
    $sql = "SELECT 
                ud.ind_user_det_id, d.ind_det_id,
                t.code kode_sasaran, t.name nama_sasaran,
                d.kode kode_indikator, d.nama nama_indikator, d.satuan satuan_indikator, 
                d.target target_indikator, coalesce(cv.nilai, d.target) target_indikator_value,
                d.tipe tipe_indikator, realisasi
            FROM indicator h
            JOIN indicator_detail d ON h.indicator_id = d.indicator_id
            LEFT JOIN indicator_custom_value cv on d.ind_det_id = cv.ind_det_id and d.target = cv.nilai
            JOIN program p ON d.program_id = p.program_id
            JOIN target t ON t.target_id = p.target_id
            JOIN (indicator_user_detail ud 
              JOIN indicator_user uh on ud.ind_user_id = uh.ind_user_id )
              ON d.ind_det_id = ud.ind_det_id 
            WHERE h.status = 'Disetujui' and uh.uid = ?
            ORDER BY d.kode ASC ";
    $data->details = $this->db->query($sql, array($uid))->result();
    foreach ($data->details as $det){
      $sql = "SELECT file_id id, file_name filename, file_path file
              FROM indicator_user_det_file f
              WHERE ind_user_det_id = ? ORDER BY FILE_ID ASC ";
      $dokumen = $this->db->query($sql,array($det->ind_user_det_id))->result();
      $det->dokumen = $dokumen;
      if($det->tipe_indikator=="Pilihan Kustom"){
        $sql   = "SELECT custval_id, nama, nilai FROM indicator_custom_value WHERE ind_det_id = ? ORDER BY custval_id ASC";
        $pilihan = $this->db->query($sql,array($det->ind_det_id))->result();
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
            WHERE h.status = 'Disetujui' AND h.indicator_id = ?
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
      //var_dump($indikator_id);
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

        $message = "KPI berhasil disimpan";
      }else{
        $sql = "INSERT INTO indicator_user 
                  (indicator_id, user_id, created_by, status, uid, user_role_id)
                VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, array($indikator_id, $user->user_id, $user->username, "Draft", bin2hex(random_bytes(20)), $_SESSION["role_id"]));
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

  public function submit_kpi($uid){
    try {
      $this->db->trans_start();
      $message = ""; $ok = 1;
      $kpi = $this->db->query("SELECT ind_user_id 
                              FROM indicator_user 
                              WHERE uid = ? ", array($uid))->row();
      $outstanding = $this->db->query(
                      "SELECT count(1) count 
                      FROM indicator_user_detail 
                      WHERE ind_user_id = ? AND (realisasi IS NULL OR realisasi = '') ", array($kpi->ind_user_id))->row();

      $total_det = $this->db->query(
                      "SELECT count(1) count 
                      FROM indicator_user_detail 
                      WHERE ind_user_id = ?", array($kpi->ind_user_id))->row();
      
      if($total_det->count==0){//update
        $ok = 0; $message = "Gagal mengirimkan KPI.\\nKPI tidak memiliki detail. Silahkan tambahkan detail terlebih dahulu.";
      }else if($outstanding->count>0){
        $ok = 0; $message = "Gagal mengirimkan KPI.\\nAda nilai realisasi yang belum diisi.";
      }else{
        $this->db->query("UPDATE indicator_user
                          SET status = 'Dikirimkan'
                          WHERE uid = ?", array($uid));
        $res = $this->db->query("select name from indicator_user k
                                join user u on k.user_id = u.user_id
                                where k.uid = ?", array($uid))->row();
        $title = "Pengumpulan KPI oleh ".$res->name;
        $content = "User ".$res->name." telah mengumpulkan KPI pada ". (new \DateTime())->format('d/m/Y H:i:s').". Silahkan melakukan pengecekan KPI pada menu yang tersedia. ";
        $this->db->query("INSERT INTO notification (userid, username, type, title, content, tstamp, byuser) 
                          select u.user_id, u.username, 'KPI_SUBMISSION', ?, ?, ?, ? 
                          from user u 
                          join organization o on u.org_id = o.org_id
                          where u.org_id = 23 and u.is_active = 1", array($title, $content, date('Y-m-d H:i:s'), $_SESSION["username"]));      
        $message = "KPI berhasil dikirimkan ke HROD untuk diperiksa.";
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

  public function get_check_kpi_list(){
    $sql = "SELECT 
              p.period_id, 
              DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              d.draft_id, i.indicator_id, i.org_id, 
              iu.ind_user_id, iu.user_id, coalesce(iu.status,'Belum Ada') status, 
              iu.remarks, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              u.user_id, u.username, u.name, o.org_name, iu.uid
            FROM indicator_user iu
            JOIN indicator i ON iu.indicator_id = i.indicator_id and i.status = 'Disetujui'
            JOIN draft d ON i.draft_id = d.draft_id
            JOIN period p  ON p.draft_id = d.draft_id
            JOIN user u ON iu.user_id = u.user_id
            JOIN organization o ON i.org_id = o.org_id
            WHERE iu.status in ('Dikirimkan')
            ORDER BY period_from DESC";
    $data = $this->db->query($sql)->result();
    return $data;
  }

  public function get_all_kpi_list($period_id=0, $org_id=0){
    $data = new stdClass();
    $sqlWhere = "WHERE 1=1 ";
    if($period_id!==null){
      $sqlWhere = $sqlWhere." AND p.period_id = ".$period_id;
    }
    if($period_id!==null){
      $sqlWhere = $sqlWhere." AND o.org_id = ".$org_id;
    }
    $sql = "SELECT 
              p.period_id, 
              DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              d.draft_id, i.indicator_id, i.org_id, 
              iu.ind_user_id, iu.user_id, coalesce(iu.status,'Belum Ada') status, 
              iu.remarks, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              u.user_id, u.username, u.name, o.org_name, iu.uid
            FROM indicator_user iu
            JOIN indicator i ON iu.indicator_id = i.indicator_id and i.status = 'Disetujui'
            JOIN draft d ON i.draft_id = d.draft_id
            JOIN period p  ON p.draft_id = d.draft_id
            JOIN user u ON iu.user_id = u.user_id
            JOIN organization o ON i.org_id = o.org_id ".
            $sqlWhere." ".
            "ORDER BY period_from DESC";
    $data->ok = 1;
    $data->records = $this->db->query($sql)->result();
    return $data;
  }

  public function process_kpi($ind_user_id, $action, $remarks){
    $message = ""; $ok = 1;
    $res = $this->db->query("select name, status from indicator_user k
                                join user u on k.user_id = u.user_id
                                where k.ind_user_id = ?", array($ind_user_id))->row();
    $user = $this->db->query("select name from user u where username = ?", array($_SESSION["username"]))->row();

    if($res->status=="Dikirimkan"){
      if($action=="approve"){
        $status = "Disetujui";
        $approved_by = $_SESSION["username"];
        $approved_at = date('Y-m-d H:i:s');
        $type = "APPROVE_KPI";
        $title = "KPI anda telah disetujui";
        $content = "User ".$user->name." telah menyetujui KPI Anda pada ". (new \DateTime())->format('d/m/Y H:i:s').".";
      }else{
        $status = "Menunggu Revisi";
        $approved_by = null;
        $approved_at = null;
        $type = "REJECT_KPI";
        $title = "KPI anda telah ditolak";
        $content = "User ".$user->name." telah menolak KPI Anda pada ". (new \DateTime())->format('d/m/Y H:i:s').". Silahkan mengecek kembali dan merevisi KPI Anda pada menu \"KPI Saya\".";
      }
      if($remarks=="")
        $remarks = "null";
      $sql = "UPDATE indicator_user 
                SET
                  updated_by = ?,
                  updated_at = now(),
                  status = ?,
                  approved_by = ?,
                  approved_at = ?,
                  remarks = ?
              WHERE ind_user_id = ?";
      $this->db->query($sql, array($_SESSION["username"], $status, $approved_by, $approved_at, $remarks, $ind_user_id));
      
      $this->db->query("INSERT INTO notification (userid, username, type, title, content, tstamp, byuser) 
                        select u.user_id, u.username, ?, ?, ?, ?, ? 
                        from indicator_user k
                        join user u on k.user_id = u.user_id
                        where ind_user_id = ?", 
                        array($type, $title, $content, date('Y-m-d H:i:s'), $_SESSION["username"],$ind_user_id));    
    }else{
      $message = "Gagal melakukan persetujuan. Status KPI ".$res->status."";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }
}