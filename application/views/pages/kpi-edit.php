<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('pages/_partials/header');
?>
<style>
  table tbody td{
    word-wrap:break-word;
  }
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
  input.inpReal{
    text-align: center;
  }
  .file{
    padding: 5px 7px !important;
  }
  input[type=file]::file-selector-button {
    margin-right: 20px;
    border: none;
    background: #6777ef;
    padding: 4px 8px !important;
    border-radius: 2px;
    color: #fff;
    cursor: pointer;
    font-size: 10px;
    top: -4px;
    transition: background .2s ease-in-out;
  }

  input[type=file]::file-selector-button:hover {
    background: #0d45a5;
  }

</style>
<script>
  var indikator = [];
  var tempid = 1;

</script>
<div class="modal" id="modal-document" tabindex="-1" role="dialog">
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
          <?php if($indicator->status=="Belum Ada"||$indicator->status=="Draft"||$indicator->status=="Menunggu Revisi"){ ?>
          <div class="col-md-10">
            <input type="file" class="form-control file" id="inp-file" multiple/>
            <input type="hidden" class="form-control" id="inp-detid" readonly/>
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-primary btn-upload" style="top: 5px;position: relative;">Unggah</button>
          </div>
          <?php } ?>
        </div>
        <div class="row">
          <div class="col-md-12">
            <table style="width:100%; table-layout: fixed;" id="table-document" class="table table-striped table-md">
              <thead>
                <tr>
                  <th style="width:5%">No</th>
                  <th style="width:75%">Nama Dokumen</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr><td colspan="3" style="text-align:center">Tidak ada data</td></tr>
              </tbody>
            </table>
          </div>
        </div>
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
                  <label>Nama</label>
                  <input type="text" class="form-control" id="name" name="name" autocomplete="off" value="<?php echo ($indicator->period_name); ?>" readonly>
                  <input type="hidden" class="form-control" id="id" name="id" autocomplete="off" value="<?php echo $indicator->indicator_id; ?>" required>
              </div>
              <div class="form-group col-md-5">
                  <label>Periode Pengisian</label>
                  <input type="text" class="form-control" id="period" name="period" autocomplete="off" value="<?php echo ($indicator->period_from." - ".$indicator->period_to); ?>" readonly>
              </div>
            </div>
            <?php if($indicator->status=="Belum Ada"||$indicator->status=="Draft"||$indicator->status=="Menunggu Revisi"){ ?>
            <div class="row">
              <div class="form-group col-md-8">
                <input type="text" class="form-control" id="cboIndicator"/>
              </div>
              <div class="form-group col-md-2">
                <button type="button" class="btn btn-sm btn-primary" title="Tambah" id="btnAddIndicator" style="top: 5px;position: relative;">
                  &nbsp;<i class='fa fa-plus'></i>&nbsp;
                </button>
                <button type="button" class="btn btn-sm btn-primary" title="Tambah Semua" id="btnAddAllIndicators" style="top: 5px;position: relative;">
                  &nbsp;<i class='fa fa-arrow-down'></i>&nbsp;
                </button>
              </div>
            </div>
            <hr/>
            <?php } ?>
            <?php if($indicator->remarks!==""){ ?>
            <div class="row">
              <div class="form-group col-md-10" <?php if($indicator->status!=="Menunggu Revisi"){ echo "style=\"display: none\""; } ?>>
                  <label>Catatan</label>
                  <textarea class="form-control" id="note" name="note" autocomplete="off" disabled><?php echo $indicator->remarks; ?></textarea>
              </div>
            </div>
            <?php } ?>
            <div id="divIsi">
              
            </div>
          </div>
          <div class="card-footer bg-whitesmoke">
            <?php if($indicator->status=="Belum Ada"||$indicator->status=="Draft"||$indicator->status=="Menunggu Revisi"){ ?>
              <button type="button" id="btn-save" class="btn btn-primary">Simpan</button>
            <?php } ?>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
