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
        <button class="btn btn-sm btn-primary" onclick="markAsRead();" style="float: right"><i class="fas fa-check-double"></i> Tanda sudah dibaca</button><br><br>

        <table id="table-notification" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Notifikasi</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notif as $obj){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo("<b>".$obj->title."</b><br>".$obj->content); ?></td>
                        <td><?php echo($obj->tstamp); ?></td>
                        <td><?php if($obj->isread=="0"){ echo("Belum Dibaca"); } else { echo("Dibaca") ;} ?></td>
                        <td>
                          <?php if($obj->isread=="0"){ ?>
                          <button type="button" class="btn btn-sm btn-primary" title="Tandai Dibaca" onclick="markAsRead(<?php echo $obj->idnotification; ?>);"><i class="fas fa-check-double"></i></button>
                          <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
      </div>
    </div>
  </section>
</div>
<?php $this->load->view('pages/_partials/footer'); ?>
<script>
    function editUser(id){
      window.location.replace("user/edit/"+id);
    }
    function deleteUser(id){
      if(confirm("Confirm delete this user?")){
        window.location.replace("user/delete/"+id);
      }
    }
    $(document).ready(function() {
        $("#table-notification").dataTable({
            "columnDefs": [
                { width: 20, targets: 0 },
                { width: 150, targets: -2 },
                { width: 60, targets: -1 }
            ],
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                $('td:eq(0)', nRow).html(iDisplayIndexFull +1);
            }
          }
        );

        <?php if($this->session->flashdata('message')){ ?>
          alert("<?php echo $this->session->flashdata('message'); ?>");
        <?php } ?>
    });
</script>
