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
                t.target_id, t.code kode_sasaran, t.name nama_sasaran, 
                h.indicator_id, 
                d.ind_det_id, d.kode kode_indikator, d.nama nama_indikator, d.target target_indikator, d.tipe tipe_indikator, 
                uh.ind_user_id, ud.ind_user_det_id, ud.realisasi, ud.nilai
            FROM indicator h
            JOIN indicator_detail d ON h.indicator_id = d.indicator_id
            JOIN program p ON d.program_id = p.program_id
            JOIN target t ON t.target_id = p.target_id
            LEFT JOIN (indicator_user_detail ud 
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
}