<?php $this->load->view('pages/_partials/footer'); ?>
<script>
  var tempindikator = [];
  <?php if($this->session->flashdata('message')){ ?>
    alert("<?php echo $this->session->flashdata('message'); ?>");
  <?php } ?>
  
  var indikator = [];

  <?php echo "indikator = ".json_encode($indicator->details).";"; ?>
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
        tempindikator = data.indicator;
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
      ind_user_det_id: -1,
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
      realisasi: null,
      dokumen: []
    }
    indikator.push(obj);
    reloadTable();
  });

  $(document).on("click","#btnAddAllIndicators",function(){
    for(var i=0;i<tempindikator.length;i++){
      selected = tempindikator[i];
      
      if(indikator.findIndex(item=>item.ind_det_id==selected.ind_det_id)>-1){}else{
        var obj = {
          ind_user_det_id: -1,
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
          realisasi: null,
          dokumen: []
        }
        indikator.push(obj);
      }
    }
    
    reloadTable();
  });

  function reloadTable(){
    var html = "";
    var prev_sasaran = "";
    indikator.sort((a,b) => ( (a.kode_indikator).localeCompare((b.kode_indikator), 'en', { numeric: true })));
    var totalpersasaran = 0, count = 0, grandtotal = 0, countsasaran = 0;    
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
          <?php if($indicator->status=="Belum Ada"||$indicator->status=="Draft"||$indicator->status=="Menunggu Revisi"){ ?>
            htmlInp += `<input type="text" class="form-control percentage inpReal smallInput" autocomplete="off" data-detid="${obj.ind_det_id}" value="${obj.realisasi??""}"/>`;
          <?php }else{ ?>
            htmlInp += `<span style="text-align:center;">${obj.realisasi??""}</span>`;
          <?php } ?>

          if(obj.tipe_indikator=="Persentase"&&obj.realisasi!==null&&obj.realisasi!==""){
            nilai = Math.round(parseFloat(obj.realisasi)/parseFloat(obj.target_indikator_value)*100*100)/100;
          }else if(obj.tipe_indikator=="Batas Persentase"&&obj.realisasi!==null&&obj.realisasi!==""){
            console.log("realisasi",(parseFloat(obj.target_indikator_value)-parseFloat(obj.realisasi)),);
            nilai = Math.round(parseFloat(obj.target_indikator_value)/parseFloat(obj.realisasi)*100*100)/100;
          }
      }else if(obj.tipe_indikator=="Angka"||obj.tipe_indikator=="Batas Angka"){
        <?php if($indicator->status=="Belum Ada"||$indicator->status=="Draft"||$indicator->status=="Menunggu Revisi"){ ?>
          htmlInp += `<input type="text" class="form-control number inpReal smallInput" autocomplete="off" data-detid="${obj.ind_det_id}" value="${obj.realisasi??""}"/>`;
        <?php }else{ ?>
          htmlInp += `<span style="text-align:center;">${obj.realisasi??""}</span>`;
        <?php } ?>

        if(obj.tipe_indikator=="Angka"&&obj.realisasi!==null&&obj.realisasi!==""){
          nilai = Math.round(parseFloat(obj.realisasi)/parseFloat(obj.target_indikator_value)*100*100)/100;
        }else if(obj.tipe_indikator=="Batas Angka"&&obj.realisasi!==null&&obj.realisasi!==""){
          nilai = Math.round((parseFloat(obj.target_indikator_value)/parseFloat(obj.realisasi))*100*100)/100;
        }
      }else{
        <?php if($indicator->status=="Belum Ada"||$indicator->status=="Draft"||$indicator->status=="Menunggu Revisi"){ ?>
          htmlInp += `<select class="form-control inpReal smallSelect" data-detid="${obj.ind_det_id}">`;
          for(var j=0; j<obj.pilihan.length; j++){
            htmlInp += `<option value="${obj.pilihan[j].nilai}" ${(obj.pilihan[j].nilai==obj.realisasi?"selected":"")}>${obj.pilihan[j].nama}</option>`;
          }
          htmlInp += `</select>`;
        <?php }else{ ?>
          var selected = obj.pilihan.find(item=>item.nilai==obj.realisasi);
          htmlInp += `<span style="text-align:center;">${selected.nama??""}</span>`;
        <?php } ?>
        if(obj.realisasi!==null){
          nilai = parseFloat(obj.realisasi);
        }
      }
      if(nilai>100)
        nilai = 100;
      totalpersasaran+=nilai; count++;
      html += `<tr>
                  <td>${obj.kode_indikator}</td>
                  <td>${obj.nama_indikator}</td>
                  <td class="center">${obj.satuan_indikator}</td>
                  <td>${htmlInp}</td>
                  <td class="center">${((obj.tipe_indikator=="Persentase"||obj.tipe_indikator=="Batas Persentase")?obj.target_indikator+"%":obj.target_indikator)}</td>
                  <td class="center"><span class="spanNilai">${nilai}</span></td>
                  <td><span>${obj.dokumen.length}</span><button type="button" data-detid="${obj.ind_det_id}" class="btn btn-icon btn-sm btn-secondary btn-document" style="float: right" onclick="return false;"><i class="fa fa-search"></i></button></td>
                  <td>
                    <?php if($indicator->status=="Belum Ada"||$indicator->status=="Draft"||$indicator->status=="Menunggu Revisi"){ ?>
                    <button type="button" data-detid="${obj.ind_det_id}" class="btn btn-icon btn-sm btn-danger btn-remove-indicator"><i class="fa fa-times"></i></button>
                    <?php } ?>
                  </td>
               </tr>`;
      if(i==indikator.length-1||indikator[i+1].kode_sasaran!==obj.kode_sasaran){  
        countsasaran++;
        grandtotal+=Math.round(totalpersasaran/count*100)/100;
           
        html += `<tr style="background-color: aliceblue;">
                  <td colspan="5" style="text-align: right">JUMLAH</td>
                  <td class="center"><span class="spanNilai">${Math.round(totalpersasaran/count*100)/100}</span></td>
                  <td  colspan="2"></td>
               </tr>`;     
        if(i==indikator.length-1){
          html += `<tr style="background-color: lightgrey;">
                      <td colspan="5" style="text-align: right">TOTAL NILAI</td>
                      <td class="center"><span class="spanNilai">${Math.round(grandtotal/countsasaran*100)/100}</span></td>
                      <td  colspan="2"></td>
                  </tr>`; 
        }
        html +=         `</tbody>
                      </table>
                    </div>
                  </div>
                  <hr>`;
        totalpersasaran = 0; count = 0;
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
      this.value = this.value.replace(/[^\d.]+/g, '');
    }
  });

  $(document).on("input",".percentage",function(){
    if (/\D/g.test(this.value))
    {
      // Filter non-digits from input value.
      this.value = this.value.replace(/[^\d.]+/g, '');
      if(parseFloat(this.value)>100)
        this.value = 100;
    }
  });

  $(document).on("click",".btn-remove-indicator", function(){
    if(confirm("Hapus indikator ini dari KPI Anda?")){
      var ind_det_id = $(this).data("detid");
      var idx = indikator.findIndex(item=>item.ind_det_id==ind_det_id);
      indikator.splice(idx,1);
      reloadTable();
    }
  });

  $(document).on("click",".btn-document", function(){
    var ind_det_id = $(this).data("detid");
    var obj = indikator.find(item=>item.ind_det_id==ind_det_id);
    $("#modal-document .modal-title").html(obj.kode_indikator+" "+obj.nama_indikator);
    $("#modal-document #inp-detid").val(ind_det_id);
    populateAttachmentTable(ind_det_id);
    $("#modal-document").modal("show");
  });

  $(document).on("click",".btn-upload", function(){
    if(confirm("Unggah dokumen ini ?")){
        if(1==1){//tambah ke temporary table
            var items = $("#inp-file")[0];
            var filelength = items.files.length;
            var ind_det_id = $("#inp-detid").val().trim();
            var selected = indikator.find(item=>item.ind_det_id==ind_det_id);
            if(items.files.length > 0){
                for(var i=0;i<items.files.length;i++){
                    (function(file,selected){
                        toBase64(file).then(function(datafile){
                            console.log(datafile.file.name);
                            var d = new Date();
                            var splitname = datafile.file.name.split(".");
                            var ext = splitname[splitname.length-1];
                            selected.dokumen.push({ id: -1, filename: datafile.file.name, file: datafile.value });
                        });
                    })(items.files[i],selected);
                    if(i==items.files.length-1){
                        setTimeout(function(){
                            $("#inp-file").val(null);
                            populateAttachmentTable(ind_det_id);
                            reloadTable();
                        },500);
                    }
                }
            }
        }
    }
  });

  function populateAttachmentTable(ind_det_id){
    var selected = indikator.find(item=>item.ind_det_id==ind_det_id);
    var html = "";
    for(var i=0; i<selected.dokumen.length; i++){
      var obj = selected.dokumen[i];
      html += `<tr>
                  <td>${(i+1)}</td>
                  <td>${(obj.filename)}</td>
                  <td>`;
      if(obj.id==-1){
        html+=     `<button type="button" data-idx="${i}" data-detid="${ind_det_id}" class="btn btn-icon btn-sm btn-primary btn-download-temp" onclick="downloadBase64File('${obj.file}','${obj.filename}')">
                      <i class="fa fa-download"></i>
                    </button>`;
      }else{
        html+=     `<a href="<?php echo base_url(); ?>${obj.file}" target="_blank" class="btn btn-icon btn-sm btn-primary btn-download-temp">
                      <i class="fa fa-download"></i>
                    </a>`;
      }
        html+=      `<?php if($indicator->status=="Belum Ada"||$indicator->status=="Draft"||$indicator->status=="Menunggu Revisi"){ ?>
                    <button type="button" data-idx="${i}" data-detid="${ind_det_id}" class="btn btn-icon btn-sm btn-danger btn-remove-doc">
                      <i class="fa fa-times"></i>
                    </button>
                    <?php } ?>
                  </td>
              </tr>`;
    }
    if(html==""){
      html +=   `<tr>
                  <td colspan="3" style="text-align: center;">Tidak ada data</td>
                </tr>`;
    }
    $("#table-document tbody").html(html);
  }

  $(document).on("click", ".btn-remove-doc", function(){
    if(confirm("Hapus dokumen ini?")){
      var selected = indikator.find(item=>item.ind_det_id==parseInt($(this).data("detid")));
      selected.dokumen.splice(parseInt($(this).data("idx")),1);
      populateAttachmentTable(parseInt($(this).data("detid")));
      reloadTable();
    }
  });
  <?php if($indicator->status=="Belum Ada"||$indicator->status=="Draft"||$indicator->status=="Menunggu Revisi"){ ?>
  $(document).on("click", "#btn-save", function(){
    $.ajax({
        url: '<?php echo base_url(); ?>kpi/api/save_kpi',
        type: 'POST',
        data: JSON.stringify({
                id: $("#id").val(),
                data: indikator
              }),
        dataType : "json",
        contentType: "application/json; charset=utf-8",
        success: function(data) {
          console.log(data);
          alert(data.message);
          if(data.ok==1){
            window.location.replace("<?php echo base_url(); ?>kpi");
          }
        },
        error: function(data) {
            console.log(data);
        }
    });
  });
  <?php } ?>

  function toBase64(file){
    console.log("toBase64 called");
    return new Promise(function(resolve, reject) {
      var reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = function(){
          dataURL = reader.result;
          dataURL = dataURL.replace('data:', '').replace(/^.+,/, '');
          
          resolve({value: dataURL, file: file});
      }
      // Typescript users: use following line
      // reader.onload = () => resolve(reader.result as string);
      reader.onerror = reject;
    });
  }

  function downloadBase64File(contentBase64, fileName) {
    var splitname = fileName.split(".");                
    var tempExt = splitname[splitname.length-1]
    var mimetype = getMimeByExt(tempExt.toString().toLowerCase());
    console.log(mimetype);
    const linkSource = "data:"+mimetype+";base64,"+contentBase64;
    const downloadLink = document.createElement('a');
    document.body.appendChild(downloadLink);

    downloadLink.href = linkSource;
    downloadLink.target = '_self';
    downloadLink.download = fileName;
    downloadLink.click(); 
  }   


  (function() {
    var extToMimes = {
        'jpg': 'image/jpeg',
        'png': 'image/png',
        'pdf': 'application/pdf',
        'xlsx': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xls': 'application/vnd.ms-excel',
        'doc': 'application/msword',
        'docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    }

    window.getMimeByExt = function(ext) {
        if (extToMimes.hasOwnProperty(ext)) {
            return extToMimes[ext];
        }
        return false;
    }

  })();
  reloadTable();
</script>
