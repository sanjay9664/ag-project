<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/react-circular-progressbar/2.0.3/styles.css" rel="stylesheet">
</head>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
}

.asset {
    font-size: 1.2rem;
    padding: 15px;
    color: #fff;
    background: #002E6E;
}

.navbar {
    background-color: #002E6E;
    padding: 10px 20px;
}

.navbar .logo-img {
    height: 40px;
    width: auto;
}

.navbar .navbar-brand {
    color: white;
    font-size: 1.25rem;
    font-weight: bold;
    margin-left: 10px;
}

.navbar .form-select {
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .navbar .logo-img {
        height: 30px;
    }

    .navbar .navbar-brand {
        font-size: 1rem;
    }

    .navbar .form-select {
        width: 100%;
        margin-top: 10px;
    }
}

.navbar .form-select {
    padding: 5px 10px;
    font-size: 0.9rem;
}

.card-header {
    font-size: 1.2rem;
    font-weight: bold;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    box-shadow: rgba(0, 0, 0, 0.2) 0px 2px 8px;
    overflow-x: auto;
}

.table th,
.table td {
    text-align: center;
    vertical-align: middle;
    padding: 10px;
    font-size: 15px;
    word-wrap: break-word;
}

.table th {
    background-color: #002E6E;
    color: white !important;
    font-weight: bold;

}

.table td {
    background-color: #f9f9f9;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

/* Responsive table styles */
@media (max-width: 768px) {
    .table {
        display: block;
        padding: 11px 15px;

    }

    .table tr {
        display: block;
        margin-bottom: 15px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .table td {
        display: flex;
        flex-direction: column;
        padding: 8px;
        border-bottom: 1px solid #eee;
        text-align: center;
        font-size: 20px;
    }

    .table td:last-child {
        border-bottom: none;
    }

    .table th {

        padding: 17px 88px;
    }

    .table td span.original-content {
        font-size: 0.9rem;
        word-break: break-word;
    }

    .table td[data-label="SN"]::before {
        content: "Serial No.";
    }

    .table td[data-label="Asset Name"]::before {
        content: "Asset";
    }
}

/* Engine parameters specific styles */
.parameter-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 5px 20px;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    height: 100%;
}

.parameter-icon {
    font-size: 2rem;
    margin-bottom: 8px;
}

.parameter-label {
    font-weight: bold;
    font-size: 0.9rem;
    margin-bottom: 5px;

}

.parameter-value {
    font-size: 1rem;
    font-weight: 500;

}

/* Three-phase value styles */
.phase-values {
    display: flex;
    justify-content: space-around;
    width: 100%;
    margin-top: 5px;
}

.phase-value {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 5px;
}

.phase-label {
    font-size: 0.7rem;
    color: #666;
    margin-bottom: 2px;
}

.phase-number {
    font-weight: bold;
    font-size: 0.9rem;

}

/* Header styles */
.header-container {
    background: #002E6E;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.header-title {
    margin: 0;
    padding: 10px 0;
    flex-grow: 1;
    text-align: center;
}

.logo-img {
    width: 120px;
    height: 40px;
    background: white;
    border-radius: 50px;
}

/* Run status section */
.run-status-container {
    display: flex;
    justify-content: space-around;
    align-items: center;
    flex-wrap: wrap;
    padding: 8px;
}

.status-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 5px;
    min-width: 120px;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .parameter-box {
        margin-bottom: 10px;
    }
}

@media (max-width: 768px) {

    .parameter-box {
        padding: 17px 81px;
        height: 70%;
        width: 100%;
        font-size: 0.9rem;
    }

    .asset {
        font-size: 1.2rem;
        padding: 15px 81px;

    }

    .parameter-label {

        font-size: 25px;
    }

    .parameter-value {

        font-size: 26px;
    }

    .phase-number {

        font-size: 17px;
    }
}
</style>

