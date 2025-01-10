<div class="user-profile pull-right">
    {{-- <img class="avatar user-thumb" src="{{ asset('backend/assets/images/author/avatar.png') }}" alt="avatar"> --}}
    <h4 class="user-name dropdown-toggle" data-toggle="dropdown">
        {{ Auth::guard('admin')->user()->name }}
        <i class="fa fa-angle-down"></i>
    </h4>

    <div class="dropdown-menu">
        @if(!auth()->user()->hasRole('superadmin'))
        @if (session('original_superadmin_id'))
        <!-- Button to login back as Superadmin -->
        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ session('original_superadmin_id') }}">
            <button type="submit" class="dropdown-item">Return to Superadmin</button>
        </form>
        @endif
        @endif
        <a class="dropdown-item" href="{{ route('admin.logout.submit') }}" onclick="event.preventDefault();
                      document.getElementById('admin-logout-form').submit();">Log Out</a>
    </div>

    <form id="admin-logout-form" action="{{ route('admin.logout.submit') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>