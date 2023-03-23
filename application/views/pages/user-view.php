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
        <button class="btn btn-sm btn-primary" onclick="window.location.replace('user/add')">Tambah Baru</button><br><br>

        <table id="table-users" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Org ID</th>
                    <th>Role ID</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_data as $user){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo($user->name); ?></td>
                        <td><?php echo($user->address); ?></td>
                        <td><?php echo($user->email); ?></td>
                        <td><?php echo($user->username); ?></td>
                        <td><?php echo($user->org_name); ?></td>
                        <td><?php echo($user->role_name); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editUser(<?php echo $user->user_id; ?>)" title="Edit"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user->user_id; ?>)" title="Set Inactive">&nbsp;<i class="fa fa-times"></i>&nbsp;</button>
                            <!-- <button class="btn btn-sm btn-warning" onclick="editUser(<?php echo $user->user_id; ?>)" >Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user->user_id; ?>)" >Delete</button> -->
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
    function editUser(id){
      window.location.replace("user/edit/"+id);
    }
    function deleteUser(id){
      if(confirm("Confirm delete this user?")){
        window.location.replace("user/delete/"+id);
      }
    }
    $(document).ready(function() {
        $("#table-users").dataTable({
            "scrollX": true,
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
