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
        <button type="button" class="btn btn-primary" onclick="saveIndikator()">Simpan</button>
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
                  <input type="hidden" class="form-control" id="id" name="id" autocomplete="off" value="<?php echo $indicator->draft_id; ?>" required>
              </div>
            </div>
            <?php 
                $i = 1;
                foreach($indicator->targets as $target){ 
                  echo "<script>indikator.push({target_id:".$target->id.",details:[]});</script>";
            ?>

            <div class="row">
              <div class="form-group col-md-12">
                <label>
                  <b>SASARAN STRATEGIS <?php echo $i." : ".$target->nama; ?></b>
                  <button type='button' class='btn btn-sm btn-primary' onclick="addIndikator(<?php echo $target->id; ?>);">&nbsp;<i class='fa fa-plus'></i>&nbsp;</button>
                </label>
              </div>
              <div class="col-md-12">
                <table style="width:100%" id="table-i-<?php echo $target->id; ?>" class="table table-striped table-md">
                  <thead>
                    <tr>
                      <th>KODE</th>
                      <th>INDIKATOR KINERJA PROGRAM</th>
                      <th>SASARAN</th>
                      <th>TARGET</th>
                      <th style="width: 60px"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                        <td colspan="5" style="text-align:center">Tidak ada data</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <hr>
            <?php $i++; } ?>
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
    $("#rowCustomValue").hide();
  });
  
  function addIndikator(target_id, tempid=null){
    resetModal();
    $.ajax({
        url: '<?php echo base_url(); ?>indicator/api/get_program',
        type: 'POST',
        data: JSON.stringify({target_id: target_id}),
        dataType : "json",
        contentType: "application/json; charset=utf-8",
        success: function(data) {
          if(data.ok==1){
            $(".modal-title").html("SASARAN STRATEGIS "+data.kode.substr(1)+" : "+data.nama);
            var program = data.program;
            var option = "";
            for(var i=0; i<program.length; i++){
              option += "<option data-kode='"+program[i].kode+"' value='"+program[i].id+"'>"+program[i].kode+" "+program[i].nama+"</option>";
            }
            $("#sasaran").val(data.id);
            $("#tableChoice tbody").html("");
            $("#program").html(option);
            if(tempid!==null){
              var target = indikator.find(item=>item.target_id==target_id);
              var indk = target.details.find(item=>item.tempid==tempid);

              $("#tempid").val(tempid);
              $("#program").val(indk.program_id).trigger("change");
              $("#indikator").val(indk.indikator);
              $("#tipe").val(indk.tipe).trigger("change");
              $("#satuan").val(indk.satuan);
              $("#target").val(indk.target);

              var choices = indk.pilihan;
              for(var c=0; c<choices.length; c++){
                var html = "<tr>"+
                              "<td><input type=\"text\" class=\"form-control pilihan-nama\" value=\""+choices[c].nama+"\"/> </td>"+
                              "<td><input type=\"text\" class=\"form-control pilihan-nilai number\" value=\""+choices[c].nilai+"\"/></td>"+
                              "<td style=\"text-align: center\"><button type=\"button\" class=\"btn btn-md btn-set btn-secondary\">Set</button></td>"+
                              "<td style=\"text-align: center\">"+
                                "<button type=\"button\" class=\"btn btn-md btn-delete-choice btn-danger\"><i class=\"fa fa-times\"></i></button>"+
                              "</td>"+
                            "</tr>";
                $("#tableChoice tbody").append(html);
              }
            }
            
            $("#modal-tambah-indikator").modal("show");
            //window.location.replace("/eKPI/draft");
          }else{
            alert(data.msg);
          }
        },
        error: function(data) {
            console.log(data);
        }
    });
    
  };

  $(document).on("change","#tipe", function(){
    if($(this).val()=="Pilihan Kustom"){
      $("#rowCustomValue").show();
      $("#target").prop("readonly",true).removeClass("number").val("");
    }else{
      $("#rowCustomValue").hide();
      $("#target").prop("readonly",false).addClass("number").val("");
      $("#tableChoice tbody").html("");
    }
  });

  $(document).on("click",".btn-add-choice", function(){
    var html = "<tr>"+
                  "<td><input type=\"text\" class=\"form-control pilihan-nama\" /> </td>"+
                  "<td><input type=\"text\" class=\"form-control pilihan-nilai number\" /></td>"+
                  "<td style=\"text-align: center\">"+
                    "<button type=\"button\" class=\"btn btn-md btn-set btn-secondary\">Set</button>"+
                  "</td>"+
                  "<td style=\"text-align: center\">"+
                    "<button type=\"button\" class=\"btn btn-md btn-delete-choice btn-danger\"><i class=\"fa fa-times\"></i></button>"+
                  "</td>"+
                "</tr>";
    $("#tableChoice tbody").append(html);
  });

  $(document).on("click",".btn-set", function(){
    $("#target").val($(this).closest("tr").find(".pilihan-nama").first().val());
  });

  $(document).on("click",".btn-delete-choice", function(){
    $(this).closest("tr").remove();
  });
  
  function saveIndikator(){
    if($("#program").val().trim()==""){
      alert("Silahkan pilih program"); return;
    }else if($("#tipe").val()==""){
      alert("Silahkan pilih tipe indikator"); return;
    }else if($("#indikator").val().trim()==""){
      alert("Silahkan isi penjelasan mengenai indikator"); return;
    }else if($("#satuan").val().trim()==""){
      alert("Silahkan isi satuan yang dipakai"); return;
    }else if($("#tipe").val()=="Pilihan Kustom"&&$(".pilihan-nama").length==0){
      alert("Silahkan tambahkan pilihan kustom"); return;
    }else if($("#target").val().trim()==""){
      alert("Silahkan target dari indikator"); return;
    }

    var temppilihan = []; var pilihanok = true;
    if($("#tipe").val()=="Pilihan Kustom"&&$(".pilihan-nama").length>0){
      $(".pilihan-nama").each(function(){
        if($(this).closest("tr").find(".pilihan-nilai").first().val().trim()==""){
          alert("Silahkan isi nilai angka dari pilihan kustom yang telah ditambahkan");
          pilihanok = false;
          return false; 
        }
        temppilihan.push({
          nama: $(this).val().trim(),
          nilai: $(this).closest("tr").find(".pilihan-nilai").first().val().trim()
        });
      });
    }
    
    if(!pilihanok)
      return;
    if($("#tempid").val()==""){
      var objInd = {
        tempid: tempid,
        program_id: $("#program").val(),
        program_kode: $("#program option:selected").data("kode"),
        target_id: $("#sasaran").val(),
        indikator: $("#indikator").val().trim(),
        satuan: $("#satuan").val().trim(),
        target: $("#target").val().trim(),
        tipe: $("#tipe").val(),
        pilihan: temppilihan
      };
      indikator.find(item=>item.target_id==parseInt($("#sasaran").val())).details.push(objInd);
      tempid++;
    }else{
      var curTarget = indikator.find(item=>item.target_id==parseInt($("#sasaran").val()));
      var curIndk = curTarget.details.find(item=>item.tempid==parseInt($("#tempid").val()));
      curIndk.program_id= $("#program").val();
      curIndk.program_kode= $("#program option:selected").data("kode");
      curIndk.target_id= $("#sasaran").val();
      curIndk.indikator= $("#indikator").val().trim();
      curIndk.satuan= $("#satuan").val().trim();
      curIndk.target= $("#target").val().trim();
      curIndk.tipe= $("#tipe").val();
      curIndk.pilihan= temppilihan;
    }
    reloadTable();
    resetModal();
    $("#modal-tambah-indikator").modal("hide");
  }

  function reloadTable(target_id=""){
    var html = "";
    if(target_id==""){//reload semua table
      for(var i=0; i<indikator.length; i++){
        var obj = indikator[i];var count = 1;var program_kode = "";
        html = "";
        obj.details.sort((a,b) => ( (a.program_kode+"."+a.tempid).localeCompare((b.program_kode+"."+b.tempid), 'en', { numeric: true })));
        for(var x=0; x<obj.details.length; x++){
          var row = obj.details[x];
          if(program_kode!==row.program_kode)
            count=1;
          html += "<tr>" +
                    "<td>IK"+row.program_kode+"."+count+"</td>" +
                    "<td>"+row.indikator+"</td>" +
                    "<td>"+row.satuan+"</td>" +
                    "<td>"+row.target+"</td>" +
                    "<td><button type=\"button\" class=\"btn btn-xs btn-secondary\" onclick=\"addIndikator("+row.target_id+","+row.tempid+")\"><i class=\"fa fa-edit\"></i></button></td>" +
                  "</tr>";
          program_kode = row.program_kode;
          row.indikator_kode = "IK"+row.program_kode+"."+count;
          count++;
        }
        if(html==""){
          html += "<tr>"+
                        "<td colspan=\"5\" style=\"text-align:center\">Tidak ada data</td>"+
                  "</tr>";
        }
        $("#table-i-"+obj.target_id+" tbody").html(html);
        console.log(html);
      }
    }else{//hanya reload yang target_id
      var obj = indikator.find(item=>item.target_id==target_id+"");var count = 1;var program_kode = "";
      html = "";
      obj.details.sort((a,b) => ( (a.program_kode+"."+a.tempid).localeCompare((b.program_kode+"."+b.tempid), 'en', { numeric: true })));
      for(var x=0; x<obj.details.length; x++){
        var row = obj.details[x];
        if(program_kode!==row.program_kode)
          count=1;
        html += "<tr>" +
                  "<td>IK"+row.program_kode+"."+count+"</td>" +
                  "<td>"+row.indikator+"</td>" +
                  "<td>"+row.satuan+"</td>" +
                  "<td>"+row.target+"</td>" +
                  "<td><button type=\"button\" class=\"btn btn-xs btn-secondary\" onclick=\"addIndikator("+row.target_id+","+row.tempid+")\"><i class=\"fa fa-edit\"></i></button></td>" +
                "</tr>";
        program_kode = row.program_kode;
        row.indikator_kode = "IK"+row.program_kode+"."+count;
        count++;
      }
      if(html==""){
        html += "<tr>"+
                      "<td colspan=\"5\" style=\"text-align:center\">Tidak ada data</td>"+
                "</tr>";
      }
      $("#table-i-"+obj.target_id+" tbody").html(html);
      console.log(html);
    }
  }
  
  function resetModal(){
    $("#tempid").val("");
    $("#program").html("");
    $("#sasaran").val("");
    $("#indikator").val("");
    $("#satuan").val("");
    $("#target").val("");
    $("#tipe").val("");
    $("#tableChoice tbody").html("");
  }

  $(document).on("input",".number",function(){
    if (/\D/g.test(this.value))
    {
      // Filter non-digits from input value.
      this.value = this.value.replace(/\D/g, '');
    }
  });
</script>
