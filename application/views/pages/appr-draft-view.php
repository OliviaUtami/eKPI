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
        <button class="btn btn-sm btn-primary" onclick="window.location.replace('draft/add')">Tambah Baru</button><br><br>

        <table id="table-list" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Draft</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Status Persetujuan</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($draft_data as $data){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo($data->name); ?></td>
                        <td><?php echo($data->status); ?></td>
                        <td><?php echo($data->created_by."<br/>".$data->created_at); ?></td>
                        <td><?php echo($data->approved_by."<br/>".$data->approved_at); ?></td>
                        <td>
                            <?php if($data->status=="Menunggu Persetujuan"){ ?>
                            <button class="btn btn-sm btn-success" onclick="editDraft(<?php echo $data->draft_id; ?>)" title="Cek Draft"><i class="fa fa-eye"></i></button>
                            <?php }else if($data->status=="Disetujui"){ ?>
                            <button class="btn btn-sm btn-danger" onclick="cancelApproval(<?php echo $data->draft_id; ?>)" title="Batalkan Persetujuan"><i class="fa fa-lock-open"></i></button>
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

    function editDraft(id){
      window.location.replace("draft-approval/edit/"+id);
    }

    function cancelApproval(id){
      if(confirm("Batalkan persetujuan untuk draft KPI berikut?\nDraft akan kembali ke status \"Menunggu Persetujuan\""))
        window.location.replace("draft-approval/cancel/"+id);
    }
</script>
