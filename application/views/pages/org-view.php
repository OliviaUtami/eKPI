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
        <button class="btn btn-sm btn-primary" onclick="window.location.replace('org/add')">Tambah Baru</button><br><br>

        <table id="table-users" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $obj){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo($obj->org_name); ?></td>
                        <td>
                          <button class="btn btn-sm btn-warning" onclick="editOrg(<?php echo $obj->org_id; ?>)" title="Edit"><i class="fa fa-edit"></i></button>
                          <button class="btn btn-sm btn-danger" onclick="deleteOrg(<?php echo $obj->org_id; ?>)" title="Set Inactive">&nbsp;<i class="fa fa-times"></i>&nbsp;</button>
                          <!-- <div class="btn-group" role="group" aria-label="Basic example">

                          </div> -->
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
    function editOrg(id){
      window.location.replace("org/edit/"+id);
    }
    function deleteOrg(id){
      if(confirm("Confirm delete this organization?")){
        window.location.replace("org/delete/"+id);
      }
    }
    $(document).ready(function() {
        $("#table-users").dataTable({
            "columnDefs": [
                { width: 20, targets: 0 },
                { width: 30, targets: -1 }
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
