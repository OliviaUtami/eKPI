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
                        <label>Nama</label>
                        <input type="hidden" class="form-control" name="id" autocomplete="off" value="<?php echo $data->org_id; ?>" required>
                        <input type="text" class="form-control" name="name" autocomplete="off" value="<?php echo $data->org_name; ?>" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-5">
                      <label>Hirarki</label>
                      <input type="text" class="form-control" name="hierarchy" id="hierarchy" value="<?php echo $data->hierarchy; ?>" autocomplete="off" required>
                    </div>
                  </div>
                </div>
                <div class="card-footer bg-whitesmoke">
                  <button type="submit" class="btn btn-primary">Simpan</button>
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
				name: "Silahkan isi nama",
        email: "Silahkan isi alamat email yang valid",
        organization: "Silihakan pilih organisasi",
        role: "Silahkan pilih role",
				username: {
					required: "Silahkan masukkan username",
					minlength: "Panjang username minimal 3 karakter"
				},
				password: {
					required: "Silahkan masukkan password",
					minlength: "Panjang password minimal 8 karakter"
				},
				address: "Silahkan masukkan alamat",
			},
      errorPlacement: function(error, element) {
        element.closest(".form-group").append(error);
      }
	});
  $(document).on("change", ".form-control.select2", function(e) {
    $(this).closest(".form-group").find(".error").remove(); //remove label
  });
</script>
