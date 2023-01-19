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

  
}