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


<body class="font-family:Arial, sans-serif  ">
    <!-- Genset Section -->
    <div class="text-center text-white py-1 m-1 rounded d-flex justify-content-between align-items-center"
        style="background:#002E6E; flex-wrap: wrap;">
        <!-- Logo Section -->
        <a class="navbar-brand" href="#">
            <img src="https://genset.innovatorautomation.co.in/assets/logo.svg" alt="sochiot_Logo"
                style="width: 120px; height: 40px;background: white;border-radius: 50px;margin-left: 40px;">
        </a>
        <!-- Title Section -->
        <h5 class="my-3" style="padding-right: 117px;">DG SET MONITORING SYSTEM</h5>
        <!-- Empty Section to Keep Alignment -->
        <h5 class="my-3"></h5>
    </div>

    <style>
    /* Make the navbar items stack vertically on smaller screens */
    @media (max-width: 600px) {
        .text-center {
            flex-direction: column;
            /* Stack items vertically on mobile */
            align-items: center;
            /* Center the items */
        }

        h5 {
            padding-right: 0px;
            /* Remove right padding on smaller screens */
        }

        .navbar-brand {
            margin-bottom: 10px;
            /* Add some spacing below the logo */
        }
    }

    /* Ensure the navbar remains in a row layout for larger screens */
    @media (min-width: 600px) {
        .text-center {
            flex-direction: row;
            /* Keep items in a row on larger screens */
        }
    }
    </style>


    <!-- Device Info Section -->
    <div class=" mt-2 ml-5 p-3 shadow-lg p-3 mb-5 bg-white shadow-lg p-3 mb-5 bg-white rounded">


        <div class="row mt-1" id="event-data">
            <!-- First Card -->
            <div class="col-md-3  ">
                <div class="card shadow g-5" style="width: 100%; height: 615px; overflow: hidden; margin-top:-24px ">
                    <div class="card-header bg-light text-start text-center" style="font-size:15px">
                        <!-- <div ></div> -->
                        <div class="card-header text-center fw-bold  text-white fs-6 " style="background:#002E6E;">
                            ASSET NAME: {{ $sitejsonData->asset_name }}
                        </div>
                        <div
                            style="text-align: left; font-size: 16px; color: #333; font-weight: bold; line-height: 1.5; padding: 10px; width: 100%; max-width: 400px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Location</span> <span style="font-weight: normal;">
                                    {{ $sitejsonData->group }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Generator</span> <span
                                    style="font-weight: normal;">{{ $sitejsonData->generator }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>S/N</span> <span
                                    style="font-weight: normal;">{{ $sitejsonData->serial_number }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Model</span> <span style="font-weight: normal;">{{ $sitejsonData->model }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Brand</span> <span style="font-weight: normal;">{{ $sitejsonData->brand }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Capacity</span> <span
                                    style="font-weight: normal;">{{ $sitejsonData->capacity }}</span>
                            </div>
                        </div>


                    </div>
                    <?php
                        $keya = $sitejsonData->electric_parameters->voltage_l_l->a->add;
                        $addValuerun = '_';

                        foreach ($eventData as $event) {
                            $eventArraya = $event->getArrayCopy();
                            if ($eventArraya['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->a->md) {
                                if (array_key_exists($keya, $eventArraya)) {
                                    $addValuerun = $eventArraya[$keya];
                                }
                                break;
                            }
                        }
                    ?>
                    <div class="card-body p-2">
                        <!-- Run Status -->
                        @if($addValuerun > 0)
                        <div class="card mb-1 shadow-sm border-0">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <!-- Left: Running Icon -->
                                <i class="fas fa-cogs" style="color: teal; font-size: 24px;"></i>

                                <!-- Center: Run Status -->
                                <div class="fw-bold text-center flex-grow-1">
                                    Run Status
                                </div>

                                <!-- Right: Running Status -->
                                <div style="color: teal; font-weight: bold;">
                                    Running
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card mb-1 shadow-sm border-0">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <!-- Left: Stopped Icon -->
                                <i class="fas fa-cogs" style="color: red; font-size: 24px;"></i>

                                <!-- Center: Run Status -->
                                <div class="fw-bold text-center flex-grow-1">
                                    Run Status
                                </div>

                                <!-- Right: Stop Status -->
                                <div style="color: red; font-weight: bold;">
                                    Stop
                                </div>
                            </div>
                        </div>
                        @endif


                        <?php
                        $key = $sitejsonData->running_hours->add;
                        $addValue = '_';
                        $increaseMinutes = isset($sitejsonData->running_hours->increase_minutes) 
                            ? $sitejsonData->running_hours->increase_minutes 
                            : null;

                        foreach ($eventData as $event) {
                            $eventArray = $event->getArrayCopy();
                            if ($eventArray['module_id'] == $sitejsonData->running_hours->md) {
                                if (array_key_exists($key, $eventArray)) {
                                    $addValue = (float)$eventArray[$key];

                                    if (is_numeric($increaseMinutes) && (float)$increaseMinutes > 0) {
                                        $addValue = $addValue / (float)$increaseMinutes;
                                    }

                                    $addValue = number_format($addValue, 2);
                                }
                                break;
                            }
                        }
                        ?>

                        <?php
    $addValue = (float) $addValue; // Cast string to float
    $hours = floor($addValue);
    $minutes = round(($addValue - $hours) * 60);
?>

                        <div class="card mb-1 shadow-sm border-0">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center gap-4 mb-1">
                                    <!-- Running Person Icon (Logo) -->
                                    <i class="fas fa-running"
                                        style="color: goldenrod; font-size: 40px; margin-right: 20px;"></i>

                                    <!-- Running Hours Label and Value -->
                                    <div>
                                        <div style="font-weight: bold; font-size: 16px; color: #333;">
                                            Running Hours: <span
                                                style="font-weight: normal; font-size: 14px; font-weight: bold;">{{ $hours }}
                                                hrs {{ $minutes }} mins
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ************************************************************************************* -->
                        <!-- last running -->

                        <!-- ************************************************************************************* -->


                        <?php
                            $key = $sitejsonData->total_kwh->add;
                            $addKwhValue = '_';

                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->total_kwh->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addKwhValue = $eventArray[$key];
                                    }
                                    break;
                                }
                            }

                            if (is_numeric($addKwhValue)) {
                                $truncatedKwhValue = floor($addKwhValue * 100) / 100;
                                $formattedKwhValue = number_format($truncatedKwhValue, 2);
                            } else {
                                $formattedKwhValue = '_';
                            }
                        ?>
                        <div class="card mb-1 shadow-sm border-0">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center gap-4 mb-1">
                                    <!-- Icon (Logo) on the left -->
                                    <i class='fas fa-charging-station' style='font-size:48px;color:red'></i>

                                    <!-- Total kWh Label and Value on the right -->
                                    <div>
                                        <div style="font-weight: bold; font-size: 16px; color: #333;">
                                            Total kWh: <span
                                                style="font-weight: normal; font-size: 14px; font-weight: bold;">{{$formattedKwhValue}}
                                                Units
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- *************************************************************         -->

                        <!-- ********************************************************         -->
                        <?php
                            $capacity = $sitejsonData->capacity;
                            $key = $sitejsonData->parameters->fuel->add;
                            $addValue = '_';
                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->parameters->fuel->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = $eventArray[$key];
                                    }
                                    break;
                                }
                            }
                            if (is_numeric($addValue)) {
                                $percentage = $addValue;
                            } else {
                                preg_match('/\d+/', $addValue, $matches);
                                $percentage = isset($matches[0]) ? $matches[0] : 0;
                            }
                            $percentageDecimal = $percentage / 100;
                            $totalFuelLiters = $capacity * $percentageDecimal;
                        ?>
                        <div class="card mb-2 shadow-sm border-0">
                            <div class="d-flex align-items-center gap-5 mb-1 fw-bold">
                                <!-- Lottie animation (fuel indicator) on the left -->
                                <dotlottie-player id="fuelIndicator"
                                    src="https://lottie.host/673a61fb-6dc7-4428-99db-f704828eb32f/sB24KCBCEk.lottie"
                                    background="transparent" speed="1" style="width: 50px; height: 50px" loop autoplay>
                                </dotlottie-player>

                                <!-- Text on the right -->
                                <div style="display: flex; gap: 15px; font-size: 16px;">
                                    <span>Fuel: <strong><?php echo htmlspecialchars($percentage); ?>%</strong></span>
                                    <span>Total Fuel Liter:
                                        <strong><?php echo htmlspecialchars($totalFuelLiters); ?>L</strong></span>
                                </div>


                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <!-- Engine Parameters Section -->
            <div class="col-md-3 bg-white rounded">


                <div class="card" style="width: 100%; height: 605px; margin-top:-20px ">
                    <div class="card-header text-center  text-white fw-bold" style="background:#002E6E;">
                        ENGINE PARAMETERS
                    </div>
                    <div class="card-body" style="font-size: 15px; padding: 8px;">
                        <div class="row d-flex justify-content-center">
                            <!-- Coolant Temperature -->
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
                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li><i class="fas fa-thermometer-half text-primary" style="font-size: 2rem;"></i>
                                    </li>
                                    <li class="text-start flex-grow-1" style="margin-left:60px">Coolant Temperature</li>
                                    <li class="text-start">{{ $addValue }} °C</li> <!-- Added degree sign -->
                                </ul>
                            </div>
                            <hr />

                            <!-- Oil Temperature -->
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
                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li><i class="fas fa-oil-can text-warning" style="font-size: 2rem;"></i></li>
                                    <li class="text-start flex-grow-1" style="margin-left:40px">Oil Temperature</li>
                                    <li class="text-start">{{ $addValue }} °C</li> <!-- Added degree sign -->
                                </ul>
                            </div>
                            <hr />

                            <!-- Oil Pressure -->
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
                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li><i class="fas fa-gas-pump text-danger" style="font-size: 2rem;"></i></li>
                                    <li class="text-start flex-grow-1" style="margin-left:50px">Oil Pressure</li>
                                    <li class="text-start">{{ $addValue }}</li>
                                </ul>
                            </div>
                            <hr />

                            <!-- RPM -->
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
                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li><i class="fas fa-tachometer-alt text-danger" style="font-size: 2rem;"></i></li>
                                    <li class="text-start flex-grow-1" style="margin-left:50px">RPM</li>
                                    <li class="text-start">{{ $addValue }}</li>
                                </ul>
                            </div>
                            <hr />

                            <!-- Number of Starts -->
                            <?php
                    $key = $sitejsonData->parameters->number_of_starts->add;
                    $addValue = '_';
                    foreach ($eventData as $event) {
                        $eventArray = $event->getArrayCopy();
                        if ($eventArray['module_id'] == $sitejsonData->parameters->number_of_starts->md) {
                            if (array_key_exists($key, $eventArray)) {
                                $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                            }
                            break;
                        }
                    }
                ?>
                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li><i class="fas fa-key text-secondary" style="font-size: 2rem;"></i></li>
                                    <li class="text-start flex-grow-1" style="margin-left:55px">Number Of Starts</li>
                                    <li class="text-start">{{ $addValue }}</li>
                                </ul>
                            </div>
                            <hr />

                            <!-- Battery Voltage -->
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
                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li><i class="fas fa-battery-half text-info" style="font-size: 2rem;"></i></li>
                                    <li class="text-start flex-grow-1" style="margin-left:50px">Battery Voltage</li>
                                    <li class="text-start">{{ $addValue }} V</li> <!-- Added V -->
                                </ul>
                            </div>
                            <hr />


                            <!-- Latest Date and time -->

                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li class="text-start flex-grow-1 fw-bold d-flex">
                                        <span class="me-4">UpdatedAt</span>
                                        <span class="mx-4">:</span>
                                        <span class="fw-normal">{{ $latestCreatedAt }}</span>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <!-- Electric Parameters Section -->
            <div class="col-md-3 bg-white rounded">
                <div class="card " style="width: 100%; height: 605px; font-size:13px; margin-top:-20px">
                    <div class="card-header text-center  text-white fs-6 fw-bold" style="background:#002E6E;">
                        ELECTRICAL PARAMETERS
                    </div>
                    <div class="card-body" style="font-size: 15px; padding: 8px;">
                        <!-- Table Header -->
                        <div class="row border-bottom pb-3 mb-3">
                            <div class="col-4 text-start">Phase</div>
                            <div class="col text-center">R</div>
                            <div class="col text-center">Y</div>
                            <div class="col text-center">B</div>
                        </div>
                        <hr />
                        <?php
                            $keya = $sitejsonData->electric_parameters->voltage_l_l->a->add;
                            $addValuea = '_';

                            foreach ($eventData as $event) {
                                $eventArraya = $event->getArrayCopy();
                                if ($eventArraya['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->a->md) {
                                    if (array_key_exists($keya, $eventArraya)) {
                                        $addValuea = $eventArraya[$keya];
                                    }
                                    break;
                                }
                            }

                            $keyb = $sitejsonData->electric_parameters->voltage_l_l->b->add;
                            $addValueb = '_';

                            foreach ($eventData as $event) {
                                $eventArrayb = $event->getArrayCopy();
                                if ($eventArrayb['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->b->md) {
                                    if (array_key_exists($keyb, $eventArrayb)) {
                                        $addValueb = $eventArrayb[$keyb];
                                    }
                                    break;
                                }
                            }

                            $keyc = $sitejsonData->electric_parameters->voltage_l_l->c->add;
                            $addValuec = '_';

                            foreach ($eventData as $event) {
                                $eventArrayc = $event->getArrayCopy();
                                if ($eventArrayc['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->c->md) {
                                    if (array_key_exists($keyc, $eventArrayc)) {
                                        $addValuec = $eventArrayc[$keyc];
                                    }
                                    break;
                                }
                            }
                        ?>
                        <!-- Voltage L-L -->
                        <div class="row mb-3 ">
                            <div class="col-3 text-start">Volt L-L</div>
                            <div class="col text-center text-warning">{{$addValuea}}</div>
                            <div class="col text-center text-danger">{{$addValueb}}</div>
                            <div class="col text-center">{{$addValuec}}</div>
                        </div>
                        <hr />
                        <?php
                            $keya = $sitejsonData->electric_parameters->voltage_l_n->a->add;
                            $addValueaaa = '_';
                            foreach ($eventData as $event) {
                                $eventArraya = $event->getArrayCopy();
                                if ($eventArraya['module_id'] == $sitejsonData->electric_parameters->voltage_l_n->a->md) {
                                    if (array_key_exists($keya, $eventArraya)) {
                                        $addValueaaa = $eventArraya[$keya];
                                    }
                                    break;
                                }
                            }

                            $keyb = $sitejsonData->electric_parameters->voltage_l_n->b->add;
                            $addValuebbb = '_';
                            foreach ($eventData as $event) {
                                $eventArrayb = $event->getArrayCopy();
                                if ($eventArrayb['module_id'] == $sitejsonData->electric_parameters->voltage_l_n->b->md) {
                                    if (array_key_exists($keyb, $eventArrayb)) {
                                        $addValuebbb = $eventArrayb[$keyb];
                                    }
                                    break;
                                }
                            }

                            $keyc = $sitejsonData->electric_parameters->voltage_l_n->c->add;
                            $addValueccc = '_';
                            foreach ($eventData as $event) {
                                $eventArrayc = $event->getArrayCopy();
                                if ($eventArrayc['module_id'] == $sitejsonData->electric_parameters->voltage_l_n->c->md) {
                                    if (array_key_exists($keyc, $eventArrayc)) {
                                        $addValueccc = $eventArrayc[$keyc];
                                    }
                                    break;
                                }
                            }

                            $formattedValueaaa = is_numeric($addValueaaa) ? number_format((float)$addValueaaa, 2) : $addValueaaa;
                            $formattedValuebbb = is_numeric($addValuebbb) ? number_format((float)$addValuebbb, 2) : $addValuebbb;
                            $formattedValueccc = is_numeric($addValueccc) ? number_format((float)$addValueccc, 2) : $addValueccc;
                        ?>

                        <!-- Voltage L-N -->
                        <div class="row mb-3">
                            <div class="col-3 text-start" style="font-size:14px;">Volts L-N</div>
                            <div class="col text-center">{{ $formattedValueaaa }}</div>
                            <div class="col text-center">{{ $formattedValuebbb }}</div>
                            <div class="col text-center">{{ $formattedValueccc }}</div>
                        </div>

                        <hr />
                        <?php
                        $keya = $sitejsonData->electric_parameters->current->a->add;
                        $addValueaa = '_';

                        foreach ($eventData as $event) {
                            $eventArraya = $event->getArrayCopy();
                            if ($eventArraya['module_id'] == $sitejsonData->electric_parameters->current->a->md) {
                                if (array_key_exists($keya, $eventArraya)) {
                                    $addValueaa = $eventArraya[$keya];
                                }
                                break;
                            }
                        }

                        $keyb = $sitejsonData->electric_parameters->current->b->add;
                        $addValuebb = '_';

                        foreach ($eventData as $event) {
                            $eventArrayb = $event->getArrayCopy();
                            if ($eventArrayb['module_id'] == $sitejsonData->electric_parameters->current->b->md) {
                                if (array_key_exists($keyb, $eventArrayb)) {
                                    $addValuebb = $eventArrayb[$keyb];
                                }
                                break;
                            }
                        }

                        $keyc = $sitejsonData->electric_parameters->current->c->add;
                        $addValuecc = '_';

                        foreach ($eventData as $event) {
                            $eventArrayc = $event->getArrayCopy();
                            if ($eventArrayc['module_id'] == $sitejsonData->electric_parameters->current->c->md) {
                                if (array_key_exists($keyc, $eventArrayc)) {
                                    $addValuecc = $eventArrayc[$keyc];
                                }
                                break;
                            }
                        }

                        // Ensure values are numeric before formatting
                        $formattedValueaa = is_numeric($addValueaa) ? number_format((float) $addValueaa, 2) : '_';
                        $formattedValuebb = is_numeric($addValuebb) ? number_format((float) $addValuebb, 2) : '_';
                        $formattedValuecc = is_numeric($addValuecc) ? number_format((float) $addValuecc, 2) : '_';

                        ?>

                        <!-- Current -->
                        <div class="row mb-3">
                            <div class="col-3 text-start " style="font-size:12px;">Current, A</div>
                            <div class="col text-center"><?= $formattedValueaa ?></div>
                            <div class="col text-center"><?= $formattedValuebb ?></div>
                            <div class="col text-center"><?= $formattedValuecc ?></div>
                        </div>

                        <hr />
                        <?php
                            $key = $sitejsonData->electric_parameters->pf_data->add;
                            $addValue = '_';

                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->electric_parameters->pf_data->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = $eventArray[$key];
                                    }
                                    break;
                                }
                            }

                            if (is_numeric($addValue)) {
                                $truncatedValue = floor($addValue * 100) / 100;
                                $formattedpfValue = number_format($truncatedValue, 2);
                            } else {
                                $formattedpfValue = '_';
                            }
                        ?>
                        <!-- Pf-data -->
                        <div class="row mb-1 align-items-center">
                            <!-- Label Column -->
                            <div class="col-6 text-start">
                                <span>Power Factor</span>
                            </div>
                            <!-- Value Column -->
                            <div class="col-6 text-center">
                                <span>{{ $formattedpfValue }}</span>
                            </div>
                        </div>

                        <hr />
                        <?php
                            $key = $sitejsonData->electric_parameters->frequency->add;
                            $addValuec = '_';

                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->electric_parameters->frequency->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValuec = $eventArray[$key];
                                    }
                                    break;
                                }
                            }

                            if (is_numeric($addValuec)) {
                                $truncatedValuee = floor($addValuec * 100) / 100;
                                $formattedfrequencyValue = number_format($truncatedValuee, 2);
                            } else {
                                $formattedfrequencyValue = '_';
                            }
                        ?>
                        <!-- Frequency -->
                        <div class="row mb-2">
                            <div class="col-6 text-start ">Frequency</div>
                            <div class="col text-center">{{$formattedfrequencyValue}} HZ</div>
                        </div>
                        <hr />
                        <?php
                            $key = $sitejsonData->active_power_kw->add;
                            $addValue = '_';

                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->active_power_kw->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = $eventArray[$key];
                                    }
                                    break;
                                }
                            }
                        ?>
                        <div class="card mb-1 shadow-sm border-0">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center  gap-4 mb-1">
                                    <!-- Icon (Bolt) on the left -->
                                    <i class="fas fa-bolt"
                                        style="color: goldenrod; font-size: 40px; margin-right: 60px;"></i>

                                    <!-- Active Power, kW Label and Value on the right -->
                                    <div>
                                        <div style="font-weight: bold;  font-size: 16px; color: #333; ">
                                            Active power, kW
                                        </div>
                                        <div style="width: 40px; height: 40px; margin: auto;">
                                            <!-- Circular background for value -->
                                            <div
                                                style="background-color: #d6d6d6; border-radius: 50%; width: 80%; height: 80%; position: relative; text-align: center; line-height: 30px;">
                                                <span style="font-size: 13px; color: #333;">
                                                    {{ $addValue }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php
                            $key = $sitejsonData->active_power_kva->add;
                            $addValue = '_';

                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->active_power_kva->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = $eventArray[$key];
                                    }
                                    break;
                                }
                            }
                        ?>
                        <div class="card mb-1 shadow-sm border-0">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center gap-4 mb-1">
                                    <!-- Icon (Bolt) on the left -->
                                    <i class="fas fa-bolt"
                                        style="color: goldenrod; font-size: 40px; margin-right: 60px;"></i>

                                    <!-- Active power, kVA Label and Value on the right -->
                                    <div>
                                        <div style="font-weight: bold; font-size: 16px; color: #333;">
                                            Active power, kVA
                                        </div>
                                        <div style="width: 40px; height: 40px; margin: auto;">
                                            <!-- Circular background for value -->
                                            <div
                                                style="background-color: #d6d6d6; border-radius: 50%; width: 80%; height: 80%; position: relative; text-align: center; line-height: 30px;">
                                                <span style="font-size: 14px; color: #333;">
                                                    {{ $addValue }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KW -->
                        <div class="row">
                            <!-- <div class="col-xs-4 text-start">KW</div>
                            <div class="col text-center">12.2</div>
                            <div class="col text-center">12</div>
                            <div class="col text-center">12</div> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alarm Status Section -->
            <div class="col-md-3 bg-white rounded">
                <div class="card shadow-sm " style="height: 605px; width: 100%; font-size: 13px; margin-top:-20px">
                    <div class="card-header text-center text-white fs-6 fw-bold" style="background: #002E6E;">
                        ALARM STATUS
                    </div>
                    <div
                        style="display: flex; flex-direction: row; padding: 15px; gap: 15px; flex-wrap: wrap; justify-content: center;">
                        <!-- NO ALARM -->
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <div style="
            width: 20px; 
            height: 20px; 
            border-radius: 50%; 
            animation: blink 1.5s infinite; 
            border: 2px solid #ccc; 
            background-color: palegreen;">
                            </div>
                            <span style="margin-left: 10px; font-weight: bold; color: palegreen;">NO ALARM</span>
                        </div>
                        <!-- WARNING -->
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <div style="
            width: 20px; 
            height: 20px; 
            border-radius: 50%; 
            animation: blink 1.5s infinite; 
            border: 2px solid #ccc; 
            background-color: orange;">
                            </div>
                            <span style="margin-left: 10px; font-weight: bold; color: orange;">WARNING</span>
                        </div>
                        <!-- TRIP -->
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <div style="
            width: 20px; 
            height: 20px; 
            border-radius: 50%; 
            animation: blink 1.5s infinite; 
            border: 2px solid #ccc; 
            background-color: red;">
                            </div>
                            <span style="margin-left: 10px; font-weight: bold; color: red;">TRIP</span>
                        </div>
                    </div>

                    <style>
                    @keyframes blink {

                        0%,
                        100% {
                            opacity: 1;
                        }

                        50% {
                            opacity: 0.5;
                        }
                    }

                    /* Responsive design for small devices */
                    @media (max-width: 600px) {
                        div[style*="flex-direction: row"] {
                            flex-direction: column;
                            /* Stack items vertically on smaller screens */
                            align-items: center;
                        }

                        div[style*="margin-left: 10px"] {
                            margin-left: 5px;
                            /* Reduce spacing for smaller screens */
                        }
                    }

                    /* Ensure proper alignment on larger screens */
                    @media (min-width: 600px) {
                        div[style*="flex-direction: row"] {
                            flex-direction: row;
                            /* Keep items in row on larger screens */
                        }
                    }
                    </style>


                    <div class="card-body p-3 d-flex flex-column justify-content-center">
                        <div class="row h-100">
                            <!-- Left Column -->
                            <div class="col-xs-6 border-end border-dark d-flex flex-column justify-content-around">
                                <?php
                                    $keya = $sitejsonData->alarm_status->coolant_temperature_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->coolant_temperature_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Coolant Temperature</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->fuel_level_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->fuel_level_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Fuel Level</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->emergency_stop_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->emergency_stop_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Emergency stop</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->high_coolant_temperature_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->high_coolant_temperature_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>High coolant temperature</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->under_speed_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->under_speed_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Under speed</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->fail_to_start_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->fail_to_start_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Fail to start</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->loss_of_speed_sensing_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->loss_of_speed_sensing_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Loss of speed sensing</span>
                                </div>
                            </div>
                            <!-- Right Column -->
                            <div class="col-xs-6 d-flex flex-column justify-content-around">
                                <?php
                                    $keya = $sitejsonData->alarm_status->oil_pressure_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->oil_pressure_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Oil Pressure</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->battery_level_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->battery_level_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Battery Level</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->low_oil_pressure_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->low_oil_pressure_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Low oil pressure</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->high_oil_temperature_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->high_oil_temperature_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>High oil temperature</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->over_speed_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->over_speed_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Over speed</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->fail_to_come_to_rest_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->fail_to_come_to_rest_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Fail to come to rest</span>
                                </div>
                                <?php
                                    $keya = $sitejsonData->alarm_status->generator_low_voltage_status->add;
                                    $addValue = '_';

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if ($eventArraya['module_id'] == $sitejsonData->alarm_status->generator_low_voltage_status->md) {
                                            if (array_key_exists($keya, $eventArraya)) {
                                                $addValue = $eventArraya[$keya];
                                            }
                                            break;
                                        }
                                    }

                                    $backgroundColor = 'transparent';
                                    switch ($addValue) {
                                        case '1':
                                            $backgroundColor = 'green';
                                            break;
                                        case '2':
                                            $backgroundColor = 'yellow';
                                            break;
                                        case '3':
                                            $backgroundColor = 'red';
                                            break;
                                        case '15':
                                            $backgroundColor = 'grey';
                                            break;
                                        case '0':
                                        default:
                                            $backgroundColor = 'transparent';
                                            break;
                                    }
                                ?>
                                <div class="d-flex align-items-center justify-content-start">
                                    <span class="me-2 rounded-circle"
                                        style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                    <span>Generator low voltage</span>
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
                                <div class="row">
                                    <!-- First Card -->
                                     <div class="col-md-3  ">
                <div class="card shadow g-5" style="width: 100%; height: 615px; overflow: hidden; margin-top:-24px ">
                    <div class="card-header bg-light text-start text-center"  style="font-size:15px">
                        <!-- <div ></div> -->
                        <div class="card-header text-center  fw-bold  text-white fs-6 " style="background:#002E6E;">
                             ASSET NAME: {{ $sitejsonData->asset_name }}
                        </div>
  <div style="text-align: left; font-size: 16px; color: #333; font-weight: bold; line-height: 1.5; padding: 10px; width: 100%; max-width: 400px;">
    <div style="display: flex; justify-content: space-between;">
        <span>Location</span> <span style="font-weight: normal;"> {{ $sitejsonData->group }}</span>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <span>Generator</span> <span style="font-weight: normal;">{{ $sitejsonData->generator }}</span>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <span>S/N</span> <span style="font-weight: normal;">{{ $sitejsonData->serial_number }}</span>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <span>Model</span> <span style="font-weight: normal;">{{ $sitejsonData->model }}</span>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <span>Brand</span> <span style="font-weight: normal;">{{ $sitejsonData->brand }}</span>
    </div>
    <div style="display: flex; justify-content: space-between;">
        <span>Capacity</span> <span style="font-weight: normal;">{{ $sitejsonData->capacity }}</span>
    </div>
