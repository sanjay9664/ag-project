<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/react-circular-progressbar/2.0.3/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="{{url('backend/assets/css/site-details.css')}}">
    
<style>
/* Wrap both buttons and status together */
.reading-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    height: 180px; /* fixed height to stop layout jumping */
    position: relative;
}

/* Buttons section */
.reading-controls {
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: center;
}

/* Common button design */
.reading-btn {
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    width: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
    font-size: 0.85rem;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.reading-on-btn {
    background-color: #28a745;
    color: #fff;
    border: 1px solid #28a745;
}

.reading-on-btn:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.reading-off-btn {
    background-color: #dc3545;
    color: #fff;
    border: 1px solid #dc3545;
}

.reading-off-btn:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

/* Status box */
.reading-status-box {
    background-color: #ffffff;
    border-radius: 8px;
    padding: 6px 10px;
    text-align: center;
    border: 1px solid #dee2e6;
    margin-top: -2px; /* slightly closer to buttons */
    width: 160px;     /* increased width */
    height: 70px; /* fixed height — prevents movement */
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

/* Label */
.reading-status-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.72rem;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
    text-transform: uppercase;
}

/* Value */
.reading-status-value {
    font-size: 0.9rem;
    font-weight: 700;
}

.status-increasing {
    color: #28a745;
}

.status-normal {
    color: #17a2b8;
}
</style>

</head>

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
                                    <strong>Controller-type:</strong> {{ $sitejsonData->asset_name }}
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
                                    // Get increased running hours from DB
                                    $increased_running_hours = DB::table('running_hours')->where('site_id', $siteData->id)->first();
                                    $increaseRunningHours = (float) ($increased_running_hours->increase_running_hours ?? 0);

                                    $addValue = 0;
                                    $key = $sitejsonData->running_hours->add ?? null;

                                    // Extract addValue from eventData
                                    foreach ($eventData as $event) {
                                        $eventArray = $event->getArrayCopy();
                                        if (
                                            isset($eventArray['module_id']) &&
                                            $eventArray['module_id'] == ($sitejsonData->running_hours->md ?? null)
                                        ) {
                                            if ($key && array_key_exists($key, $eventArray)) {
                                                $rawValue = $eventArray[$key];
                                                if (is_numeric($rawValue)) {
                                                    $addValue = (float) $rawValue;
                                                }
                                            }
                                            break;
                                        }
                                    }

                                    // Calculate increased value per minute
                                    $increaseMinutes = $sitejsonData->running_hours->increase_minutes ?? null;
                                    $inc_addValue = $addValue;

                                    if (is_numeric($increaseMinutes) && (float)$increaseMinutes > 0) {
                                        $inc_addValue /= (float)$increaseMinutes;
                                    }

                                    // Final total running hours
                                    $inc_addValueFormatted = $inc_addValue + $increaseRunningHours;

                                    // Convert to hours and minutes
                                    $hours = floor($inc_addValueFormatted);
                                    $minutes = round(($inc_addValueFormatted - $hours) * 60);
                                ?>

                                <?php
                                    $keya = $sitejsonData->run_status->add ?? null;
                                    $addValuerunstatus = 0.0;

                                    foreach ($eventData as $event) {
                                        $eventArraya = $event->getArrayCopy();
                                        if (
                                            isset($eventArraya['module_id']) &&
                                            $eventArraya['module_id'] == ($sitejsonData->run_status->md ?? null)
                                        ) {
                                            if ($keya && array_key_exists($keya, $eventArraya)) {
                                                $value = $eventArraya[$keya];
                                                if (is_numeric($value)) {
                                                    $addValuerunstatus = (float) $value;
                                                }
                                            }
                                            break;
                                        }
                                    }
                                ?>

                                <td colspan="7">
                                    <div class="run-status-final">
                                        <!-- Left Column - Start/Stop -->
                                        <div class="control-column d-flex align-items-center gap-2">
                                            <form id="start-form" class="m-0 p-0">
                                                <!-- Hidden Inputs -->
                                                <input type="hidden" name="argValue" value="1">
                                                @if(isset($sitejsonData->start_md->md))
                                                <input type="hidden" name="moduleId"
                                                    value="{{ $sitejsonData->start_md->md }}">
                                                @endif

                                                @if(isset($sitejsonData->start_md->add))
                                                <input type="hidden" name="cmdField"
                                                    value="{{ $sitejsonData->start_md->add }}">
                                                @endif

                                                @if(isset($sitejsonData->start_md->argument))
                                                <input type="hidden" name="cmdArg"
                                                    value="{{ $sitejsonData->start_md->argument }}">
                                                @endif

                                                <!-- START Button -->
                                                <button type="button" class="control-btn start-btn btn btn-success">
                                                    <i class="fas fa-play"></i> START
                                                </button>
                                            </form>

                                            <!-- STOP Button -->
                                            <form id="stop-form" class="m-0 p-0">
                                                <!-- Hidden Inputs -->
                                                <input type="hidden" name="argValue" value="1">
                                                @if(isset($sitejsonData->stop_md->md))
                                                <input type="hidden" name="moduleId"
                                                    value="{{ $sitejsonData->stop_md->md }}">
                                                @endif

                                                @if(isset($sitejsonData->stop_md->add))
                                                <input type="hidden" name="cmdField"
                                                    value="{{ $sitejsonData->stop_md->add }}">
                                                @endif

                                                @if(isset($sitejsonData->stop_md->argument))
                                                <input type="hidden" name="cmdArg"
                                                    value="{{ $sitejsonData->stop_md->argument }}">
                                                @endif

                                                <!-- STOP Button -->
                                                <button type="button" class="control-btn stop-btn btn btn-danger">
                                                    <i class="fas fa-stop"></i> STOP
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Center Column - Status Indicators -->
                                        <div class="status-column">
                                            <div class="status-box">
                                                <i class="fas fa-cogs" style="color: teal; font-size: 24px;"></i>
                                                <p class="fw-bold">Run Status</p>
                                                @if($addValuerunstatus > 0)
                                                <span class="badge bg-success px-2 py-1">Running</span>
                                                @else
                                                <span class="badge bg-danger px-2 py-1">Stop</span>
                                                @endif
                                            </div>

                                            <div class="control-column">
                                                <form id="auto-form" class="m-0 p-0">
                                                    <!-- Hidden Inputs -->
                                                    <input type="hidden" name="argValue" value="1">

                                                    @if(isset($sitejsonData->auto_md->md))
                                                    <input type="hidden" name="moduleId"
                                                        value="{{ $sitejsonData->auto_md->md }}">
                                                    @endif

                                                    @if(isset($sitejsonData->auto_md->add))
                                                    <input type="hidden" name="cmdField"
                                                        value="{{ $sitejsonData->auto_md->add }}">
                                                    @endif

                                                    @if(isset($sitejsonData->auto_md->argument))
                                                    <input type="hidden" name="cmdArg"
                                                        value="{{ $sitejsonData->auto_md->argument }}">
                                                    @endif

                                                    <!-- AUTO Button -->
                                                    <button class="control-btn auto-btn active">
                                                        <i class="fas fa-robot"></i> AUTO
                                                    </button>
                                                </form>

                                                <form id="manual-form" class="m-0 p-0">
                                                    <!-- Hidden Inputs -->
                                                    <input type="hidden" name="argValue" value="1">

                                                    @if(isset($sitejsonData->manual_md->md))
                                                    <input type="hidden" name="moduleId"
                                                        value="{{ $sitejsonData->manual_md->md }}">
                                                    @endif

                                                    @if(isset($sitejsonData->manual_md->add))
                                                    <input type="hidden" name="cmdField"
                                                        value="{{ $sitejsonData->manual_md->add }}">
                                                    @endif

                                                    @if(isset($sitejsonData->manual_md->argument))
                                                    <input type="hidden" name="cmdArg"
                                                        value="{{ $sitejsonData->manual_md->argument }}">
                                                    @endif

                                                    <!-- MANUAL Button -->
                                                    <button class="control-btn manual-btn">
                                                        <i class="fas fa-hand-paper"></i> MANUAL
                                                    </button>
                                                </form>

                                            </div>

                                            <?php
                                                $keyaa = $sitejsonData->mode_md->add ?? null;
                                                $addValueModestatus = null;

                                                foreach ($eventData as $event) {
                                                    $eventArraya = $event->getArrayCopy();
                                                    if (
                                                        isset($eventArraya['module_id']) &&
                                                        $eventArraya['module_id'] == ($sitejsonData->mode_md->md ?? null)
                                                    ) {
                                                        if ($keyaa && array_key_exists($keyaa, $eventArraya)) {
                                                            $value = $eventArraya[$keyaa];
                                                            if (is_numeric($value)) {
                                                                $addValueModestatus = (float) $value;
                                                            }
                                                        }
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <div class="mode-display-box">
                                                <div class="mode-label">CURRENT MODE</div>
                                                @if($addValueModestatus === 1.0)
                                                    <div class="mode-value" id="current-mode">MANUAL</div>
                                                @elseif($addValueModestatus === 0.0)
                                                    <div class="mode-value" id="current-mode">AUTO</div>
                                                @else
                                                    <div class="mode-value" id="current-mode">-</div>
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

                                        
                                            <!-- start reading control here -->
                                            <!-- New Reading Controls -->
                                         </div>
                                                                    <div class="reading-controls">
                                                               <form id="reading-on-form" class="m-0 p-0">
                                                            <!-- Hidden Inputs -->
                                                            <input type="hidden" name="argValue" value="1">
                                                            @if(isset($sitejsonData->readOn_md->md))
                                                            <input type="hidden" name="moduleId"
                                                                value="{{ $sitejsonData->readOn_md->md }}">
                                                            @endif

                                                            @if(isset($sitejsonData->readOn_md->add))
                                                            <input type="hidden" name="cmdField"
                                                                value="{{ $sitejsonData->readOn_md->add }}">
                                                            @endif

                                                            @if(isset($sitejsonData->readOn_md->argument))
                                                            <input type="hidden" name="cmdArg"
                                                                value="{{ $sitejsonData->readOn_md->argument }}">
                                                            @endif

                                                            <!-- Reading On Button -->
                                                            <button type="button" class="reading-btn reading-on-btn btn btn-sm px-2 py-1">
                                                            <i class="fas fa-toggle-on"></i> Reading (On)
                                                            </button>

                                                        </form>

                                                <form id="reading-off-form" class="m-0 p-0">
                                                    <!-- Hidden Inputs -->
                                                    <input type="hidden" name="argValue" value="1">
                                                    @if(isset($sitejsonData->readOff_md->md))
                                                    <input type="hidden" name="moduleId"
                                                        value="{{ $sitejsonData->readOff_md->md }}">
                                                    @endif

                                                    @if(isset($sitejsonData->readOff_md->add))
                                                    <input type="hidden" name="cmdField"
                                                        value="{{ $sitejsonData->readOff_md->add }}">
                                                    @endif

                                                    @if(isset($sitejsonData->readOff_md->argument))
                                                    <input type="hidden" name="cmdArg"
                                                        value="{{ $sitejsonData->readOff_md->argument }}">
                                                    @endif

                                                    <!-- Reading Off Button -->
                                                    <button type="button" class="reading-btn reading-off-btn btn btn-sm px-2 py-1">
                                                      <i class="fas fa-toggle-off"></i> Reading (Off)
                                                      </button>

                                                </form>
                                            </div>
                                            <!-- Reading Status Display -->
                                            <div class="reading-status-box">
                                                <div class="reading-status-label">READING STATUS</div>
                                                <div class="reading-status-value" id="reading-status">
                                                    <?php
                                                        // Determine reading status based on your logic
                                                        // This is a placeholder - replace with your actual logic
                                                        $readingStatus = "Increasing"; // or "Normal"
                                                    ?>
                                                    @if($readingStatus == "Increasing")
                                                        <span class="status-increasing">Increasing</span>
                                                    @else
                                                        <span class="status-normal">Normal</span>
                                                    @endif
                                                </div>
                                            </div>
                                                                </div>
                                            
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                                                            <strong>Controller-type:</strong> {{ $sitejsonData->asset_name }}
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
                                                            <div class="run-status-final">
                                                                <!-- Left Column - Start/Stop -->
                                                               <div class="control-column d-flex align-items-center gap-2">
                                                                    <form id="start-form" class="m-0 p-0">
                                                                        <!-- Hidden Inputs -->
                                                                        <input type="hidden" name="argValue" value="1">
                                                                        @if(isset($sitejsonData->start_md->md))
                                                                        <input type="hidden" name="moduleId"
                                                                            value="{{ $sitejsonData->start_md->md }}">
                                                                        @endif

                                                                        @if(isset($sitejsonData->start_md->add))
                                                                        <input type="hidden" name="cmdField"
                                                                            value="{{ $sitejsonData->start_md->add }}">
                                                                        @endif

                                                                        @if(isset($sitejsonData->start_md->argument))
                                                                        <input type="hidden" name="cmdArg"
                                                                            value="{{ $sitejsonData->start_md->argument }}">
                                                                        @endif

                                                                        <!-- START Button -->
                                                                        <button type="button" class="control-btn start-btn btn btn-success">
                                                                            <i class="fas fa-play"></i> START
                                                                        </button>
                                                                    </form>

                                                                    <!-- STOP Button -->
                                                                    <form id="stop-form" class="m-0 p-0">
                                                                        <!-- Hidden Inputs -->
                                                                        <input type="hidden" name="argValue" value="1">
                                                                        @if(isset($sitejsonData->stop_md->md))
                                                                        <input type="hidden" name="moduleId"
                                                                            value="{{ $sitejsonData->stop_md->md }}">
                                                                        @endif

                                                                        @if(isset($sitejsonData->stop_md->add))
                                                                        <input type="hidden" name="cmdField"
                                                                            value="{{ $sitejsonData->stop_md->add }}">
                                                                        @endif

                                                                        @if(isset($sitejsonData->stop_md->argument))
                                                                        <input type="hidden" name="cmdArg"
                                                                            value="{{ $sitejsonData->stop_md->argument }}">
                                                                        @endif

                                                                        <!-- STOP Button -->
                                                                        <button type="button" class="control-btn stop-btn btn btn-danger">
                                                                            <i class="fas fa-stop"></i> STOP
                                                                        </button>
                                                                    </form>
                                                                </div>

                                                                <!-- Center Column - Status Indicators -->
                                                                <div class="status-column">
                                                                    <div class="status-box">
                                                                        <i class="fas fa-cogs" style="color: teal; font-size: 24px;"></i>
                                                                        <p class="fw-bold">Run Status</p>
                                                                        @if($addValuerunstatus > 0)
                                                                        <span class="badge bg-success px-2 py-1">Running</span>
                                                                        @else
                                                                        <span class="badge bg-danger px-2 py-1">Stop</span>
                                                                        @endif
                                                                    </div>
                                        
                                                                    <div class="control-column">
                                                                        <form id="auto-form" class="m-0 p-0">
                                                                            <!-- Hidden Inputs -->
                                                                            <input type="hidden" name="argValue" value="1">

                                                                            @if(isset($sitejsonData->auto_md->md))
                                                                                <input type="hidden" name="moduleId" value="{{ $sitejsonData->auto_md->md }}">
                                                                            @endif

                                                                            @if(isset($sitejsonData->auto_md->add))
                                                                                <input type="hidden" name="cmdField" value="{{ $sitejsonData->auto_md->add }}">
                                                                            @endif

                                                                            @if(isset($sitejsonData->auto_md->argument))
                                                                                <input type="hidden" name="cmdArg" value="{{ $sitejsonData->auto_md->argument }}">
                                                                            @endif

                                                                            <!-- AUTO Button -->
                                                                            <button class="control-btn auto-btn active">
                                                                                <i class="fas fa-robot"></i> AUTO
                                                                            </button>
                                                                        </form>

                                                                        <form id="manual-form" class="m-0 p-0">
                                                                            <!-- Hidden Inputs -->
                                                                            <input type="hidden" name="argValue" value="1">
                                                                            @if(isset($sitejsonData->manual_md->md))
                                                                                <input type="hidden" name="moduleId" value="{{ $sitejsonData->manual_md->md }}">
                                                                            @endif

                                                                            @if(isset($sitejsonData->manual_md->add))
                                                                                <input type="hidden" name="cmdField" value="{{ $sitejsonData->manual_md->add }}">
                                                                            @endif

                                                                            @if(isset($sitejsonData->manual_md->argument))
                                                                                <input type="hidden" name="cmdArg" value="{{ $sitejsonData->manual_md->argument }}">
                                                                            @endif

                                                                            <!-- MANUAL Button -->
                                                                            <button class="control-btn manual-btn">
                                                                                <i class="fas fa-hand-paper"></i> MANUAL
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                    
                                                                        <?php
                                                                        $keyaa = $sitejsonData->mode_md->add ?? null;
                                                                        $addValueModestatus = null;

                                                                        foreach ($eventData as $event) {
                                                                            $eventArraya = $event->getArrayCopy();
                                                                            if (
                                                                                isset($eventArraya['module_id']) &&
                                                                                $eventArraya['module_id'] == ($sitejsonData->mode_md->md ?? null)
                                                                            ) {
                                                                                if ($keyaa && array_key_exists($keyaa, $eventArraya)) {
                                                                                    $value = $eventArraya[$keyaa];
                                                                                    if (is_numeric($value)) {
                                                                                        $addValueModestatus = (float) $value;
                                                                                    }
                                                                                }
                                                                                break;
                                                                            }
                                                                        }
                                                                    ?>
                                                                                        <div class="mode-display-box">
                                                                                            <div class="mode-label">CURRENT MODE</div>
                                                                                            @if($addValueModestatus === 1.0)
                                                                                                <div class="mode-value" id="current-mode">MANUAL</div>
                                                                                            @elseif($addValueModestatus === 0.0)
                                                                                                <div class="mode-value" id="current-mode">AUTO</div>
                                                                                            @else
                                                                                                <div class="mode-value" id="current-mode">-</div>
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

                                                    <!-- start reading control here -->
                                                    <!-- New Reading Controls -->
                                                    </div>
                                                                                <div class="reading-controls">
                                                                    <form id="reading-on-form" class="m-0 p-0">
                                                                        <!-- Hidden Inputs -->
                                                                        <input type="hidden" name="argValue" value="1">
                                                                        @if(isset($sitejsonData->readOn_md->md))
                                                                        <input type="hidden" name="moduleId"
                                                                            value="{{ $sitejsonData->readOn_md->md }}">
                                                                        @endif

                                                                        @if(isset($sitejsonData->readOn_md->add))
                                                                        <input type="hidden" name="cmdField"
                                                                            value="{{ $sitejsonData->readOn_md->add }}">
                                                                        @endif

                                                                        @if(isset($sitejsonData->readOn_md->argument))
                                                                        <input type="hidden" name="cmdArg"
                                                                            value="{{ $sitejsonData->readOn_md->argument }}">
                                                                        @endif

                                                                        <!-- Reading On Button -->
                                                                        <button type="button" class="reading-btn reading-on-btn btn btn-sm px-2 py-1">
                                                                        <i class="fas fa-toggle-on"></i> Reading (On)
                                                                        </button>

                                                                    </form>

                                                            <form id="reading-off-form" class="m-0 p-0">
                                                                <!-- Hidden Inputs -->
                                                                <input type="hidden" name="argValue" value="1">
                                                                @if(isset($sitejsonData->readOff_md->md))
                                                                <input type="hidden" name="moduleId"
                                                                    value="{{ $sitejsonData->readOff_md->md }}">
                                                                @endif

                                                                @if(isset($sitejsonData->readOff_md->add))
                                                                <input type="hidden" name="cmdField"
                                                                    value="{{ $sitejsonData->readOff_md->add }}">
                                                                @endif

                                                                @if(isset($sitejsonData->readOff_md->argument))
                                                                <input type="hidden" name="cmdArg"
                                                                    value="{{ $sitejsonData->readOff_md->argument }}">
                                                                @endif

                                                                <!-- Reading Off Button -->
                                                                <button type="button" class="reading-btn reading-off-btn btn btn-sm px-2 py-1">
                                                                <i class="fas fa-toggle-off"></i> Reading (Off)
                                                                </button>

                                                            </form>
                                                        </div>
                                                        <!-- Reading Status Display -->
                                                        <div class="reading-status-box">
                                                            <div class="reading-status-label">READING STATUS</div>
                                                            <div class="reading-status-value" id="reading-status">
                                                                <?php
                                                                    // Determine reading status based on your logic
                                                                    // This is a placeholder - replace with your actual logic
                                                                    $readingStatus = "Increasing"; // or "Normal"
                                                                ?>
                                                                @if($readingStatus == "Increasing")
                                                                    <span class="status-increasing">Increasing</span>
                                                                @else
                                                                    <span class="status-normal">Normal</span>
                                                                @endif
                                                            </div>
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
    // setInterval(fetchSiteData, 10000);
    setInterval(fetchSiteData, 10000000);
    </script>

    <script>
    $(document).on('click', '.start-btn, .stop-btn, .auto-btn, .manual-btn, .reading-on-btn, .reading-off-btn', function(e) {
        e.preventDefault();

        let form = $(this).closest('form');
        let actionType = '';

        if ($(this).hasClass('start-btn')) {
            actionType = 'start';
        } else if ($(this).hasClass('stop-btn')) {
            actionType = 'stop';
        } else if ($(this).hasClass('auto-btn')) {
            actionType = 'auto';
        } else if ($(this).hasClass('manual-btn')) {
            actionType = 'manual';
        } else if ($(this).hasClass('reading-on-btn')) {
            actionType = 'reading_on';
        } else if ($(this).hasClass('reading-off-btn')) {
            actionType = 'reading_off';
        }

        let argValue = form.find('input[name="argValue"]').val();
        let moduleId = form.find('input[name="moduleId"]').val();
        let cmdField = form.find('input[name="cmdField"]').val();
        let cmdArg = form.find('input[name="cmdArg"]').val();

        if (!argValue || !moduleId || !cmdField || !cmdArg) {
            Swal.fire({
                icon: 'warning',
                title: 'Service Not Active',
                text: 'Service is not active for this site, kindly contact the team!',
                confirmButtonText: 'OK'
            });
            return;
        }

        const ajaxCall = () => {
            $.ajax({
                url: '/admin/start-process',
                method: 'POST',
                data: {
                    argValue,
                    moduleId,
                    cmdField,
                    cmdArg,
                    actionType,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: `${actionType.charAt(0).toUpperCase() + actionType.slice(1)}ed!`,
                        text: response.message
                    });

                    console.log('External Response:', response.external_response);

                    // ✅ Backend confirmed mode change — only update UI now
                    if (actionType === 'auto' || actionType === 'manual') {
                        if (response.mode_status === 0) {
                            $('#current-mode').text('AUTO');
                        } else if (response.mode_status === 1) {
                            $('#current-mode').text('MANUAL');
                        }
                    }
                    
                    // Update reading status if applicable
                    if (actionType === 'reading_on' || actionType === 'reading_off') {
                        // Update reading status display based on your logic
                        // This is a placeholder - replace with your actual logic
                        if (actionType === 'reading_on') {
                            $('#reading-status').html('<span class="status-increasing">Increasing</span>');
                        } else {
                            $('#reading-status').html('<span class="status-normal">Normal</span>');
                        }
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                    console.error(xhr.responseText);
                }
            });
        };

        if (actionType === 'start') {
            Swal.fire({
                title: 'Are you sure?',
                text: `Are you sure you want to START this genset?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Start',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    ajaxCall();
                }
            });
        } else {
            ajaxCall();
        }
    });
</script>

</body>

</html>