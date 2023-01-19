<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="<?php echo base_url(); ?>dist/index">
              <span style="display: flex;justify-content: space-around;line-height: 100%;margin-top: 24px; font-weight:bolder;font-size: 24px;">XOX</span>

            </a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="<?php echo base_url(); ?>dist/index">XOX</a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">Admin</li>
            <li class="<?php if($menu=="dashboard"){ echo "active"; } ?>"><a class="nav-link" href="<?php echo base_url()."dashboard"; ?>"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li class="<?php if($menu=="org"){ echo "active"; } ?>"><a class="nav-link" href="<?php echo base_url()."org"; ?>"><i class="fas fa-users"></i> <span>Organisasi</span></a></li>
            <li class="<?php if($menu=="user"){ echo "active"; } ?>"><a class="nav-link" href="<?php echo base_url()."user"; ?>"><i class="fas fa-user"></i> <span>User</span></a></li>
            <li class="<?php if($menu=="period"){ echo "active"; } ?>"><a class="nav-link" href="<?php echo base_url()."period"; ?>"><i class="fas fa-chart-bar"></i><span>Periode</span></a></li>
            <li class="<?php if($menu=="draft"){ echo "active"; } ?>"><a class="nav-link" href="<?php echo base_url()."draft"; ?>"><i class="fas fa-bullseye"></i><span>Draft KPI</span></a></li>
            <li class="<?php if($menu=="draft-approval"){ echo "active"; } ?>"><a class="nav-link" href="<?php echo base_url()."draft-approval"; ?>"><i class="fas fa-bullseye"></i><span>Persetujuan Draft KPI</span></a></li>
            <li class="<?php if($menu=="indicator"){ echo "active"; } ?>"><a class="nav-link" href="<?php echo base_url()."indicator"; ?>"><i class="fas fa-list"></i><span>Indikator Program</span></a></li>
            <li class="<?php if($menu=="kpi"){ echo "active"; } ?>"><a class="nav-link" href="<?php echo base_url()."kpi"; ?>"><i class="fas fa-pencil-alt"></i><span>KPI Saya</span></a></li>
          </ul>

        </aside>
      </div>
