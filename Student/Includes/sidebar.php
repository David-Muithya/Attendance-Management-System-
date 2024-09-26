<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
  <!-- Sidebar Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" style="background-color: #121F2B;" href="index.php">
    <div class="sidebar-brand-icon">
      <img src="img/logo/logo.png">
    </div>
    <div class="sidebar-brand-text mx-3">AMS</div>
  </a>
  <hr class="sidebar-divider my-0">

  <!-- Dashboard -->
  <li class="nav-item active">
    <a class="nav-link" href="index.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>
  
  <!-- Attendance Section -->
  <hr class="sidebar-divider">
  <div class="sidebar-heading">
    Attendance
  </div>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAttendance" aria-expanded="true" aria-controls="collapseAttendance">
      <i class="fas fa-user-graduate"></i>
      <span> View Attendance</span>
    </a>
    <div id="collapseAttendance" class="collapse" aria-labelledby="headingAttendance" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">View Attendance</h6>
        <a class="collapse-item" href="viewStudentAttendance.php">View Attendance</a>
      </div>
    </div>
  </li>

  
  <!-- Profile Details Section -->
  <hr class="sidebar-divider">
  <div class="sidebar-heading">
    View Details
  </div>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProfile" aria-expanded="true" aria-controls="collapseProfile">
      <i class="fas fa-user-graduate"></i>
      <span> View Details</span>
    </a>
    <div id="collapseProfile" class="collapse" aria-labelledby="headingProfile" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">View Details</h6>
        <a class="collapse-item" href="Studentdetails.php">View Profile Details</a>
        <a class="collapse-item" href="TeacherDetails.php">Class Teacher Details</Details></a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">
<div class="sidebar-heading">
     Setting
   </div>
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
  </div
 </li>
  
</ul>
