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
                        <td><?php echo($data->status); ?></td>
                        <td><?php echo($data->created_by."<br/>".$data->created_at); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openKPI(<?php echo $data->indicator_id; ?>)" title="Isi KPI"><i class="fa fa-edit"></i></button>
                            <?php if($data->indicator_id!==NULL&&$data->status=="Draft"){ ?>
                            <button class="btn btn-sm btn-primary" onclick="publish(<?php echo $data->indicator_id; ?>)" title="Publikasi Indikator"><i class="fa fa-paper-plane"></i></button>
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

    function openKPI(draft_id){
      window.location.replace("kpi/edit/"+draft_id);
    }

    function publish(indicator_id){
      
    }
</script>
