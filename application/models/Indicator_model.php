<?php
class indicator_model extends CI_Model {
  public function get_org_active_period_indicators($org_id){
    $sql = "SELECT 
              p.period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.draft_id, i.indicator_id, i.org_id, coalesce(i.status,'Belum Ada') status, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at
            FROM period p 
            LEFT JOIN indicator i ON p.draft_id = i.draft_id and i.org_id = ?
            WHERE DATE(SYSDATE()) BETWEEN p.period_from AND p.period_to AND p.status = 'Aktif' ";
    $data = $this->db->query($sql, array($org_id))->result();
    return $data;
  }
  public function get_indicator_by_draft_org($draft_id, $org_id){
    $sql = "SELECT 
              p.period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.draft_id, i.indicator_id, i.org_id, coalesce(i.status,'Belum Ada') status, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              i.remarks
            FROM period p 
            LEFT JOIN indicator i ON p.draft_id = i.draft_id AND i.org_id = ?
            WHERE DATE(SYSDATE()) BETWEEN p.period_from AND p.period_to AND p.status = 'Aktif' AND p.draft_id = ? ";
    $data = $this->db->query($sql,array($org_id, $draft_id))->row();
    
    $sqlTarget = "SELECT 
                  t.target_id id, t.code, t.name nama
                FROM `target` t
                JOIN `purpose` p ON  t.purpose_id = p.purpose_id
                JOIN `mission` m on p.mission_id = m.mission_id
                LEFT JOIN `indicator` i on m.draft_id = i.draft_id
                WHERE m.draft_id = ?
                ORDER BY t.target_id ASC";
    $targets = $this->db->query($sqlTarget,array($draft_id))->result();
    $tempid = 1;
    foreach($targets as $target){
      $sql   = "SELECT 
                    d.ind_det_id, d.nama indikator, d.kode indikator_kode,
                    d.program_id, p.code program_kode, d.satuan, d.target,
                    p.target_id, d.tipe
                FROM indicator_detail d
                JOIN indicator h on d.indicator_id = h.indicator_id
                JOIN program p on d.program_id = p.program_id
                WHERE h.draft_id = ? and h.org_id = ? and p.target_id = ?
                ORDER BY d.kode ASC";
      $indikator = $this->db->query($sql,array($draft_id, $org_id, $target->id))->result();
      foreach($indikator as $ind){
        $ind->tempid = $tempid;
        if($ind->tipe=="Pilihan Kustom"){
          $sql   = "SELECT custval_id, nama, nilai FROM indicator_custom_value WHERE ind_det_id = ? ORDER BY custval_id ASC";
          $pilihan = $this->db->query($sql,array($ind->ind_det_id))->result();
          $ind->pilihan = $pilihan;
        }else{
          $ind->pilihan = array();
        }
        $tempid++;
      }
      $target->indicators = $indikator;
    }
    $data->targets = $targets;
    
    return $data;
  }

  public function get_program_by_target($target_id){
    $sql = "SELECT 
              t.target_id id, t.code kode, t.name nama
            FROM `target` t
            WHERE t.target_id = ?
            ORDER BY t.target_id ASC";
    $data = new stdClass();
    $data = $this->db->query($sql,array($target_id))->row();
    $data-> ok = 1;
    $sql = "SELECT 
              p.program_id id, p.code kode, p.name nama
            FROM `program` p
            WHERE p.target_id = ?
            ORDER BY p.program_id ASC";
    $data->program =  $this->db->query($sql,array($target_id))->result();
    return $data;
  }

