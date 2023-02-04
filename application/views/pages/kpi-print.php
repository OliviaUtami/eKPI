<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
  table, tr, td, th {
    border-collapse: collapse;
    border: 1px solid;
    font-size: 12px;
  }
  .center{
    text-align: center;
  }
  table td {
    padding: 4px !important;
    font-size: 12px;
  }
  @media print{
    body * {
      visibility: hidden;
    }
    #divIsi, #divIsi * {
      visibility: visible;
    }
    #divIsi {
      position: absolute;
      left: 0;
      top: 0;
    }
    table, tr, td, th {
      border-collapse: collapse;
      border: 1px solid;
      font-size: 12px;
    }
    table td {
      padding: 4px !important;
      font-size: 12px;
    }
    .center{
      text-align: center;
    }
  }
</style>
<script>
  var indikator = [];
  var tempid = 1;

</script>
<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <form id="formAdd" method="POST" action="">
        <div class="card">
          <div class="card-body">
            <table style="width:100%" id="divIsi" class="table table-striped table-md">
                <tr>
                  <td colspan="7"><b><?php echo ($indicator->period_name); ?></b></td>
                </tr>
                <tr>
                  <td style="width: 90px;"><b>Periode </b></td>
                  <td colspan="6" style="text-align: left;"><b><?php echo ($indicator->period_from." - ".$indicator->period_to); ?></b></td>
                </tr>
                <tr>
                  <td style="width: 90px;"><b>Organisasi </b></td>
                  <td colspan="6" style="text-align: left;"><b><?php echo ($indicator->org_name); ?></b></td>
                </tr>
                <tr>
                  <td style="width: 90px;"><b>Nama </b></td>
                  <td colspan="6" style="text-align: left;"><b><?php echo ($indicator->name); ?></b></td>
                </tr>
            </table>
          </div>
          <div class="card-footer bg-whitesmoke">
            <button id="btnExport" class="btn green btn-xs" onclick="ExportToExcel('divIsi')">Export To Excel<i class="fa fa-expand"></i></button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
