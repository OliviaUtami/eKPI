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
<div class="modal" id="modal-tambah-tujuan" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Purpose</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input id="inpTempIDMisi" class="form-control" type="hidden" autocomplete="off"/>
        <input id="inpTempID" class="form-control" type="hidden" autocomplete="off"/>
        <textarea id="inpPurpose" class="form-control" autocomplete="off"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="saveTujuan()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal" id="modal-tambah-target" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Target</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input id="inpTempIDMisi" class="form-control" type="hidden" autocomplete="off"/>
        <input id="inpTempIDTujuan" class="form-control" type="hidden" autocomplete="off"/>
        <input id="inpTempID" class="form-control" type="hidden" autocomplete="off"/>
        <textarea id="inpTarget" class="form-control" autocomplete="off"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="saveTarget()">Save</button>
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
            <form id="formAdd" method="POST" action="add/process">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-md-5">
                        <label>Draft Name</label>
                        <input type="text" class="form-control" name="name" autocomplete="off" required>
                    </div>
                    <div class="form-group col-md-5">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" autocomplete="off" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-12">
                      <button type="button" class="btn btn-sm btn-primary" onclick="openModalMisi();"><i class="fa fa-plus"></i></button><br/><br/>
                      <table id="table-misi" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th style="width:30px">No</th>
                                <th>Missions</th>
                                <th style="width:120px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    </div>
                  </div>
                  
                </div>
                <div class="card-footer bg-whitesmoke">
                  <button type="submit" class="btn btn-primary">SAVE</button>
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
    if(confirm("Confirm to delete this mission from current draft?")){
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
          if(k==0){
            html_target = "<table class='table-target tujuan-"+target.tempid+" table table-striped table-bordered' cellspacing='0' width='100%'>" + 
                            "<thead>" + 
                                "<tr>"+
                                    "<th style='width:30px'>No</th>" +
                                    "<th>Purposes</th>" +
                                    "<th style='width:100px'>Action</th>" +
                                "</tr>" +
                            "</thead>" + 
                            "<tbody>" ;
          }
          html_target += "<tr class='target-"+target.tempid+"'>" +
                          "<td>" + (k+1) + "</td>" +
                          "<td>" + target.nama + "</td>" +
                          "<td>" + 
                            //"<button type='button' class='btn btn-sm btn-primary btn-add-target' title='Add Purpose' onclick='openModalTarget("+misi[i].tempid+");'>&nbsp;<i class='fa fa-plus'></i>&nbsp;</button>" + 
                            "<button type='button' class='btn btn-sm btn-warning btn-edit-purpose' title='Edit' onclick='openModalTarget("+misi[i].tempid+","+tujuan.tempid+","+target.tempid+");'>&nbsp;<i class='fa fa-edit'></i></button>" + 
                            "<button type='button' class='btn btn-sm btn-danger btn-delete-purpose' title='Delete' onclick='deleteTarget("+misi[i].tempid+","+tujuan.tempid+","+target.tempid+");'>&nbsp;<i class='fa fa-times'></i>&nbsp;</button>" + 
                          "</td>" +
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
                                  "<th>Purposes</th>" +
                                  "<th style='width:110px'>Action</th>" +
                              "</tr>" +
                          "</thead>" + 
                          "<tbody>" ;
        }
        html_tujuan += "<tr class='tujuan-"+tujuan.tempid+"'>" +
                          "<td>" + (j+1) + "</td>" +
                          "<td>" + tujuan.nama + "</td>" +
                          "<td>" + 
                            "<button type='button' class='btn btn-sm btn-primary btn-add-target' title='Add Purpose' onclick='openModalTarget("+misi[i].tempid+","+tujuan.tempid+");'>&nbsp;<i class='fa fa-plus'></i>&nbsp;</button>" + 
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
                    "<td>" + (i+1) + "</td>" +
                    "<td class='td-nama' data-tempid='"+misi[i].tempid+"'>" + (misi[i].nama) + "</td>" +
                    "<td>" + 
                      "<button type='button' class='btn btn-sm btn-primary btn-add-tujuan' title='Add Purpose' onclick='openModalTujuan("+misi[i].tempid+");'>&nbsp;<i class='fa fa-plus'></i>&nbsp;</button>" + 
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