</div>

                    </div>
                    <?php
                        $keya = $sitejsonData->electric_parameters->voltage_l_l->a->add;
                        $addValuerun = '_';

                        foreach ($eventData as $event) {
                            $eventArraya = $event->getArrayCopy();
                            if ($eventArraya['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->a->md) {
                                if (array_key_exists($keya, $eventArraya)) {
                                    $addValuerun = $eventArraya[$keya];
                                }
                                break;
                            }
                        }
                    ?>
                    <div class="card-body p-2">
                        <!-- Run Status -->
                        @if($addValuerun > 0)
<div class="card mb-1 shadow-sm border-0">
    <div class="card-body d-flex align-items-center justify-content-between">
        <!-- Left: Running Icon -->
        <i class="fas fa-cogs" style="color: teal; font-size: 24px;"></i>

        <!-- Center: Run Status -->
        <div class="fw-bold text-center flex-grow-1">
            Run Status
        </div>

        <!-- Right: Running Status -->
        <div style="color: teal; font-weight: bold;">
            Running
        </div>
    </div>
</div>
@else
<div class="card mb-1 shadow-sm border-0">
    <div class="card-body d-flex align-items-center justify-content-between">
        <!-- Left: Stopped Icon -->
        <i class="fas fa-cogs" style="color: red; font-size: 24px;"></i>

        <!-- Center: Run Status -->
        <div class="fw-bold text-center flex-grow-1">
            Run Status
        </div>

        <!-- Right: Stop Status -->
        <div style="color: red; font-weight: bold;">
            Stop
        </div>
    </div>
</div>
@endif

                        <?php
                    $key = $sitejsonData->running_hours->add;
                    $addValue = '_';
                    $increaseMinutes = isset($sitejsonData->running_hours->increase_minutes) 
                        ? $sitejsonData->running_hours->increase_minutes 
                        : null;

                    foreach ($eventData as $event) {
                        $eventArray = $event->getArrayCopy();
                        if ($eventArray['module_id'] == $sitejsonData->running_hours->md) {
                            if (array_key_exists($key, $eventArray)) {
                                $addValue = (float)$eventArray[$key];

                                if (is_numeric($increaseMinutes) && (float)$increaseMinutes > 0) {
                                    $addValue = $addValue / (float)$increaseMinutes;
                                }

                                $addValue = number_format($addValue, 2);
                            }
                            break;
                        }
                    }
                    ?>

                        <?php
                            $addValue = (float) $addValue;
                            $hours = floor($addValue);
                            $minutes = round(($addValue - $hours) * 60);
                        ?>


                        <div class="card mb-1 shadow-sm border-0">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center gap-4 mb-1"> 
                                    <!-- Running Person Icon (Logo) -->
                                    <i class="fas fa-running" style="color: goldenrod; font-size: 40px; margin-right: 20px;"></i>
                                    
                                    <!-- Running Hours Label and Value -->
                                    <div>
                                    <div style="font-weight: bold; font-size: 16px; color: #333;">
                                                                    Running Hours: <span
                                                                        style="font-weight: normal; font-size: 14px; font-weight: bold;">{{ $hours }} hrs {{ $minutes }} mins
                                                                        </span>
                                                                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
 <!-- ************************************************************************************* -->
                        <!-- last running -->
                        
                        <!-- ************************************************************************************* -->
                        <?php
                            $key = $sitejsonData->total_kwh->add;
                            $addKwhValue = '_';

                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->total_kwh->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addKwhValue = $eventArray[$key];
                                    }
                                    break;
                                }
                            }

                            if (is_numeric($addKwhValue)) {
                                $truncatedKwhValue = floor($addKwhValue * 100) / 100;
                                $formattedKwhValue = number_format($truncatedKwhValue, 2);
                            } else {
                                $formattedKwhValue = '_';
                            }
                        ?>
                        <div class="card mb-1 shadow-sm border-0">
    <div class="card-body text-center">
        <div class="d-flex align-items-center gap-4 mb-1">
            <!-- Icon (Logo) on the left -->
           <i class='fas fa-charging-station' style='font-size:48px;color:red'></i>
            
            <!-- Total kWh Label and Value on the right -->
            <div>
                
                <div style="font-weight: bold; font-size: 16px; color: #333;">
                                            Total kWh: <span
                                                style="font-weight: normal; font-size: 14px; font-weight: bold;">{{$formattedKwhValue}} Units
                                                </span>
                                        </div>
            </div>
        </div>
    </div>