<?php $this->load->view('pages/_partials/footer'); ?>
<script>
  function ExportToExcel(tableid) {
      var tab_text = "<table><tr>";
      var textRange; var j = 0;
      tab = document.getElementById(tableid);//.getElementsByTagName('table'); // id of table
      if (tab==null||tab.rows.length == 0) {
          tab_text = "<table>";
      }
      if (tab.rows.length > 0) {
          for (j = 0 ; j < tab.rows.length ; j++) {
              tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
              //tab_text=tab_text+"</tr>";
          }
      }
      
      tab_text = tab_text + "</table>";
      tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
      tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
      tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

      var ua = window.navigator.userAgent;
      var msie = ua.indexOf("MSIE ");

      if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
      {
          txtArea1.document.open("txt/html", "replace");
          txtArea1.document.write(tab_text);
          txtArea1.document.close();
          txtArea1.focus();
          sa = txtArea1.document.execCommand("SaveAs", true, "download.xls");
      }
      else                 //other browser not tested on IE 11
          //sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
          try {
              var blob = new Blob([tab_text], { type: "application/vnd.ms-excel" });
              window.URL = window.URL || window.webkitURL;
              link = window.URL.createObjectURL(blob);
              a = document.createElement("a");
              if (document.getElementById("caption")!=null) {
                  a.download=document.getElementById("caption").innerText;
              }
              else
              {
                  a.download = 'download.xls';
              }

              a.href = link;

              document.body.appendChild(a);

              a.click();

              document.body.removeChild(a);
          } catch (e) {
          }


      return false;
  }

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
        
        html += ` <tr>
                    <td colspan="7">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="7"><b>SASARAN STRATEGIS ${obj.kode_sasaran.substr(1)+" : "+obj.nama_sasaran}</b></td>
                  </tr>
                  <tr>   
                    
                          <tr>
                            <th style="width: 100px;">KODE</th>
                            <th>INDIKATOR KINERJA PROGRAM</th>
                            <th class="center" style="width: 120px;">SATUAN</th>
                            <th class="center" style="width: 100px;">REALISASI</th>
                            <th class="center" style="width: 100px;">TARGET</th>
                            <th class="center" style="width: 100px;">NILAI</th>
                            <th class="center" style="width: 100px;">DOKUMEN</th>
                          </tr>
                        `;
      }
      var input_type = "number"; var htmlInp = ""; var nilai = 0;
      if(obj.tipe_indikator=="Persentase"||obj.tipe_indikator=="Batas Persentase"){
        htmlInp += `${obj.realisasi??""}`;
        if(obj.tipe_indikator=="Persentase"&&obj.realisasi!==null){
          nilai = Math.round(parseInt(obj.realisasi)/parseInt(obj.target_indikator_value)*100*100)/100;
        }else if(obj.tipe_indikator=="Batas Persentase"&&obj.realisasi!==null){
          nilai = Math.round((parseInt(obj.target_indikator_value)-parseInt(obj.realisasi))/parseInt(obj.target)*100*100)/100;
        }
      }else if(obj.tipe_indikator=="Angka"||obj.tipe_indikator=="Batas Angka"){
        htmlInp += `${obj.realisasi??""}`;
        if(obj.tipe_indikator=="Angka"&&obj.realisasi!==null){
          nilai = Math.round(parseInt(obj.realisasi)/parseInt(obj.target_indikator_value)*100*100)/100;
        }else if(obj.tipe_indikator=="Angka"&&obj.realisasi!==null){
          nilai = Math.round((parseInt(obj.target_indikator_value)-parseInt(obj.realisasi))/parseInt(obj.target_indikator_value)*100*100)/100;
        }
      }else{
        for(var j=0; j<obj.pilihan.length; j++){
          htmlInp += `${(obj.pilihan[j].nilai==obj.realisasi?obj.pilihan[j].nama:"")}`;
        }
        nilai = parseInt(obj.realisasi);
      }
      totalpersasaran+=nilai; count++;
      html += `<tr>
                  <td>${obj.kode_indikator}</td>
                  <td>${obj.nama_indikator}</td>
                  <td class="center">${obj.satuan_indikator}</td>
                  <td class="center">${htmlInp}</td>
                  <td class="center">${obj.target_indikator}</td>
                  <td class="center"><span class="spanNilai">${nilai}</span></td>
                  <td class="center"><span>${obj.dokumen.length}</span></td>
               `;
      if(i==indikator.length-1||indikator[i+1].kode_sasaran!==obj.kode_sasaran){  
        countsasaran++;
        grandtotal+=Math.round(totalpersasaran/count*100)/100;
           
        html += `<tr style="background-color: aliceblue;">
                  <td colspan="5" style="text-align: right">JUMLAH</td>
                  <td class="center"><span class="spanNilai">${Math.round(totalpersasaran/count*100)/100}</span></td>
                  <td></td>
               </tr>`;     
        if(i==indikator.length-1){
          html += `<tr style="background-color: lightgrey;">
                      <td colspan="5" style="text-align: right">TOTAL NILAI</td>
                      <td class="center"><span class="spanNilai">${Math.round(grandtotal/countsasaran*100)/100}</span></td>
                      <td></td>
                  </tr>`; 
        }
        html +=         `
                  `;
        totalpersasaran = 0; count = 0;
      }
      prev_sasaran = obj.kode_sasaran;
    }
    $("#divIsi").append(html);
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
        html+=      `<button type="button" data-idx="${i}" data-detid="${ind_det_id}" class="btn btn-icon btn-sm btn-danger btn-remove-doc">
                      <i class="fa fa-times"></i>
                    </button>
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
            location.reload();
          }
        },
        error: function(data) {
            console.log(data);
        }
    });
  });

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
