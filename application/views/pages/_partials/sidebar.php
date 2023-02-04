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
            <?php foreach($_SESSION["menu"] as $user_menu){ ?>
              <li class="<?php if($menu==$user_menu->category){ echo "active"; } ?>"><a class="nav-link" href="<?php echo base_url().$user_menu->url; ?>"><i class="<?php echo $user_menu->icon; ?>"></i> <span><?php echo $user_menu->name; ?></span></a></li>
            <?php } ?>
          </ul>

        </aside>
      </div>