</div>
                        <?php
                            $capacity = $sitejsonData->capacity;
                            $key = $sitejsonData->parameters->fuel->add;
                            $addValue = '_';
                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->parameters->fuel->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = $eventArray[$key];
                                    }
                                    break;
                                }
                            }
                            if (is_numeric($addValue)) {
                                $percentage = $addValue;
                            } else {
                                preg_match('/\d+/', $addValue, $matches);
                                $percentage = isset($matches[0]) ? $matches[0] : 0;
                            }
                            $percentageDecimal = $percentage / 100;
                            $totalFuelLiters = $capacity * $percentageDecimal;
                        ?>
                        <div class="card mb-2 shadow-sm border-0">
    <div class="d-flex align-items-center gap-5 mb-1 fw-bold">
        <!-- Lottie animation (fuel indicator) on the left -->
        <dotlottie-player id="fuelIndicator"
            src="https://lottie.host/673a61fb-6dc7-4428-99db-f704828eb32f/sB24KCBCEk.lottie"
            background="transparent" speed="1" style="width: 50px; height: 50px" loop autoplay>
        </dotlottie-player>

        <!-- Text on the right -->
      <div style="display: flex; gap: 15px; font-size: 16px;">
    <span>Fuel: <strong><?php echo htmlspecialchars($percentage); ?>%</strong></span>
    <span>Total Fuel Liter: <strong><?php echo htmlspecialchars($totalFuelLiters); ?>L</strong></span>
