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
                    <div class="form-group col-md-6">
                      <div class="row">
                        <div class="form-group col-md-12">
                          <label>Nama Periode</label>
                          <input class="form-control" name="name" id="name" autocomplete="off" required/>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label>Periode Awal</label>
                          <input type="text" class="form-control date-input" name="period_from" id="period_from" autocomplete="off" required readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Periode Akhir</label>
                          <input type="text" class="form-control date-input" name="period_to" id="period_to" autocomplete="off" required readonly>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12">
                          <label>Pilih Draft KPI</label>
                          <select class="form-control select2" name="draft" id="draft" autocomplete="off">
                            <option value="">Pilih Draft</option>
                            <?php foreach ($draft_data as $draft) {
                              echo "<option value='".$draft->id."'>".$draft->nama."</option>";
                            } ?>
                          </select>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12">
                          <label>Status</label>
                          <select class="form-control select2" name="status" id="status" autocomplete="off" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
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
				name: "Silahkan isi nama periode",
        period_from: "Silahkan isi tanggal awal periode",
        period_to: "Silahkan isi tanggal akhir periode"
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
