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

<body class="font-family:sans-serif">
    <!-- Genset Section -->
    <div class=" text-center bg-primary text-white py-1 m-1 rounded d-flex justify-content-between   ">
        <a class="navbar-brand " href="#">
            <img src="https://sochiot.com/wp-content/uploads/2022/04/sochiotlogo-re-e1688377669450.png"
                alt="sochiot_Logo" style="width: 120px; height: 40px; background: transparent;">
        </a>
        <h5 class="my-3" style="padding-right: 117px;">GENSET</h5>
        <h5 class="my-3"></h5>
    </div>

    <!-- Device Info Section -->
    <div class=" mt-2 ml-5 p-3 shadow-lg p-3 mb-5 bg-white shadow-lg p-3 mb-5 bg-white rounded">
        <div class="row mt-1" id="event-data">
            <!-- First Card -->
            <div class="col-md-3  ">
                <div class="card shadow g-5" style="width: 100%; height: 605px; overflow: hidden; margin-top:-20px ">
                    <div class="card-header bg-light   text-start text-center  ">
                        <!-- <div ></div> -->
                        <div class="card-header text-center bg-secondary text-white fs-6 fw-bold">
                            Asset name: {{ $sitejsonData->asset_name }}
                        </div>
                        <div style="font-size: 13px;  ">
                            Group: {{ $sitejsonData->group }} <br />
                            Generator: {{ $sitejsonData->generator }} <br />
                            S/N: {{ $sitejsonData->serial_number }} <br />
                            Model: {{ $sitejsonData->model }} <br />
                            Brand: {{ $sitejsonData->brand }} <br />
                            Capacity: {{ $sitejsonData->capacity }}
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
                            <div class="card-body text-center">
                                <div class=" ">Run status</div>
                                <div class="text-info fs-6">
                                    <i class="fas fa-cogs" style="color: teal; font-size: 20px; margin-right: 8px;"></i>
                                    Running
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card mb-1 shadow-sm border-0">
                            <div class="card-body text-center">
                                <div class=" ">Run status</div>
                                <div class="text-info fs-6">
                                    <i class="fas fa-cogs" style="color: teal; font-size: 20px; margin-right: 8px;"></i>
                                    <div style="color:red;">Stop</div>
                                </div>
                            </div>
                        </div>
                        @endif
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
                        <div class="card mb-1 shadow-sm border-0 ">
                            <div class="card-body text-center">
                                <div class="  mb-1">Active power, kW</div>
                                <div style="width: 40px; height: 40px; margin: auto;">
                                    <div
                                        style="background-color: #d6d6d6; border-radius: 50%; width: 80%; height: 80%; position: relative; text-align: center; line-height: 30px;">
                                        <span style="font-size: 13px;  color: #333;">
                                            {{ $addValue }}
                                        </span>
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
                        <div class="card mb-1 shadow-sm border-0 ">
                            <div class="card-body text-center">
                                <div class="  mb-1">Active power, kVA</div>
                                <div style="width: 40px; height: 40px; margin: auto;">
                                    <div
                                        style="background-color: #d6d6d6; border-radius: 50%;width: 80%; height: 80%; 100%; position: relative; text-align: center; line-height: 30px;">
                                        <span style="font-size: 14px; color: #333;">
                                            {{ $addValue }}
                                        </span>
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
                            <div class="card-body text-center d-flex justify-content-around ">
                                <center>
                                    <dotlottie-player id="fuelIndicator"
                                        src="https://lottie.host/673a61fb-6dc7-4428-99db-f704828eb32f/sB24KCBCEk.lottie"
                                        background="transparent" speed="1" style="width: 50px; height: 50px" loop
                                        autoplay>
                                    </dotlottie-player>
                                </center>
                                <div>
                                    <div class="fs-6">Fuel - <span id="fuelValue"
                                            class="fw-bold"><?php echo htmlspecialchars($percentage); ?>%</span></div>
                                    <div class="">Total Fuel Liter - <span id="fuelValue"
                                            class="fw-bold"><?php echo htmlspecialchars($totalFuelLiters); ?>L</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Engine Parameters Section -->
            <div class="col-md-3 bg-white rounded">
                <div class="card" style="width: 100%; height: 605px; margin-top:-20px">
                    <div class="card-header text-center bg-secondary text-white fw-bold">
                        ENGINE ELECTRONICS
                    </div>
                    <div class="card-body" style="font-size: 13px; padding: 8px;">
                        <div class="row d-flex justify-content-center">
                            <!-- Coolant Temperature -->
                            <?php
                                $key = $sitejsonData->parameters->coolant_temperature->add;
                                $addValue = '_';
                                foreach ($eventData as $event) {
                                    $eventArray = $event->getArrayCopy();
                                    if ($eventArray['module_id'] == $sitejsonData->parameters->coolant_temperature->md) {
                                        if (array_key_exists($key, $eventArray)) {
                                            $addValue = number_format($eventArray[$key], 4);
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
                                    <li class="text-start">{{ $addValue }}</li>
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
                                            $addValue = number_format($eventArray[$key], 4);
                                        }
                                        break;
                                    }
                                }
                            ?>
                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li><i class="fas fa-oil-can text-warning" style="font-size: 2rem;"></i></li>
                                    <li class="text-start flex-grow-1" style="margin-left:40px">Oil Temperature</li>
                                    <li class="text-start">{{ $addValue }}</li>
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
                                            $addValue = number_format($eventArray[$key], 4);
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
                                            $addValue = number_format($eventArray[$key], 4);
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
                                            $addValue = number_format($eventArray[$key], 4);
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
                                            $addValue = number_format((float)$eventArray[$key], 2);
                                        }
                                        break;
                                    }
                                }
                            ?>
                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li><i class="fas fa-battery-half text-info" style="font-size: 2rem;"></i></li>
                                    <li class="text-start flex-grow-1" style="margin-left:50px">Battery Voltage</li>
                                    <li class="text-start">{{ $addValue }}</li>
                                </ul>
                            </div>
                            <hr />

                            <!-- Running Hours -->
                            <?php
                                $key = $sitejsonData->running_hours->add;
                                $addValue = '_';
                                foreach ($eventData as $event) {
                                    $eventArray = $event->getArrayCopy();
                                    if ($eventArray['module_id'] == $sitejsonData->running_hours->md) {
                                        if (array_key_exists($key, $eventArray)) {
                                            $addValue = number_format((float)$eventArray[$key], 2);
                                        }
                                        break;
                                    }
                                }
                            ?>
                            <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                    <li><i class="fas fa-clock text-secondary" style="font-size: 2rem;"></i></li>
                                    <li class="text-start flex-grow-1" style="margin-left:55px">Running Hours</li>
                                    <li class="text-start">{{ $addValue }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Electric Parameters Section -->
            <div class="col-md-3 bg-white rounded">
                <div class="card " style="width: 100%; height: 605px; font-size:13px; margin-top:-20px">
                    <div class="card-header text-center bg-secondary text-white fs-6 fw-bold">
                        ELECTRIC PARAMETERS
                    </div>
                    <div class="card-body" style="font-size: 13px; padding: 8px;">
                        <!-- Table Header -->
                        <div class="row border-bottom pb-3 mb-3">
                            <div class="col-4 text-start  ">Heading</div>
                            <div class="col text-center">A</div>
                            <div class="col text-center">B</div>
                            <div class="col text-center">C</div>
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
                            <div class="col-4 text-start  ">Voltage L-L,V</div>
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
                        <div class="row mb-2">
                            <div class="col-5 text-start">Voltage L-N, V</div>
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

                            // Format the values to 2 decimal places
                            $formattedValueaa = number_format($addValueaa, 2);
                            $formattedValuebb = number_format($addValuebb, 2);
                            $formattedValuecc = number_format($addValuecc, 2);
                        ?>

                        <!-- Current -->
                        <div class="row mb-2">
                            <div class="col-4 text-start ">Current, A</div>
                            <div class="col text-center">{{$formattedValueaa}}</div>
                            <div class="col text-center">{{$formattedValuebb}}</div>
                            <div class="col text-center">{{$formattedValuecc}}</div>
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
                        <div class="row mb-2">
                            <div class="col-4 text-start ">Pf-data</div>
                            <div class="col text-center">{{$formattedpfValue}}</div>
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
                            <div class="col-4 text-start ">Frequency</div>
                            <div class="col text-center">{{$formattedfrequencyValue}}</div>
                        </div>
                        <hr />
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
                        <!-- / total kwh -->
                        <div class="card mb-2  border-0;">
                            <div class="card-body text-center">
                                <div class="text-secondary ">Total kWh</div>
                                <div>
                                    <i class="fas fa-bolt"
                                        style="color: goldenrod; font-size: 20px; margin-bottom: 5px;"></i>
                                </div>
                                <div class=" fs-6">{{$formattedKwhValue}}</div>
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
                    <div class="card-header text-center bg-secondary text-white fs-6 fw-bold">
                        ALARM STATUS
                    </div>
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
                                        <div class="card shadow g-5" style="width: 100%; height: 605px; overflow: hidden; margin-top:-20px ">
                                            <div class="card-header bg-light   text-start text-center  ">
                                                <!-- <div ></div> -->
                                                <div class="card-header text-center bg-secondary text-white fs-6 fw-bold">
                                                    Asset name: {{ $sitejsonData->asset_name }}
                                                </div>
                                                <div style="font-size: 13px;  ">
                                                    Group: {{ $sitejsonData->group }} <br />
                                                    Generator: {{ $sitejsonData->generator }} <br />
                                                    S/N: {{ $sitejsonData->serial_number }} <br />
                                                    Model: {{ $sitejsonData->model }} <br />
                                                    Brand: {{ $sitejsonData->brand }} <br />
                                                    Capacity: {{ $sitejsonData->capacity }}
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
                                                    <div class="card-body text-center">
                                                        <div class=" ">Run status</div>
                                                        <div class="text-info fs-6">
                                                            <i class="fas fa-cogs" style="color: teal; font-size: 20px; margin-right: 8px;"></i>
                                                            Running
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="card mb-1 shadow-sm border-0">
                                                    <div class="card-body text-center">
                                                        <div class=" ">Run status</div>
                                                        <div class="text-info fs-6">
                                                            <i class="fas fa-cogs" style="color: teal; font-size: 20px; margin-right: 8px;"></i>
                                                            <div style="color:red;">Stop</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
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
                                                <div class="card mb-1 shadow-sm border-0 ">
                                                    <div class="card-body text-center">
                                                        <div class="  mb-1">Active power, kW</div>
                                                        <div style="width: 40px; height: 40px; margin: auto;">
                                                            <div
                                                                style="background-color: #d6d6d6; border-radius: 50%; width: 80%; height: 80%; position: relative; text-align: center; line-height: 30px;">
                                                                <span style="font-size: 13px;  color: #333;">
                                                                    {{ $addValue }}
                                                                </span>
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
                                                <div class="card mb-1 shadow-sm border-0 ">
                                                    <div class="card-body text-center">
                                                        <div class="  mb-1">Active power, kVA</div>
                                                        <div style="width: 40px; height: 40px; margin: auto;">
                                                            <div
                                                                style="background-color: #d6d6d6; border-radius: 50%;width: 80%; height: 80%; 100%; position: relative; text-align: center; line-height: 30px;">
                                                                <span style="font-size: 14px; color: #333;">
                                                                    {{ $addValue }}
                                                                </span>
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
                                                    <div class="card-body text-center d-flex justify-content-around ">
                                                        <center>
                                                            <dotlottie-player id="fuelIndicator"
                                                                src="https://lottie.host/673a61fb-6dc7-4428-99db-f704828eb32f/sB24KCBCEk.lottie"
                                                                background="transparent" speed="1" style="width: 50px; height: 50px" loop
                                                                autoplay>
                                                            </dotlottie-player>
                                                        </center>
                                                        <div>
                                                            <div class="fs-6">Fuel - <span id="fuelValue"
                                                                    class="fw-bold"><?php echo htmlspecialchars($percentage); ?>%</span></div>
                                                            <div class="">Total Fuel Liter - <span id="fuelValue"
                                                                    class="fw-bold"><?php echo htmlspecialchars($totalFuelLiters); ?>L</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Engine Parameters Section -->
                                    <div class="col-md-3 bg-white rounded">
                                        <div class="card" style="width: 100%; height: 605px; margin-top:-20px">
                                            <div class="card-header text-center bg-secondary text-white fw-bold">
                                                ENGINE ELECTRONICS
                                            </div>
                                            <div class="card-body" style="font-size: 13px; padding: 8px;">
                                                <div class="row d-flex justify-content-center">
                                                    <!-- Coolant Temperature -->
                                                    <?php
                                                        $key = $sitejsonData->parameters->coolant_temperature->add;
                                                        $addValue = '_';
                                                        foreach ($eventData as $event) {
                                                            $eventArray = $event->getArrayCopy();
                                                            if ($eventArray['module_id'] == $sitejsonData->parameters->coolant_temperature->md) {
                                                                if (array_key_exists($key, $eventArray)) {
                                                                    $addValue = number_format($eventArray[$key], 4);
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
                                                            <li class="text-start">{{ $addValue }}</li>
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
                                                                    $addValue = number_format($eventArray[$key], 4);
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    ?>
                                                    <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                                        <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                                            <li><i class="fas fa-oil-can text-warning" style="font-size: 2rem;"></i></li>
                                                            <li class="text-start flex-grow-1" style="margin-left:40px">Oil Temperature</li>
                                                            <li class="text-start">{{ $addValue }}</li>
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
                                                                    $addValue = number_format($eventArray[$key], 4);
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
                                                                    $addValue = number_format($eventArray[$key], 4);
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
                                                                    $addValue = number_format($eventArray[$key], 4);
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
                                                                    $addValue = number_format((float)$eventArray[$key], 2);
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    ?>
                                                    <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                                        <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                                            <li><i class="fas fa-battery-half text-info" style="font-size: 2rem;"></i></li>
                                                            <li class="text-start flex-grow-1" style="margin-left:50px">Battery Voltage</li>
                                                            <li class="text-start">{{ $addValue }}</li>
                                                        </ul>
                                                    </div>
                                                    <hr />

                                                    <!-- Running Hours -->
                                                    <?php
                                                        $key = $sitejsonData->running_hours->add;
                                                        $addValue = '_';
                                                        foreach ($eventData as $event) {
                                                            $eventArray = $event->getArrayCopy();
                                                            if ($eventArray['module_id'] == $sitejsonData->running_hours->md) {
                                                                if (array_key_exists($key, $eventArray)) {
                                                                    $addValue = number_format((float)$eventArray[$key], 2);
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    ?>
                                                    <div class="col-12 d-flex justify-content-start align-items-center py-2">
                                                        <ul class="list-unstyled d-flex align-items-center justify-content-start w-100">
                                                            <li><i class="fas fa-clock text-secondary" style="font-size: 2rem;"></i></li>
                                                            <li class="text-start flex-grow-1" style="margin-left:55px">Running Hours</li>
                                                            <li class="text-start">{{ $addValue }}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Electric Parameters Section -->
                                    <div class="col-md-3 bg-white rounded">
                                        <div class="card " style="width: 100%; height: 605px; font-size:13px; margin-top:-20px">
                                            <div class="card-header text-center bg-secondary text-white fs-6 fw-bold">
                                                ELECTRIC PARAMETERS
                                            </div>
                                            <div class="card-body" style="font-size: 13px; padding: 8px;">
                                                <!-- Table Header -->
                                                <div class="row border-bottom pb-3 mb-3">
                                                    <div class="col-4 text-start  ">Heading</div>
                                                    <div class="col text-center">A</div>
                                                    <div class="col text-center">B</div>
                                                    <div class="col text-center">C</div>
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
                                                    <div class="col-4 text-start  ">Voltage L-L,V</div>
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
                                                <div class="row mb-2">
                                                    <div class="col-5 text-start">Voltage L-N, V</div>
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

                                                    // Format the values to 2 decimal places
                                                    $formattedValueaa = number_format($addValueaa, 2);
                                                    $formattedValuebb = number_format($addValuebb, 2);
                                                    $formattedValuecc = number_format($addValuecc, 2);
                                                ?>

                                                <!-- Current -->
                                                <div class="row mb-2">
                                                    <div class="col-4 text-start ">Current, A</div>
                                                    <div class="col text-center">{{$formattedValueaa}}</div>
                                                    <div class="col text-center">{{$formattedValuebb}}</div>
                                                    <div class="col text-center">{{$formattedValuecc}}</div>
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
                                                <div class="row mb-2">
                                                    <div class="col-4 text-start ">Pf-data</div>
                                                    <div class="col text-center">{{$formattedpfValue}}</div>
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
                                                    <div class="col-4 text-start ">Frequency</div>
                                                    <div class="col text-center">{{$formattedfrequencyValue}}</div>

                                                </div>
                                                <hr />
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
                                                <!-- / total kwh -->
                                                <div class="card mb-2  border-0;">
                                                    <div class="card-body text-center">
                                                        <div class="text-secondary ">Total kWh</div>
                                                        <div>
                                                            <i class="fas fa-bolt"
                                                                style="color: goldenrod; font-size: 20px; margin-bottom: 5px;"></i>
                                                        </div>
                                                        <div class=" fs-6">{{$formattedKwhValue}}</div>
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
                                            <div class="card-header text-center bg-secondary text-white fs-6 fw-bold">
                                                ALARM STATUS
                                            </div>
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