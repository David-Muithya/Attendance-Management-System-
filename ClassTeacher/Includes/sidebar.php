
    <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
  <a class="sidebar-brand d-flex align-items-center justify-content-center" style="background-color: #121F2B;" href="index.php">
    <div class="sidebar-brand-icon">
      <img src="img/logo/logo.png">
    </div>
    <div class="sidebar-brand-text mx-3">AMS</div>
  </a>
  <hr class="sidebar-divider my-0">
  <li class="nav-item active">
    <a class="nav-link" href="index.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>
  <hr class="sidebar-divider">
  <div class="sidebar-heading">Students</div>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap2" aria-expanded="true" aria-controls="collapseBootstrap2">
      <i class="fas fa-user-graduate"></i>
      <span>Manage Students</span>
    </a>
    <div id="collapseBootstrap2" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Manage Students</h6>
        <a class="collapse-item" href="viewStudents.php">View Students</a>
        <!-- <a class="collapse-item" href="#">Assets Type</a> -->
      </div>
    </div>
  </li>
  <hr class="sidebar-divider">
  <div class="sidebar-heading">Attendance</div>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapcon" aria-expanded="true" aria-controls="collapseBootstrapcon">
      <i class="fa fa-calendar-alt"></i>
      <span>Manage Attendance</span>
    </a>
    <div id="collapseBootstrapcon" class="collapse" aria-labelledby="headingBootstrapcon" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Manage Attendance</h6>
        <a class="collapse-item" href="takeAttendance.php">Take Attendance</a>
        <a class="collapse-item" href="viewAttendance.php">View Class Attendance</a>
        <a class="collapse-item" href="editStudentAttendance.php">Edit Student Attendance</a>
        <a class="collapse-item" href="viewStudentAttendance.php">View Student Attendance</a>
        <a class="collapse-item" href="downloadRecord.php">Attendance Report (xls)</a>
        <!-- <a class="collapse-item" href="addMemberToContLevel.php ">Add Member to Level</a> -->
      </div>
    </div>
  </li>
  <hr class="sidebar-divider">
  <div class="sidebar-heading">Setting</div>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSettings" aria-expanded="true" aria-controls="collapseSettings">
      <i class="fas fa-fw fa-key"></i>
      <span>Manage Password</span>
    </a>
    <div id="collapseSettings" class="collapse" aria-labelledby="headingSettings" data-parent="#accordionSidebar">
      <div class="bg-white py-3 collapse-inner rounded">
        <h6 class="collapse-header">Change Password</h6>
        <a class="collapse-item" href="change_pwd.php">Change Password</a>
        <!-- Add more settings options here if needed -->
      </div>
    </div>
  </li>
</ul>
