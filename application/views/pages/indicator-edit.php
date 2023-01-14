<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('pages/_partials/header');
?>
<style>
  .date-input:read-only{
    background-color: #fdfdff;
  }
</style>
<div class="modal" id="modal-tambah-misi" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Mission</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input id="inpTempID" class="form-control" type="hidden" autocomplete="off"/>
        <textarea id="inpMission" class="form-control" autocomplete="off"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="saveMisi()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                foreach($indicator->targets as $target){ ?>
            <div class="row">
              <div class="form-group col-md-12">
                <label><b>SASARAN STRATEGIS <?php echo $i." : ".$target->nama; ?></b></label>
              </div>
            </div><hr>
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

  var misi = [];
  var tempid_misi = 1;
  var tempid_tujuan = 1;
  var tempid_target = 1;
  var tempid_program = 1;
  <?php
    echo ("misi = ".json_encode($draft->details).";");
  ?>
  //populate
  for(var i=0; i<misi.length; i++){
    misi[i].tempid = tempid_misi;
    for(var j=0; j<misi[i].tujuan.length; j++){
      misi[i].tujuan[j].tempid = tempid_tujuan;
      for(var k=0; k<misi[i].tujuan[j].target.length; k++){
        misi[i].tujuan[j].target[k].tempid = tempid_target;
	      for(var l=0; l<misi[i].tujuan[j].target[k].program.length; l++){
          misi[i].tujuan[j].target[k].program[l].tempid = tempid_program;
          tempid_program++;
        }
        tempid_target++;
      }
      tempid_tujuan++;
    }
    tempid_misi++;
  }
  reloadTable();
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
    /*$("#table-misi").dataTable({
      "search": false,
      "paging": false,
      "bInfo": false,
      "columnDefs": [
          { width: 20, targets: 0 },
          { width: 70, targets: -1 }
      ],
      "fixedColumns": true,
      }
    );*/
  });
  $(document).on("click","#btn-save",function(){
    if($("#name").val().trim()==""){
      alert("Silahkan isi nama draft");
      return;
    }
    if(misi.length==0){
      alert("Silahkan tambahkan minimal 1 misi");
      return;
    }
    for (var i=0; i<misi.length; i++) {
      var currmisi = misi[i];
      var arrtujuan = currmisi.tujuan;
      if(arrtujuan.length==0){
        alert("Misi harus minimal mempunyai 1 tujuan");
        return;
      }
      for (var j=0; j<arrtujuan.length; j++){
        var currtujuan = arrtujuan[j];
        var arrtarget = currtujuan.target;
        if(arrtarget.length==0){
          alert("Tujuan harus minimal mempunyai satu sasaran");
          return;
        }
        for (var k=0; k<arrtarget.length; k++){
          var currtarget = arrtarget[k];
          var arrprogram = currtarget.program;
          if(arrprogram.length==0){
            alert("Sasaran harus minimal mempunyai satu program");
            return;
          }
        }
      }
    }
    var param = {
      name: $("#name").val().trim(),
      id: $("#id").val().trim(),
      details: misi
    };
    $.ajax({
      url: 'process',
      type: 'POST',
      data: JSON.stringify(param),
      dataType : "json",
      contentType: "application/json; charset=utf-8",
      success: function(data) {
        if(data.ok==1){
          window.location.replace("/eKPI/draft");
        }else{
          alert(data.msg);
        }
      },
      error: function(data) {
          console.log(data);
      }
  });
  });
  function openModalMisi(tempid=0){
    $("#modal-tambah-misi #inpTempID").val(tempid);
    $("#inpMission").val("");
    if(tempid!==0){
      var selMisi = misi.find(item=>(item.tempid==tempid));
      if(selMisi!==null){
        $("#inpMission").val(selMisi.nama);
      }
    }
    $("#modal-tambah-misi").modal("show");
  }

  function saveMisi(){
    if($("#inpMission").val().trim()==""){
      alert("Mission cannot be empty");
      return;
    }
    if($("#modal-tambah-misi #inpTempID").val()=="0"){
      misi.push({
        tempid: tempid_misi,
        nama: $("#inpMission").val().trim(),
        tujuan: []
      });
      tempid_misi++;
    }else{
      var idx = misi.findIndex(item=>(item.tempid==parseInt($("#modal-tambah-misi #inpTempID").val())));
      if(idx>-1){
        misi[idx].nama=$("#inpMission").val().trim();
      }
    }

    reloadTable();
    $("#modal-tambah-misi").modal("hide");
  }

  function deleteMisi(tempid){
    if(confirm("Konfirmasi untuk menghapus misi dalam draft ini?")){
      var idx = misi.findIndex(item=>(item.tempid==tempid));
      if(idx>-1){
        misi.splice(idx,1);
        reloadTable();
      }
    }
  }

  function openModalTujuan(tempid_misi,tempid=0){
    $("#modal-tambah-tujuan #inpTempIDMisi").val(tempid_misi);
    $("#modal-tambah-tujuan #inpTempID").val(tempid);
    $("#inpPurpose").val("");
    if(tempid!==0){
      var selMisi = misi.find(item=>(item.tempid==tempid_misi));
      var selTujuan = selMisi.tujuan.find(item=>(item.tempid==tempid));
      if(selTujuan!==null){
        $("#inpPurpose").val(selTujuan.nama);
      }
    }
    $("#modal-tambah-tujuan").modal("show");
  }

  function saveTujuan(){
    if($("#inpPurpose").val().trim()==""){
      alert("Purpose cannot be empty");
      return;
    }
    var tempidmisi = parseInt($("#modal-tambah-tujuan #inpTempIDMisi").val());
    var selMisi = misi.find(item=>(item.tempid==tempidmisi));
    if($("#modal-tambah-tujuan #inpTempID").val()=="0"){
      selMisi.tujuan.push({
        tempid: tempid_tujuan,
        nama: $("#inpPurpose").val().trim(),
        target: []
      });
      tempid_tujuan++;
    }else{
      var idx = selMisi.tujuan.findIndex(item=>(item.tempid==parseInt($("#modal-tambah-tujuan #inpTempID").val())));
      if(idx>-1){
        selMisi.tujuan[idx].nama=$("#inpPurpose").val().trim();
      }
    }

    reloadTable();
    $("#modal-tambah-tujuan").modal("hide");
  }

  function deleteTujuan(tempid_misi, tempid){
    if(confirm("Confirm to delete this purpose from current draft?")){
      var selMisi = misi.find(item=>(item.tempid==tempid_misi));
      var idx = selMisi.tujuan.findIndex(item=>(item.tempid==tempid));
      if(idx>-1){
        selMisi.tujuan.splice(idx,1);
        reloadTable();
      }
    }
  }

  function openModalTarget(tempid_misi,tempid_tujuan,tempid=0){
    $("#modal-tambah-target #inpTempIDMisi").val(tempid_misi);
    $("#modal-tambah-target #inpTempIDTujuan").val(tempid_tujuan);
    $("#modal-tambah-target #inpTempID").val(tempid);
    $("#inpTarget").val("");
    if(tempid!==0){
      var selMisi = misi.find(item=>(item.tempid==tempid_misi));
      var selTujuan = selMisi.tujuan.find(item=>(item.tempid==tempid_tujuan));
      var selTarget = selTujuan.target.find(item=>(item.tempid==tempid));
      if(selTarget!==null){
        $("#inpTarget").val(selTarget.nama);
      }
    }
    $("#modal-tambah-target").modal("show");
  }

  function saveTarget(){
    if($("#inpTarget").val().trim()==""){
      alert("Target cannot be empty");
      return;
    }
    var tempidmisi = parseInt($("#modal-tambah-target #inpTempIDMisi").val());
    var tempidtujuan = parseInt($("#modal-tambah-target #inpTempIDTujuan").val());
    var selMisi = misi.find(item=>(item.tempid==tempidmisi));
    var selTujuan = selMisi.tujuan.find(item=>(item.tempid==tempidtujuan));
    if($("#modal-tambah-target #inpTempID").val()=="0"){
      selTujuan.target.push({
        tempid: tempid_target,
        nama: $("#inpTarget").val().trim(),
        program: []
      });
      tempid_target++;
    }else{
      var idx = selTujuan.target.findIndex(item=>(item.tempid==parseInt($("#modal-tambah-target #inpTempID").val())));
      if(idx>-1){
        selTujuan.target[idx].nama=$("#inpTarget").val().trim();
      }
    }

    reloadTable();
    $("#modal-tambah-target").modal("hide");
  }

  function deleteTarget(tempid_misi, tempid_tujuan, tempid){
    if(confirm("Confirm to delete this target from current draft?")){
      var selMisi = misi.find(item=>(item.tempid==tempid_misi));
      var selTujuan = selMisi.tujuan.find(item=>(item.tempid==tempid_tujuan));
      var idx = selTujuan.target.findIndex(item=>(item.tempid==tempid));
      if(idx>-1){
        selTujuan.target.splice(idx,1);
        reloadTable();
      }
    }
  }

  function openModalProgram(tempid_misi,tempid_tujuan,tempid_target,tempid=0){
    $("#modal-tambah-program #inpTempIDMisi").val(tempid_misi);
    $("#modal-tambah-program #inpTempIDTujuan").val(tempid_tujuan);
    $("#modal-tambah-program #inpTempIDTarget").val(tempid_target);
    $("#modal-tambah-program #inpTempID").val(tempid);
    $("#inpProgram").val("");
    if(tempid!==0){
      var selMisi = misi.find(item=>(item.tempid==tempid_misi));
      var selTujuan = selMisi.tujuan.find(item=>(item.tempid==tempid_tujuan));
      var selTarget = selTujuan.target.find(item=>(item.tempid==tempid_target));
      var selProgram = selTarget.program.find(item=>(item.tempid==tempid));
      if(selProgram!==null){
        $("#inpProgram").val(selProgram.nama);
      }
    }
    $("#modal-tambah-program").modal("show");
  }

  function saveProgram(){
    if($("#inpProgram").val().trim()==""){
      alert("Program cannot be empty");
      return;
    }
    var tempidmisi = parseInt($("#modal-tambah-program #inpTempIDMisi").val());
    var tempidtujuan = parseInt($("#modal-tambah-program #inpTempIDTujuan").val());
    var tempidtarget = parseInt($("#modal-tambah-program #inpTempIDTarget").val());
    var selMisi = misi.find(item=>(item.tempid==tempidmisi));
    var selTujuan = selMisi.tujuan.find(item=>(item.tempid==tempidtujuan));
    var selTarget = selTujuan.target.find(item=>(item.tempid==tempidtarget));
    console.log(selTarget);
    if($("#modal-tambah-program #inpTempID").val()=="0"){
      console.log("masuk 1");
      selTarget.program.push({
        tempid: tempid_program,
        id: -1,
        nama: $("#inpProgram").val().trim(),
        pic: []
      });
      tempid_program++;
    }else{
      console.log("masuk 2");
      var idx = selTarget.program.findIndex(item=>(item.tempid==parseInt($("#modal-tambah-program #inpTempID").val())));
      if(idx>-1){
        selTarget.program[idx].nama=$("#inpProgram").val().trim();
      }
    }

    reloadTable();
    $("#modal-tambah-program").modal("hide");
  }

  function deleteProgram(tempid_misi, tempid_tujuan, tempid_target, tempid){
    if(confirm("Confirm to delete this program from current draft?")){
      var selMisi = misi.find(item=>(item.tempid==tempid_misi));
      var selTujuan = selMisi.tujuan.find(item=>(item.tempid==tempid_tujuan));
      var selTarget = selTujuan.target.find(item=>(item.tempid==tempid_target));
      var idx = selTarget.program.findIndex(item=>(item.tempid==tempid));
      if(idx>-1){
        selTarget.program.splice(idx,1);
        reloadTable();
      }
    }
  }

  function reloadTable(){
    //$("#table-misi").DataTable().clear();
    //$("#table-misi").DataTable().destroy();
    var html_misi = "";
    for(var i=0;i<misi.length; i++){
      var html_tujuan = "";
      for(var j=0;j<misi[i].tujuan.length;j++){
        var tujuan = misi[i].tujuan[j];
        var html_target = "";
        for(var k=0;k<tujuan.target.length;k++){
          var target = tujuan.target[k];
          var html_program = "";
          for(var l=0;l<target.program.length;l++){
            var program = target.program[l];
            if(l==0){
              html_program = "<table class='table-program target-"+target.tempid+" table table-striped table-bordered' cellspacing='0' width='100%'>" +
                                "<thead>" +
                                    "<tr>"+
                                        "<th style='width:30px'>No</th>" +
                                        "<th>Program</th>" +
                                        "<th style='width:90px'>Action</th>" +
                                    "</tr>" +
                                "</thead>" +
                                "<tbody>" ;
            }
            html_program += "<tr class='program-"+program.tempid+"'>" +
                            "<td>P" + (k+1) + "." + (l+1) + "</td>" +
                            "<td>" + program.nama + "</td>" +
                            "<td>" +
                              //"<button type='button' class='btn btn-sm btn-primary btn-add-program' title='Add Program' onclick='openModalProgram("+misi[i].tempid+","+tujuan.tempid+","+target.tempid+");'>&nbsp;<i class='fa fa-plus'></i>&nbsp;</button>" +
                              "<button type='button' class='btn btn-sm btn-warning btn-edit-purpose' title='Edit' onclick='openModalProgram("+misi[i].tempid+","+tujuan.tempid+","+target.tempid+","+program.tempid+");'>&nbsp;<i class='fa fa-edit'></i></button>" +
                              "<button type='button' class='btn btn-sm btn-danger btn-delete-purpose' title='Delete' onclick='deleteProgram("+misi[i].tempid+","+tujuan.tempid+","+target.tempid+","+program.tempid+");'>&nbsp;<i class='fa fa-times'></i>&nbsp;</button>" +
                            "</td>" +
                          "</tr>";
            if(l==target.program.length-1){
              html_program +="</tbody></table>";
            }
          }
          if(k==0){
            html_target = "<table class='table-target tujuan-"+target.tempid+" table table-striped table-bordered' cellspacing='0' width='100%'>" +
                            "<thead>" +
                                "<tr>"+
                                    "<th style='width:30px'>No</th>" +
                                    "<th>Sasaran</th>" +
                                    "<th style='width:100px'>Action</th>" +
                                "</tr>" +
                            "</thead>" +
                            "<tbody>" ;
          }
          html_target += "<tr class='target-"+target.tempid+"'>" +
                          "<td>S" + (k+1) + "</td>" +
                          "<td>" + target.nama + "</td>" +
                          "<td>" +
                            "<button type='button' class='btn btn-sm btn-primary btn-add-program' title='Tambah Program' onclick='openModalProgram("+misi[i].tempid+","+tujuan.tempid+","+target.tempid+");'>&nbsp;<i class='fa fa-plus'></i>&nbsp;</button>" +
                            "<button type='button' class='btn btn-sm btn-warning btn-edit-purpose' title='Edit' onclick='openModalTarget("+misi[i].tempid+","+tujuan.tempid+","+target.tempid+");'>&nbsp;<i class='fa fa-edit'></i></button>" +
                            "<button type='button' class='btn btn-sm btn-danger btn-delete-purpose' title='Delete' onclick='deleteTarget("+misi[i].tempid+","+tujuan.tempid+","+target.tempid+");'>&nbsp;<i class='fa fa-times'></i>&nbsp;</button>" +
                          "</td>" +
                        "</tr>"+
                        "<tr class='target-"+target.tempid+" tr-program' style='"+(html_program==""?"display:none":"")+"'>" +
                          "<td></td><td colspan='2' style='padding:10px;'>" + html_program + "</td>" +
                        "</tr>";
          if(k==tujuan.target.length-1){
              html_target +="</tbody></table>";
          }
        }
        if(j==0){
          html_tujuan = "<table class='table-tujuan mission-"+misi[i].tempid+" table table-striped table-bordered' cellspacing='0' width='100%'>" +
                          "<thead>" +
                              "<tr>"+
                                  "<th style='width:30px'>No</th>" +
                                  "<th>Tujuan</th>" +
                                  "<th style='width:110px'>Action</th>" +
                              "</tr>" +
                          "</thead>" +
                          "<tbody>" ;
        }
        html_tujuan += "<tr class='tujuan-"+tujuan.tempid+"'>" +
                          "<td>T" + (j+1) + "</td>" +
                          "<td>" + tujuan.nama + "</td>" +
                          "<td>" +
                            "<button type='button' class='btn btn-sm btn-primary btn-add-target' title='Tambah Tujuan' onclick='openModalTarget("+misi[i].tempid+","+tujuan.tempid+");'>&nbsp;<i class='fa fa-plus'></i>&nbsp;</button>" +
                            "<button type='button' class='btn btn-sm btn-warning btn-edit-purpose' title='Edit' onclick='openModalTujuan("+misi[i].tempid+","+tujuan.tempid+");'>&nbsp;<i class='fa fa-edit'></i></button>" +
                            "<button type='button' class='btn btn-sm btn-danger btn-delete-purpose' title='Delete' onclick='deleteTujuan("+misi[i].tempid+","+tujuan.tempid+");'>&nbsp;<i class='fa fa-times'></i>&nbsp;</button>" +
                          "</td>" +
                        "</tr>" +
                        "<tr class='tujuan-"+tujuan.tempid+" tr-target' style='"+(html_target==""?"display:none":"")+"'>" +
                          "<td></td><td colspan='2' style='padding:10px;'>" + html_target + "</td>" +
                        "</tr>";
        if(j==misi[i].tujuan.length-1){
          html_tujuan +="</tbody></table>";
        }
      }
      html_misi +=   "" +
                  "<tr class='misi-"+misi[i].tempid+"'>" +
                    "<td>M" + (i+1) + "</td>" +
                    "<td class='td-nama' data-tempid='"+misi[i].tempid+"'>" + (misi[i].nama) + "</td>" +
                    "<td>" +
                      "<button type='button' class='btn btn-sm btn-primary btn-add-tujuan' title='Tambah Misi' onclick='openModalTujuan("+misi[i].tempid+");'>&nbsp;<i class='fa fa-plus'></i>&nbsp;</button>" +
                      "<button type='button' class='btn btn-sm btn-warning btn-edit-misi' title='Edit' onclick='openModalMisi("+misi[i].tempid+");'>&nbsp;<i class='fa fa-edit'></i></button>" +
                      "<button type='button' class='btn btn-sm btn-danger btn-delete-misi' title='Delete' onclick='deleteMisi("+misi[i].tempid+");'>&nbsp;<i class='fa fa-times'></i>&nbsp;</button>" +
                    "</td>" +
                  "</tr>" +
                  "<tr class='misi-"+misi[i].tempid+" tr-tujuan' style='"+(html_tujuan==""?"display:none":"")+"'>" +
                    "<td></td><td colspan='2' style='padding:10px;'>" + html_tujuan + "</td>" +
                  "</tr>" +
                "";
    }
    $("#table-misi tbody").html(html_misi);
    /*$("#table-misi").dataTable({
      "search": false,
      "paging": false,
      "bInfo": false,
      "columnDefs": [
          { width: 20, targets: 0 },
          { width: 70, targets: -1 }
      ],
      "fixedColumns": true,
      }
    );*/
  }


</script>
