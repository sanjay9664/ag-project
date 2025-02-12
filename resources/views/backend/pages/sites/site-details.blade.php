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
.navbar {
    background-color: #007bff;
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
    white-space: nowrap;
}

.table th {
    background-color: #007bff;
    color: white;
    font-weight: bold;
}

.table td {
    background-color: #f9f9f9;
}

@media (max-width: 768px) {
    .table {
        display: block;
    }

    .table thead {
        display: none;
    }

    .table tr {
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        align-items: center;
        text-align: center;
    }

    .table td {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 10px;
        font-size: 0.9rem;
        white-space: normal;
        border-bottom: 1px solid #ddd;
    }

    .table td:last-child {
        border-bottom: none;
    }

    .table td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #333;
        display: block;
        margin-bottom: 5px;
    }

    .table img {
        max-width: 80px;
        height: auto;
        margin-bottom: 10px;
        border-radius: 5px;
    }
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>

<body class="font-family:Arial, sans-serif ">
    <div class=" text-center text-white py-1 m-1 rounded d-flex justify-content-between align-items-center"
        style="background:#002E6E; flex-wrap: wrap;">
        <a class="navbar-brand" href="#">
            <img src="https://genset.innovatorautomation.co.in/assets/logo.svg" alt="sochiot_Logo" style="width: 120px; height: 40px; background: white;
  border-radius: 50px ;  margin-left:40px; " />
        </a>
        <h5 class="my-3" style="padding-right: 117px;">DG SET MONITORING SYSTEM</h5>

    </div>







    <div class="row mt-3" id="event-data">
        <!-- First Table for Asset Information -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-center text-white fw-bold fs-4 p-3"
                    style="background: #002E6E;">
                    ASSET INFORMATION
                </div>
                <table class="table table-bordered table-striped table-hover text-white"
                    style="box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;">
                    <tbody>
                        <tr>
                            <th style="font-size: 1.2rem; padding: 15px; color:#fff ">
                                Asset Name: {{ $sitejsonData->asset_name }}
                            </th>
                            <td>
                                <p><strong>Group:</strong></p>
                                {{ $sitejsonData->group }}
                            </td>
                            <td>
                                <p><strong>Generator:</strong></p>
                                {{ $sitejsonData->group }}
                            </td>
                            <td>
                                <p><strong>S/N:</strong></p>
                                {{ $sitejsonData->serial_number }}
                            </td>
                            <td>
                                <p><strong>Model:</strong></p>
                                {{ $sitejsonData->model }}
                            </td>
                            <td>
                                <p><strong>Brand:</strong></p>
                                {{ $sitejsonData->brand }}
                            </td>
                            <td>
                                <p><strong>Capacity:</strong></p>
                                {{ $sitejsonData->capacity }}
                            </td>
                        </tr>
                        <tr>
                            <?php
                                $increased_running_hours = DB::table('running_hours')->where('site_id', $siteData->id)->first();

                                $increaseRunningHours = 0;
                                $siteId = $sitejsonData->id ?? null;

                                if ($siteId && $increased_running_hours) { // Ensure the record exists
                                    foreach ((array) $increased_running_hours as $runningHour) {
                                        if ($runningHour->site_id == $siteId) {
                                            $increaseRunningHours = (float) $runningHour->increase_running_hours; // Convert to float
                                            break;
                                        }
                                    }
                                }

                                // Get addValue from event data
                                $key = $sitejsonData->running_hours->add ?? null;
                                $addValue = 0;

                                foreach ($eventData as $event) {
                                    $eventArray = $event->getArrayCopy();
                                    if (isset($eventArray['module_id']) && $eventArray['module_id'] == ($sitejsonData->running_hours->md ?? null)) {
                                        if ($key && array_key_exists($key, $eventArray)) {
                                            $addValue = (float) $eventArray[$key]; // Convert to float
                                        }
                                        break;
                                    }
                                }

                                // Handle null case safely
                                $inc_addValue = $addValue + ($increased_running_hours->increase_running_hours ?? 0);

                                $inc_addValueFormatted = number_format($inc_addValue, 2);
                            ?>

                            <td colspan="3">
                                <div class="d-flex justify-content-around align-items-center text-white">
                                    <div class="text-center" style="vertical-align: middle;">
                                        <i class="fas fa-cogs"
                                            style="color: teal; font-size: 24px; margin-bottom: 8px;"></i>
                                        <p class="fw-bold">Run Status</p>
                                        @if($inc_addValueFormatted > 0)
                                        <span class="badge bg-success px-2 py-1">Running</span>
                                        @else
                                        <span class="badge bg-danger px-2 py-1">Stop</span>
                                        @endif
                                    </div>
                                    <div class="text-center" style="vertical-align: middle;">
                                        <i class="fas fa-running text-primary"></i>
                                        <p><strong>Running Hours:</strong></p>
                                        @if(auth()->user()->hasRole('superadmin'))
                                        <h4 class="text-dark">{{ $inc_addValueFormatted }} Hrs</h4>
                                        @else
                                        <h4 class="text-dark">{{ $inc_addValueFormatted; }} Hrs
                                        </h4>
                                        @endif
                                    </div>
                                    <div class="text-center text-white" style="vertical-align: middle;">
                                        <i class="fas fa-clock text-info"></i>
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
        <div class="col-md-12">
            <div>
                <div class="card-header bg-primary text-center text-white fw-bold fs-5 p-3"
                    style="background:#002E6E; ">
                    ENGINE PARAMETERS
                </div>
                <table class="table table-bordered table-striped table-hover"
                    style="box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px; height:150px">
                    <tbody>
                        <tr>
                            <th class="table-column-title" style="color:#fff">ENGINE PARAMETERS</th>
                            <?php
                            $key = $sitejsonData->parameters->coolant_temperature->add;
                            $addValue = '_';
                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->parameters->coolant_temperature->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                                    }
                                    break;
                                }
                            }
                        ?>
                            <td class="table-column-data">
                                <i class="fas fa-thermometer-half text-primary" style="font-size: 2rem;"></i>

                                Coolant Temperature:{{ $addValue }} °C
                            </td>

                            <?php
                            $key = $sitejsonData->parameters->oil_temperature->add;
                            $addValue = '_';
                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->parameters->oil_temperature->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                                    }
                                    break;
                                }
                            }
                        ?>
                            <td class="table-column-data">
                                <i class="fas fa-oil-can text-warning" style="font-size: 2rem;"></i>
                                Oil Temperature:{{ $addValue }} °C
                            </td>

                            <?php
                            $key = $sitejsonData->parameters->oil_pressure->add;
                            $addValue = '_';
                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->parameters->oil_pressure->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                                    }
                                    break;
                                }
                            }
                        ?>
                            <td class="table-column-data">
                                <i class="fas fa-gas-pump text-danger" style="font-size: 2rem;"></i>
                                Oil Pressure:{{ $addValue }}
                            </td>

                            <?php
                            $key = $sitejsonData->parameters->rpm->add;
                            $addValue = '_';
                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->parameters->rpm->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                                    }
                                    break;
                                }
                            }
                        ?>
                            <td class="table-column-data"> <i class="fas fa-tachometer-alt text-danger"
                                    style="font-size: 2rem;"></i>
                                RPM:{{ $addValue }}
                            </td>

                            <td class="table-column-data">
                                <i class='fas fa-book-dead' style="font-size: 2rem;"></i>
                                DEF:-
                            </td>

                            <?php
                            $key = $sitejsonData->parameters->battery_voltage->add;
                            $addValue = '_';
                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->parameters->battery_voltage->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = number_format((float)$eventArray[$key], 2);  // Limiting decimal to 2
                                    }
                                    break;
                                }
                            }
                        ?>
                            <td class="table-column-data">
                                <i class="fas fa-battery-half text-info" style="font-size: 2rem;"></i>
                                Battery Voltage:<b> {{ $addValue }} V </b>
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
                         <div class="container ml-5">
                         
                                    <div class="row mt-3" id="event-data">
                                            <!-- First Table for Asset Information -->
                                                   <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-header bg-primary text-center text-white fw-bold fs-4 p-3"
                                                        style="background: #002E6E;">
                                                        ASSET INFORMATION
                                                    </div>
                                                    <table class="table table-bordered table-striped table-hover"
                                                        style="box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;">
                                                        <tbody>
                                                            <tr>
                                                                <th style="font-size: 1.2rem; padding: 15px; color:#fff">
                                                                    Asset Name: {{ $sitejsonData->asset_name }}
                                                                </th>
                                                                <td>
                                                                    <p><strong>Group:</strong></p>
                                                                    {{ $sitejsonData->group }}
                                                                </td>
                                                                <td>
                                                                    <p><strong>Generator:</strong></p>
                                                                    {{ $sitejsonData->group }}
                                                                </td>
                                                                <td>
                                                                    <p><strong>S/N:</strong></p>
                                                                    {{ $sitejsonData->serial_number }}
                                                                </td>
                                                                <td>
                                                                    <p><strong>Model:</strong></p>
                                                                    {{ $sitejsonData->model }}
                                                                </td>
                                                                <td>
                                                                    <p><strong>Brand:</strong></p>
                                                                    {{ $sitejsonData->brand }}
                                                                </td>
                                                                <td>
                                                                    <p><strong>Capacity:</strong></p>
                                                                    {{ $sitejsonData->capacity }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <?php
                                                                    $increased_running_hours = DB::table('running_hours')->where('site_id', $siteData->id)->first();

                                                                    $increaseRunningHours = 0;
                                                                    $siteId = $sitejsonData->id ?? null;

                                                                    if ($siteId && $increased_running_hours) { // Ensure the record exists
                                                                        foreach ((array) $increased_running_hours as $runningHour) {
                                                                            if ($runningHour->site_id == $siteId) {
                                                                                $increaseRunningHours = (float) $runningHour->increase_running_hours; // Convert to float
                                                                                break;
                                                                            }
                                                                        }
                                                                    }

                                                                    // Get addValue from event data
                                                                    $key = $sitejsonData->running_hours->add ?? null;
                                                                    $addValue = 0;

                                                                    foreach ($eventData as $event) {
                                                                        $eventArray = $event->getArrayCopy();
                                                                        if (isset($eventArray['module_id']) && $eventArray['module_id'] == ($sitejsonData->running_hours->md ?? null)) {
                                                                            if ($key && array_key_exists($key, $eventArray)) {
                                                                                $addValue = (float) $eventArray[$key]; // Convert to float
                                                                            }
                                                                            break;
                                                                        }
                                                                    }

                                                                    // Handle null case safely
                                                                    $inc_addValue = $addValue + ($increased_running_hours->increase_running_hours ?? 0);

                                                                    $inc_addValueFormatted = number_format($inc_addValue, 2);
                                                                ?>
                                                                <td colspan="3">
                                                                    <div class="d-flex justify-content-around align-items-center">
                                                                        <div class="text-center" style="vertical-align: middle;">
                                                                            <i class="fas fa-cogs"
                                                                                style="color: teal; font-size: 24px; margin-bottom: 8px;"></i>
                                                                            <p class="fw-bold">Run Status</p>
                                                                            @if($inc_addValueFormatted > 0)
                                                                            <span class="badge bg-success px-2 py-1">Running</span>
                                                                            @else
                                                                            <span class="badge bg-danger px-2 py-1">Stop</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="text-center" style="vertical-align: middle;">
                                                                            <i class="fas fa-running text-primary"></i>
                                                                            <p><strong>Running Hours:</strong></p>
                                                                            @if(auth()->user()->hasRole('superadmin'))
                                                                            <h4 class="text-dark">{{ $inc_addValueFormatted }} Hrs</h4>
                                                                            @else
                                                                            <h4 class="text-dark">{{ $inc_addValueFormatted; }} Hrs
                                                                            </h4>
                                                                            @endif
                                                                        </div>
                                                                        <div class="text-center" style="vertical-align: middle;">
                                                                            <i class="fas fa-clock text-info"></i>
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
                                            <div class="col-md-12">
                                                <div>
                                                    <div class="card-header bg-primary text-center text-white fw-bold fs-5 p-3" style="background:#002E6E; color:#fff">
                                                        ENGINE PARAMETERS
                                                    </div>
                                                    <table class="table table-bordered table-striped table-hover"
                                                        style="box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px; height:150px">
                                                        <tbody>
                                                            <tr>
                                                                <th class="table-column-title"  style="color:#fff">ENGINE PARAMETERS</th>
                                                                <?php
                                                                $key = $sitejsonData->parameters->coolant_temperature->add;
                                                                $addValue = '_';
                                                                foreach ($eventData as $event) {
                                                                    $eventArray = $event->getArrayCopy();
                                                                    if ($eventArray['module_id'] == $sitejsonData->parameters->coolant_temperature->md) {
                                                                        if (array_key_exists($key, $eventArray)) {
                                                                            $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                                                                        }
                                                                        break;
                                                                    }
                                                                }
                                                            ?>
                                                                <td class="table-column-data">
                                                                    <i class="fas fa-thermometer-half text-primary" style="font-size: 2rem;"></i>

                                                                    Coolant Temperature:{{ $addValue }} °C
                                                                </td>

                                                                <?php
                                                                $key = $sitejsonData->parameters->oil_temperature->add;
                                                                $addValue = '_';
                                                                foreach ($eventData as $event) {
                                                                    $eventArray = $event->getArrayCopy();
                                                                    if ($eventArray['module_id'] == $sitejsonData->parameters->oil_temperature->md) {
                                                                        if (array_key_exists($key, $eventArray)) {
                                                                            $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                                                                        }
                                                                        break;
                                                                    }
                                                                }
                                                            ?>
                                                                <td class="table-column-data">
                                                                    <i class="fas fa-oil-can text-warning" style="font-size: 2rem;"></i>
                                                                    Oil Temperature:{{ $addValue }} °C
                                                                </td>

                                                                <?php
                                                                $key = $sitejsonData->parameters->oil_pressure->add;
                                                                $addValue = '_';
                                                                foreach ($eventData as $event) {
                                                                    $eventArray = $event->getArrayCopy();
                                                                    if ($eventArray['module_id'] == $sitejsonData->parameters->oil_pressure->md) {
                                                                        if (array_key_exists($key, $eventArray)) {
                                                                            $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                                                                        }
                                                                        break;
                                                                    }
                                                                }
                                                            ?>
                                                                <td class="table-column-data">
                                                                    <i class="fas fa-gas-pump text-danger" style="font-size: 2rem;"></i>
                                                                    Oil Pressure:{{ $addValue }}
                                                                </td>

                                                                <?php
                                                                $key = $sitejsonData->parameters->rpm->add;
                                                                $addValue = '_';
                                                                foreach ($eventData as $event) {
                                                                    $eventArray = $event->getArrayCopy();
                                                                    if ($eventArray['module_id'] == $sitejsonData->parameters->rpm->md) {
                                                                        if (array_key_exists($key, $eventArray)) {
                                                                            $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                                                                        }
                                                                        break;
                                                                    }
                                                                }
                                                            ?>
                                                                <td class="table-column-data"> <i class="fas fa-tachometer-alt text-danger"
                                                                        style="font-size: 2rem;"></i>
                                                                    RPM:{{ $addValue }}
                                                                </td>

                                                                <td class="table-column-data">
                                                                    <i class='fas fa-book-dead' style="font-size: 2rem;"></i>
                                                                    DEF:-
                                                                </td>

                                                                <?php
                                                                $key = $sitejsonData->parameters->battery_voltage->add;
                                                                $addValue = '_';
                                                                foreach ($eventData as $event) {
                                                                    $eventArray = $event->getArrayCopy();
                                                                    if ($eventArray['module_id'] == $sitejsonData->parameters->battery_voltage->md) {
                                                                        if (array_key_exists($key, $eventArray)) {
                                                                            $addValue = number_format((float)$eventArray[$key], 2);  // Limiting decimal to 2
                                                                        }
                                                                        break;
                                                                    }
                                                                }
                                                            ?>
                                                                <td class="table-column-data ">
                                                                    <i class="fas fa-battery-half text-info" style="font-size: 2rem;"></i>
                                                                    Battery Voltage:<b> {{ $addValue }} V</b>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>   
                                                    

                                        </div>`;
                    }

                    // Insert the populated event card into the eventlist container
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
    setInterval(fetchSiteData, 60000);
    </script>



</body>

</html>