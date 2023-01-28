<?php
class draft_model extends CI_Model {
  public function get_draft(){
    $sql = "SELECT 
              draft_id, name, status, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') created_at, created_by, 
              DATE_FORMAT(approved_at, '%d/%m/%Y %H:%i:%s') approved_at, approved_by
            FROM `draft` 
            ORDER BY draft_id DESC;";
    return $this->db->query($sql)->result();
  }

  public function get_draft_by_id($draft_id){
    $sql = "SELECT 
              draft_id id, name nama, status, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') created_at, created_by, 
              DATE_FORMAT(approved_at, '%d/%m/%Y %H:%i:%s') approved_at, approved_by, remarks
            FROM `draft` 
            WHERE draft_id = ? ";
    $data = $this->db->query($sql,array($draft_id))->row();
    
    $sqlMisi = "SELECT 
                mission_id id, code, name nama, created_by, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') created_at,
                updated_by, DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i:%s') created_at, status,
                approved_by, DATE_FORMAT(approved_at, '%d/%m/%Y %H:%i:%s') approved_at
            FROM `mission` m
            WHERE m.draft_id = ? 
            ORDER BY m.mission_id ASC ";
    $missions = $this->db->query($sqlMisi,array($draft_id))->result();
    foreach($missions as $mission){
      $sqlTujuan = "SELECT 
                      purpose_id id, code, name nama, created_by, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') created_at,
                      updated_by, DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i:%s') created_at, status,
                      approved_by, DATE_FORMAT(approved_at, '%d/%m/%Y %H:%i:%s') approved_at
                  FROM `purpose` p
                  WHERE p.mission_id = ?
                  ORDER BY p.purpose_id ASC ";
      $purposes = $this->db->query($sqlTujuan,array($mission->id))->result();
      $mission->tujuan = $purposes;

      foreach($purposes as $purpose){
        $sqlTarget = "SELECT 
                        target_id id, code, name nama, created_by, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') created_at,
                        updated_by, DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i:%s') created_at, status,
                        approved_by, DATE_FORMAT(approved_at, '%d/%m/%Y %H:%i:%s') approved_at
                    FROM `target` t
                    WHERE t.purpose_id = ?
                    ORDER BY t.target_id ASC ";
        $targets = $this->db->query($sqlTarget,array($purpose->id))->result();
        $purpose->target = $targets;

        foreach($targets as $target){
          $sqlProgram = "SELECT 
                          program_id id, code, name nama, created_by, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') created_at,
                          updated_by, DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i:%s') created_at, status,
                          approved_by, DATE_FORMAT(approved_at, '%d/%m/%Y %H:%i:%s') approved_at
                      FROM `program` p
                      WHERE p.target_id = ?
                      ORDER BY p.program_id ASC ";
          $programs = $this->db->query($sqlProgram,array($target->id))->result();
          $target->program = $programs;
        }
      }
    }
    $data->details = $missions;
    
    return $data;
  }
  
