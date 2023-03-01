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
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($periods as $data){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo($data->period_from." - ".$data->period_to); ?></td>
                        <td><?php echo($data->status); ?></td>
                        <td><?php echo($data->created_by."<br/>".$data->created_at); ?></td>
                        <td>
                            <?php if($data->status=="Belum Ada"||$data->status=="Draft"||$data->status=="Menunggu Revisi"){ ?>
                            <button class="btn btn-sm btn-warning" onclick="manageIndicators(<?php echo $data->draft_id; ?>)" title="Indikator KPI"><i class="fa fa-edit"></i></button>
                            <?php } ?>
                            <?php if($data->indicator_id!==NULL&&$data->status=="Draft"){ ?>
                            <button class="btn btn-sm btn-primary" onclick="reqAppr(<?php echo $data->indicator_id; ?>)" title="Ajukan Indikator"><i class="fa fa-paper-plane"></i></button>
                            <?php } ?>
                            <?php if($data->status=="Menunggu Persetujuan"||$data->status=="Disetujui"){ ?>
                            <button class="btn btn-sm btn-primary" onclick="manageIndicators(<?php echo $data->draft_id; ?>)" title="Indikator KPI"><i class="fa fa-eye"></i></button>
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

    function manageIndicators(draft_id){
      window.location.replace("indicator/edit/"+draft_id);
    }

    function reqAppr(indicator_id){
      if(confirm("Ajukan indikator kinerja program KPI ke WR agar bisa digunakan tim dalam pengisian KPI ?\n"))
        window.location.replace("indicator/rfa/"+indicator_id);
    }
</script>
