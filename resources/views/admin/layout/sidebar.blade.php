<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">MCX TRADING</div>
    </div>
    
    <div class="sidebar-menu">
        <a href="{{ url('/admin/dashboard') }}" class="menu-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <i class="fa fa-th-large"></i>
            <span>Dashboard</span>
        </a>

        <div class="menu-label">User Management</div>
        <a href="{{ url('/admin/users') }}" class="menu-item {{ Request::is('admin/users') && !request('requests') ? 'active' : '' }}">
            <i class="fa fa-users"></i>
            <span>All Users</span>
        </a>
        <a href="{{ url('/admin/users/requests') }}" class="menu-item {{ Request::is('admin/users/requests') ? 'active' : '' }}">
            <i class="fa fa-user-plus"></i>
            <span>Activation Requests</span>
        </a>

        <div class="menu-label">Order Management</div>
        <a href="{{ url('/admin/orders') }}" class="menu-item {{ Request::is('admin/orders') && !Request::is('admin/orders/pending') && !Request::is('admin/orders/completed') ? 'active' : '' }}">
            <i class="fa fa-shopping-cart"></i>
            <span>All Orders</span>
        </a>
        <a href="{{ url('/admin/orders/pending') }}" class="menu-item {{ Request::is('admin/orders/pending') ? 'active' : '' }}">
            <i class="fa fa-clock"></i>
            <span>Pending Settlements</span>
        </a>
        <a href="{{ url('/admin/orders/completed') }}" class="menu-item {{ Request::is('admin/orders/completed') ? 'active' : '' }}">
            <i class="fa fa-check-circle"></i>
            <span>Completed Orders</span>
        </a>

        <div class="menu-label">Product Management</div>
        <a href="{{ route('admin.products.index') }}" class="menu-item {{ Request::is('admin/products*') ? 'active' : '' }}">
            <i class="fa fa-box"></i>
            <span>Product Rows</span>
        </a>
        <a href="{{ route('admin.coins.index') }}" class="menu-item {{ Request::is('admin/coins*') ? 'active' : '' }}">
            <i class="fa fa-circle"></i>
            <span>Coins</span>
        </a>

        <div class="menu-label">Market Management</div>
        <a href="{{ url('/admin/rates/gold') }}" class="menu-item {{ Request::is('admin/rates/gold') ? 'active' : '' }}">
            <i class="fa fa-coins"></i>
            <span>Gold Rate</span>
        </a>
        <a href="{{ url('/admin/rates/silver') }}" class="menu-item {{ Request::is('admin/rates/silver') ? 'active' : '' }}">
            <i class="fa fa-solid fa-circle-notch"></i>
            <span>Silver Rate</span>
        </a>

        <div class="menu-label">Data & Reports</div>
        <a href="{{ url('/admin/reports/history') }}" class="menu-item {{ Request::is('admin/reports/history') ? 'active' : '' }}">
            <i class="fa fa-file-alt"></i>
            <span>Order History</span>
        </a>

        <div class="menu-label">System</div>
        <a href="{{ url('/admin/settings') }}" class="menu-item {{ Request::is('admin/settings') ? 'active' : '' }}">
            <i class="fa fa-cog"></i>
            <span>Settings</span>
        </a>
        
        <a href="{{ url('/admin/login') }}" class="menu-item" style="margin-top: 20px; color: var(--danger);">
            <i class="fa fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
