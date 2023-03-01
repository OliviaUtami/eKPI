<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('cookie'); 
?>
<script>
function markAsRead() {
  $.ajax({
      url: '<?php echo base_url(); ?>notification/markasread',
      type: 'POST',
      data: "",
      dataType : "json",
      contentType: "application/json; charset=utf-8",
      success: function(data) {
        if(data.ok==1){
          window.location.reload();
        }
      },
      error: function(data) {
          console.log(data);
      }
  });
}
</script>
<body class="<?php if(get_cookie('sidebar')=="mini") { echo "sidebar-mini"; } ?>">
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" style="top: 5px;position: relative;" class="nav-link notification-toggle nav-link-lg <?php if(isset($_SESSION['notif'])&&count($_SESSION['notif'])>0) { echo "beep"; } ?>"><i class="far fa-bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
              <div class="dropdown-header">Notifications
                <div class="float-right">
                  <a href="#" onclick="markAsRead();">Mark All As Read</a>
                </div>
              </div>
              <div class="dropdown-list-content dropdown-list-icons">
                <?php
                  foreach($_SESSION['notif'] as $notif){
                ?>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-icon bg-info text-white">
                    <i class="fas fa-bell"></i>
                  </div>
                  <div class="dropdown-item-desc">
                    <strong><?php echo $notif->title; ?></strong><br>
                    <?php echo $notif->content; ?>
                    <div class="time"><?php echo $notif->tstamp; ?></div>
                  </div>
                </a>
                <?php
                  } 
                ?>
                
              </div>
              <div class="dropdown-footer text-center">
                <a href="javascript:window.location.reload();">Refresh</a>
              </div>
            </div>
          </li>
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="<?php echo base_url(); ?>assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">Hi, <?php if(isset($_SESSION["username"])) { echo($this->session->userdata('username')); } ?></div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <a href="<?php echo base_url(); ?>login/session_logout" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