  public function get_approved_draft(){
    $sql = "SELECT 
              draft_id id, name nama, status, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') created_at, created_by, 
              DATE_FORMAT(approved_at, '%d/%m/%Y %H:%i:%s') approved_at, approved_by, remarks
            FROM `draft` 
            WHERE status = 'Disetujui' ";
    $data = $this->db->query($sql)->result();
    
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
                (code, name, created_by, status)
              VALUES (?, ?, ?)";
      $this->db->query($sql, array($draft_name, $_SESSION["username"], 'Draft'));
      $draft_id = $this->db->insert_id();
      $t = 1; $s = 1;
      for ($i=0; $i < count($arrmission); $i++) { 
        $sql = "INSERT INTO mission 
                  (draft_id, code, name, created_by, status)
                VALUES (?, ?, ?, ?, ?)";
        $this->db->query($sql, array($draft_id, "M".($i+1), $arrmission[$i]->nama, $_SESSION["username"], 'Draft'));
        $mission_id = $this->db->insert_id();
        $arrtujuan = $arrmission[$i]->tujuan;
        for ($j=0; $j < count($arrtujuan); $j++) { 
          $sql = "INSERT INTO purpose 
                    (mission_id, code, name, created_by, status)
                  VALUES (?, ?, ?, ?, ?)";
          $this->db->query($sql, array($mission_id, "T".$t, $arrtujuan[$j]->nama, $_SESSION["username"], 'Draft'));
          $purpose_id = $this->db->insert_id();
          $arrtarget = $arrtujuan[$j]->target;
          for ($k=0; $k < count($arrtarget); $k++) { 
            $sql = "INSERT INTO target 
                      (purpose_id, code, name, created_by, status)
                    VALUES (?, ?, ?, ?, ?)";
            $this->db->query($sql, array($purpose_id, "S".$s, $arrtarget[$k]->nama, $_SESSION["username"], 'Draft'));
            $target_id = $this->db->insert_id();
            $arrprogram = $arrtarget[$k]->program;
            for ($l=0; $l < count($arrprogram); $l++) { 
              $sql = "INSERT INTO program 
                        (target_id, code, name, created_by, status)
                      VALUES (?, ?, ?, ?)";
              $this->db->query($sql, array($target_id, "P".$s.".".($l+1), $arrprogram[$k]->nama, $_SESSION["username"], 'Draft'));
            }
            $s++;
          }
          $t++;
        }
      }
      $message = "Draft berhasil ditambbahkan";
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
      $message = "User dengan username yang sama sudah ada";
    }else{
      $sql = "UPDATE draft 
                SET
                  name = ?, 
                  updated_by = ?,
                  updated_at = now(),
                  status = ?
              WHERE draft_id = ?";
      $this->db->query($sql, array($draft_name, $_SESSION["username"], 'Draft', $draft_id));
      $sql = "DELETE FROM mission 
              WHERE draft_id = ?";
      $this->db->query($sql, array($draft_id));
      $t = 1; $s = 1;
      for ($i=0; $i < count($arrmission); $i++) { 
        $sql = "INSERT INTO mission 
                  (draft_id, code, name, created_by, status)
                VALUES (?, ?, ?, ?, ?)";
        $this->db->query($sql, array($draft_id, "M".($i+1), $arrmission[$i]->nama, $_SESSION["username"], 'Draft'));
        $mission_id = $this->db->insert_id();
        $arrtujuan = $arrmission[$i]->tujuan;
        for ($j=0; $j < count($arrtujuan); $j++) { 
          $sql = "INSERT INTO purpose 
                    (mission_id, code, name, created_by, status)
                  VALUES (?, ?, ?, ?, ?)";
          $this->db->query($sql, array($mission_id, "T".$t, $arrtujuan[$j]->nama, $_SESSION["username"], 'Draft'));
          $purpose_id = $this->db->insert_id();
          $arrtarget = $arrtujuan[$j]->target;
          for ($k=0; $k < count($arrtarget); $k++) { 
            $sql = "INSERT INTO target 
                      (purpose_id, code, name, created_by, status)
                    VALUES (?, ?, ?, ?, ?)";
            $this->db->query($sql, array($purpose_id, "S".$s, $arrtarget[$k]->nama, $_SESSION["username"], 'Draft'));
            $target_id = $this->db->insert_id();
            $arrprogram = $arrtarget[$k]->program;
            for ($l=0; $l < count($arrprogram); $l++) { 
              $sql = "INSERT INTO program 
                        (target_id, code, name, created_by, status)
                      VALUES (?, ?, ?, ?, ?)";
              $this->db->query($sql, array($target_id, "P".$s.".".($l+1), $arrprogram[$l]->nama, $_SESSION["username"], 'Draft'));
            }
            $s++;
          }
          $t++;
        }
      }
      $message = "Draft berhasil diperbarui";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function rfa_draft($draft_id){
    $message = ""; $ok = 1;
    $res = $this->db->query("SELECT status FROM draft WHERE draft_id = ?",array($draft_id))->row();
    if($res->status=="Draft"||$res->status=="DRAFT"){
      $sql = "UPDATE draft 
                SET
                  updated_by = ?,
                  updated_at = now(),
                  status = ?
              WHERE draft_id = ?";
      $this->db->query($sql, array($_SESSION["username"], "Menunggu Persetujuan", $draft_id));
    }else{
      $message = "Gagal melakukan pengajuan. Status draft ".$res->status."";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function get_draft_approval(){
    $sql = "SELECT 
              draft_id, name, status, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') created_at, created_by, 
              DATE_FORMAT(approved_at, '%d/%m/%Y %H:%i:%s') approved_at, approved_by
            FROM `draft` 
            WHERE status IN ('Menunggu Persetujuan','Disetujui')
            ORDER BY draft_id DESC;";
    return $this->db->query($sql)->result();
  }

  public function process_draft($draft_id, $action, $remarks){
    $message = ""; $ok = 1;
    $res = $this->db->query("SELECT status FROM draft WHERE draft_id = ?",array($draft_id))->row();
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
      $sql = "UPDATE draft 
                SET
                  updated_by = ?,
                  updated_at = now(),
                  status = ?,
                  approved_by = ?,
                  approved_at = ?,
                  remarks = ?
              WHERE draft_id = ?";
      $this->db->query($sql, array($_SESSION["username"], $status, $approved_by, $approved_at, $remarks, $draft_id));
    }else{
      $message = "Gagal melakukan pengajuan. Status draft ".$res->status."";
    }
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }

  public function cancel_draft_approval($draft_id){
    $message = ""; $ok = 1;
    $used = $this->db->query("SELECT count(1) cnt FROM period WHERE draft_id = ?",array($draft_id))->row();
    if($used->cnt>0){
      $message = "Gagal membatalkan persetujuan. Draft KPI sudah digunakan untuk periode pengisian.";
    }else{
      $res = $this->db->query("SELECT status FROM draft WHERE draft_id = ?",array($draft_id))->row();
      if($res->status=="Disetujui"){
        $status = "Menunggu Persetujuan";
        $approved_by = null;
        $approved_at = null;
        
        $sql = "UPDATE draft 
                  SET
                    updated_by = ?,
                    updated_at = now(),
                    status = ?,
                    approved_by = ?,
                    approved_at = ?,
                    remarks = ?
                WHERE draft_id = ?";
        $this->db->query($sql, array($_SESSION["username"], $status, $approved_by, $approved_at, $remarks, $draft_id));
      }else{
        $message = "Gagal membatalkan persetujuan. Status draft ".$res->status."";
      }
    }
    
    $data = (object) [
			"ok"      => $ok,
      "message" => $message
    ];
    return $data;
  }
}