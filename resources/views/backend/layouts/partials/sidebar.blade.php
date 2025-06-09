 <!-- sidebar menu area start -->
 @php
 $usr = Auth::guard('admin')->user();
 @endphp
 <div class="sidebar-menu">
     <div class="sidebar-header">
         <div class="logo">
             <a href="{{ route('admin.dashboard') }}">
                 <h2 class="text-white" style="margin-left: -3.5rem !important;">SUPERADMIN</h2>

             </a>
         </div>
     </div>
     <div class="main-menu">
         <div class="menu-inner">
             <nav>
                 <ul class="metismenu" id="menu">

                     @if ($usr->can('dashboard.view'))
                     <li class="active">
                         <a href="javascript:void(0)" aria-expanded="true"><i
                                 class="ti-dashboard"></i><span>dashboard</span></a>
                         <ul class="collapse">
                             <li class="{{ Route::is('admin.dashboard') ? 'active' : '' }}"><a
                                     href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                         </ul>
                     </li>
                     @endif

                     @if ($usr->can('role.create') || $usr->can('role.view') || $usr->can('role.edit') ||
                     $usr->can('role.delete'))
                     <li>
                         <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                                 Roles & Permissions
                             </span></a>
                         <ul
                             class="collapse {{ Route::is('admin.roles.create') || Route::is('admin.roles.index') || Route::is('admin.roles.edit') || Route::is('admin.roles.show') ? 'in' : '' }}">
                             @if ($usr->can('role.view'))
                             <li
                                 class="{{ Route::is('admin.roles.index')  || Route::is('admin.roles.edit') ? 'active' : '' }}">
                                 <a href="{{ route('admin.roles.index') }}">All Roles</a>
                             </li>
                             @endif
                             @if ($usr->can('role.create'))
                             <li class="{{ Route::is('admin.roles.create')  ? 'active' : '' }}"><a
                                     href="{{ route('admin.roles.create') }}">Create Role</a></li>
                             @endif
                         </ul>
                     </li>
                     @endif


                     @if ($usr->can('admin.create') || $usr->can('admin.view') || $usr->can('admin.edit') ||
                     $usr->can('admin.delete'))
                     <li>
                         <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                                 Admins
                             </span></a>
                         <ul
                             class="collapse {{ Route::is('admin.admins.create') || Route::is('admin.admins.index') || Route::is('admin.admins.edit') || Route::is('admin.admins.show') ? 'in' : '' }}">

                             @if ($usr->can('admin.view'))
                             <li
                                 class="{{ Route::is('admin.admins.index')  || Route::is('admin.admins.edit') ? 'active' : '' }}">
                                 <a href="{{ route('admin.admins.index') }}">All Admins</a>
                             </li>
                             @endif

                             @if ($usr->can('admin.create'))
                             <li class="{{ Route::is('admin.admins.create')  ? 'active' : '' }}"><a
                                     href="{{ route('admin.admins.create') }}">Create Admin</a></li>
                             @endif
                         </ul>
                     </li>
                     @endif

                     @if ($usr->can('site.create') || $usr->can('site.view') || $usr->can('site.edit') ||
                     $usr->can('site.delete'))
                     <li>
                         <a href="javascript:void(0)" aria-expanded="true">
                             <i class="fa fa-cogs"></i> <!-- Updated icon -->
                             <span>Site</span>
                         </a>
                         <ul
                             class="collapse {{ Route::is('admin.sites.create') || Route::is('admin.sites.index') || Route::is('admin.sites.edit') || Route::is('admin.sites.show') ? 'in' : '' }}">

                             @if ($usr->can('site.view'))
                             <li
                                 class="{{ Route::is('admin.sites.index')  || Route::is('admin.sites.edit') ? 'active' : '' }}">
                                 <a href="{{ route('admin.sites.index') }}">All Sites</a>
                             </li>
                             @endif

                             @if ($usr->can('site.create'))
                             <li class="{{ Route::is('admin.sites.create')  ? 'active' : '' }}">
                                 <a href="{{ route('admin.sites.create') }}">Create Site</a>
                             </li>
                             @endif
                         </ul>
                     </li>
                     @endif

                     <li>
                         <a href="javascript:void(0)" aria-expanded="true">
                             <i class="fa fa-cogs"></i> <!-- Updated icon -->
                             <span>Notification </span>
                         </a>
                         <ul
                             class="collapse {{ Route::is('admin.notification.create') || Route::is('admin.notification.index') || Route::is('admin.notification.edit') || Route::is('admin.notification.show') ? 'in' : '' }}">
                             <li class="{{ Route::is('admin.notification.create')  ? 'active' : '' }}">
                                 <a href="{{ route('admin.notification.create') }}"> Notification Form</a>
                             </li>
                             <li
                                 class="{{ Route::is('admin.notification.list')  || Route::is('admin.notification.edit') ? 'active' : '' }}">
                                 <a href="{{ route('admin.notification.list') }}">Notification List</a>
                             </li>
                         </ul>

                     </li>

                 </ul>
             </nav>
         </div>
     </div>
 </div>
 <!-- sidebar menu area end -->