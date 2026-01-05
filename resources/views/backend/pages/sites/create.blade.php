@extends('backend.layouts.master')

@section('title')
Site Create - Admin Panel
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<style>
* {
    text-decoration: none !important;
}

.form-check-label {
    text-transform: capitalize;

}

.select2-container--default.select2-container--focus .select2-selection--multiple {
    border: solid black 1px;
    outline: 0;
    padding: 8px !important;
}

input {
    width: 15%;
}

.inp {
    width: 50%;

}
</style>
@endsection


@section('admin-content')

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix ">
                <h4 class="page-title pull-left">Site Create</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.sites.index') }}">All Sites</a></li>
                    <li><span>Create Site</span></li>
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
                    <h4 class="header-title">Create New Site</h4>
                    <!-- <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="createAdminDropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('Create New Admin') }}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="createAdminDropdown">
                            <a class="dropdown-item" href="{{ route('admin.admins.create', ['type' => 'dg']) }}">DG</a>
                            <a class="dropdown-item"
                                href="{{ route('admin.admins.create', ['type' => 'energy']) }}">Energy</a>
                            <a class="dropdown-item"
                                href="{{ route('admin.admins.create', ['type' => 'meter']) }}">Meter</a>
                            <a class="dropdown-item"
                                href="{{ route('admin.admins.create', ['type' => 'tanks']) }}">Tanks</a>
                            <a class="dropdown-item"
                                href="{{ route('admin.admins.create', ['type' => 'pump']) }}">Pump</a>
                            <a class="dropdown-item"
                                href="{{ route('admin.admins.create', ['type' => 'lighting']) }}">Lighting</a>
                        </div>
                    </div> -->
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('admin.sites.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="name">Site Name</label>
                                <input type="text" class="form-control" id="site_name" name="site_name"
                                    placeholder="Enter Name" required autofocus MD="{{ old('name') }}">
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="email">Site Email</label>
                                <select name="email" id="email" class="form-control select2" required>
                                    <option MD="">Select Email</option>
                                    @foreach($user_emails as $email)
                                    <option MD="{{ $email->email }}">{{ $email->email }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="name">Default Device ID</label>
                                <input type="text" class="form-control" id="device_id" name="device_id"
                                    placeholder="Enter Device Id" required autofocus MD="{{ old('device_id') }}">
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label for="name">Cluster ID</label>
                                <input type="text" class="form-control" id="clusterID" name="clusterID"
                                    placeholder="Enter Device Id" required autofocus MD="{{ old('clusterID') }}">
                            </div>
                             <div class="form-group col-md-4 col-sm-12">
                                <label for="name">Alternative Device ID</label>
                                <input type="text" class="form-control" id="alternate_device_id" name="alternate_device_id">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="container mt-2 ml-5 d-flex">
                                <div class="col-md-3">
                                    <div class="card shadow-lg g-5"
                                        style="width: 100%; height: 1750px; overflow: hidden;">
                                        <div class="card-header bg-light text-start text-center">
                                            <!-- site Name -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Site name:</span>
                                                <input type="text" name="site_name" class="form-control ms-2"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Asset Name -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Asset name:</span>
                                                <input type="text" name="asset_name" class="form-control ms-2"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Group -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Group:</span>
                                                <input type="text" name="group" class="form-control ms-2"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Generator -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Generator:</span>
                                                <input type="text" name="generator" class="form-control ms-2"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Serial Number -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>S/N:</span>
                                                <input type="text" name="serial_number" class="form-control ms-2"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Model -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Model:</span>
                                                <input type="text" name="model" class="form-control ms-2"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Brand -->
                                            <div class="d-flex justify-content-between align-items-center"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Brand:</span>
                                                <input type="text" name="brand" class="form-control ms-2"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- capicity liter -->
                                            <div class="d-flex justify-content-between align-items-center"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Capacity:</span>
                                                <input type="text" name="capacity" class="form-control ms-2"
                                                    style="width: 70%;">
                                            </div>
                                        </div>

                                        <div class="card-body p-2">
                                            <!-- Run Status -->
                                            <div class="card mb-1 shadow-lg border-0">
                                                <div class="card-body text-center">
                                                    <div class="fw-bold text-secondary">Run status</div>
                                                    <div class="text-info fw-bold fs-6">
                                                        <i class="fas fa-cogs"
                                                            style="color: teal; font-size: 20px; margin-right: 8px;"></i>
                                                        Running
                                                    </div>
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" name="run_status_md" class="form-control ms-2"
                                                        style="width: 50%;" placeholder="MD">
                                                    <input type="text" name="run_status_add" class="form-control ms-2"
                                                        style="width: 50%;" placeholder="ADD">
                                                </div>
                                            </div>

                                            <!-- Active Power (kW) -->
                                            <div class="card mb-1 shadow-lg border-0">
                                                <div class="card-body text-center">
                                                    <div class="fw-bold text-secondary mb-1">Active power, kW</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="active_power_kw_md"
                                                            class="form-control ms-2" style="width: 50%;"
                                                            placeholder="MD">
                                                        <input type="text" name="active_power_kw_add"
                                                            class="form-control ms-2" style="width: 50%;"
                                                            placeholder="ADD">
                                                    </div>
                                                    <div class="fw-bold text-secondary mb-1 shadow-lg my-3">Active
                                                        power, kVA</div>
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" name="active_power_kva_md"
                                                        class="form-control ms-2" style="width: 50%;" placeholder="MD">
                                                    <input type="text" name="active_power_kva_add"
                                                        class="form-control ms-2" style="width: 50%;" placeholder="ADD">
                                                </div>
                                            </div>

                                            <!-- Power Factor -->
                                            <div class="card mb-1 shadow-lg border-0">
                                                <div class="card-body text-center">
                                                    <div class="fw-bold text-secondary">Power factor</div>
                                                    <div class="text-info fw-bold fs-6">0.96</div>
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" name="power_factor_md" class="form-control ms-2"
                                                        style="width: 50%;" placeholder="MD">
                                                    <input type="text" name="power_factor_add" class="form-control ms-2"
                                                        style="width: 50%;" placeholder="ADD">
                                                </div>
                                            </div>

                                            <!-- Total kWh -->
                                            <div class="card mb-2 shadow-lg border-0;">
                                                <div class="card-body text-center">
                                                    <div class="text-secondary fw-bold">Total kWh</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="total_kwh_md" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="MD">
                                                        <input type="text" name="total_kwh_add"
                                                            class="form-control ms-2" style="width: 50%;"
                                                            placeholder="ADD">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mb-2 shadow-lg border-0;">
                                                <div class="card-body text-center">
                                                    <div class="text-secondary fw-bold">Start</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="start_md" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="MD">
                                                        <input type="text" name="start_add" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="ADD">
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" name="start_arg" class="form-control ms-2"
                                                            placeholder="ARGUMENT">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card mb-2 shadow-lg border-0;">
                                                <div class="card-body text-center">
                                                    <div class="text-secondary fw-bold">Stop</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="stop_md" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="MD">
                                                        <input type="text" name="stop_add" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="ADD">
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" name="stop_arg" class="form-control ms-2"
                                                            placeholder="ARGUMENT">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card mb-2 shadow-lg border-0;">
                                                <div class="card-body text-center">
                                                    <div class="text-secondary fw-bold">Auto</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="auto_md" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="MD">
                                                        <input type="text" name="auto_add" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="ADD">
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" name="auto_arg" class="form-control ms-2"
                                                            placeholder="ARGUMENT">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card mb-2 shadow-lg border-0;">
                                                <div class="card-body text-center">
                                                    <div class="text-secondary fw-bold">Manual</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="manual_md" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="MD">
                                                        <input type="text" name="manual_add" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="ADD">
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" name="manual_arg1" class="form-control ms-2"
                                                            placeholder="ARGUMENT">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- **************************Fault-reset**************************************************** -->
                                            <div class="card mb-2 shadow-lg border-0;">
                                                <div class="card-body text-center">
                                                    <div class="text-secondary fw-bold">Fault_Reset</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="fault_reset_md" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="MD">
                                                        <input type="text" name="fault_reset_add" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="ADD">
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" name="fault_reset_arg1" class="form-control ms-2"
                                                            placeholder="ARGUMENT">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- **************************Fault-reset_End**************************************************** -->
                                            
                                            
                                            <div class="card mb-2 shadow-lg border-0;">
                                                <div class="card-body text-center">
                                                    <div class="text-secondary fw-bold">MODE_DISPLAY</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="mode_md" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="MD">
                                                        <input type="text" name="mode_add" class="form-control ms-2"
                                                            style="width: 50%;" placeholder="ADD">
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card" style="width: 100%; height: 905px;">
                                        <div class="card-header text-center bg-secondary text-white">
                                            ENGINE PARAMETERS
                                        </div>
                                        <div class="card-body" style="font-size: 10px; padding: 8px; overflow: hidden;">
                                            <div class="row">
                                                <!-- Parameter Section Template -->
                                                <div class="col text-center fw-bold">
                                                    COOLANT TEMPERATURE
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-thermometer-half me-2 text-primary"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="coolant_temperature_md"
                                                            class="form-control me-2" style="width: 80px;"
                                                            placeholder="MD">
                                                        <input type="text" name="coolant_temperature_add"
                                                            class="form-control" style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold">
                                                    OIL TEMPERATURE
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-oil-can me-2 text-warning"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="oil_temperature_md"
                                                            class="form-control me-2" style="width: 80px;"
                                                            placeholder="MD">
                                                        <input type="text" name="oil_temperature_add"
                                                            class="form-control" style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold">
                                                    OIL PRESSURE
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-tachometer-alt me-2 text-success"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="oil_pressure_md"
                                                            class="form-control me-2" style="width: 80px;"
                                                            placeholder="MD">
                                                        <input type="text" name="oil_pressure_add" class="form-control"
                                                            style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold">
                                                    RPM
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-tachometer-alt me-2 text-danger"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="rpm_md" class="form-control me-2"
                                                            style="width: 80px;" placeholder="MD">
                                                        <input type="text" name="rpm_add" class="form-control"
                                                            style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold">
                                                    NUMBER OF STARTS
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-key me-2 text-secondary"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="number_of_starts_md"
                                                            class="form-control me-2" style="width: 80px;"
                                                            placeholder="MD">
                                                        <input type="text" name="number_of_starts_add"
                                                            class="form-control" style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold">
                                                    BATTERY VOLTAGE
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-battery-half me-2 text-info"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="battery_voltage_md"
                                                            class="form-control me-2" style="width: 80px;"
                                                            placeholder="MD">
                                                        <input type="text" name="battery_voltage_add"
                                                            class="form-control" style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold">
                                                    FUEL
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-gas-pump me-2 text-success"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="fuel_md" class="form-control me-2"
                                                            style="width: 80px;" placeholder="MD">
                                                        <input type="text" name="fuel_add" class="form-control"
                                                            style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- Running Hours Section -->
                                            <div class="row align-items-center"
                                                style="font-size: 10px; padding: 8px; margin-bottom: 0; margin-top: -20px;">
                                                <div
                                                    class="col-xs-6 d-flex align-items-center justify-content-center fw-bold">
                                                    <i class="fas fa-clock text-secondary" style="font-size: 1rem;"></i>
                                                    Running Hours
                                                </div>
                                                <div class="col-xs-3 text-center fw-bold">8.34 H</div>
                                                <div class="col-xs-3 text-center fw-bold">
                                                    <i class="fas fa-question-circle me-2 text-muted"
                                                        style="font-size: 1rem;"></i>
                                                    N/A
                                                </div>
                                                <div class="container d-flex">
                                                    <input type="text" name="running_hours_md" class="form-control me-2"
                                                        style="width: 80px;" placeholder="MD">
                                                    <input type="text" name="running_hours_add" class="form-control"
                                                        style="width: 80px;" placeholder="ADD">
                                                    <input type="text" name="increase_minutes" class="form-control"
                                                        style="width: 60px;  margin-left: -20px;" placeholder="Minutes">
                                                </div>
                                                <div class="d-flex">
                                                    <div class="fw-bold text-secondary text-center my-4">
                                                        Hide/showing Increase Running Hours
                                                        <input type="checkbox" name="increase_running_hours_status"
                                                            value="1">
                                                    </div>
                                                </div>

                                                <div class="col text-center fw-bold">
                                                    Read(On)
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        
                                                        <input type="text" name="readOn_md" class="form-control me-2"
                                                            style="width: 80px;" placeholder="MD">
                                                        <input type="text" name="readOn_add" class="form-control"
                                                            style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <div class="col text-center fw-bold">
                                                    READING STATUS
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        
                                                        <input type="text" name="readstatus_md" class="form-control me-2"
                                                            style="width: 80px;" placeholder="MD">
                                                        <input type="text" name="readstatus_add" class="form-control"
                                                            style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card fw-bold" style="width: 100%; margin: auto; height: 900px;">
                                        <div class="card-header text-center bg-secondary text-white fs-6">
                                            ELECTRIC PARAMETERS
                                        </div>
                                        <div class="card-body" style="font-size: 10px; padding: 8px;">
                                            <!-- Table Header -->
                                            <div class="row border-bottom pb-3 mb-3">
                                                <div class="col text-center">Heading</div>
                                                <div class="col text-center">A</div>
                                                <div class="col text-center">B</div>
                                                <div class="col text-center">C</div>
                                            </div>
                                            <hr />
                                            <!-- Voltage L-L -->
                                            <div class="row mb-4">
                                                <div class="col-12 text-center">Voltage L-L, V</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_l_a_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_l_a_add" placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_l_b_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_l_b_add" placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_l_c_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_l_c_add" placeholder="ADD">
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- Voltage L-N -->
                                            <div class="row mb-4">
                                                <div class="col-12 text-center">Voltage L-N, V</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_n_a_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_n_a_add" placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_n_b_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_n_b_add" placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_n_c_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_n_c_add" placeholder="ADD">
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- Current -->
                                            <div class="row mb-4">
                                                <div class="col-12 text-center">Current, A</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="current_a_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="current_a_add" placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="current_b_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="current_b_add" placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="current_c_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="current_c_add" placeholder="ADD">
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- pf-data -->
                                            <div class="row mb-4">
                                                <div class="col-12 text-center">pf-data</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="pf_data_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="pf_data_add" placeholder="ADD">
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- Frequency -->
                                            <div class="row mb-4">
                                                <div class="col-12 text-center">Frequency</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="frequency_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="frequency_add" placeholder="ADD">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card shadow-sm fw-bold"
                                        style="height: 900px; width: 100%; font-size: 10px;">
                                        <div class="card-header text-center bg-secondary text-white fs-6">
                                            ALARM STATUS
                                        </div>
                                        <div class="card-body p-3 d-flex flex-column justify-content-center">
                                            <div class="row h-100">
                                                <!-- Left Column -->
                                                <div
                                                    class="col-xs-6 border-end border-dark d-flex flex-column justify-content-around">
                                                    <!-- Coolant Temperature -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Coolant Temperature</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="coolant_temperature_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="coolant_temperature_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- Fuel Level -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>Fuel Level</span>
                                                    </div>



                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fuel_level_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fuel_level_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- Emergency Stop -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Emergency stop</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="emergency_stop_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="emergency_stop_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- High Coolant Temperature -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>High coolant temperature</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="high_coolant_temperature_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="high_coolant_temperature_add_status"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Under Speed -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Under speed</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="under_speed_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="under_speed_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- Fail to Start -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>Fail to start</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fail_to_start_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fail_to_start_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- Loss of Speed Sensing -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Loss of speed sensing</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="loss_of_speed_sensing_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="loss_of_speed_sensing_add_status" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <!-- Right Column -->
                                                <div class="col-xs-6 d-flex flex-column justify-content-around">
                                                    <!-- Oil Pressure -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Oil Pressure</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="oil_pressure_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="oil_pressure_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- Battery Level -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>Battery Level</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="battery_level_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="battery_level_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- Low Oil Pressure -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Low oil pressure</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="low_oil_pressure_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="low_oil_pressure_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- High Oil Temperature -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>High oil temperature</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="high_oil_temperature_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="high_oil_temperature_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- Over Speed -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Over speed</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="over_speed_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="over_speed_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- Fail to Come to Rest -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>Fail to come to rest</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fail_to_come_to_rest_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fail_to_come_to_rest_add_status" placeholder="ADD">
                                                    </div>

                                                    <!-- Generator Low Voltage -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Generator low voltage</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="generator_low_voltage_md_status" placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="generator_low_voltage_add_status" placeholder="ADD">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Save</button>
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary mt-4 pr-4 pl-4">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();
})
</script>
@endsection