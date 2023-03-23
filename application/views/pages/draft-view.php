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
                            <?php if($data->status=="Draft"||$data->status=="Menunggu Revisi"){ ?>
                              <button class="btn btn-sm btn-warning" onclick="editDraft(<?php echo $data->draft_id; ?>)" title="Perbarui Draft"><i class="fa fa-edit"></i></button>
                              <button class="btn btn-sm btn-success" onclick="reqApproval(<?php echo $data->draft_id; ?>)" title="Minta Persetujuan"><i class="fa fa-share"></i></button>
                              <?php if($data->status=="Draft"){ ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteDraft(<?php echo $data->draft_id; ?>)" title="Hapus Draft"><i class="fa fa-times"></i></button>
                              <?php } ?>
                            <?php } ?>
                            <?php if($data->status=="Menunggu Persetujuan"){ ?>
                            <button class="btn btn-sm btn-primary" onclick="editDraft(<?php echo $data->draft_id; ?>)" title="Lihat Draft"><i class="fa fa-eye"></i></button>
                            <?php } ?>
                            <?php if($data->status=="Disetujui"){ ?>
                            <button class="btn btn-sm btn-primary" onclick="editDraft(<?php echo $data->draft_id; ?>)" title="Lihat Draft"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-sm btn-primary" onclick="copyDraft(<?php echo $data->draft_id; ?>)" title="Salin Draft"><i class="fa fa-copy"></i></button>
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
            "scrollX": true,
            "columnDefs": [
                { width: 20, targets: 0 },
                { width: 90, targets: -1 }
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
      window.location.replace("draft/edit/"+id);
    }

    function reqApproval(id){
      if(confirm("Ajukan draft sebagai acuan pengisian KPI tahunan?\nDraft yang diajukan dan disetujui tidak akan bisa diperbarui"))
        window.location.replace("draft/rfa/"+id);
    }

    function copyDraft(id){
      if(confirm("Salin draft ini?"))
        window.location.replace("draft/copy/"+id);
    }

    function deleteDraft(id){
      if(confirm("Hapus draft ini ?"))
        window.location.replace("draft/delete/"+id);
    }
</script>
