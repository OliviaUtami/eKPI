<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('pages/_partials/header');
?>
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Add User</h1>
          </div>
          <div class="section-body">
            <form id="formAdd" method="POST" action="add/process">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-md-5">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" autocomplete="off" required>
                    </div>
                    <div class="form-group col-md-5">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" autocomplete="off" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-5">
                      <label>Organization</label>
                      <select class="form-control select2" name="organization" id="organization" autocomplete="off" required>
                        <option></option>
                        <?php
                          foreach ($organizations as $organization) {
                            echo "<option value='".$organization->org_id."'>".$organization->org_name."</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <div class="form-group col-md-5">
                      <label for="role">Role</label>
                      <select class="form-control select2" name="role" id="role" autocomplete="off" required>
                        <option></option>
                        <?php
                          foreach ($roles as $role) {
                            echo "<option value='".$role->role_id."'>".$role->role_name."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-5">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" autocomplete="off" required>
                    </div>
                    <div class="form-group col-md-5">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" autocomplete="off" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-5">
                        <label>Address</label>
                        <textarea class="form-control" name="address" autocomplete="off" required></textarea>
                    </div>
                  </div>
                </div>
                <div class="card-footer bg-whitesmoke">
                  <button type="submit" class="btn btn-primary">SAVE</button>
                </div>
              </div>
            </form>
          </div>
        </section>
      </div>
<?php $this->load->view('pages/_partials/footer'); ?>
<script>
  <?php if($this->session->flashdata('message')){ ?>
    alert("<?php echo $this->session->flashdata('message'); ?>");
  <?php } ?>
  $("#formAdd").validate({
			rules: {
				name: "required",
				email: {
					required: true,
					email: true
				},
        organization: "required",
        role: "required",
				username: {
					required: true,
					minlength: 3
				},
				password: {
					required: true,
					minlength: 8
				},
				address: "required",
			},
			messages: {
				name: "Please enter a name",
        email: "Please enter a valid email address",
        organization: "Please select an organization",
        role: "Please enter a role",
				username: {
					required: "Please enter a username",
					minlength: "Username must consist of at least 3 characters"
				},
				password: {
					required: "Please provide a password",
					minlength: "Password must be at least 8 characters long"
				},
				address: "Please enter an address",
			},
      errorPlacement: function(error, element) {
        element.closest(".form-group").append(error);
      }
	});
  $(document).on("change", ".form-control.select2", function(e) {
    $(this).closest(".form-group").find(".error").remove(); //remove label
  });
</script>