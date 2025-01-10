@extends('backend.layouts.master')

@section('title')
Dashboard Page - Admin Panel
@endsection

@section('styles')
<!-- Start datatable css -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
<style>
.dot-green {
    height: 20px;
    width: 20px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
    background-color: green;
    margin-left: 20px;
}

.dot-red {
    height: 20px;
    width: 20px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
    background-color: red;
    margin-left: 20px;
}
</style>
@endsection

@section('admin-content')
<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">{{ __('Logins') }}</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li><span>{{ __('All Logins') }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    @if(auth()->user()->hasRole('superadmin'))
                    <h4 class="header-title float-left">{{ __('Login Logs') }}</h4>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">{{ __('Sl') }}</th>
                                    <th width="15%">{{ __('User Name') }}</th>
                                    <th width="15%">Fuel Indicator</th>
                                    <th width="15%">DG Running Status</th>
                                    <th width="15%">Site Name</th>
                                    <th width="10%">{{ __('Login Time') }}</th>
                                    <th width="10%">Login Status</th>
                                    <th width="10%">{{ __('Login as User') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logins as $login)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $login->name }}</td>
                                    <td >
                                        <?php
                                            $addValuerun = 0;

                                            foreach ($sites as $site) {
                                                $data = json_decode($site->data);
                                                if ($data && isset($data->parameters)) {
                                                    $parameters = $data->parameters->fuel;
                                                    $capacity = $data->capacity;

                                                    foreach ($events as $event) {
                                                        if (isset($data->parameters->fuel->md) && $event['admin_id'] == $login->user_id) {
                                                            $moduleId = $data->parameters->fuel->md;
                                                            $fieldValue = $data->parameters->fuel->add;

                                                            if ($event['module_id'] == $moduleId && isset($event[$fieldValue])) {
                                                                $addValuerun = (float) $event[$fieldValue]; // Ensure it's numeric
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        ?>
                                        <!-- Display result -->
                                        <b >{{ $addValuerun }} % /
                                        {{ $addValuerun * $capacity / 100 }} L</b>
                                        </td>  
                                    <td>
                                        <?php
                                            $isRunning = false;
                                            $siteFound = false;

                                            foreach ($sites as $site) {
                                                if ($site->email == $login->email) {
                                                    $siteFound = true;
                                                    $data = json_decode($site->data);

                                                    if ($data && isset($data->electric_parameters->voltage_l_l)) {
                                                        foreach ($data->electric_parameters->voltage_l_l as $value) {

                                                            $moduleId = (int) $value->md;
                                                            $addValue = $value->add;

                                                            foreach ($events as $event) {
                                                                if ($event['admin_id'] == $login->user_id) {

                                                                    $eventModuleIds = (array) $event['module_id']; 

                                                                    if (in_array($moduleId, $eventModuleIds)) {
                                                                        if (isset($event[$addValue]) && $event[$addValue] > 0) {
                                                                            $isRunning = true;
                                                                            break 3;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    break;
                                                }
                                            }
                                        ?>

                                        {{ $siteFound ? ($isRunning ? 'Running' : 'Stop') : '_' }}
                                    </td>

                                    <td>
                                        @php
                                        $site = $sites->firstWhere('email', $login->email);
                                        @endphp
                                        {{ $site ? $site->site_name : '_' }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($login->created_at)->timezone('Asia/Kolkata')->format('d M\' Y h:i A') }}
                                    </td>
                                    <td>
                                    <span class="dot-{{ $login->status === 'active' ? 'green' : 'red' }}"></span>
                                    </td>
                                    <td>
                                        @if(auth()->user()->hasRole('superadmin'))
                                        <form action="{{ route('admin.login.submit') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $login->user_id }}">
                                            <button type="submit" class="btn btn-primary text-white">
                                                Login as {{ $login->name }}
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                    
                                </tr>
                                @endforeach

                            </tbody>

                        </table>
                    </div>
                    @else
                    <div class="main-content-inner">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-6 mt-5 mb-3">
                                        <div class="card">
                                            <div class="seo-fact sbg1">
                                                <a href="{{ route('admin.sites.index') }}">
                                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                                        <div class="seofct-icon"><i class="fa fa-users"></i>Total Sites
                                                        </div>
                                                        <h2>{{ $total_sites }}</h2>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- data table end -->
    </div>
</div>
@endsection

@section('scripts')
<!-- Start datatable js -->
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

<script>
if ($('#dataTable').length) {
    $('#dataTable').DataTable({
        responsive: true
    });
}
</script>
@endsection