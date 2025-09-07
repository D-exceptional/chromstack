<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #181d38 !important;overflow-y: auto;overflow-x: hidden !important;">
    <!-- Brand Logo -->
    <a href="../" class="brand-link">
       <img src="../../../assets/img/short-logo.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="width: 35px;height: 40px;border-radius: 50%;opacity: .9;">
      <span class="brand-text font-weight-light"><b>Chromstack</b></span>
      <input type="text" id="active-email" value="<?php echo $email; ?>" hidden>
      <input type="text" id="admin-name" value="<?php echo $fullname; ?>" hidden>
    </a>
    <!-- Sidebar -->
    <div class="sidebar" style="height: 630px;overflow-x: hidden !important;">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
        <?php
            if ($profile !== 'null') {
                 echo "<img src='../../../uploads/$profile' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;border: 2px solid #c2c7d0;' alt='User Image'>";
            }
            else {
              echo "<img src='../../../assets/img/user.png' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;border: 2px solid #c2c7d0;' alt='User Image'>";
            }
          ?>
        </div>
        <div class="info">
        <?php
            if ($fullname !== 'null') {
                 echo "<a href='../views/profile.php' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>$fullname</a>";
            }
            else {
              echo "<a href='../views/profile.php' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>Admin</a>";
            }
          ?>
        </div>
      </div>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
           <li class="nav-item">
            <a href="../index.php" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
                <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item" id="eLearning">
            <a href="../../../eLearning/index.php?access=Admin&accessID=<?php echo $adminID; ?>" class="nav-link">
              <i class="fas fa-university" style='font-size: 20px;padding-right: 4px;'></i>
              <p>e-Learning</p>
            </a>
          </li>
          <!-- All Courses -->
          <li class="nav-item">
            <a href="../views/courses.php" class="nav-link">
              <i class="nav-icon fas fa-layer-group"></i>
                <p>Courses</p>
            </a>
          </li>
          <!-- Transactions -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                Payments
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../views/actions.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Approvals</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/transaction.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Analysis</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/orders.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>History</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/withdrawals.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Payouts</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="../views/tickets.php" class="nav-link">
              <i class="nav-icon fas fa-ticket-alt"></i>
              <p>
                Tickets
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../views/wallet.php" class="nav-link">
              <i class="nav-icon fas fa-random"></i>
              <p>
                Withdrawal
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-medal"></i>
              <p>
                Contest
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../views/active-contests.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Active</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/completed-contests.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Completed</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-tag"></i>
              <p>
                Affiliates
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../views/fully-registered-affiliates.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Active</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/created-affiliates.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Created</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/pending-affiliates.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pending</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-graduate"></i>
              <p>
                Vendors
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../views/active-vendors.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Registered</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/pending-vendors.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pending</p>
                </a>
              </li>
            </ul>
          </li>
           <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../views/users.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Students</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-plus"></i>
              <p>
                Create
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../views/create-affiliate.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Affiliate</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/create-vendor.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Vendor</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-envelope-open"></i>
              <p>
                Mail
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../views/mailbox.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inbox</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../views/compose-mail.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Compose</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-globe"></i>
              <p>
                Community
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
             <li class="nav-item">
                <a href="https://x.com/chromstack?s=21" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>X</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://chat.whatsapp.com/LjgB5DhGbh9KCrHgvNtQ5z" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>WhatsApp</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://t.me/+gc9Fr20Y70A0NTdk" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Telegram</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://facebook.com/profile.php?id=61556804134821" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Facebook</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://instagram.com/chromstack?igshid=MzRIODBiNWFIZA==" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Instagram</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://www.tiktok.com/@chromstack" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tiktok</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://www.youtube.com/@Chromstack" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>YouTube</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="../views/profile.php" class="nav-link">
              <i class="fas fa-cog nav-icon"></i>
              <p>Settings</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../server/logout.php" class="nav-link">
              <i class="nav-icon fas fa-arrow-left"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>