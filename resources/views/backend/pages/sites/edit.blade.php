@extends('backend.layouts.master')

@section('title')
Site Edit - Admin Panel
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
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Site Edit</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.sites.index') }}">All Sites</a></li>
                    <li><span>Edit Site - {{ $site->name }}</span></li>
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
                    <h4 class="header-title">Edit Site - {{ $site->name }}</h4>
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('admin.sites.update', $site->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="name">Site Name</label>
                                <input type="text" class="form-control" id="site_name" name="site_name"
                                    placeholder="Enter Name" value="{{ $site->site_name }}" required autofocus>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="email">Site Email</label>
                                <select name="email" id="email" class="form-control select2" required>
                                    @foreach($user_emails as $email)
                                    <option value="{{ $email->email }}" @if(old('email', $site->email)==$email->
                                        email) selected @endif>
                                        {{ $email->email }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 col-sm-12">
                                <label for="name">Device ID</label>
                                <input type="text" class="form-control" id="device_id" name="device_id"
                                    value="{{ $site->device_id }}" placeholder="Enter Device Id" required autofocus
                                    MD="{{ old('device_id') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="container mt-2 ml-5 d-flex">
                                <div class="col-md-3">
                                    <div class="card shadow-lg g-5"
                                        style="width: 100%; height: 915px; overflow: hidden;">
                                        <div class="card-header bg-light text-start text-center">
                                            <!-- Asset Name -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Asset name:</span>
                                                <input type="text" name="asset_name" class="form-control ms-2"
                                                    value="{{ old('asset_name', $siteData['asset_name'] ?? '') }}"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Group -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Location:</span>
                                                <input type="text" name="group" class="form-control ms-2"
                                                    value="{{ old('group', $siteData['group'] ?? '') }}"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Generator -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Generator:</span>
                                                <input type="text" name="generator" class="form-control ms-2"
                                                    value="{{ old('generator', $siteData['generator'] ?? '') }}"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Serial Number -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>S/N:</span>
                                                <input type="text" name="serial_number" class="form-control ms-2"
                                                    value="{{ old('serial_number', $siteData['serial_number'] ?? '') }}"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Model -->
                                            <div class="d-flex justify-content-between align-items-center mb-2"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Model:</span>
                                                <input type="text" name="model" class="form-control ms-2"
                                                    value="{{ old('model', $siteData['model'] ?? '') }}"
                                                    style="width: 70%;">
                                            </div>
                                            <!-- Brand -->
                                            <div class="d-flex justify-content-between align-items-center"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Brand:</span>
                                                <input type="text" name="brand" class="form-control ms-2"
                                                    value="{{ old('brand', $siteData['brand'] ?? '') }}"
                                                    style="width: 70%;">
                                            </div>

                                            <!-- capicity liter -->
                                            <div class="d-flex justify-content-between align-items-center"
                                                style="font-size: 12px; margin-top: 10px;">
                                                <span>Capacity:</span>
                                                <input type="text" name="capacity" class="form-control ms-2"
                                                    value="{{ old('capacity', $siteData['capacity'] ?? '') }}"
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
                                                        value="{{ old('run_status', $siteData['run_status']['md'] ?? '') }}"
                                                        style="width: 50%;" placeholder="MD">
                                                    <input type="text" name="run_status_add" class="form-control ms-2"
                                                        value="{{ old('run_status', $siteData['run_status']['add'] ?? '') }}"
                                                        style="width: 50%;" placeholder="ADD">
                                                </div>
                                            </div>

                                            <!-- Active Power (kW) -->
                                            <div class="card mb-1 shadow-lg border-0">
                                                <div class="card-body text-center">
                                                    <div class="fw-bold text-secondary mb-1">Active power, kW</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="active_power_kw_md"
                                                            class="form-control ms-2"
                                                            value="{{ old('active_power_kw', $siteData['active_power_kw']['md'] ?? '') }}"
                                                            style="width: 50%;" placeholder="MD">
                                                        <input type="text" name="active_power_kw_add"
                                                            class="form-control ms-2"
                                                            value="{{ old('active_power_kw', $siteData['active_power_kw']['add'] ?? '') }}"
                                                            style="width: 50%;" placeholder="ADD">
                                                    </div>
                                                    <div class="fw-bold text-secondary mb-1 shadow-lg my-3">Active
                                                        power, kVA</div>
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" name="active_power_kva_md"
                                                        class="form-control ms-2"
                                                        value="{{ old('active_power_kva', $siteData['active_power_kva']['md'] ?? '') }}"
                                                        style="width: 50%;" placeholder="MD">
                                                    <input type="text" name="active_power_kva_add"
                                                        class="form-control ms-2"
                                                        value="{{ old('active_power_kva', $siteData['active_power_kva']['add'] ?? '') }}"
                                                        style="width: 50%;" placeholder="ADD">
                                                </div>
                                            </div>

                                            <!-- Power Factor -->
                                            <div class="card mb-1 shadow-lg border-0">
                                                <div class="card-body text-center">
                                                    <div class="fw-bold text-secondary">Power factor</div>
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" name="power_factor_md" class="form-control ms-2"
                                                        value="{{ old('power_factor', $siteData['power_factor']['md'] ?? '') }}"
                                                        style="width: 50%;" placeholder="MD">
                                                    <input type="text" name="power_factor_add" class="form-control ms-2"
                                                        value="{{ old('power_factor', $siteData['power_factor']['add'] ?? '') }}"
                                                        style="width: 50%;" placeholder="ADD">
                                                </div>
                                            </div>

                                            <!-- Total kWh -->
                                            <div class="card mb-2 shadow-lg border-0;">
                                                <div class="card-body text-center">
                                                    <div class="text-secondary fw-bold">Total kWh</div>
                                                    <div class="d-flex">
                                                        <input type="text" name="total_kwh_md" class="form-control ms-2"
                                                            value="{{ old('total_kwh', $siteData['total_kwh']['md'] ?? '') }}"
                                                            style="width: 50%; margin-top:30px" placeholder="MD">
                                                        <input type="text" name="total_kwh_add"
                                                            class="form-control ms-2"
                                                            value="{{ old('total_kwh', $siteData['total_kwh']['add'] ?? '') }}"
                                                            style="width: 50%; margin-top:30px" placeholder="ADD">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card" style="width: 100%; height: 975px;">
                                        <div class="card-header text-center bg-secondary text-white">
                                            ENGINE PARAMETERS
                                        </div>
                                        <div class="card-body"
                                            style="font-size: 10px; padding: 20px; overflow: hidden;">
                                            <div class="row">
                                                <!-- Parameter Section Template -->
                                                <div class="col text-center fw-bold  p-2 shadow-lg border-0">
                                                    COOLANT TEMPERATURE
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-thermometer-half me-2 text-primary"
                                                            style="font-size: 2rem;"></i>
                                                        <input type="text" name="coolant_temperature_md"
                                                            class="form-control me-2" style="width: 80px;"
                                                            value="{{ old('parameters', $siteData['parameters']['coolant_temperature']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" name="coolant_temperature_add"
                                                            value="{{ old('parameters', $siteData['parameters']['coolant_temperature']['add'] ?? '') }}"
                                                            class="form-control" style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold  p-2 shadow-lg border-0">
                                                    OIL TEMPERATURE
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-oil-can me-2 text-warning"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="oil_temperature_md"
                                                            class="form-control me-2" style="width: 80px;"
                                                            value="{{ old('parameters', $siteData['parameters']['oil_temperature']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" name="oil_temperature_add"
                                                            value="{{ old('parameters', $siteData['parameters']['oil_temperature']['add'] ?? '') }}"
                                                            class="form-control" style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold  p-2 shadow-lg border-0">
                                                    OIL PRESSURE
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-tachometer-alt me-2 text-success"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="oil_pressure_md"
                                                            class="form-control me-2"
                                                            value="{{ old('parameters', $siteData['parameters']['oil_pressure']['md'] ?? '') }}"
                                                            style="width: 80px;" placeholder="MD">
                                                        <input type="text" name="oil_pressure_add" class="form-control"
                                                            value="{{ old('parameters', $siteData['parameters']['oil_pressure']['add'] ?? '') }}"
                                                            style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold  p-2 shadow-lg border-0">
                                                    RPM
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-tachometer-alt me-2 text-danger"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="rpm_md" class="form-control me-2"
                                                            value="{{ old('parameters', $siteData['parameters']['rpm']['md'] ?? '') }}"
                                                            style="width: 80px;" placeholder="MD">
                                                        <input type="text" name="rpm_add" class="form-control"
                                                            value="{{ old('parameters', $siteData['parameters']['rpm']['add'] ?? '') }}"
                                                            style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold  p-2 shadow-lg border-0">
                                                    NUMBER OF STARTS
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-key me-2 text-secondary"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="number_of_starts_md"
                                                            value="{{ old('parameters', $siteData['parameters']['number_of_starts']['md'] ?? '') }}"
                                                            class="form-control me-2" style="width: 80px;"
                                                            placeholder="MD">
                                                        <input type="text" name="number_of_starts_add"
                                                            value="{{ old('parameters', $siteData['parameters']['number_of_starts']['add'] ?? '') }}"
                                                            class="form-control" style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold  p-2 shadow-lg border-0">
                                                    BATTERY VOLTAGE
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-battery-half me-2 text-info"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="battery_voltage_md"
                                                            value="{{ old('parameters', $siteData['parameters']['battery_voltage']['md'] ?? '') }}"
                                                            class="form-control me-2" style="width: 80px;"
                                                            placeholder="MD">
                                                        <input type="text" name="battery_voltage_add"
                                                            value="{{ old('parameters', $siteData['parameters']['battery_voltage']['add'] ?? '') }}"
                                                            class="form-control" style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="col text-center fw-bold  p-2 shadow-lg border-0">
                                                    FUEL
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-gas-pump me-2 text-success"
                                                            style="font-size: 1rem;"></i>
                                                        <input type="text" name="fuel_md" class="form-control me-2"
                                                            value="{{ old('parameters', $siteData['parameters']['fuel']['md'] ?? '') }}"
                                                            style="width: 80px;" placeholder="MD">
                                                        <input type="text" name="fuel_add" class="form-control"
                                                            value="{{ old('parameters', $siteData['parameters']['fuel']['add'] ?? '') }}"
                                                            style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="col text-center fw-bold  p-2 shadow-lg border-0">
                                                    Total Fuel Capacity/Liter
                                                    <div class="d-flex justify-content-center align-items-center py-1">
                                                        <i class="fas fa-gas-pump me-2 text-success"
                                                            style="font-size: 1rem;"></i>

                                                        <input type="text" name="fuel_add" class="form-control"
                                                            value="{{ old('parameters', $siteData['parameters']['fuel']['add'] ?? '') }}"
                                                            style="width: 80px;" placeholder="ADD">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- Running Hours Section -->
                                            <div class="row align-items-center p-2 shadow-lg border-0"
                                                style="font-size: 10px; padding: 8px; margin-bottom: 0; margin-top: -20px;">
                                                <div
                                                    class="col-xs-6 d-flex align-items-center justify-content-center fw-bold  ">
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
                                                        value="{{ old('running_hours', $siteData['running_hours']['md'] ?? '') }}"
                                                        style="width: 80px;" placeholder="MD">
                                                    <input type="text" name="running_hours_add" class="form-control"
                                                        value="{{ old('running_hours', $siteData['running_hours']['add'] ?? '') }}"
                                                        style="width: 80px;" placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <div class="fw-bold text-secondary text-center">
                                                        For admin Running status
                                                        <input type="text" name="admin_run_hours"
                                                            class="form-control ms-2"
                                                            value="{{ old('running_hours', $siteData['running_hours']['admin_run_hours'] ?? '') }}"
                                                            style="width: 90%;margin-bottom:10px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card fw-bold" style="width: 110%; margin: auto; height: 915px;">
                                        <div class="card-header text-center bg-secondary text-white fs-6">
                                            ELECTRIC PARAMETERS
                                        </div>
                                        <div class="card-body"
                                            style="font-size: 10px; padding: 8px; p-2 shadow-lg border-0">
                                            <!-- Table Header -->
                                            <div class="row border-bottom pb-3 mb-3">
                                                <div class="col text-center">Heading</div>
                                                <div class="col text-center">A</div>
                                                <div class="col text-center">B</div>
                                                <div class="col text-center">C</div>
                                            </div>
                                            <hr />
                                            <!-- Voltage L-L -->
                                            <div class="row mb-4 p-2 shadow-lg border-0">
                                                <div class="col-12 text-center">Voltage L-L, V</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_l']['a']['md']) }}"
                                                        name="voltage_l_l_a_md" placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_l']['a']['add'] ?? '') }}"
                                                        name="voltage_l_l_a_add" placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_l_b_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_l']['b']['add'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_l_b_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_l']['b']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_l_c_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_l']['c']['add'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_l_c_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_l']['c']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- Voltage L-N -->
                                            <div class="row mb-4 p-2 shadow-lg border-0">
                                                <div class="col-12 text-center">Voltage L-N, V</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_n_a_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_n']['a']['md'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_n_a_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_n']['a']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_n_b_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_n']['b']['md'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_n_b_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_n']['b']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="voltage_l_n_c_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_n']['c']['md'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="voltage_l_n_c_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['voltage_l_n']['c']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- Current -->
                                            <div class="row mb-4 p-2 shadow-lg border-0">
                                                <div class="col-12 text-center">Current, A</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="current_a_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['current']['a']['md'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="current_a_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['current']['a']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="current_b_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['current']['b']['md'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="current_b_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['current']['b']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                                <div class="d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="current_c_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['current']['c']['md'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="current_c_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['current']['c']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- pf-data -->
                                            <div class="row mb-4 p-2 shadow-lg border-0">
                                                <div class="col-12 text-center">pf-data</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="pf_data_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['pf_data']['md'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="pf_data_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['pf_data']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                            </div>
                                            <hr />
                                            <!-- Frequency -->
                                            <div class="row mb-4 p-2 shadow-lg border-0">
                                                <div class="col-12 text-center">Frequency</div>
                                                <div class="col text-center d-flex">
                                                    <input type="text" class="form-control me-2" style="width: 110px;"
                                                        name="frequency_md"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['frequency']['md'] ?? '') }}"
                                                        placeholder="MD">
                                                    <input type="text" class="form-control" style="width: 110px;"
                                                        name="frequency_add"
                                                        value="{{ old('electric_parameters', $siteData['electric_parameters']['frequency']['add'] ?? '') }}"
                                                        placeholder="ADD">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card shadow-sm fw-bold"
                                        style="height: 915px; width: 100%; font-size: 10px;">
                                        <div class="card-header text-center bg-secondary text-white fs-6">
                                            ALARM STATUS
                                        </div>
                                        <div class="card-body p-3 d-flex flex-column justify-content-center">
                                            <div class="row h-100">
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
                                                            name="coolant_temperature_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['coolant_temperature_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="coolant_temperature_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['coolant_temperature_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Fuel Level -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>Fuel Level</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fuel_level_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['fuel_level_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fuel_level_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['fuel_level_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Emergency Stop -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Emergency stop</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="emergency_stop_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['emergency_stop_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="emergency_stop_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['emergency_stop_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- High Coolant Temperature -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>High coolant temperature</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="high_coolant_temperature_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['high_coolant_temperature_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="high_coolant_temperature_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['high_coolant_temperature_status']['add'] ?? '') }}"
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
                                                            name="under_speed_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['under_speed_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="under_speed_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['under_speed_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Fail to Start -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>Fail to start</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fail_to_start_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['fail_to_start_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fail_to_start_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['fail_to_start_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Loss of Speed Sensing -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Loss of speed sensing</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="loss_of_speed_sensing_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['loss_of_speed_sensing_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="loss_of_speed_sensing_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['loss_of_speed_sensing_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
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
                                                            name="oil_pressure_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['oil_pressure_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="oil_pressure_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['oil_pressured_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Battery Level -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>Battery Level</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="battery_level_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['battery_level_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="battery_level_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['battery_level_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Low Oil Pressure -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Low oil pressure</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="low_oil_pressure_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['low_oil_pressure_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="low_oil_pressure_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['low_oil_pressure_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- High Oil Temperature -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>High oil temperature</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="high_oil_temperature_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['high_oil_temperature_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="high_oil_temperature_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['high_oil_temperature_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Over Speed -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Over speed</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="over_speed_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['over_speed_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="over_speed_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['over_speed_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Fail to Come to Rest -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: red; animation: blink 1.5s infinite;"></span>
                                                        <span>Fail to come to rest</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fail_to_come_to_rest_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['fail_to_come_to_rest_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="fail_to_come_to_rest_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['fail_to_come_to_rest_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
                                                    </div>

                                                    <!-- Generator Low Voltage -->
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <span class="me-2 rounded-circle"
                                                            style="width: 10px; height: 10px; background-color: green; animation: blink 1.5s infinite;"></span>
                                                        <span>Generator low voltage</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="generator_low_voltage_md_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['generator_low_voltage_status']['md'] ?? '') }}"
                                                            placeholder="MD">
                                                        <input type="text" class="form-control ms-2" style="width: 50%;"
                                                            name="generator_low_voltage_add_status"
                                                            value="{{ old('alarm_status', $siteData['alarm_status']['generator_low_voltage_status']['add'] ?? '') }}"
                                                            placeholder="ADD">
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