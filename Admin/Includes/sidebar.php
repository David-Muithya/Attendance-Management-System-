 <ul class="navbar-nav sidebar sidebar-light accordion " id="accordionSidebar">
   <a class="sidebar-brand d-flex align-items-center   justify-content-center" style="background-color: #121F2B;" href="index.php">
     <div class="sidebar-brand-icon">
       <img src="img/logo/logo.png">
     </div>
     <div class="sidebar-brand-text mx-3">AMS</div>
   </a>
   <hr class="sidebar-divider my-0">
   <li class="nav-item active">
     <a class="nav-link" href="index.php">
       <i class="fas fa-fw fa-tachometer-alt"></i>
       <span>Dashboard</span></a>
   </li>
   <hr class="sidebar-divider">
   <div class="sidebar-heading">
     Class and Class Arms
   </div>
   <li class="nav-item">
     <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap" aria-expanded="true" aria-controls="collapseBootstrap">
       <i class="fas fa-chalkboard"></i>
       <span>Manage Classes</span>
     </a>
     <div id="collapseBootstrap" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
       <div class="bg-white py-2 collapse-inner rounded">
         <h6 class="collapse-header">Manage Classes</h6>
         <a class="collapse-item" href="createClass.php">Create Class</a>
         <!-- <a class="collapse-item" href="#">Member List</a> -->
       </div>
     </div>
   </li>
   <li class="nav-item">
     <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapusers" aria-expanded="true" aria-controls="collapseBootstrapusers">
       <i class="fas fa-code-branch"></i>
       <span>Manage Class Arms</span>
     </a>
     <div id="collapseBootstrapusers" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
       <div class="bg-white py-2 collapse-inner rounded">
         <h6 class="collapse-header">Manage Class Arms</h6>
         <a class="collapse-item" href="createClassArms.php">Create Class Arms</a>
         <!-- <a class="collapse-item" href="usersList.php">User List</a> -->
       </div>
     </div>
   </li>
   <hr class="sidebar-divider">
   <div class="sidebar-heading">
     Teachers
   </div>
   <li class="nav-item">
     <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapassests" aria-expanded="true" aria-controls="collapseBootstrapassests">
       <i class="fas fa-chalkboard-teacher"></i>
       <span>Manage Teachers</span>
     </a>
     <div id="collapseBootstrapassests" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
       <div class="bg-white py-2 collapse-inner rounded">
         <h6 class="collapse-header">Manage Class Teachers</h6>
         <a class="collapse-item" href="createClassTeacher.php">Create Class Teachers</a>
         <!-- <a class="collapse-item" href="assetsCategoryList.php">Assets Category List</a>
             <a class="collapse-item" href="createAssets.php">Create Assets</a> -->
       </div>
     </div>
   </li>
   <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrapschemes"
          aria-expanded="true" aria-controls="collapseBootstrapschemes">
          <i class="fas fa-home"></i>
          <span>Manage Schemes</span>
        </a>
        <div id="collapseBootstrapschemes" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Manage Schemes</h6>
             <a class="collapse-item" href="createSchemes.php">Create Scheme</a>
            <a class="collapse-item" href="schemeList.php">Scheme List</a>
          </div>
        </div>
      </li> -->

   <hr class="sidebar-divider">
   <div class="sidebar-heading">
     Students
   </div>
   </li>
   <li class="nav-item">
     <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap2" aria-expanded="true" aria-controls="collapseBootstrap2">
       <i class="fas fa-user-graduate"></i>
       <span>Manage Students</span>
     </a>
     <div id="collapseBootstrap2" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
       <div class="bg-white py-2 collapse-inner rounded">
         <h6 class="collapse-header">Manage Students</h6>
         <a class="collapse-item" href="createStudents.php">Create Students</a>
         <!-- <a class="collapse-item" href="#">Assets Type</a> -->
       </div>
     </div>
   </li>

   <hr class="sidebar-divider">
   <div class="sidebar-heading">
     Session & Term
   </div>
   <!-- Manage Session & Term Dropdown -->
<li class="nav-item">
  <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSessionTerm" aria-expanded="true" aria-controls="collapseSessionTerm">
    <i class="fa fa-calendar-alt"></i>
    <span>Manage Session & Term</span>
  </a>
  <div id="collapseSessionTerm" class="collapse" aria-labelledby="headingSessionTerm" data-parent="#accordionSidebar">
    <div class="bg-white py-2 collapse-inner rounded">
      <h6 class="collapse-header">Contribution</h6>
      <a class="collapse-item" href="createSessionTerm.php">Create Session and Term</a>
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
      <a class="collapse-item" href="change_teacherspwd.php">Teacher Password</a>
      <a class="collapse-item" href="change_studentspwd.php">Student Password</a>
      <!-- Add more settings options here if needed -->
    </div>
  </div
 </li>


  






  

 </ul>