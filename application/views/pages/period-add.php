<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('pages/_partials/header');
?>
<style>
  .date-input:read-only{
    background-color: #fdfdff;
  }
</style>
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1><?php echo $title ?></h1>
          </div>
          <div class="section-body">
            <form id="formAdd" method="POST" action="add/process">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-md-5">
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label>Mulai Periode</label>
                          <input type="text" class="form-control date-input" name="period_from" id="period_from" autocomplete="off" required readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Periode Berakhir</label>
                          <input type="text" class="form-control date-input" name="period_to" id="period_to" autocomplete="off" required readonly>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12">
                          <label>Status</label>
                          <select class="form-control select2" name="status" id="status" autocomplete="off" required>
                            <option value="Active">Aktif</option>
                            <option value="Inactive">Tidak Aktif</option>
                          </select>
                        </div>
                      </div>
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
  <?php if($this->session->flashdata('message')){ ?>
    alert("<?php echo $this->session->flashdata('message'); ?>");
  <?php } ?>
  $(document).ready(function(){
    $('.date-input').datepicker({
      autoclose: true,
      //todayHighlight: true,
      //toggleActive: true,
      format: "dd/mm/yyyy"
    }).on('changeDate', function(selected){
      //console.log("s",$(this).val());
      updateDate(selected);
    });;
  });
  function updateDate(selected){
    var minDate = new Date(selected.date.valueOf());
    $('#period_to').datepicker('setStartDate', minDate);
    $('#period_from').datepicker('setEndDate', minDate);
  }

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
