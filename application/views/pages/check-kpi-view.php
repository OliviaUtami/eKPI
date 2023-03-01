<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('pages/_partials/header');
?>

<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1><?php echo $title ?></h1>
    </div>
    <div class="section-body">
      <div class="card">
        <div class="card-body">
        <table id="table-list" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Periode Pengisian</th>
                    <th>Organisasi</th>
                    <th>Karyawan</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kpi as $data){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo($data->period_from." - ".$data->period_to); ?></td>
                        <td><?php echo($data->org_name); ?></td>
                        <td><?php echo($data->name); ?></td>
                        <td><?php echo($data->status); ?></td>
                        <td><?php echo($data->created_by."<br/>".$data->created_at); ?></td>
                        <td>
                            <?php if($data->status=="Belum Ada"){ ?>
                            <button class="btn btn-sm btn-warning" onclick="openKPI(<?php echo $data->indicator_id; ?>)" title="Isi KPI"><i class="fa fa-edit"></i></button>
                            <?php }else{ ?>
                              <button class="btn btn-sm btn-success" onclick="openExist(<?php echo $data->ind_user_id; ?>)" title="Cek KPI"><i class="fa fa-edit"></i></button>
                              <button class="btn btn-sm btn-primary" onclick="print(<?php echo $data->ind_user_id; ?>)" title="Print KPI"><i class="fa fa-print"></i></button>
                            <?php } ?>
                            <?php if($data->ind_user_id!==NULL&&$data->ind_user_id!==-1&&($data->status=="Draft"||$data->status=="Menunggu Revisi")){ ?>
                            <button class="btn btn-sm btn-primary" onclick="sendKPI(<?php echo $data->ind_user_id; ?>)" title="Kirimkan KPI"><i class="fa fa-paper-plane"></i></button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <!-- <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Org ID</th>
                    <th>Role ID</th>
                </tr>
            </tfoot> -->
        </table>
        </div>
      </div>
    </div>
  </section>
</div>
<?php $this->load->view('pages/_partials/footer'); ?>
<script>
    function editPeriod(id){
      window.location.replace("period/edit/"+id);
    }

    $(document).ready(function() {
        $("#table-list").dataTable({
            "columnDefs": [
                { width: 20, targets: 0 },
                { width: 30, targets: -1 }
            ],
            "fixedColumns": true,
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                $('td:eq(0)', nRow).html(iDisplayIndexFull +1);
            }
          }
        );

        <?php if($this->session->flashdata('message')){ ?>
          alert("<?php echo $this->session->flashdata('message'); ?>");
        <?php } ?>
    });

    function openKPI(indicator_id){
      window.location.assign("kpi/add/"+indicator_id);
    }

    function openExist(ind_user_id){
      window.location.assign("check-kpi/edit/"+ind_user_id);
    }

    function print(ind_user_id){
      window.location.assign("kpi/print/"+ind_user_id);
    }

    function sendKPI(indicator_id){
      window.location.assign("kpi/submit/"+indicator_id);
    }
</script>
