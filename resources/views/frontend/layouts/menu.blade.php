  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{asset('assets')}}/images/logo_chat.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Chat Box</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->

      <ul class="sidebar-menu" data-widget="tree">

        <li>
          <a href="#" data-toggle="modal" data-target="#createRoomModal">
            <i class="fa fa-th"></i> <span>Create Rooms</span>
            <span class="pull-right-container">
              <small class="label pull-right bg-green">+</small>
            </span>
          </a>
        </li>

        
        
      </ul>
      
    </section>
    <!-- /.sidebar -->
  </aside>

 <!-- Custom Script for Modal Handling -->
 <script>
  $('#createRoomModal').on('hidden.bs.modal', function () {
      // Tự động làm mới giao diện hoặc chuyển hướng nếu cần
      window.location.reload(); // Hoặc chuyển đến trang khác
  });
</script>
