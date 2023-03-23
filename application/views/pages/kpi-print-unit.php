<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
  table, tr, td, th {
    border-collapse: collapse;
    border: 1px solid #dee2e6 !important;
    font-size: 12px;
  }
  .center{
    text-align: center;
  }
  table td {
    padding: 4px !important;
    font-size: 12px;
  }

  #table-realisasi td {
    padding: 0.75rem !important;
  }
  
  @media print{
    body * {
      visibility: hidden;
    }
    #divIsi, #divIsi * {
      visibility: visible;
    }
    .print-hide{
      display: none !important;
    }
    a { 
      color: black !important;
      text-decoration: none !important; 
    }
    #divIsi {
      position: absolute;
      left: 0;
      top: 0;
    }
    table, tr, td, th {
      border-collapse: collapse;
      border: 1px solid #000000 !important;
      font-size: 12px;
    }
    .center{
      text-align: center;
    }
    table td {
      padding: 4px !important;
      font-size: 12px;
    }
  }
  @page {
    size: a4 landscape !important;
  }
</style>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.0/css/all.min.css" integrity="sha512-3PN6gfRNZEX4YFyz+sIyTF6pGlQiryJu9NlGhu9LrLMQ7eDjNgudQoFDK3WSNAayeIKc6B8WXXpo4a7HqxjKwg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
<script>
  var tempid = 1;
  const average = function(array){
    console.log(array);
    if(array.length>0)
      return Math.round(array.reduce((a, b) => a + b) / array.length*100)/100
    else
      return null;
  };
  const sum = function(array){
    console.log(array);
    if(array.length>0)
      return Math.round(array.reduce((a, b) => a + b)*100)/100
    else
      return null;
  };
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
<div class="modal" id="modal-realisasi" tabindex="-1" role="dialog">
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
          <div class="col-md-12">
            <table style="width:100%; table-layout: fixed;" id="table-realisasi" class="table table-striped table-md">
              <thead>
                <tr>
                  <th style="width:10%">No</th>
                  <th style="width:75%">Nama</th>
                  <th style="width:75%">Realisasi</th>
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
    <div class="section-body">
      <br>
      <div class="row print-hide">
        <div class="form-group col-md-12">
          <div class="alert alert-danger">
            <div class="alert-title"><b>Catatan</b></div>
            <div class="alert-content">Nilai masih dapat berubah selama periode pengisian masih berlangsung</div>
          </div>              
        </div>
      </div>
      <form id="formAdd" method="POST" action="">
        <div class="card">
          <div class="card-body">
            <table style="width:100%" id="divIsi" class="table table-striped table-md">
                <tr>
                  <td colspan="7"><b><?php echo ($indicator->period_name); ?></b></td>
                </tr>
                <tr>
                  <td style="width: 75px;"><b>Periode </b></td>
                  <td colspan="6" style="text-align: left;"><b><?php echo ($indicator->period_from." - ".$indicator->period_to); ?></b></td>
                </tr>
                <tr>
                  <td style="width: 75px;"><b>Unit </b></td>
                  <td colspan="6" style="text-align: left;"><b><?php echo ($indicator->org_name); ?></b></td>
                </tr>
            </table>
          </div>
          <div class="card-footer bg-whitesmoke">
            <button id="btnExport" class="btn btn-sm btn-primary" onclick="makeExcel()" style="height: 24px; line-height: 14px; font-size: 12px; background-color: #6777ef;">
              Export To Excel&nbsp;&nbsp;<i class="fas fa-file-export"></i>
            </button>
            <button id="btnPrint" class="btn btn-sm btn-primary" onclick="window.print()" style="height: 24px; line-height: 14px; font-size: 12px; background-color: #6777ef;">
              Print&nbsp;&nbsp;<i class="fas fa-print"></i>
            </button>
            <button id="btnClose" class="btn btn-sm btn-danger" onclick="window.close();" style="height: 24px; line-height: 14px; font-size: 12px; background-color: #e84755;">
              Close&nbsp;&nbsp;<i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
