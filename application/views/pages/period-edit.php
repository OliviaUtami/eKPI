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
            <form id="formEdit" method="POST" action="process">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-md-6">
                      <div class="row">
                        <div class="form-group col-md-12">
                          <label>Nama Periode</label>
                          <input class="form-control" name="name" id="name" autocomplete="off" value="<?php echo $perioddata->period_name; ?>" required/>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label>Periode Awal</label>
                          <input type="hidden" class="form-control" name="id" autocomplete="off"  value="<?php echo $perioddata->period_id; ?>" required>
                          <input type="text" class="form-control date-input" name="period_from" id="period_from" value="<?php echo $perioddata->period_from; ?>" autocomplete="off" required readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Periode Akhir</label>
                          <input type="text" class="form-control date-input" name="period_to" id="period_to" value="<?php echo $perioddata->period_to; ?>" autocomplete="off" required readonly>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12">
                          <label>Pilih Draft KPI</label>
                          <select class="form-control select2" name="draft" id="draft" autocomplete="off">
                            <option value="">Pilih Draft</option>
                            <?php foreach ($draft_data as $draft) {
                              echo "<option value='".$draft->id."' ".($draft->id==$perioddata->draft_id?"selected":"").">".$draft->nama."</option>";
                            } ?>
                          </select>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12">
                          <label>Status</label>
                          <select class="form-control select2" name="status" id="status" autocomplete="off" required>
                            <option value="Aktif" <?php if($perioddata->status=="Aktif") {echo "selected"; } ?>>Aktif</option>
                            <option value="Tidak Aktif" <?php if($perioddata->status=="Tidak Aktif") {echo "selected"; } ?>>Tidak Aktif</option>
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
    $("#draft").trigger("change");
  });
  function updateDate(selected){
    var minDate = new Date(selected.date.valueOf());
    $('#period_to').datepicker('setStartDate', minDate);
    $('#period_from').datepicker('setEndDate', minDate);
  }

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
  $(document).on("change", "#draft", function() {
    console.log($(this).val());
    if($(this).val()==""){
      $("#status option[value='Aktif']").prop('disabled',true);
      $("#status").val("Tidak Aktif");
      $("#status").select2();
    }else{
      $("#status option[value='Aktif']").prop('disabled',false);
      $("#status").select2();
    }
  });
</script>