</div>

    </div>
    
                        
</div>

                    </div>
                </div>
            </div>

                                    <!--  ENGINE PARAMETERS Section -->
                                    <div class="col-md-3 bg-white rounded">
                                        <div class="card" style="width: 100%; height: 605px; margin-top:-20px">
                                            <div class="card-header text-center  text-white fw-bold" style="background:#002E6E;">
                                                ENGINE PARAMETERS
                                            </div>
                                            <div class="card-body" style="font-size: 15px; padding: 8px;">
            <div class="row d-flex justify-content-center">
                <!-- Coolant Temperature -->
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
                <div class="col-12 d-flex justify-content-start align-items-center py-2">
                    <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                        <li><i class="fas fa-thermometer-half text-primary" style="font-size: 2rem;"></i></li>
                        <li class="text-start flex-grow-1" style="margin-left:60px">Coolant Temperature</li>
                        <li class="text-start">{{ $addValue }} °C</li> <!-- Added degree sign -->
                    </ul>
                </div>
                <hr />

                <!-- Oil Temperature -->
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
                <div class="col-12 d-flex justify-content-start align-items-center py-2">
                    <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                        <li><i class="fas fa-oil-can text-warning" style="font-size: 2rem;"></i></li>
                        <li class="text-start flex-grow-1" style="margin-left:40px">Oil Temperature</li>
                        <li class="text-start">{{ $addValue }} °C</li> <!-- Added degree sign -->
                    </ul>
                </div>
                <hr />

                <!-- Oil Pressure -->
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
                <div class="col-12 d-flex justify-content-start align-items-center py-2">
                    <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                        <li><i class="fas fa-gas-pump text-danger" style="font-size: 2rem;"></i></li>
                        <li class="text-start flex-grow-1" style="margin-left:50px">Oil Pressure</li>
                        <li class="text-start">{{ $addValue }}</li>
                    </ul>
                </div>
                <hr />

                <!-- RPM -->
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
                <div class="col-12 d-flex justify-content-start align-items-center py-2">
                    <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                        <li><i class="fas fa-tachometer-alt text-danger" style="font-size: 2rem;"></i></li>
                        <li class="text-start flex-grow-1" style="margin-left:50px">RPM</li>
                        <li class="text-start">{{ $addValue }}</li>
                    </ul>
                </div>
                <hr />

                <!-- Number of Starts -->
                <?php
                    $key = $sitejsonData->parameters->number_of_starts->add;
                    $addValue = '_';
                    foreach ($eventData as $event) {
                        $eventArray = $event->getArrayCopy();
                        if ($eventArray['module_id'] == $sitejsonData->parameters->number_of_starts->md) {
                            if (array_key_exists($key, $eventArray)) {
                                $addValue = number_format($eventArray[$key], 2);  // Limiting decimal to 2
                            }
                            break;
                        }
                    }
                ?>
                <div class="col-12 d-flex justify-content-start align-items-center py-2">
                    <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                        <li><i class="fas fa-key text-secondary" style="font-size: 2rem;"></i></li>
                        <li class="text-start flex-grow-1" style="margin-left:55px">Number Of Starts</li>
                        <li class="text-start">{{ $addValue }}</li>
                    </ul>
                </div>
                <hr />

                <!-- Battery Voltage -->
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
                <div class="col-12 d-flex justify-content-start align-items-center py-2">
                    <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                        <li><i class="fas fa-battery-half text-info" style="font-size: 2rem;"></i></li>
                        <li class="text-start flex-grow-1" style="margin-left:50px">Battery Voltage</li>
                        <li class="text-start">{{ $addValue }} V</li> <!-- Added V -->
                    </ul>
                </div>
                <hr />

                
                <!-- Latest Date and Time -->
                 <?php
                //     $key = $sitejsonData->running_hours->add;
                //     $addValue = '_';
                //     foreach ($eventData as $event) {
                //         $eventArray = $event->getArrayCopy();
                //         if ($eventArray['module_id'] == $sitejsonData->running_hours->md) {
                //             if (array_key_exists($key, $eventArray)) {
                //                 $addValue = number_format((float)$eventArray[$key], 2);  // Limiting decimal to 2
                //             }
                //             break;
                //         }
                //     }
                // ?>
                <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li class="text-start flex-grow-1 fw-bold d-flex">
                                        <span class="me-4">UpdatedAt</span>
                                        <span class="mx-4">:</span>
                                        <span class="fw-normal">{{ $latestCreatedAt }}</span>
                                    </li>
                                </ul>
                            </div>
            </div>
        </div>
                                        </div>
                                    </div>

                                    <!-- Electric Parameters Section -->
                                    <div class="col-md-3 bg-white rounded">
                                        <div class="card " style="width: 100%; height: 605px; font-size:13px; margin-top:-20px">
                                            <div class="card-header text-center  text-white fs-6 fw-bold" style="background:#002E6E;">
                                                 ELECTRICAL  PARAMETERS
                                            </div>
                                            <div class="card-body" style="font-size: 15px; padding: 8px;">
                                                <!-- Table Header -->
                                                <div class="row border-bottom pb-3 mb-3">
                                                    <div class="col-4 text-start  ">Phase</div>
                                                    <div class="col text-center">R</div>
                                                    <div class="col text-center">Y</div>
                                                    <div class="col text-center">B</div>
                                                </div>
                                                <hr />
                                                <?php
                                                    $keya = $sitejsonData->electric_parameters->voltage_l_l->a->add;
                                                    $addValuea = '_';

                                                    foreach ($eventData as $event) {
                                                        $eventArraya = $event->getArrayCopy();
                                                        if ($eventArraya['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->a->md) {
                                                            if (array_key_exists($keya, $eventArraya)) {
                                                                $addValuea = $eventArraya[$keya];
                                                            }
                                                            break;
                                                        }
                                                    }

                                                    $keyb = $sitejsonData->electric_parameters->voltage_l_l->b->add;
                                                    $addValueb = '_';

                                                    foreach ($eventData as $event) {
                                                        $eventArrayb = $event->getArrayCopy();
                                                        if ($eventArrayb['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->b->md) {
                                                            if (array_key_exists($keyb, $eventArrayb)) {
                                                                $addValueb = $eventArrayb[$keyb];
                                                            }
                                                            break;
                                                        }
                                                    }

                                                    $keyc = $sitejsonData->electric_parameters->voltage_l_l->c->add;
                                                    $addValuec = '_';

                                                    foreach ($eventData as $event) {
                                                        $eventArrayc = $event->getArrayCopy();
                                                        if ($eventArrayc['module_id'] == $sitejsonData->electric_parameters->voltage_l_l->c->md) {
                                                            if (array_key_exists($keyc, $eventArrayc)) {
                                                                $addValuec = $eventArrayc[$keyc];
                                                            }
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <!-- Volts L-L -->
                                                <div class="row mb-3 ">
                                                    <div class="col-3 text-start  ">Volt L-L</div>
                                                    <div class="col text-center text-warning">{{$addValuea}}</div>
                                                    <div class="col text-center text-danger">{{$addValueb}}</div>
                                                    <div class="col text-center">{{$addValuec}}</div>
                                                </div>
                                                <hr />
                                                <?php
                                                    $keya = $sitejsonData->electric_parameters->voltage_l_n->a->add;
                                                    $addValueaaa = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArraya = $event->getArrayCopy();
                                                        if ($eventArraya['module_id'] == $sitejsonData->electric_parameters->voltage_l_n->a->md) {
                                                            if (array_key_exists($keya, $eventArraya)) {
                                                                $addValueaaa = $eventArraya[$keya];
                                                            }
                                                            break;
                                                        }
                                                    }

                                                    $keyb = $sitejsonData->electric_parameters->voltage_l_n->b->add;
                                                    $addValuebbb = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArrayb = $event->getArrayCopy();
                                                        if ($eventArrayb['module_id'] == $sitejsonData->electric_parameters->voltage_l_n->b->md) {
                                                            if (array_key_exists($keyb, $eventArrayb)) {
                                                                $addValuebbb = $eventArrayb[$keyb];
                                                            }
                                                            break;
                                                        }
                                                    }

                                                    $keyc = $sitejsonData->electric_parameters->voltage_l_n->c->add;
                                                    $addValueccc = '_';
                                                    foreach ($eventData as $event) {
                                                        $eventArrayc = $event->getArrayCopy();
                                                        if ($eventArrayc['module_id'] == $sitejsonData->electric_parameters->voltage_l_n->c->md) {
                                                            if (array_key_exists($keyc, $eventArrayc)) {
                                                                $addValueccc = $eventArrayc[$keyc];
                                                            }
                                                            break;
                                                        }
                                                    }

                                                    $formattedValueaaa = is_numeric($addValueaaa) ? number_format((float)$addValueaaa, 2) : $addValueaaa;
                                                    $formattedValuebbb = is_numeric($addValuebbb) ? number_format((float)$addValuebbb, 2) : $addValuebbb;
                                                    $formattedValueccc = is_numeric($addValueccc) ? number_format((float)$addValueccc, 2) : $addValueccc;
                                                ?>

                                                <!-- Volts L-N -->
                                                <div class="row mb-3">
                                                    <div class="col-3 text-start" style="font-size:14px;">Volts L-N</div>
                                                    <div class="col text-center">{{ $formattedValueaaa }}</div>
                                                    <div class="col text-center">{{ $formattedValuebbb }}</div>
                                                    <div class="col text-center">{{ $formattedValueccc }}</div>
                                                </div>

                                                <hr />
                                                <?php
                        $keya = $sitejsonData->electric_parameters->current->a->add;
                        $addValueaa = '_';

                        foreach ($eventData as $event) {
                            $eventArraya = $event->getArrayCopy();
                            if ($eventArraya['module_id'] == $sitejsonData->electric_parameters->current->a->md) {
                                if (array_key_exists($keya, $eventArraya)) {
                                    $addValueaa = $eventArraya[$keya];
                                }
                                break;
                            }
                        }

                        $keyb = $sitejsonData->electric_parameters->current->b->add;
                        $addValuebb = '_';

                        foreach ($eventData as $event) {
                            $eventArrayb = $event->getArrayCopy();
                            if ($eventArrayb['module_id'] == $sitejsonData->electric_parameters->current->b->md) {
                                if (array_key_exists($keyb, $eventArrayb)) {
                                    $addValuebb = $eventArrayb[$keyb];
                                }
                                break;
                            }
                        }

                        $keyc = $sitejsonData->electric_parameters->current->c->add;
                        $addValuecc = '_';

                        foreach ($eventData as $event) {
                            $eventArrayc = $event->getArrayCopy();
                            if ($eventArrayc['module_id'] == $sitejsonData->electric_parameters->current->c->md) {
                                if (array_key_exists($keyc, $eventArrayc)) {
                                    $addValuecc = $eventArrayc[$keyc];
                                }
                                break;
                            }
                        }

                        // Ensure values are numeric before formatting
                        $formattedValueaa = is_numeric($addValueaa) ? number_format((float) $addValueaa, 2) : '_';
                        $formattedValuebb = is_numeric($addValuebb) ? number_format((float) $addValuebb, 2) : '_';
                        $formattedValuecc = is_numeric($addValuecc) ? number_format((float) $addValuecc, 2) : '_';

                        ?>

                        <!-- Current -->
                        <div class="row mb-3">
                            <div class="col-3 text-start " style="font-size:12px;">Current, A</div>
                            <div class="col text-center"><?= $formattedValueaa ?></div>
                            <div class="col text-center"><?= $formattedValuebb ?></div>
                            <div class="col text-center"><?= $formattedValuecc ?></div>
                        </div>

                                                <hr />
                                                <?php
                                                    $key = $sitejsonData->electric_parameters->pf_data->add;
                                                    $addValue = '_';

                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->electric_parameters->pf_data->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValue = $eventArray[$key];
                                                            }
                                                            break;
                                                        }
                                                    }

                                                    if (is_numeric($addValue)) {
                                                        $truncatedValue = floor($addValue * 100) / 100;
                                                        $formattedpfValue = number_format($truncatedValue, 2);
                                                    } else {
                                                        $formattedpfValue = '_';
                                                    }
                                                ?>
                                                <!-- Pf-data -->
                                                <div class="row mb-1 align-items-center">
                                                    <!-- Label Column -->
                                                    <div class="col-6 text-start">
                                                        <span>Power Factor</span>
                                                    </div>
                                                    <!-- Value Column -->
                                                    <div class="col-6 text-center">
                                                        <span>{{ $formattedpfValue }}</span>
                                                    </div>
                                                </div>
                                                <hr />
                                                <?php
                                                    $key = $sitejsonData->electric_parameters->frequency->add;
                                                    $addValuec = '_';

                                                    foreach ($eventData as $event) {
                                                        $eventArray = $event->getArrayCopy();
                                                        if ($eventArray['module_id'] == $sitejsonData->electric_parameters->frequency->md) {
                                                            if (array_key_exists($key, $eventArray)) {
                                                                $addValuec = $eventArray[$key];
                                                            }
                                                            break;
                                                        }
                                                    }

                                                    if (is_numeric($addValuec)) {
                                                        $truncatedValuee = floor($addValuec * 100) / 100;
                                                        $formattedfrequencyValue = number_format($truncatedValuee, 2);
                                                    } else {
                                                        $formattedfrequencyValue = '_';
                                                    }
                                                ?>
                                                <!-- Frequency -->
                                                <div class="row mb-2">
                                                    <div class="col-6 text-start ">Frequency</div>
                                                    <div class="col text-center">{{$formattedfrequencyValue}} HZ</div>

                                                </div>
                                                <hr />
                                                <?php
                            $key = $sitejsonData->active_power_kw->add;
                            $addValue = '_';

                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->active_power_kw->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = $eventArray[$key];
                                    }
                                    break;
                                }
                            }
                        ?>
                        <div class="card mb-1 shadow-sm border-0">
    <div class="card-body text-center">
        <div class="d-flex align-items-center gap-4 mb-1">
            <!-- Icon (Bolt) on the left -->
            <i class="fas fa-bolt" style="color: goldenrod; font-size: 40px; margin-right: 60px;"></i>
            
            <!-- Active Power, kW Label and Value on the right -->
            <div>
                <div style="font-weight: bold; font-size: 16px; color: #333;">
                    Active power, kW
                </div>
                <div style="width: 40px; height: 40px; margin: auto;">
                    <!-- Circular background for value -->
                    <div
                        style="background-color: #d6d6d6; border-radius: 50%; width: 80%; height: 80%; position: relative; text-align: center; line-height: 30px;">
                        <span style="font-size: 13px; color: #333;">
                            {{ $addValue }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                        <?php
                            $key = $sitejsonData->active_power_kva->add;
                            $addValue = '_';

                            foreach ($eventData as $event) {
                                $eventArray = $event->getArrayCopy();
                                if ($eventArray['module_id'] == $sitejsonData->active_power_kva->md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = $eventArray[$key];
                                    }
                                    break;
                                }
                            }
                        ?>
                        <div class="card mb-1 shadow-sm border-0">
    <div class="card-body text-center">
        <div class="d-flex align-items-center gap-4 mb-1">
            <!-- Icon (Bolt) on the left -->
            <i class="fas fa-bolt" style="color: goldenrod; font-size: 40px; margin-right: 60px;"></i>
            
            <!-- Active power, kVA Label and Value on the right -->
            <div>
                <div style="font-weight: bold; font-size: 16px; color: #333;">
                    Active power, kVA
                </div>
                <div style="width: 40px; height: 40px; margin: auto;">
                    <!-- Circular background for value -->
                    <div
                        style="background-color: #d6d6d6; border-radius: 50%; width: 80%; height: 80%; position: relative; text-align: center; line-height: 30px;">
                        <span style="font-size: 14px; color: #333;">
                            {{ $addValue }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                                                <!-- KW -->
                                                <div class="row">
                                                    <!-- <div class="col-xs-4 text-start">KW</div>
                                                    <div class="col text-center">12.2</div>
                                                    <div class="col text-center">12</div>
                                                    <div class="col text-center">12</div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Alarm Status Section -->
                                    <div class="col-md-3 bg-white rounded">
                                        <div class="card shadow-sm " style="height: 605px; width: 100%; font-size: 13px; margin-top:-20px">
                                            <div class="card-header text-center text-white fs-6 fw-bold" style="background: #002E6E;">
    ALARM STATUS
</div>
<div style="display: flex; flex-direction: row; padding: 15px; gap: 15px; flex-wrap: wrap; justify-content: center;">
    <!-- NO ALARM -->
    <div style="display: flex; align-items: center; margin-bottom: 10px;">
        <div style="
            width: 20px; 
            height: 20px; 
            border-radius: 50%; 
            animation: blink 1.5s infinite; 
            border: 2px solid #ccc; 
            background-color: palegreen;">
        </div>
        <span style="margin-left: 10px; font-weight: bold; color: palegreen;">NO ALARM</span>
    </div>
    <!-- WARNING -->
    <div style="display: flex; align-items: center; margin-bottom: 10px;">
        <div style="
            width: 20px; 
            height: 20px; 
            border-radius: 50%; 
            animation: blink 1.5s infinite; 
            border: 2px solid #ccc; 
            background-color: orange;">
        </div>
        <span style="margin-left: 10px; font-weight: bold; color: orange;">WARNING</span>
    </div>
    <!-- TRIP -->
    <div style="display: flex; align-items: center; margin-bottom: 10px;">
        <div style="
            width: 20px; 
            height: 20px; 
            border-radius: 50%; 
            animation: blink 1.5s infinite; 
            border: 2px solid #ccc; 
            background-color: red;">
        </div>
        <span style="margin-left: 10px; font-weight: bold; color: red;">TRIP</span>
    </div>
</div>

<style>
    @keyframes blink {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    /* Responsive design for small devices */
    @media (max-width: 600px) {
        div[style*="flex-direction: row"] {
            flex-direction: column; /* Stack items vertically on smaller screens */
            align-items: center;
        }

        div[style*="margin-left: 10px"] {
            margin-left: 5px; /* Reduce spacing for smaller screens */
        }
    }

    /* Ensure proper alignment on larger screens */
    @media (min-width: 600px) {
        div[style*="flex-direction: row"] {
            flex-direction: row; /* Keep items in row on larger screens */
        }
    }
</style>


                                            <div class="card-body p-3 d-flex flex-column justify-content-center">
                                                <div class="row h-100">
                                                    <!-- Left Column -->
                                                    <div class="col-xs-6 border-end border-dark d-flex flex-column justify-content-around">
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->coolant_temperature_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->coolant_temperature_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Coolant Temperature</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->fuel_level_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->fuel_level_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Fuel Level</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->emergency_stop_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->emergency_stop_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Emergency stop</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->high_coolant_temperature_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->high_coolant_temperature_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>High coolant temperature</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->under_speed_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->under_speed_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Under speed</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->fail_to_start_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->fail_to_start_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Fail to start</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->loss_of_speed_sensing_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->loss_of_speed_sensing_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Loss of speed sensing</span>
                                                        </div>
                                                    </div>
                                                    <!-- Right Column -->
                                                    <div class="col-xs-6 d-flex flex-column justify-content-around">
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->oil_pressure_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->oil_pressure_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Oil Pressure</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->battery_level_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->battery_level_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Battery Level</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->low_oil_pressure_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->low_oil_pressure_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Low oil pressure</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->high_oil_temperature_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->high_oil_temperature_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>High oil temperature</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->over_speed_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->over_speed_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Over speed</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->fail_to_come_to_rest_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->fail_to_come_to_rest_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Fail to come to rest</span>
                                                        </div>
                                                        <?php
                                                            $keya = $sitejsonData->alarm_status->generator_low_voltage_status->add;
                                                            $addValue = '_';

                                                            foreach ($eventData as $event) {
                                                                $eventArraya = $event->getArrayCopy();
                                                                if ($eventArraya['module_id'] == $sitejsonData->alarm_status->generator_low_voltage_status->md) {
                                                                    if (array_key_exists($keya, $eventArraya)) {
                                                                        $addValue = $eventArraya[$keya];
                                                                    }
                                                                    break;
                                                                }
                                                            }

                                                            $backgroundColor = 'transparent';
                                                            switch ($addValue) {
                                                                case '1':
                                                                    $backgroundColor = 'green';
                                                                    break;
                                                                case '2':
                                                                    $backgroundColor = 'yellow';
                                                                    break;
                                                                case '3':
                                                                    $backgroundColor = 'red';
                                                                    break;
                                                                case '15':
                                                                    $backgroundColor = 'grey';
                                                                    break;
                                                                case '0':
                                                                default:
                                                                    $backgroundColor = 'transparent';
                                                                    break;
                                                            }
                                                        ?>
                                                        <div class="d-flex align-items-center justify-content-start">
                                                            <span class="me-2 rounded-circle"
                                                                style="width: 10px; height: 10px; background-color: <?php echo $backgroundColor; ?>; animation: blink 1.5s infinite;"></span>
                                                            <span>Generator low voltage</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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