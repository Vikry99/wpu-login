       <!-- Sidebar -->
       <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

           <!-- Sidebar - Brand -->
           <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
               <div class="sidebar-brand-icon rotate-n-15">
                   <i class="fas fa-laptop-code"></i>
               </div>
               <div class="sidebar-brand-text mx-3">Web Vikry</div>
           </a>

           <!-- Divider -->
           <hr class="sidebar-divider">

           <!-- Kita akan melakukan query dari menu yang sudah di buat dari database -->
           <!-- lalu melakukan join dengan menu table sql -->
           <?php
            // query menu yang pertama adalah mengquery menu dari id, yang petama kita akan memilih id mana yang akan kita query di dalam database, yang kedua jenis datanya yang ketiga adalah nama databasenya
            // menggunakan titik supaya tidak ambigu karena database id itu ada banyak jadi kita akan query id yang berada pada user_menu   
            $role_id = $this->session->userdata('role_id');
            $queryMenu = "SELECT `user_menu`.`id`, `menu`
                          FROM `user_menu` JOIN `user_access_menu` 
                          ON `user_menu` . `id`= `user_access_menu` . `menu_id`
                          WHERE `user_access_menu` . `role_id` = $role_id
                        --   lalu kita urutkan berdasarkan menu idnya memakai ORDER BY lalu ASC terurut menaik
                        ORDER BY `user_access_menu` . `menu_id` ASC
                          ";
            // lalu kita jalankan menunya
            $menu = $this->db->query($queryMenu)->result_array();

            ?>

           <!-- Looping Menu -->
           <!-- Heading -->
           <?php foreach ($menu as $m) : ?>
               <div class="sidebar-heading">
                   <!-- kita ambil menunya -->
                   <?= $m['menu']; ?>
               </div>

               <!-- melakukan looping sub menu -->
               <!-- siapkan sub menu sesuai menu misalkan admin berrti menunya aadlah dasboard jika user berrti menu adalah my profile -->
               <?php
                $menuId = $m['id'];
                $querySubMenu = "SELECT *
                                FROM `user_sub_menu` JOIN `user_menu` 
                                ON `user_sub_menu` . `menu_id` = `user_menu` . `id`
                                WHERE `user_sub_menu` . `menu_id` = $menuId
                                AND `user_sub_menu` . `is_actived` = 1
                
                ";
                // lalu kita masukan ke dalma result
                $subMenu = $this->db->query($querySubMenu)->result_array();

                ?>
               <!-- Ada forEach di dalam forEach -->
               <?php foreach ($subMenu as $sm) : ?>
                   <!-- Nav Item - Dashboard -->
                   <!-- Sebuah Kondisi untuk ketika kita berada pada halaman menu tertentu menu tersebut akan active jadi seperti lebih cerah di banding yang lainnya -->
                   <?php if ($title == $sm['title']) : ?>
                       <li class="nav-item active">
                       <?php else : ?>
                       <li class="nav-item">
                       <?php endif; ?>
                       <a class="nav-link" href="<?= base_url($sm['url']); ?>">
                           <i class="<?= $sm['icon']; ?>"></i>
                           <span><?= $sm['title']; ?></span></a>
                       </li>
                   <?php endforeach; ?>
                   <!-- Divider -->
                   <hr class="sidebar-divider">
               <?php endforeach; ?>

               <li class="nav-item">
                   <a class="nav-link" href="<?= base_url('auth/logout'); ?>">
                       <i class="fas fa-fw fa-sign-out-alt"></i>
                       <span>Logout</span></a>
               </li>

               <!-- Divider -->
               <hr class="sidebar-divider d-none d-md-block">

               <!-- Sidebar Toggler (Sidebar) -->
               <div class="text-center d-none d-md-inline">
                   <button class="rounded-circle border-0" id="sidebarToggle"></button>
               </div>

       </ul>
       <!-- End of Sidebar -->