<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('pages/_partials/header');
?>
<style>
  .date-input:read-only{
    background-color: #fdfdff;
  }
  .btn-md {
    height: 32px;
  }
  .mySelect {
    height: 32px !important;
    font-size: 13px !important;
    padding-top: 4px !important;
    padding-bottom: 4px !important;
    padding-left: 8px !important;
    padding-right: 8px !important;
  }
  .modal-header{
    padding-bottom: 15px;
    background-color: whitesmoke;
  }
  .modal-title{
    font-size: 14px !important;
  }
  .modal-body label{
    font-weight: bold;
  }
  .modal-body .row{
    margin-bottom: 16px;
  }
  #tableChoice{
    max-width: 500px;
    width: 100%;

  }
  .smallSelect{
    height: 24px !important;
    padding: 0px 4px !important;
    width: 100% !important;
  }
  .smallInput{
    height: 24px !important;
    padding: 0px 4px !important;
    width: 100% !important;
  }
</style>
<script>
  var indikator = [];
  var tempid = 1;

</script>
<div class="modal" id="modal-tambah-indikator" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      </hr>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-2">
            <label>Program</label>
          </div>
          <div class="col-md-9">
          <input type="hidden" class="form-control" id="tempid"/>
          <input type="hidden" class="form-control" id="sasaran"/>
          <select class="form-control mySelect" id="program" autocomplete="off" required>
            <option></option>
          </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2">
            <label>Indikator</label>
          </div>
          <div class="col-md-9">
          <textarea id="indikator" class="form-control"></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2">
            <label>Tipe</label>
          </div>
          <div class="col-md-9">
          <select class="form-control mySelect" id="tipe" autocomplete="off" required>
            <option value="Persentase">Persentase</option>
            <option value="Batas Persentase">Batas Persentase</option>
            <option value="Angka">Angka</option>
            <option value="Batas Angka">Batas Angka</option>
            <option value="Pilihan Kustom">Pilihan Kustom</option>
          </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2">
            <label>Satuan</label>
          </div>
          <div class="col-md-3">
            <input type="text" class="form-control" id="satuan"/>
          </div>
          <div class="col-md-1"></div>
          <div class="col-md-2">
            <label>Target</label>
          </div>
          <div class="col-md-3">
            <input type="text" class="form-control" id="target"/>
          </div>
        </div>
        <div class="row" id="rowCustomValue">
          <div class="col-md-2">
            <label>Pilihan</label> <button type='button' class='btn btn-sm btn-add-choice'>&nbsp;<i class='fa fa-plus'></i>&nbsp;</button>
          </div>
          <div class="col-md-9">
            <table id="tableChoice" class="table table-striped table-sm">
              <thead>
                <tr>
                  <th>Pilihan</th>
                  <th style="width: 70px;">Nilai</th>
                  <th style="width: 20px;">Target</th>
                  <th style="width: 20px;"></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="row" style="display:none">
          <div class="col-md-2">
            <label>Min <span style="color:red">*)</span></label>
          </div>
          <div class="col-md-3">
            <input type="text" class="form-control" id="min" value=""/>
          </div>
          <div class="col-md-1"></div>
          <div class="col-md-2">
            <label>Max <span style="color:red">*)</span></label>
          </div>
          <div class="col-md-3">
            <input type="text" class="form-control" id="max" value="100"/>
          </div><br>
          <div class="col-md-12">
            <span style="color:red">*) Jika diisi, nilai minimal atau nilai maksimal dari suatu indikator sesuai dengan nilai yang diisikan</span>
          </div>
        </div>
        <div class="row"  style="display:none">
          <div class="col-md-12">
            <span style="color:red">*) Jika diisi, nilai minimal atau nilai maksimal dari suatu indikator sesuai dengan nilai yang diisikan</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <?php if($indicator->status!=="Dipublikasi"){ ?>
        <button type="button" class="btn btn-primary" onclick="saveIndikator()">Simpan</button>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1><?php echo $title ?></h1>
    </div>
    <div class="section-body">
      <form id="formAdd" method="POST" action="/edit/process">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="form-group col-md-5">
                  <label>Periode Pengisian</label>
                  <input type="text" class="form-control" id="name" name="name" autocomplete="off" value="<?php echo ($indicator->period_from." - ".$indicator->period_to); ?>" readonly>
                  <input type="hidden" class="form-control" id="id" name="id" autocomplete="off" value="<?php echo $indicator->indicator_id; ?>" required>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-8">
                <input type="text" class="form-control" id="cboIndicator"/>
              </div>
              <div class="form-group col-md-2">
                <button type="button" class="btn btn-sm btn-primary" title="Tambah" id="btnAddIndicator">
                  &nbsp;<i class='fa fa-plus'></i>&nbsp;
                </button>
              </div>
            </div>
            <hr/>
            <div id="divIsi">
              
            </div>
          </div>
          <div class="card-footer bg-whitesmoke">
            
            <button type="button" id="btn-save" class="btn btn-primary">Simpan</button>
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
  var indikator = [];
  function populateIndikator(){
    var params = {
      indicator_id: <?php echo $indicator->indicator_id; ?>
    };
    $.ajax({
        type: "POST",
        url: '<?php echo base_url(); ?>kpi/api/get_indicator',
        dataType: "json",
        data: JSON.stringify(params)
    }).done(function (data) {
        var result = data.indicator;
        console.log(result);
        $("#cboIndicator").select2({
            placeholder: 'Pilih Indikator',
            allowClear: true,
            data: result,
            language: {
              "noResults": function(){
                  return "Indikator tidak ditemukan";
              }
            },
        });
    });
  }

  $(document).ready(function(){
    populateIndikator();
    $('.date-input').datepicker({
      autoclose: true,
      //todayHighlight: true,
      //toggleActive: true,
      format: "dd/mm/yyyy"
    }).on('changeDate', function(selected){
      //console.log("s",$(this).val());
      updateDate(selected);
    });;
    $("#rowCustomValue").hide();
  });

  $(document).on("click","#btnAddIndicator",function(){
    if(typeof $("#cboIndicator").val() == "undefined"||$("#cboIndicator").val()==""){
      alert("Silahkan pilih indikator yang ingin ditambahkan");
      return;
    }
    if(indikator.findIndex(item=>item.ind_det_id==$("#cboIndicator").select2("data")[0].ind_det_id)>-1){
      alert("Indikator sudah pernah ditambahkan sebelumnya, silahkan isi nilai realisasi dan upload dokumen yang dibutuhkan");
      return;
    }
    var selected = $("#cboIndicator").select2("data")[0];
    var obj = {
      ind_det_user_id: -1,
      ind_det_id: selected.ind_det_id,
      kode_sasaran: selected.kode_sasaran,
      nama_sasaran: selected.nama_sasaran,
      kode_indikator: selected.kode_indikator,
      nama_indikator: selected.nama_indikator,
      satuan_indikator: selected.satuan_indikator,
      target_indikator: selected.target_indikator,
      target_indikator_value: selected.target_indikator_val,
      tipe_indikator: selected.tipe_indikator,
      pilihan: selected.pilihan,
      realisasi: null
    }
    indikator.push(obj);
    reloadTable();
  });

  function reloadTable(){
    var html = "";
    var prev_sasaran = "";
    indikator.sort((a,b) => ( (a.kode_indikator).localeCompare((b.kode_indikator), 'en', { numeric: true })));
        
    for(var i=0; i<indikator.length; i++){
      var obj = indikator[i];
      if(prev_sasaran!==obj.kode_sasaran){
        html += `<div class="row">
                    <div class="form-group col-md-12">
                      <label>
                        <b>SASARAN STRATEGIS ${obj.kode_sasaran.substr(1)+" : "+obj.nama_sasaran}</b>
                      </label>
                    </div>
                    <div class="col-md-12">
                      <table style="width:100%" id="table-i-${obj.target_id}" class="table table-striped table-md">
                        <thead>
                          <tr>
                            <th style="width: 100px;">KODE</th>
                            <th>INDIKATOR KINERJA PROGRAM</th>
                            <th class="center" style="width: 120px;">SATUAN</th>
                            <th class="center" style="width: 120px;">REALISASI</th>
                            <th class="center" style="width: 80px;">TARGET</th>
                            <th class="center" style="width: 80px;">NILAI</th>
                            <th class="center" style="width: 80px;">DOKUMEN</th>
                            <th style="width: 60px"></th>
                          </tr>
                        </thead>
                        <tbody>`;
      }
      var input_type = "number"; var htmlInp = ""; var nilai = 0;
      if(obj.tipe_indikator=="Persentase"||obj.tipe_indikator=="Batas Persentase"){
        htmlInp += `<input type="text" class="form-control percentage inpReal smallInput" autocomplete="off" data-detid="${obj.ind_det_id}" value="${obj.realisasi??""}"/>`;
        if(obj.tipe_indikator=="Persentase"&&obj.realisasi!==null){
          nilai = parseInt(obj.realisasi)/parseInt(obj.target_indikator_value)*100;
        }
      }else if(obj.tipe_indikator=="Angka"||obj.tipe_indikator=="Batas Angka"){
        htmlInp += `<input type="text" class="form-control number inpReal smallInput" autocomplete="off" data-detid="${obj.ind_det_id}" value="${obj.realisasi??""}"/>`;
        if(obj.tipe_indikator=="Angka"&&obj.realisasi!==null){
          nilai = Math.round(parseInt(obj.realisasi)/parseInt(obj.target_indikator_value)*100*100)/100;
        }
      }else{
        htmlInp += `<select class="form-control inpReal smallSelect" data-detid="${obj.ind_det_id}">`;
        for(var j=0; j<obj.pilihan.length; j++){
          htmlInp += `<option value="${obj.pilihan[j].nilai}" ${(obj.pilihan[j].nilai==obj.realisasi?"selected":"")}>${obj.pilihan[j].nama}</option>`;
        }
        htmlInp += `</select>`;
        if(obj.realisasi!==null){
          nilai = parseInt(obj.realisasi);
        }
      }
      html += `<tr>
                  <td>${obj.kode_indikator}</td>
                  <td>${obj.nama_indikator}</td>
                  <td class="center">${obj.satuan_indikator}</td>
                  <td>${htmlInp}</td>
                  <td class="center">${obj.target_indikator}</td>
                  <td class="center"><span class="spanNilai">${nilai}</span></td>
                  <td></td>
                  <td></td>
               </tr>`;
      if(i==indikator.length-1||indikator[i+1].kode_sasaran!==obj.kode_sasaran){          
        html+=          `</tbody>
                      </table>
                    </div>
                  </div>
                  <hr>`;
      }
      prev_sasaran = obj.kode_sasaran;
    }
    $("#divIsi").html(html);
  }

  $(document).on("change",".inpReal",function(){
    var detid = $(this).data("detid");
    console.log(detid);
    
    var obj = indikator.find(item=>item.ind_det_id==detid);
    if(obj!==null){
      obj.realisasi = $(this).val();
    }
    reloadTable();
  });

  $(document).on("input",".number",function(){
    if (/\D/g.test(this.value))
    {
      // Filter non-digits from input value.
      this.value = this.value.replace(/\D/g, '');
    }
  });

  $(document).on("input",".percentage",function(){
    if (/\D/g.test(this.value))
    {
      // Filter non-digits from input value.
      this.value = this.value.replace(/\D/g, '');
      if(parseInt(this.value)>100)
        this.value = 100;
    }
  });
</script>