<body>
    <div class="header-container">
        <a class="navbar-brand" href="#">
            <img src="https://genset.innovatorautomation.co.in/assets/logo.svg" alt="sochiot_Logo" class="logo-img" />
        </a>
        <h5 class="header-title">DG SET MONITORING SYSTEM</h5>
    </div>

    <div class="container-fluid">
        <div class="row mt-3" id="event-data">
            <!-- First Table for Asset Information -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center text-white fw-bold fs-4 p-1" style="background: #002E6E;">
                        ASSET INFORMATION
                    </div>
                    <table class="table table-bordered table-striped table-hover">
                        <tbody>
                            <tr>
                                <th class="asset">
                                    Asset Name: {{ $sitejsonData->asset_name }}
                                </th>
                                <td data-label="site_name">
                                    <strong>Site_Name:</strong> {{ $sitejsonData->site_name }}
                                </td>
                                <td data-label="Group">
                                    <strong>Location:</strong> {{ $sitejsonData->group }}
                                </td>
                                <td data-label="Generator">
                                    <strong>Bank_Name:</strong> {{ $sitejsonData->generator }}
                                </td>
                                <td data-label="S/N">
                                    <strong>S/N:</strong> {{ $sitejsonData->serial_number }}
                                </td>
                                <td data-label="Model">
                                    <strong>Model:</strong> {{ $sitejsonData->model }}
                                </td>
                                <td data-label="Brand">
                                    <strong>Brand:</strong> {{ $sitejsonData->brand }}
                                </td>
                                <td data-label="Capacity">
                                    <strong>Capacity:</strong> {{ $sitejsonData->capacity }}
                                </td>
                            </tr>

                            <tr>
                                <?php
                                    $increased_running_hours = DB::table('running_hours')->where('site_id', $siteData->id)->first();
                                    $increaseRunningHours = (float) ($increased_running_hours->increase_running_hours ?? 0);
                                    $siteId = $sitejsonData->id ?? null;
                                    $addValue = 0;
                                    $key = $sitejsonData->running_hours->add ?? null;

                                    foreach ($eventData as $event) {
                                        $eventArray = $event->getArrayCopy();
                                        if (isset($eventArray['module_id']) && $eventArray['module_id'] == ($sitejsonData->running_hours->md ?? null)) {
                                            if ($key && array_key_exists($key, $eventArray)) {
                                                $addValue = (float) $eventArray[$key];
                                            }
                                            break;
                                        }
                                    }

                                    $increaseMinutes = $sitejsonData->running_hours->increase_minutes ?? null;
                                    $inc_addValue = $addValue;

                                    if (is_numeric($increaseMinutes) && (float)$increaseMinutes > 0) {
                                        $inc_addValue /= (float)$increaseMinutes;
                                    }

                                    $tempvariable = number_format($inc_addValue, 2);
                                    $inc_addValueFormatted = $tempvariable + $increaseRunningHours;
                                ?>
                                <?php
                                    $hours = floor($inc_addValueFormatted);
                                    $minutes = round(($inc_addValueFormatted - $hours) * 60);
                                ?>
                                <?php
                                    $keya = $sitejsonData->electric_parameters->voltage_l_l->a->add;
                                    $addValuerunstatus = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->a->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValuerunstatus = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }
                                ?>
                                <td colspan="7">
                                    <div class="run-status-container">
                                        <div class="status-box">
                                            <i class="fas fa-cogs" style="color: teal; font-size: 24px;"></i>
                                            <p class="fw-bold">Run Status</p>
                                            @if($addValuerunstatus > 0)
                                            <span class="badge bg-success px-2 py-1">Running</span>
                                            @else
                                            <span class="badge bg-danger px-2 py-1">Stop</span>
                                            @endif
                                        </div>
                                        <div class="status-box">
                                            <i class="fas fa-running text-primary" style="font-size: 24px;"></i>
                                            <p><strong>Running Hours:</strong></p>
                                            <h5 class="text-dark">{{ $hours }} hrs {{ $minutes }} mins</h5>
                                        </div>
                                        <div class="status-box">
                                            <i class="fas fa-clock text-info" style="font-size: 24px;"></i>
                                            <p><strong>Updated At:</strong></p>
                                            <h5 class="text-muted">{{ $latestCreatedAt }}</h5>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Second Table for Engine Parameters -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-header text-center text-white fw-bold fs-5 p-3" style="background:#002E6E;">
                        ENGINE PARAMETERS
                    </div>
                    <div class=" card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover m-0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="parameter-box">
                                                <i class="fas fa-thermometer-half parameter-icon text-primary"></i>
                                                <span class="parameter-label">Coolant Temp</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->coolant_temperature->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->coolant_temperature->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format($eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }} °C</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="parameter-box">
                                                <i class="fas fa-oil-can parameter-icon text-warning"></i>
                                                <span class="parameter-label">Oil Temp</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->oil_temperature->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->oil_temperature->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format($eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }} °C</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="parameter-box">
                                                <i class="fas fa-gas-pump parameter-icon text-danger"></i>
                                                <span class="parameter-label">Oil Pressure</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->oil_pressure->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->oil_pressure->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format($eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }} psi</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="parameter-box">
                                                <i class="fas fa-tachometer-alt parameter-icon text-danger"></i>
                                                <span class="parameter-label">RPM</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->rpm->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->rpm->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format($eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="parameter-box">
                                                <i class="fas fa-tint parameter-icon text-info"></i>
                                                <span class="parameter-label">DEF</span>
                                                <span class="parameter-value">-</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="parameter-box">
                                                <i class="fas fa-battery-half parameter-icon text-info"></i>
                                                <span class="parameter-label">Battery Voltage</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->battery_voltage->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->battery_voltage->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format((float)$eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }} V</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <div class="parameter-box">
                                                <i class="fas fa-bolt parameter-icon text-warning"></i>
                                                <span class="parameter-label">Voltage (L-L)</span>
                                                <div class="phase-values">
                                                    <?php
                                                        $keys = [
                                                            'a' => $sitejsonData->electric_parameters->voltage_l_l->a->add,
                                                            'b' => $sitejsonData->electric_parameters->voltage_l_l->b->add,
                                                            'c' => $sitejsonData->electric_parameters->voltage_l_l->c->add
                                                        ];
                                                        $values = ['R' => '_', 'Y' => '_', 'B' => '_'];
                                                        
                                                        foreach ($eventData as $event) {
                                                            $eventArray = $event->getArrayCopy();
                                                            if ($eventArray['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->a->md) {
                                                                foreach (['a' => 'R', 'b' => 'Y', 'c' => 'B'] as $phase => $label) {
                                                                    if (array_key_exists($keys[$phase], $eventArray)) {
                                                                        $values[$label] = number_format((float)$eventArray[$keys[$phase]], 2);
                                                                    }
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    ?>
                                                    <div class="phase-value">
                                                        <span class="phase-label">R-Y</span>
                                                        <span class="phase-number">{{ $values['R'] }} V</span>
                                                    </div>
                                                    <div class="phase-value">
                                                        <span class="phase-label">Y-B</span>
                                                        <span class="phase-number">{{ $values['Y'] }} V</span>
                                                    </div>
                                                    <div class="phase-value">
                                                        <span class="phase-label">B-R</span>
                                                        <span class="phase-number">{{ $values['B'] }} V</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td colspan="3">
                                            <div class="parameter-box">
                                                <i class="fas fa-bolt parameter-icon text-success"></i>
                                                <span class="parameter-label">Current</span>
                                                <div class="phase-values">
                                                    <?php
                                                        $keys = [
                                                            'a' => $sitejsonData->electric_parameters->current->a->add,
                                                            'b' => $sitejsonData->electric_parameters->current->b->add,
                                                            'c' => $sitejsonData->electric_parameters->current->c->add
                                                        ];
                                                        $values = ['R' => '_', 'Y' => '_', 'B' => '_'];
                                                        
                                                        foreach ($eventData as $event) {
                                                            $eventArray = $event->getArrayCopy();
                                                            if ($eventArray['module_id'] == $sitejsonData->electric_parameters->current->a->md) {
                                                                foreach (['a' => 'R', 'b' => 'Y', 'c' => 'B'] as $phase => $label) {
                                                                    if (array_key_exists($keys[$phase], $eventArray)) {
                                                                        $values[$label] = number_format((float)$eventArray[$keys[$phase]], 2);
                                                                    }
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    ?>
                                                    <div class="phase-value">
                                                        <span class="phase-label">Phase R</span>
                                                        <span class="phase-number">{{ $values['R'] }} A</span>
                                                    </div>
                                                    <div class="phase-value">
                                                        <span class="phase-label">Phase Y</span>
                                                        <span class="phase-number">{{ $values['Y'] }} A</span>
                                                    </div>
                                                    <div class="phase-value">
                                                        <span class="phase-label">Phase B</span>
                                                        <span class="phase-number">{{ $values['B'] }} A</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function fetchSiteData() {
        const slug = "{{ $siteData->slug }}";
        const url = `/admin/site-data/${slug}`;

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.eventData) {
                    let eventList = '';

                    if (response.eventData) {
                        console.log(response.eventData);
                        const event = response.eventData;
                        eventList = `
                            <div class="container-fluid">
                                <div class="row mt-3" id="event-data">
                                    <!-- First Table for Asset Information -->
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header text-center text-white fw-bold fs-4 p-1" style="background: #002E6E;">
                                                ASSET INFORMATION
                                            </div>
                                            <table class="table table-bordered table-striped table-hover">
                                                <tbody>
                                                    <tr>
                                                        <th class="asset">
                                                            Asset Name: {{ $sitejsonData->asset_name }}
                                                        </th>
                                                        <td data-label="site_name">
                                    <strong>Site_Name:</strong> {{ $sitejsonData->site_name }}
                                </td>
                                                        <td data-label="Group">
                                                            <strong>Location:</strong> {{ $sitejsonData->group }}
                                                        </td>
                                                        <td data-label="Generator">
                                                            <strong>Bank_Name :</strong> {{ $sitejsonData->generator }}
                                                        </td>
                                                        <td data-label="S/N">
                                                            <strong>S/N:</strong> {{ $sitejsonData->serial_number }}
                                                        </td>
                                                        <td data-label="Model">
                                                            <strong>Model:</strong> {{ $sitejsonData->model }}
                                                        </td>
                                                        <td data-label="Brand">
                                                            <strong>Brand:</strong> {{ $sitejsonData->brand }}
                                                        </td>
                                                        <td data-label="Capacity">
                                                            <strong>Capacity:</strong> {{ $sitejsonData->capacity }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="7">
                                                            <div class="run-status-container">
                                                                <div class="status-box">
                                                                    <i class="fas fa-cogs" style="color: teal; font-size: 24px;"></i>
                                                                    <p class="fw-bold">Run Status</p>
                                                                    @if($addValuerunstatus > 0)
                                                                    <span class="badge bg-success px-2 py-1">Running</span>
                                                                    @else
                                                                    <span class="badge bg-danger px-2 py-1">Stop</span>
                                                                    @endif
                                                                </div>
                                                                <div class="status-box">
                                                                    <i class="fas fa-running text-primary" style="font-size: 24px;"></i>
                                                                    <p><strong>Running Hours:</strong></p>
                                                                    <h5 class="text-dark">{{ $hours }} hrs {{ $minutes }} mins</h5>
                                                                </div>
                                                                <div class="status-box">
                                                                    <i class="fas fa-clock text-info" style="font-size: 24px;"></i>
                                                                    <p><strong>Updated At:</strong></p>
                                                                    <h5 class="text-muted">{{ $latestCreatedAt }}</h5>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Second Table for Engine Parameters -->
                                    <div class="col-md-12 mt-4">
                                        <div class="card">
                                            <div class="card-header text-center text-white fw-bold fs-5 p-3" style="background:#002E6E;">
                                                ENGINE PARAMETERS
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped table-hover m-0">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="parameter-box">
                                                <i class="fas fa-thermometer-half parameter-icon text-primary"></i>
                                                <span class="parameter-label">Coolant Temp</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->coolant_temperature->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->coolant_temperature->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format($eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }} °C</span>
                                            </div>
                                                                </td>
                                                                <td>
                                                                    <div class="parameter-box">
                                                <i class="fas fa-oil-can parameter-icon text-warning"></i>
                                                <span class="parameter-label">Oil Temp</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->oil_temperature->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->oil_temperature->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format($eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }} °C</span>
                                            </div>
                                                                </td>
                                                                <td>
                                                                   <div class="parameter-box">
                                                <i class="fas fa-gas-pump parameter-icon text-danger"></i>
                                                <span class="parameter-label">Oil Pressure</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->oil_pressure->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->oil_pressure->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format($eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }} psi</span>
                                            </div>
                                                                </td>
                                                                <td>
                                                                   <div class="parameter-box">
                                                <i class="fas fa-tachometer-alt parameter-icon text-danger"></i>
                                                <span class="parameter-label">RPM</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->rpm->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->rpm->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format($eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }}</span>
                                            </div>
                                                                </td>
                                                                <td>
                                                                    <div class="parameter-box">
                                                                        <i class="fas fa-tint parameter-icon text-info"></i>
                                                                        <span class="parameter-label">DEF</span>
                                                                        <span class="parameter-value">-</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                   <div class="parameter-box">
                                                <i class="fas fa-battery-half parameter-icon text-info"></i>
                                                <span class="parameter-label">Battery Voltage</span>
                                                <?php
                                                    $key = $sitejsonData->parameters->battery_voltage->add;
                                                    $addValue = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->parameters->battery_voltage->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = number_format((float)$eventArray[$key], 2);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <span class="parameter-value">{{ $addValue }} V</span>
                                            </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                             <td colspan="3">
                                            <div class="parameter-box">
                                                <i class="fas fa-bolt parameter-icon text-warning"></i>
                                                <span class="parameter-label">Voltage (L-L)</span>
                                                <div class="phase-values">
                                                    <?php
                                                        $keys = [
                                                            'a' => $sitejsonData->electric_parameters->voltage_l_l->a->add,
                                                            'b' => $sitejsonData->electric_parameters->voltage_l_l->b->add,
                                                            'c' => $sitejsonData->electric_parameters->voltage_l_l->c->add
                                                        ];
                                                        $values = ['R' => '_', 'Y' => '_', 'B' => '_'];
                                                        
                                                        foreach ($eventData as $event) {
                                                            $eventArray = $event->getArrayCopy();
                                                            if ($eventArray['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->a->md) {
                                                                foreach (['a' => 'R', 'b' => 'Y', 'c' => 'B'] as $phase => $label) {
                                                                    if (array_key_exists($keys[$phase], $eventArray)) {
                                                                        $values[$label] = number_format((float)$eventArray[$keys[$phase]], 2);
                                                                    }
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    ?>
                                                    <div class="phase-value">
                                                        <span class="phase-label">R-Y</span>
                                                        <span class="phase-number">{{ $values['R'] }} V</span>
                                                    </div>
                                                    <div class="phase-value">
                                                        <span class="phase-label">Y-B</span>
                                                        <span class="phase-number">{{ $values['Y'] }} V</span>
                                                    </div>
                                                    <div class="phase-value">
                                                        <span class="phase-label">B-R</span>
                                                        <span class="phase-number">{{ $values['B'] }} V</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                                                <td colspan="3">
                                                                     <div class="parameter-box">
                                                <i class="fas fa-bolt parameter-icon text-success"></i>
                                                <span class="parameter-label">Current</span>
                                                <div class="phase-values">
                                                    <?php
                                                        $keys = [
                                                            'a' => $sitejsonData->electric_parameters->current->a->add,
                                                            'b' => $sitejsonData->electric_parameters->current->b->add,
                                                            'c' => $sitejsonData->electric_parameters->current->c->add
                                                        ];
                                                        $values = ['R' => '_', 'Y' => '_', 'B' => '_'];
                                                        
                                                        foreach ($eventData as $event) {
                                                            $eventArray = $event->getArrayCopy();
                                                            if ($eventArray['module_id'] == $sitejsonData->electric_parameters->current->a->md) {
                                                                foreach (['a' => 'R', 'b' => 'Y', 'c' => 'B'] as $phase => $label) {
                                                                    if (array_key_exists($keys[$phase], $eventArray)) {
                                                                        $values[$label] = number_format((float)$eventArray[$keys[$phase]], 2);
                                                                    }
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    ?>
                                                    <div class="phase-value">
                                                        <span class="phase-label">Phase R</span>
                                                        <span class="phase-number">{{ $values['R'] }} A</span>
                                                    </div>
                                                    <div class="phase-value">
                                                        <span class="phase-label">Phase Y</span>
                                                        <span class="phase-number">{{ $values['Y'] }} A</span>
                                                    </div>
                                                    <div class="phase-value">
                                                        <span class="phase-label">Phase B</span>
                                                        <span class="phase-number">{{ $values['B'] }} A</span>
                                                    </div>
                                                </div>
                                            </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }

                    $('#event-data').html(eventList);
                }
            },
            error: function(xhr) {
                console.error('Error fetching site data:', xhr.responseText);
            }
        });
    }

    // Fetch data immediately when the page loads
    fetchSiteData();
    setInterval(fetchSiteData, 10000);
    </script>
</body>

</html>