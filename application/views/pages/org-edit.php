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
            <form id="formEdit" method="POST" action="process">
              <div class="card">
              <div class="card-body">
                  <div class="row">
                    <div class="form-group col-md-5">
                        <label>Name</label>
                        <input type="hidden" class="form-control" name="id" autocomplete="off" value="<?php echo $data->org_id; ?>" required>
                        <input type="text" class="form-control" name="name" autocomplete="off" value="<?php echo $data->org_name; ?>" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-5">
                      <label>Hierarchy</label>
                      <input type="text" class="form-control" name="hierarchy" id="hierarchy" value="<?php echo $data->hierarchy; ?>" autocomplete="off" required>
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
    $(document).ready(function() {
        $.validator.addMethod(
            "passwordChange", 
            function(value, element) {
                if(value == "" || value.length>=8)
                    return true;
                else
                    return false;
            },
            "Password must be at least 8 characters long"
        );
        <?php if($this->session->flashdata('message')){ ?>
          alert("<?php echo $this->session->flashdata('message'); ?>");
        <?php } ?>
    });
    $("#formEdit").validate({
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
					passwordChange: true
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