  public function add_draft($draft_name, $arrmission){
    $message = ""; $ok = 1;
    //$exist = $this->db->query("SELECT COUNT(1) count FROM period WHERE period_from = ? and is_active = 1",array($username))->row();
    //if((int) $exist->count > 0){
    if(1==0){
      $message = "User with same user name exists";
    }else{
      $sql = "INSERT INTO draft 
                (name, created_by, status)
              VALUES (?, ?, ?)";
      $this->db->query($sql, array($draft_name, $_SESSION["username"], 'DRAFT'));
      $draft_id = $this->db->insert_id();
      for ($i=0; $i < count($arrmission); $i++) { 
        $sql = "INSERT INTO mission 
                  (draft_id, name, created_by, status)
                VALUES (?, ?, ?, ?)";
        $this->db->query($sql, array($draft_id, $arrmission[$i]->nama, $_SESSION["username"], 'DRAFT'));
        $mission_id = $this->db->insert_id();
        $arrtujuan = $arrmission[$i]->tujuan;
        for ($j=0; $j < count($arrtujuan); $j++) { 
          $sql = "INSERT INTO purpose 
                    (mission_id, name, created_by, status)
                  VALUES (?, ?, ?, ?)";
          $this->db->query($sql, array($mission_id, $arrtujuan[$j]->nama, $_SESSION["username"], 'DRAFT'));
          $purpose_id = $this->db->insert_id();
          $arrtarget = $arrtujuan[$j]->target;
          for ($k=0; $k < count($arrtarget); $k++) { 
            $sql = "INSERT INTO target 
                      (purpose_id, name, created_by, status)
                    VALUES (?, ?, ?, ?)";
            $this->db->query($sql, array($purpose_id, $arrtarget[$k]->nama, $_SESSION["username"], 'DRAFT'));
            $target_id = $this->db->insert_id();
            $arrprogram = $arrtarget[$k]->program;
            for ($l=0; $l < count($arrprogram); $l++) { 
              $sql = "INSERT INTO program 
                        (target_id, name, created_by, status)
                      VALUES (?, ?, ?, ?)";
              $this->db->query($sql, array($target_id, $arrprogram[$k]->nama, $_SESSION["username"], 'DRAFT'));
            }
          }
        }
      }
      $message = "Draft succesfully added";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function edit_draft($draft_id, $draft_name, $arrmission){
    $message = ""; $ok = 1;
    //$exist = $this->db->query("SELECT COUNT(1) count FROM period WHERE period_from = ? and is_active = 1",array($username))->row();
    //if((int) $exist->count > 0){
    if(1==0){
      $message = "User with same user name exists";
    }else{
      $sql = "UPDATE draft 
                SET
                  name = ?, 
                  updated_by = ?,
                  updated_at = now(),
                  status = ?
              WHERE draft_id = ?";
      $this->db->query($sql, array($draft_name, $_SESSION["username"], 'DRAFT', $draft_id));
      $sql = "DELETE FROM mission 
              WHERE draft_id = ?";
      $this->db->query($sql, array($draft_id));

      for ($i=0; $i < count($arrmission); $i++) { 
        $sql = "INSERT INTO mission 
                  (draft_id, name, created_by, status)
                VALUES (?, ?, ?, ?)";
        $this->db->query($sql, array($draft_id, $arrmission[$i]->nama, $_SESSION["username"], 'DRAFT'));
        $mission_id = $this->db->insert_id();
        $arrtujuan = $arrmission[$i]->tujuan;
        for ($j=0; $j < count($arrtujuan); $j++) { 
          $sql = "INSERT INTO purpose 
                    (mission_id, name, created_by, status)
                  VALUES (?, ?, ?, ?)";
          $this->db->query($sql, array($mission_id, $arrtujuan[$j]->nama, $_SESSION["username"], 'DRAFT'));
          $purpose_id = $this->db->insert_id();
          $arrtarget = $arrtujuan[$j]->target;
          for ($k=0; $k < count($arrtarget); $k++) { 
            $sql = "INSERT INTO target 
                      (purpose_id, name, created_by, status)
                    VALUES (?, ?, ?, ?)";
            $this->db->query($sql, array($purpose_id, $arrtarget[$k]->nama, $_SESSION["username"], 'DRAFT'));
            $target_id = $this->db->insert_id();
            $arrprogram = $arrtarget[$k]->program;
            for ($l=0; $l < count($arrprogram); $l++) { 
              $sql = "INSERT INTO program 
                        (target_id, name, created_by, status)
                      VALUES (?, ?, ?, ?)";
              $this->db->query($sql, array($target_id, $arrprogram[$l]->nama, $_SESSION["username"], 'DRAFT'));
            }
          }
        }
      }
      $message = "Draft succesfully updated";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function add_indicator($user,$reqdata){
    $message = ""; $ok = 1;
    $indikator = $reqdata->indikator;
    $draft_id = $reqdata->draft_id;
    $used = $this->db->query("SELECT count(1) cnt FROM indicator WHERE org_id = ? and draft_id = ?",array($user->org_id, $draft_id))->row();
    if($used->cnt>0){//update
      $existing = $this->db->query("SELECT indicator_id FROM indicator WHERE org_id = ? and draft_id = ?",array($user->org_id, $draft_id))->row();
      $indikator_id = $existing->indicator_id;
      $sql = "UPDATE indicator 
              SET 
                status = ?,
                updated_by = ?,
                updated_at = NOW()
              WHERE org_id = ? AND draft_id = ?";
      $this->db->query($sql, array("Draft", $user->username, $user->org_id, $draft_id));
      $this->db->query("UPDATE indicator_detail
                        SET
                          edit = 1
                        WHERE indicator_id = ? ",array($indikator_id));
      for($i=0;$i<count($indikator);$i++){
        $det = $indikator[$i]->details;
        for($j=0;$j<count($det);$j++){
          $obj = $det[$j];
          $ind_det_id = (int) $obj->ind_det_id;
          if($ind_det_id==-1){
            $sql = "INSERT INTO indicator_detail
                      (indicator_id, program_id, kode, nama, satuan, target, tipe, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $this->db->query($sql, array($indikator_id, $obj->program_id, $obj->indikator_kode, $obj->indikator, $obj->satuan, $obj->target, $obj->tipe, $user->username));
            $ind_det_id = $this->db->insert_id();
            if($obj->tipe=="Pilihan Kustom"){
              for($k=0;$k<count($obj->pilihan);$k++){
                $sql = "INSERT INTO indicator_custom_value
                          (ind_det_id, nama, nilai)
                        VALUES (?, ?, ?)";
                $this->db->query($sql,array($ind_det_id, $obj->pilihan[$k]->nama, $obj->pilihan[$k]->nilai));
              }
            }
          }else{
            $sql = "UPDATE indicator_detail
                    SET
                      program_id = ?, 
                      kode = ?, 
                      nama = ?, 
                      satuan = ?, 
                      target = ?, 
                      tipe = ?, 
                      updated_by = ?,
                      updated_at = NOW(),
                      edit = 0
                    WHERE ind_det_id = ? ";
            $this->db->query($sql, array($obj->program_id, $obj->indikator_kode, $obj->indikator, $obj->satuan, $obj->target, $obj->tipe, $user->username, $ind_det_id));
            if($obj->tipe=="Pilihan Kustom"){
              $sql = "UPDATE indicator_custom_value
                      SET 
                        edit = 1
                      WHERE ind_det_id = ?";
              $this->db->query($sql,array($ind_det_id));
              $this->db->query("DELETE FROM indicator_custom_value WHERE ind_det_id = ? and edit = 1", array($ind_det_id));
              for($k=0;$k<count($obj->pilihan);$k++){
                if((int) $obj->pilihan[$k]->custval_id ==-1){
                  $sql = "INSERT INTO indicator_custom_value
                            (ind_det_id, nama, nilai)
                          VALUES (?, ?, ?)";
                  $this->db->query($sql,array($ind_det_id, $obj->pilihan[$k]->nama, $obj->pilihan[$k]->nilai));
                }else{
                  $sql = "UPDATE indicator_custom_value
                          SET 
                            nama = ?, 
                            nilai = ?
                          WHERE custval_id = ?";
                  $this->db->query($sql,array($$obj->pilihan[$k]->nama, $obj->pilihan[$k]->nilai, $obj->pilihan[$k]->custval_id));
                }
              }
            }else{
              $sql = "DELETE FROM indicator_custom_value
                        WHERE ind_det_id = ?";
              $this->db->query($sql,array($ind_det_id));
            }
          }
        }
      }  
      $this->db->query("DELETE FROM indicator_detail WHERE indicator_id = ? and edit = 1", array($indikator_id));  
      $message = "Indikator berhasil disimpan";
    }else{
      $sql = "INSERT INTO indicator 
                (draft_id, org_id, created_by, status)
              VALUES (?, ?, ?, ?)";
      $this->db->query($sql, array($draft_id, $user->org_id, $user->username, "Draft"));
      $indikator_id = $this->db->insert_id();
      for($i=0;$i<count($indikator);$i++){
        $det = $indikator[$i]->details;
        for($j=0;$j<count($det);$j++){
          $obj = $det[$j];
          $sql = "INSERT INTO indicator_detail
                    (indicator_id, program_id, kode, nama, satuan, target, tipe, created_by)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
          $this->db->query($sql, array($indikator_id, $obj->program_id, $obj->indikator_kode, $obj->indikator, $obj->satuan, $obj->target, $obj->tipe, $user->username));
          $ind_det_id = $this->db->insert_id();
          if($obj->tipe=="Pilihan Kustom"){
            for($k=0;$k<count($obj->pilihan);$k++){
              $sql = "INSERT INTO indicator_custom_value
                        (ind_det_id, nama, nilai)
                      VALUES (?, ?, ?)";
              $this->db->query($sql,array($ind_det_id, $obj->pilihan[$k]->nama, $obj->pilihan[$k]->nilai));
            }
          }
        }
      }
      $message = "Indikator berhasil disimpan";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function rfa_indicator($indicator_id){
    $message = ""; $ok = 1;
    $res = $this->db->query("SELECT status FROM indicator WHERE indicator_id = ?",array($indicator_id))->row();
    if($res->status=="Draft"||$res->status=="DRAFT"){
      $sql = "UPDATE indicator 
                SET
                  updated_by = ?,
                  updated_at = now(),
                  status = ?
              WHERE indicator_id = ?";
      $this->db->query($sql, array($_SESSION["username"], "Menunggu Persetujuan", $indicator_id));
    }else{
      $message = "Gagal melakukan pengajuan. Status draft adalah".$res->status."";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function get_indicator_approval(){
    $sql = "SELECT 
            indicator_id, i.draft_id, org_id, i.status, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at, i.created_by, 
            DATE_FORMAT(i.approved_at, '%d/%m/%Y %H:%i:%s') approved_at, i.approved_by, 
            p.name period_name, DATE_FORMAT(p.period_from, '%d/%m/%Y') period_from, DATE_FORMAT(p.period_to, '%d/%m/%Y') period_to
          FROM indicator i
          JOIN period p on i.draft_id = p.draft_id
          WHERE p.status = 'Aktif' and i.status IN ('Menunggu Persetujuan','Disetujui')
          ORDER BY indicator_id DESC";
    return $this->db->query($sql)->result();
  }

  public function get_indicator_approval_by_id($indicator_id){
    $sql = "SELECT 
              p.period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.draft_id, i.indicator_id, i.org_id, coalesce(i.status,'Belum Ada') status, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at,
              i.remarks
            FROM period p 
            LEFT JOIN indicator i ON p.draft_id = i.draft_id
            WHERE DATE(SYSDATE()) BETWEEN p.period_from AND p.period_to AND p.status = 'Aktif' AND i.indicator_id = ? ";
    $data = $this->db->query($sql,array($indicator_id))->row();
    
    $sqlTarget = "SELECT 
                  t.target_id id, t.code, t.name nama
                FROM `target` t
                JOIN `purpose` p ON  t.purpose_id = p.purpose_id
                JOIN `mission` m on p.mission_id = m.mission_id
                LEFT JOIN `indicator` i on m.draft_id = i.draft_id
                WHERE m.draft_id = ?
                ORDER BY t.target_id ASC";
    $targets = $this->db->query($sqlTarget,array($data->draft_id))->result();
    $tempid = 1;
    foreach($targets as $target){
      $sql   = "SELECT 
                    d.ind_det_id, d.nama indikator, d.kode indikator_kode,
                    d.program_id, p.code program_kode, d.satuan, d.target,
                    p.target_id, d.tipe
                FROM indicator_detail d
                JOIN indicator h on d.indicator_id = h.indicator_id
                JOIN program p on d.program_id = p.program_id
                WHERE h.indicator_id = ? and p.target_id = ?
                ORDER BY d.kode ASC";
      $indikator = $this->db->query($sql,array($indicator_id, $target->id))->result();
      foreach($indikator as $ind){
        $ind->tempid = $tempid;
        if($ind->tipe=="Pilihan Kustom"){
          $sql   = "SELECT custval_id, nama, nilai FROM indicator_custom_value WHERE ind_det_id = ? ORDER BY custval_id ASC";
          $pilihan = $this->db->query($sql,array($ind->ind_det_id))->result();
          $ind->pilihan = $pilihan;
        }else{
          $ind->pilihan = array();
        }
        $tempid++;
      }
      $target->indicators = $indikator;
    }
    $data->targets = $targets;
    
    return $data;
  }

  public function publish_indicator($indicator_id, $action, $remarks){
    $message = ""; $ok = 1;
    $res = $this->db->query("SELECT status FROM indicator WHERE indicator_id = ?",array($indicator_id))->row();
    //var_dump("publish_indicator".$res->status);
    if($res->status=="Menunggu Persetujuan"){
      if($action=="approve"){
        $status = "Disetujui";
        $approved_by = $_SESSION["username"];
        $approved_at = date('Y-m-d H:i:s');
      }else{
        $status = "Menunggu Revisi";
        $approved_by = null;
        $approved_at = null;
      }
      
      if($remarks=="")
        $remarks = "null";
      $sql = "UPDATE indicator 
                SET
                  updated_by = ?,
                  updated_at = now(),
                  status = ?,
                  approved_by = ?,
                  approved_at = ?,
                  remarks = ?
              WHERE indicator_id = ?";
      $this->db->query($sql, array($_SESSION["username"], $status, $approved_by, $approved_at, $remarks, $indicator_id));
    }else{
      $message = "Gagal Disetujui. Status Indikator Program ".$res->status."";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function cancel_indicator_approval($indicator_id){
    $message = ""; $ok = 1;
    if(1==0){
      $message = "Gagal membatalkan persetujuan. Draft KPI sudah digunakan untuk periode pengisian.";
    }else{
      $res = $this->db->query("SELECT status FROM indicator WHERE indicator_id = ?",array($indicator_id))->row();
      if($res->status=="Disetujui"){
        $status = "Menunggu Persetujuan";
        $approved_by = null;
        $approved_at = null;
        
        $sql = "UPDATE indicator 
                  SET
                    updated_by = ?,
                    updated_at = now(),
                    status = ?,
                    approved_by = ?,
                    approved_at = ?,
                    remarks = ?
                WHERE indicator_id = ?";
        $this->db->query($sql, array($_SESSION["username"], $status, $approved_by, $approved_at, $remarks, $indicator_id));
      }else{
        $message = "Gagal membatalkan persetujuan. Status saat ini ".$res->status."";
      }
    }
    
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }
}