<?php $this->load->view('pages/_partials/js'); ?>
<script>
  function makeExcel(){
    const wb = new ExcelJS.Workbook();
    wb.calcProperties.fullCalcOnLoad = true;
    wb.views = [
      {
        x: 0, y: 0, width: 10000, height: 20000,
        firstSheet: 0, activeTab: 1, visibility: 'visible',
        showGridLines: false
      }
    ];
    
    var ws = wb.addWorksheet('Export',{
      pageSetup:{paperSize: 9, orientation:'landscape',fitToPage: true}
    });
    ws.columns = [
      { width: 15 },
      { width: 60 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
      { width: 15 },
    ];
    var row = 1;
    //JUDUL
    ws.mergeCells(`A${row}:G${row}`);
    ws.getCell(`A${row}`).value = "KPI";
    ws.getCell(`A${row}`).font = {
      name: 'Calibri',
      family: 4,
      size: 14,
      underline: false,
      bold: true
    };
    row++;
    //PERIODE
    ws.mergeCells(`B${row}:G${row}`);
    ws.getCell(`A${row}`).value = "Periode";
    ws.getCell(`A${row}`).font = {
      name: 'Calibri',
      family: 4,
      size: 12,
      underline: false,
      bold: true
    };
    ws.getCell(`B${row}`).value = "<?php echo ($indicator->period_from." - ".$indicator->period_to); ?>";
    ws.getCell(`B${row}`).font = {
      name: 'Calibri',
      family: 4,
      size: 12,
      underline: false,
      bold: true
    };
    row++;

    //UNIT
    ws.mergeCells(`B${row}:G${row}`);
    ws.getCell(`A${row}`).value = "Unit";
    ws.getCell(`A${row}`).font = {
      name: 'Calibri',
      family: 4,
      size: 12,
      underline: false,
      bold: true
    };
    ws.getCell(`B${row}`).value = "<?php echo ($indicator->org_name); ?>";
    ws.getCell(`B${row}`).font = {
      name: 'Calibri',
      family: 4,
      size: 12,
      underline: false,
      bold: true
    };
    row++;

    //LOOP THROUGH INDICATOR
    var prev_sasaran = "";
    indikator.sort((a,b) => ( (a.kode_indikator).localeCompare((b.kode_indikator), 'en', { numeric: true })));
    var totalpersasaran = 0, count = 0, grandtotal = 0, countsasaran = 0;    
    var fontHeader = {
          name: 'Calibri',
          family: 4,
          size: 12,
          underline: false,
          bold: true
        };
    var fontContent = {
          name: 'Calibri',
          family: 4,
          size: 12,
          underline: false,
          bold: false
        };
    var bgAliceBlue = {
                type: 'pattern',
                pattern: 'darkVertical',
                fgColor: {
                    argb: '00F0F8FF'
                }
            };;
    for(var i=0; i<indikator.length; i++){
      var obj = indikator[i];
      //HEADER
      if(prev_sasaran!==obj.kode_sasaran){
        ws.mergeCells(`A${row}:G${row}`);
        row++;
        ws.mergeCells(`A${row}:G${row}`);
        ws.getCell(`A${row}`).value = `SASARAN STRATEGIS ${obj.kode_sasaran.substr(1)+" : "+obj.nama_sasaran}`;
        ws.getCell(`A${row}`).font = fontHeader;
        row++;
        ws.getRow(row).alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
        ws.getRow(row).font = fontHeader;
        ws.getCell(`A${row}`).value = `KODE`;
        ws.getCell(`B${row}`).value = `INDIKATOR KINERJA PROGRAM`;
        ws.getCell(`C${row}`).value = `SATUAN`;
        ws.getCell(`D${row}`).value = `REALISASI`;
        ws.getCell(`E${row}`).value = `TARGET`;
        ws.getCell(`F${row}`).value = `NILAI`;
        ws.getCell(`G${row}`).value = `DOKUMEN`;
        [`A${row}`,`B${row}`,`C${row}`,`D${row}`,`E${row}`,`F${row}`,`G${row}`].map(key => {
              ws.getCell(key).border = {
                top: {style:'thin'},
                left: {style:'thin'},
                bottom: {style:'thin'},
                right: {style:'thin'}
              };
        });
        row++;
      }
      var input_type = "number"; var htmlInp = ""; var nilai = null;
      obj.realisasi = null;
      //cari realisasi tiap2 karyawan di unit tsb
      var arr_realisasi = realisasi.filter(item=>item.ind_det_id==obj.ind_det_id);
      obj.realisasi = average(arr_realisasi.map(item=>parseFloat(item.realisasi)));
      obj.dokumen = (arr_realisasi==null?0:sum(arr_realisasi.map(item=>parseFloat(item.dokumen))));
      if(obj.realisasi!==null){
        if(obj.tipe_indikator=="Persentase"||obj.tipe_indikator=="Batas Persentase"){
          htmlInp += `${obj.realisasi+"%"??""}`;
          if(obj.tipe_indikator=="Persentase"&&obj.realisasi!==null){
            nilai = Math.round(parseFloat(obj.realisasi)/parseFloat(obj.target_indikator_value)*100*100)/100;
          }else if(obj.tipe_indikator=="Batas Persentase"&&obj.realisasi!==null){
            nilai = Math.round(parseFloat(obj.target_indikator_value)/parseFloat(obj.realisasi)*100*100)/100;
          }
        }else if(obj.tipe_indikator=="Angka"||obj.tipe_indikator=="Batas Angka"){
          htmlInp += `${obj.realisasi??""}`;
          if(obj.tipe_indikator=="Angka"&&obj.realisasi!==null){
            nilai = Math.round(parseFloat(obj.realisasi)/parseFloat(obj.target_indikator_value)*100*100)/100;
          }else if(obj.tipe_indikator=="Batas Angka"&&obj.realisasi!==null){
            nilai = Math.round(parseFloat(obj.target_indikator_value)/parseFloat(obj.realisasi)*100*100)/100;
          }
        }else{
          for(var j=0; j<obj.pilihan.length; j++){
            htmlInp += `${(obj.pilihan[j].nilai==obj.realisasi?obj.pilihan[j].nama:"")}`;
          }
        }
      }else{
        htmlInp += `${obj.realisasi??"n/a"}`;
      }
      if(nilai>100)
        nilai = 100;
      if(nilai!==null){
        totalpersasaran+=nilai; count++;
      }

      //ISI PER INDIKATOR
      ws.getRow(row).alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
      [`A${row}`,`B${row}`,`C${row}`,`D${row}`,`E${row}`,`F${row}`,`G${row}`].map(key => {
            ws.getCell(key).border = {
              top: {style:'thin'},
              left: {style:'thin'},
              bottom: {style:'thin'},
              right: {style:'thin'}
            };
      });
      ws.getRow(row).font = fontContent;
      ws.getCell(`A${row}`).value = `${obj.kode_indikator}`;
      ws.getCell(`A${row}`).alignment = { vertical: 'middle', horizontal: 'left', wrapText: true };
      ws.getCell(`B${row}`).value = `${obj.nama_indikator}`;
      ws.getCell(`B${row}`).alignment = { vertical: 'middle', horizontal: 'left', wrapText: true };
      ws.getCell(`C${row}`).value = `${obj.satuan_indikator}`;
      ws.getCell(`D${row}`).value = `${htmlInp}`;
      ws.getCell(`E${row}`).value = `${((obj.tipe_indikator=="Persentase"||obj.tipe_indikator=="Batas Persentase")?obj.target_indikator+"%":obj.target_indikator)}`;
      ws.getCell(`F${row}`).value = `${nilai??"n/a"}`;
      ws.getCell(`G${row}`).value = `${nilai==null?"":(obj.dokumen>0?"Ada":"Tidak Ada")}`;
      row++;
      if(i==indikator.length-1||indikator[i+1].kode_sasaran!==obj.kode_sasaran){  
        countsasaran++;
        grandtotal+=Math.round(totalpersasaran/count*100)/100;
        
        ws.mergeCells(`A${row}:E${row}`);
        ws.getRow(row).font = fontHeader;
        
        ws.getCell(`A${row}`).alignment = { vertical: 'middle', horizontal: 'right', wrapText: true };
        ws.getCell(`A${row}`).value = `JUMLAH`;
        ws.getCell(`F${row}`).value = `${Math.round(totalpersasaran/count*100)/100}`;
        ws.getCell(`F${row}`).alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
        [`A${row}`,`F${row}`,`G${row}`].map(key => {
              ws.getCell(key).border = {
                top: {style:'thin'},
                left: {style:'thin'},
                bottom: {style:'thin'},
                right: {style:'thin'}
              };
        });
        row++;

        if(i==indikator.length-1){
          ws.mergeCells(`A${row}:E${row}`);
          ws.getRow(row).font = fontHeader;
          ws.getCell(`A${row}`).alignment = { vertical: 'middle', horizontal: 'right', wrapText: true };
          ws.getCell(`A${row}`).value = `TOTAL NILAI`;
          ws.getCell(`F${row}`).value = `${Math.round(grandtotal/countsasaran*100)/100}`;
          ws.getCell(`F${row}`).alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
          [`A${row}`,`F${row}`,`G${row}`].map(key => {
                ws.getCell(key).border = {
                  top: {style:'thin'},
                  left: {style:'thin'},
                  bottom: {style:'thin'},
                  right: {style:'thin'}
                };
          });
          row++;
        }
        totalpersasaran = 0; count = 0;
      }
      prev_sasaran = obj.kode_sasaran;
    }
    //$("#divIsi").append(html);

    //WRITE TO FILE
    wb.xlsx.writeBuffer( {
        base64: true
    })
    .then( function (xls64) {
        // build anchor tag and attach file (works in chrome)
        var a = document.createElement("a");
        var data = new Blob([xls64], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

        var url = URL.createObjectURL(data);
        a.href = url;
        a.download = "export.xlsx";
        document.body.appendChild(a);
        a.click();
        setTimeout(function() {
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            },
            0);
    })
    .catch(function(error) {
        console.log(error.message);
    });
  }
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
  var realisasi = [];

  <?php echo "indikator = ".json_encode($indicator->details).";"; ?>
  <?php echo "realisasi = ".json_encode($indicator->unit_details).";"; ?>
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
      var input_type = "number"; var htmlInp = ""; var nilai = null;
      obj.realisasi = null;
      //cari realisasi tiap2 karyawan di unit tsb
      var arr_realisasi = realisasi.filter(item=>item.ind_det_id==obj.ind_det_id);
      obj.realisasi = average(arr_realisasi.map(item=>parseFloat(item.realisasi)));
      obj.dokumen = (arr_realisasi==null?0:sum(arr_realisasi.map(item=>parseFloat(item.dokumen))));
      //console.log("realisasi ",obj.ind_det_id,obj.kode_indikator,obj.realisasi);
      if(obj.realisasi!==null){
        if(obj.tipe_indikator=="Persentase"||obj.tipe_indikator=="Batas Persentase"){
          htmlInp += `${obj.realisasi+"%"??""}`;
          if(obj.tipe_indikator=="Persentase"&&obj.realisasi!==null){
            nilai = Math.round(parseFloat(obj.realisasi)/parseFloat(obj.target_indikator_value)*100*100)/100;
          }else if(obj.tipe_indikator=="Batas Persentase"&&obj.realisasi!==null){
            nilai = Math.round(parseFloat(obj.target_indikator_value)/parseFloat(obj.realisasi)*100*100)/100;
          }
        }else if(obj.tipe_indikator=="Angka"||obj.tipe_indikator=="Batas Angka"){
          htmlInp += `${obj.realisasi??""}`;
          if(obj.tipe_indikator=="Angka"&&obj.realisasi!==null){
            nilai = Math.round(parseFloat(obj.realisasi)/parseFloat(obj.target_indikator_value)*100*100)/100;
          }else if(obj.tipe_indikator=="Angka"&&obj.realisasi!==null){
            nilai = Math.round(parseFloat(obj.target_indikator_value)/parseFloat(obj.realisasi)*100*100)/100;
          }
        }else{
          for(var j=0; j<obj.pilihan.length; j++){
            htmlInp += `${(obj.pilihan[j].nilai==obj.realisasi?obj.pilihan[j].nama:"")}`;
          }
          nilai = parseFloat(obj.realisasi);
        }
      }else{
        htmlInp += `${obj.realisasi??"n/a"}`;
      }
      if(nilai>100)
        nilai = 100;
      if(nilai!==null){
        totalpersasaran+=nilai; count++;
      }
      console.log("dokumen "+obj.kode_indikator,obj.dokumen);
      html += `<tr>
                  <td>${obj.kode_indikator}</td>
                  <td>${obj.nama_indikator}</td>
                  <td class="center">${obj.satuan_indikator}</td>
                  <td class="center"><a href="#" onclick="openModalRealisasi(${obj.ind_det_id});">${htmlInp}</a></td>
                  <td class="center">${((obj.tipe_indikator=="Persentase"||obj.tipe_indikator=="Batas Persentase")?obj.target_indikator+"%":obj.target_indikator)}</td>
                  <td class="center"><span class="spanNilai">${nilai??"n/a"}</span></td>
                  <td class="center"><span>${nilai==null?"":(obj.dokumen>0?"Ada":"Tidak Ada")}</span></td>
               `;
      if(i==indikator.length-1||indikator[i+1].kode_sasaran!==obj.kode_sasaran){  
        countsasaran++;
        grandtotal+=Math.round(totalpersasaran/count*100)/100;
           
        html += `<tr style="background-color: #f9f8ff;">
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

  function populateRealisasiTable(ind_det_id){
    var selected = realisasi.filter(item=>item.ind_det_id==ind_det_id);
    console.log(selected);
    var html = "";
    for(var i=0; i<selected.length; i++){
      var obj = selected[i];
      html += `<tr>
                  <td>${(i+1)}</td>
                  <td>${(obj.name)}</td>
                  <td>${obj.realisasi}</td>
              </tr>`;
    }
    if(html==""){
      html +=   `<tr>
                  <td colspan="3" style="text-align: center;">Tidak ada data</td>
                </tr>`;
    }
    $("#table-realisasi tbody").html(html);
  }

  function openModalRealisasi(ind_det_id){
    var obj = realisasi.find(item=>item.ind_det_id==ind_det_id);
    $("#modal-realisasi .modal-title").html("REALISASI");
    $("#modal-realisasi #inp-detid").val(ind_det_id);
    populateRealisasiTable(ind_det_id);
    $("#modal-realisasi").modal("show");
  };

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
