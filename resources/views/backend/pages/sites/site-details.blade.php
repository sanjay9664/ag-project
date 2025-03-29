<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DG SET MONITORING SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
    body {
        background-color: #e3f2fd;
    }

    .dashboard-container {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px,
            rgba(0, 0, 0, 0.12) 0px -12px 30px,
            rgba(0, 0, 0, 0.12) 0px 4px 6px,
            rgba(0, 0, 0, 0.17) 0px 12px 13px,
            rgba(0, 0, 0, 0.09) 0px -3px 5px;
        margin-top: 20px;
        border: 2px solid #007bff;
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    .blinking {
        animation: blink 1s infinite;
    }

    .status-running {
        color: green;
        font-weight: bold;
    }

    .status-stopped {
        color: red;
        font-weight: bold;
    }

    .table tbody tr {
        cursor: pointer;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

    .navbar-brand {
        font-weight: bold;
    }

    .card-header {
        background-color: #002E6E !important;
        color: white !important;
    }

    .parameter-card {
        border-left: 4px solid #007bff;
        margin-bottom: 15px;
    }

    .parameter-value {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .parameter-icon {
        font-size: 2rem;
        margin-right: 10px;
    }

    .fuel-indicator {
        height: 20px;
        background: linear-gradient(to right, red, yellow, green);
        border-radius: 10px;
        position: relative;
        width: 100px;
    }

    .fuel-level {
        position: absolute;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        right: 0;
        border-radius: 0 10px 10px 0;
    }

    .fuel-percentage {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        font-weight: bold;
    }

    .fuel-display {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .fuel-text {
        font-weight: bold;
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #002E6E;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="https://genset.innovatorautomation.co.in/assets/logo.svg" alt="Logo" width="120" height="40"
                    class="d-inline-block align-top">
                DG SET MONITORING SYSTEM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button class="btn btn-outline-light btn-sm mx-1" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Main Dashboard -->
        <div class="dashboard-container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary">Site Overview</h3>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="bankSelect">
                        <option selected>Select Bank</option>
                        <option value="{{ $siteData->id }}">{{ $sitejsonData->group }}</option>
                    </select>
                    <select class="form-select form-select-sm" id="locationSelect">
                        <option selected>Select Location</option>
                        <option value="{{ $siteData->id }}">{{ $siteData->name }}</option>
                    </select>
                </div>
            </div>

            <!-- Sites Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>S.No</th>
                            <th>Site Name</th>
                            <th>Bank Name</th>
                            <th>Id</th>
                            <th>Fuel Level</th>
                            <th>Total Run Hours</th>
                            <th>Updated Date</th>
                            <th>DG Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-site-id="{{ $siteData->id }}">
                            <td>1</td>
                            <td>{{ $sitejsonData->group }}</td>
                            <td>{{ $sitejsonData->group }}</td>
                            <td>{{ $sitejsonData->serial_number }}</td>
                            <td>
                                @php
                                $fuelValue = 0;
                                $capacity = $sitejsonData->capacity ?? 0;
                                $hasFuelData = false;

                                if (isset($sitejsonData->fuel_level)) {
                                $moduleId = $sitejsonData->fuel_level->md ?? null; // Should be "1621"
                                $fieldValue = $sitejsonData->fuel_level->add ?? null; // Should be "3,16406"

                                if ($moduleId && $fieldValue) {
                                foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();

                                // Debugging - check what we actually have
                                // dd($eventArray, $moduleId, $fieldValue);

                                if (($eventArray['module_id'] ?? null) == $moduleId) {
                                if (array_key_exists((string) $fieldValue, $eventArray)) {
                                $fuelValue = (float) $eventArray[(string) $fieldValue];
                                $hasFuelData = true;
                                break;
                                }
                                }
                                }
                                }
                                }

                                $liters = $hasFuelData ? ($fuelValue * $capacity / 100) : 0;
                                @endphp

                                <div class="fuel-container">
                                    <div class="fuel-indicator">
                                        <div class="fuel-level" style="width: 0%;"></div>
                                        <span class="fuel-percentage"> {{ $sitejsonData->capacity }}%</span>
                                    </div>
                                    <!-- <span class="fuel-text text-danger">No fuel data</span> -->
                                </div>

                            </td>

                            <td class="running-hours">
                                @php
                                $increased_running_hours = DB::table('running_hours')->where('site_id',
                                $siteData->id)->first();
                                $increaseRunningHours = (float) ($increased_running_hours->increase_running_hours ?? 0);
                                $siteId = $sitejsonData->id ?? null;
                                $addValue = 0;
                                $key = $sitejsonData->running_hours->add ?? null;

                                if ($key && isset($sitejsonData->running_hours->md)) {
                                foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if (isset($eventArray['module_id']) && $eventArray['module_id'] ==
                                $sitejsonData->running_hours->md) {
                                if (array_key_exists($key, $eventArray)) {
                                $addValue = (float) $eventArray[$key];
                                }
                                break;
                                }
                                }
                                }

                                $increaseMinutes = $sitejsonData->running_hours->increase_minutes ?? null;
                                $inc_addValue = $addValue;

                                if (is_numeric($increaseMinutes) && (float)$increaseMinutes > 0) {
                                $inc_addValue /= (float)$increaseMinutes;
                                }

                                $tempvariable = number_format($inc_addValue, 2);
                                $inc_addValueFormatted = $tempvariable + $increaseRunningHours;
                                @endphp
                                {{ $inc_addValueFormatted }} Hrs
                            </td>
                            <td class="last-updated">{{ $latestCreatedAt }}</td>
                            <td>
                                @php
                                $addValuerunstatus = 0;
                                if (isset($sitejsonData->electric_parameters->voltage_l_l->a)) {
                                $keya = $sitejsonData->electric_parameters->voltage_l_l->a->add;
                                $moduleId = $sitejsonData->electric_parameters->voltage_l_l->a->md;

                                foreach ($eventData as $event) {
                                $eventArraya = $event->getArrayCopy();
                                if (isset($eventArraya['module_id']) && $eventArraya['module_id'] == $moduleId) {
                                if (array_key_exists($keya, $eventArraya)) {
                                $addValuerunstatus = $eventArraya[$keya];
                                }
                                break;
                                }
                                }
                                }
                                @endphp
                                @if($addValuerunstatus > 0)
                                <span class="status-running blinking">ON</span>
                                @else
                                <span class="status-stopped">OFF</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script>
    function fetchSiteData() {
        const slug = "{{ $siteData->slug }}";
        const url = `/admin/site-data/${slug}`;

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                console.log("API Response:", response);

                if (response.eventData && response.sitejsonData) {
                    let fuelKey = String(response.sitejsonData.fuel_level.add).replace(/,/g,
                        "");;
                    let capacity = response.sitejsonData.capacity || 0;

                    let fuelValue = 0;
                    let hasFuelData = false;

                    response.eventData.forEach(event => {
                        if (event[fuelKey] !== undefined) {
                            fuelValue = parseFloat(event[fuelKey]);
                            hasFuelData = true;
                        }
                    });

                    console.log("Final Fuel Value:", fuelValue);

                    if (hasFuelData) {
                        let liters = (fuelValue * capacity / 100).toFixed(2);

                        // Use a specific container selector to target the correct row
                        let fuelContainer = $(".fuel-container");

                        if (fuelContainer.length > 0) {
                            fuelContainer.find(".fuel-level").css("width", (100 - fuelValue) + "%");
                            fuelContainer.find(".fuel-percentage").text(fuelValue + "%");
                            fuelContainer.find(".fuel-text").text(`${fuelValue}% / ${liters} L`);
                        } else {
                            console.error("Fuel container not found in DOM.");
                        }
                    } else {
                        $(".fuel-text").text(`No fuel data`);
                    }
                }
            },
            error: function(xhr) {
                console.error("Error fetching site data:", xhr.responseText);
            }
        });
    }

    $(document).ready(function() {
        fetchSiteData();
        setInterval(fetchSiteData, 10000);
    });
    </script>


</body>

</html>