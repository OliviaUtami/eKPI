<?php
class indicator_model extends CI_Model {
  public function get_org_active_period_indicators($org_id){
    $sql = "SELECT 
              p.period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.draft_id, i.indicator_id, i.org_id, coalesce(i.status,'Belum Ada') status, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at
            FROM period p 
            LEFT JOIN indicator i ON p.draft_id = i.draft_id
            WHERE DATE(SYSDATE()) BETWEEN p.period_from AND p.period_to AND p.status = 'Aktif'";
    $data = $this->db->query($sql)->result();
    return $data;
  }
  public function get_indicator_by_draft_org($draft_id, $org_id){
    $sql = "SELECT 
              p.period_id, DATE_FORMAT(period_from, '%d/%m/%Y') period_from, DATE_FORMAT(period_to, '%d/%m/%Y') period_to, 
              p.draft_id, i.indicator_id, i.org_id, coalesce(i.status,'Belum Ada') status, i.created_by, DATE_FORMAT(i.created_at, '%d/%m/%Y %H:%i:%s') created_at
            FROM period p 
            LEFT JOIN indicator i ON p.draft_id = i.draft_id AND i.org_id = ?
            WHERE DATE(SYSDATE()) BETWEEN p.period_from AND p.period_to AND p.status = 'Aktif' AND p.draft_id = ? ";
    $data = $this->db->query($sql,array($org_id, $draft_id))->row();
    
    $sqlTarget = "SELECT 
                  t.target_id id, t.name nama
                FROM `target` t
                JOIN `purpose` p ON  t.purpose_id = p.purpose_id
                JOIN `mission` m on p.mission_id = m.mission_id
                LEFT JOIN `indicator` i on m.draft_id = i.draft_id
                WHERE m.draft_id = ?
                ORDER BY t.target_id ASC";
    $targets = $this->db->query($sqlTarget,array($draft_id))->result();
    $data->targets = $targets;
    
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
}