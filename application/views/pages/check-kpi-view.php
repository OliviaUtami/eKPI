<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('pages/_partials/header');
?>

<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1><?php echo $title ?></h1>
    </div>
    <div class="section-body">
      <div class="card">
        <div class="card-body">
          <ul class="nav nav-tabs" id="nav-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active show" id="outstanding-tab" data-toggle="tab" href="#outstanding" role="tab" aria-controls="outstanding" aria-selected="true">Menunggu Persetujuan</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="list-tab" data-toggle="tab" href="#list" role="tab" aria-controls="list" aria-selected="false">Daftar KPI</a>
            </li>
          </ul>
          <div class="tab-content" id="nav-tab-content">
            <div class="tab-pane fade active show" id="outstanding" role="tabpanel" aria-labelledby="outstanding-tab">
              <div style="width:100%; overflow-x: scroll;">
                <table id="table-list" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Periode Pengisian</th>
                            <th>Unit</th>
                            <th>Karyawan</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kpi as $data){ ?>
                            <tr>
                                <td></td>
                                <td><?php echo($data->period_from." - ".$data->period_to); ?></td>
                                <td><?php echo($data->org_name); ?></td>
                                <td><?php echo($data->name); ?></td>
                                <td><?php echo($data->status); ?></td>
                                <td><?php echo($data->created_by."<br/>".$data->created_at); ?></td>
                                <td>
                                    <?php if($data->status=="Belum Ada"){ ?>
                                    <button class="btn btn-sm btn-warning" onclick="openKPI(<?php echo $data->indicator_id; ?>)" title="Isi KPI"><i class="fa fa-edit"></i></button>
                                    <?php }else{ ?>
                                      <button class="btn btn-sm btn-success" onclick="openExist('<?php echo $data->uid; ?>')" title="Cek KPI"><i class="fa fa-edit"></i></button>
                                      <button class="btn btn-sm btn-primary" onclick="print('<?php echo $data->uid; ?>')" title="Print KPI"><i class="fa fa-print"></i></button>
                                    <?php } ?>
                                    <?php if($data->ind_user_id!==NULL&&$data->ind_user_id!==-1&&($data->status=="Draft"||$data->status=="Menunggu Revisi")){ ?>
                                    <button class="btn btn-sm btn-primary" onclick="sendKPI('<?php echo $data->uid; ?>')" title="Kirimkan KPI"><i class="fa fa-paper-plane"></i></button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <!-- <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Org ID</th>
                            <th>Role ID</th>
                        </tr>
                    </tfoot> -->
                </table>
              </div>
            </div>
            <div class="tab-pane fade" id="list" role="tabpanel" aria-labelledby="list-tab">
              <div style="width:100%; overflow-x: scroll;">
                <div class="row">
                  <div class="col-md-1">
                    Periode
                  </div>
                  <div class="col-md-3">
                    <select id="cboPeriod" class="form-control select2" name="period">
                      <option></option>
                      <?php foreach ($period as $data){ ?>
                        <option value="<?php echo($data->period_id); ?>"><?php echo($data->period_name); ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="col-md-1">
                    Unit
                  </div>
                  <div class="col-md-3">
                    <select id="cboUnit" class="form-control select2" name="organization">
                      <?php foreach ($org as $data){ ?>
                        <option></option>
                        <option value="<?php echo($data->org_id); ?>"><?php echo($data->org_name); ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-md-1">
                    <button class="btn btn-md btn-primary" id="btnFilter" type="button">FILTER</button>
                  </div>
                </div>
                <br>
                <button class="btn btn-md btn-primary btn-unit-action disabled" style="float: right; margin-left: 8px;" onclick="print_kpi_unit(this)" title="Pratinjau Hasil KPI Unit" disabled="disabled">Pratinjau Hasil KPI Unit</button>
                <button class="btn btn-md btn-primary btn-unit-action disabled" style="float: right;" onclick="print_indicator(this)" title="Cetak Indikator Unit" disabled="disabled">Cetak Indikator KPI</button>&nbsp;
                <br>
                <h5>Daftar KPI</h5>
                <table id="table-kpi" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Periode Pengisian</th>
                            <th>Unit</th>
                            <th>Status Indikator</th>
                            <th>Karyawan</th>
                            <th>Status KPI</th>
                            <th>Dibuat Oleh</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                    <!-- <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Org ID</th>
                            <th>Role ID</th>
                        </tr>
                    </tfoot> -->
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php $this->load->view('pages/_partials/footer'); ?>
<script>
    function editPeriod(id){
      window.location.replace("period/edit/"+id);
    }

    $(document).ready(function() {
        $("#cboPeriod").select2({ width: '100%', placeholder: "Pilih periode" });      
        $("#cboUnit").select2({ width: '100%', placeholder: "Pilih unit" });      
        $("#table-list").dataTable({
            "scrollX": true,
            "columnDefs": [
                { width: 20, targets: 0 },
                { width: 30, targets: -1 }
            ],
            "fixedColumns": true,
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                $('td:eq(0)', nRow).html(iDisplayIndexFull +1);
            }
          }
        );
        $("#table-kpi").dataTable({
            "scrollX": true,
            "columnDefs": [
                { width: 15, targets: 0 },
                { width: 30, targets: -1 }
            ],
            "fixedColumns": true,
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                $('td:eq(0)', nRow).html(iDisplayIndexFull +1);
            }
          }
        );

        <?php if($this->session->flashdata('message')){ ?>
          alert("<?php echo $this->session->flashdata('message'); ?>");
        <?php } ?>
    });

    $(document).on("click","#btnFilter",function(){
      if($("#cboUnit").val()==""||$("#cboPeriod").val()==""){
        alert("Silahkan isi filter yang ada");
         return;
      }
      $.ajax({
          url: '<?php echo base_url(); ?>check-kpi/api/get_kpi',
          type: 'POST',
          data: JSON.stringify({organization: $("#cboUnit").val(), period: $("#cboPeriod").val()}),
          dataType : "json",
          contentType: "application/json; charset=utf-8",
          success: function(data) {
            console.log(data);
            if(data.indicator_ready==1){
              $(".btn-unit-action").prop("disabled",false).removeClass("disabled").data("uid",data.uid);
            }else{
              $(".btn-unit-action").prop("disabled",true).addClass("disabled").data("uid","");
            }
            if(data.ok==1){
              var records = data.records;
              var html = ``;
              $("#table-kpi").dataTable().fnClearTable();
              $("#table-kpi").dataTable().fnDestroy();
              for(var i=0; i<records.length; i++){
                var obj = records[i];
                var btn = ``;
                if(obj.kpi_status!=="Belum Ada"){
                  btn += `<button class="btn btn-sm btn-success" onclick="openExist('${obj.uid}')" title="Cek KPI"><i class="fa fa-edit"></i></button>
                          <button class="btn btn-sm btn-primary" onclick="print('${obj.uid}')" title="Print KPI"><i class="fa fa-print"></i></button>`;
                }
                html += `<tr>
                          <td></td>
                          <td>${obj.period_from+" - "+obj.period_to}</td>
                          <td>${obj.org_name}</td>
                          <td>${obj.indicator_status}</td>
                          <td>${obj.name}</td>
                          <td>${obj.kpi_status}</td>
                          <td>${obj.created_by+"<br>"+obj.created_at}</td>
                          <td>
                              ${(obj.kpi_status!=="Belum Ada"?btn:"")}
                          </td>
                        </tr>`;
              }
              $("#table-kpi tbody").html(html);
              
              $("#table-kpi").dataTable({
                  "scrollX": true,
                  "columnDefs": [
                      { width: 15, targets: 0 },
                      { width: 30, targets: -1 }
                  ],
                  "fixedColumns": true,
                  "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                      $('td:eq(0)', nRow).html(iDisplayIndexFull +1);
                  }
                }
              );
            }else{
              alert(data.msg);
            }
          },
          error: function(data) {
              console.log(data);
          }
      });
    });

    function openKPI(indicator_id){
      window.open("kpi/add/"+indicator_id,"_blank").focus();
    }

    function openExist(uid){
      window.open("check-kpi/edit/"+uid,"_blank").focus();
    }

    function print(uid){
      window.open("kpi/print/"+uid, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=100,left=100,width=1200,height=600"); 
    }

    function print_indicator(element){
      window.open("indicator/print/"+$(element).data("uid"), "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=100,left=100,width=1200,height=600"); 
    }

    function print_kpi_unit(element){
      window.open("kpi/print-unit/"+$(element).data("uid"), "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=100,left=100,width=1200,height=600"); 
    }

    function sendKPI(indicator_id){
      window.location.assign("kpi/submit/"+uid);
    }
</script>
