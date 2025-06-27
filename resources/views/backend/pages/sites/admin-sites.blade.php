<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DG SET MONITORING SYSTEM</title>
    <link rel="stylesheet" href="{{url('backend/assets/css/admin.css')}}">
    <link rel="stylesheet" href="{{url('backend/assets/css/admin-all-min.css')}}">
    <link rel="stylesheet" href="{{url('backend/assets/css/admin-bootstrap-min.css')}}">
    <script src="{{url('backend/assets/js/admin-bootstrap.bundle.js')}}"></script>
    <script src="{{url('backend/assets/js/adminCDN.js')}}"></script>
</head>

<style>
/* Custom Navbar Styles */
.custom-navbar {
    background: linear-gradient(135deg, #002E6E 0%, #004a8f 100%) !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    padding: 0.5rem 1rem;
}

.navbar-brand {
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 1.25rem;
    letter-spacing: 0.5px;
}

.navbar-brand img {
    margin-right: 12px;
}

.refresh-time-box {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 4px;
    padding: 5px 10px;
    margin-right: 10px;
    font-size: 0.85rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.nav-buttons {
    display: flex;
    align-items: center;
}

.nav-buttons .btn {
    margin-left: 8px;
    font-size: 0.85rem;
    padding: 5px 12px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.nav-buttons .btn:hover {
    transform: translateY(-1px);
}

.btn-refresh {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
}

.btn-logout {
    background-color: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.2);
}

.ONLINE {
    background-color: green;
}

.OFFLINE {
    background-color: red;
}

@media (max-width: 991.98px) {
    .navbar-brand {
        font-size: 1rem;
    }

    .refresh-time-box {
        margin-right: 5px;
        padding: 3px 6px;
        font-size: 0.75rem;
    }

    .nav-buttons .btn {
        font-size: 0.75rem;
        padding: 3px 8px;
    }
}

/* Make logo white */
.logo-white {
    filter: brightness(0) invert(1);

}

/* If you need to change the hover state too */
.logo-white:hover {
    opacity: 0.8;
}

.status-cell {
    width: 50px;
    /* Narrow column */
    text-align: center;
    vertical-align: middle;
    padding: 5px;
}

.status-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: inline-block;
}

/* Colors */
.gateway-dot.ONLINE,
.controller-dot.ONLINE {
    background-color: #28a745;
    /* green */
}

.gateway-dot.OFFLINE,
.controller-dot.OFFLINE {
    background-color: #dc3545;
    /* red */
}

.status-dot.loading {
    width: 14px;
    height: 14px;
    border: 2px solid #ccc;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    display: inline-block;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
        <div class="container-fluid">
            <!-- Logo on the left -->
            <a class="navbar-brand" href="#">
                <img src="https://genset.innovatorautomation.co.in/assets/logo.svg" alt="Logo" width="120" height="40"
                    class="logo-white">
            </a>

            <!-- Centered title -->
            <div class="navbar-brand mx-auto">
                <span class="d-none d-md-inline">DG SET MONITORING SYSTEM</span>
                <span class="d-md-none">DGMS</span> <!-- Shorter version for mobile -->
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <div class="nav-buttons">
                            <div class="refresh-time-box text-light">
                                <i class="fas fa-clock me-1"></i>
                                <span id="lastRefreshTime">
                                    {{ now()->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                                </span>
                            </div>

                            <form id="admin-logout-form" action="{{ route('admin.logout.submit') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>

                            <button class="btn btn-outline-light btn-logout"
                                onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </button>

                            <button class="btn btn-outline-light btn-refresh" onclick="handleRefresh()">
                                <i class="fas fa-sync-alt me-1"></i> Refresh
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Loader (Initially Hidden) -->
    <div id="loader" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
    z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container">
        <!-- Main Dashboard -->
        <div class="dashboard-container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary">Site Overview</h3>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="bankSelect">
                        <option selected>Select Bank</option>
                        @php
                        $uniqueGenerators = collect($siteData)
                        ->map(fn($site) => json_decode($site->data)->generator)
                        ->unique();
                        @endphp

                        @foreach($uniqueGenerators as $generator)
                        <option value="{{ $generator }}">{{ $generator }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" id="locationSelect">
                        <option selected>Select Location</option>
                        @php
                        $uniqueGroups = collect($siteData)
                        ->map(fn($site) => json_decode($site->data)->group)
                        ->unique();
                        @endphp
                        @foreach($uniqueGroups as $group)
                        <option value="{{$group}}">{{$group}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Sites Table -->
            <div class="table-responsive" id="siteTable">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>S.No</th>
                            <th>Site Name</th>
                            <th>RMS Status</th>
                            <th>DG Controller</th>
                            <th>Bank Name</th>
                            <th>Location</th>
                            <th>Id</th>
                            <th>Fuel Level</th>
                            <th>Total Run Hours</th>
                            <th>Updated Date</th>
                            <th>DG Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $i=1; @endphp
                        @foreach ($siteData as $site)
                        @php
                        $sitejsonData = json_decode($site->data, true);
                        $updatedAt = null;
                        $isRecent = false;
                        $formattedUpdatedAt = 'N/A';

                        if (!empty($site->updatedAt) && $site->updatedAt !== 'N/A') {
                        try {
                        $updatedAt = Carbon\Carbon::parse($site->updatedAt)->timezone('Asia/Kolkata');
                        $now = Carbon\Carbon::now('Asia/Kolkata');

                        $isRecent = $updatedAt->diffInHours($now) < 24; $formattedUpdatedAt=$updatedAt->format("d M Y
                            h:i A");
                            } catch (\Exception $e) {
                            \Log::error('Date Parsing Error: ' . $e->getMessage());
                            }
                            }
                            @endphp

                            @php
                            $gatewayStatus = $isRecent ? 'online' : 'offline';
                            $controllerStatus = $isRecent ? 'online' : 'offline';
                            @endphp

                            <tr class="site-row" data-site-id="{{ $site->id }}">
                                <td>{{ $i }}</td>
                                <td style="color: {{ $isRecent ? 'green' : 'red' }};">
                                    <a href="{{ url('admin/sites/'.$site->slug . '?role=admin') }}"
                                        style="text-decoration: none; color: inherit; font-weight: bold;"
                                        target="_blank">
                                        {{ $site->site_name }}
                                    </a>
                                </td>

                                <td class="status-cell">
                                    <div class="status-dot controller-dot"></div>
                                </td>

                                <td class="status-cell">
                                    <div class="status-dot gateway-dot"></div>
                                </td>

                                <td>{{ $sitejsonData['generator'] ?? 'N/A' }}</td>
                                <td>{{ $sitejsonData['group'] ?? 'N/A' }}</td>
                                <td>{{ $sitejsonData['serial_number'] ?? 'N/A' }}</td>
                                <td>
                                    @php
                                    $capacity = $sitejsonData['capacity'] ?? 0;

                                    $fuelMd = $sitejsonData['parameters']['fuel']['md'] ?? null;
                                    $fuelKey = $sitejsonData['parameters']['fuel']['add'] ?? null;

                                    $addValue = 0;

                                    foreach ($eventData as $event) {
                                    $eventArray = $event instanceof \ArrayObject ? $event->getArrayCopy() : (array)
                                    $event;

                                    if ($fuelMd && isset($eventArray['module_id']) && $eventArray['module_id'] ==
                                    $fuelMd) {
                                    if ($fuelKey && array_key_exists($fuelKey, $eventArray)) {
                                    $addValue = $eventArray[$fuelKey];
                                    }
                                    break;
                                    }
                                    }

                                    $percentage = is_numeric($addValue) ? floatval($addValue) : 0;
                                    $percentageDecimal = $percentage / 100;
                                    $totalFuelLiters = $capacity * $percentageDecimal;

                                    $fuelClass = $percentage <= 20 ? 'low-fuel' : 'normal-fuel' ;
                                        $lowFuelText=$percentage <=20 ? 'Low Fuel' : '' ; @endphp <div
                                        class="fuel-container" style="position: relative; width: 100%;">
                                        <div class="fuel-indicator {{ $fuelClass }}"
                                            style="display: flex; align-items: center;">
                                            <div class="fuel-level" style="width: {{ $percentage }}%;"></div>
                                            <span class="fuel-percentage">{{ $percentage }}%</span>
                                        </div>

                                        @if($lowFuelText)
                                        <span class="fueldata">{{ $lowFuelText }}</span>
                                        @endif
            </div>
            </td>


            <td class="running-hours">
                @php
                $increased_running_hours = DB::table('running_hours')->where('site_id', $site->id)->first();
                $increaseRunningHours = (float) ($increased_running_hours->increase_running_hours ?? 0);

                $addValue = 0;
                $key = $sitejsonData['running_hours']['add'] ?? null;
                $md = $sitejsonData['running_hours']['md'] ?? null;

                if ($key && $md) {
                foreach ($eventData as $event) {
                $eventArray = $event->getArrayCopy();
                if (isset($eventArray['module_id']) && $eventArray['module_id'] == $md) {
                if (array_key_exists($key, $eventArray)) {
                $addValue = (float) $eventArray[$key];
                }
                break;
                }
                }
                }

                $increaseMinutes = $sitejsonData['running_hours']['increase_minutes'] ?? 1;
                $inc_addValue = $increaseMinutes > 0 ? $addValue / $increaseMinutes : $addValue;
                $inc_addValueFormatted = round($inc_addValue + $increaseRunningHours, 2);
                $hours = floor($inc_addValueFormatted);
                $minutes = round(($inc_addValueFormatted - $hours) * 60);
                @endphp

                {{ $hours }} hrs {{ $minutes }} mins

            </td>

            <td class="last-updated">
                {{$site->updatedAt}}
            </td>

            <td>
                @php
                $addValuerunstatus = 0;

                // If controller is online, try to calculate DG status
                if ($controllerStatus === 'online') {
                if (isset($sitejsonData['electric_parameters']['voltage_l_l']['a'])) {
                $keya = $sitejsonData['electric_parameters']['voltage_l_l']['a']['add'] ?? null;
                $moduleId = $sitejsonData['electric_parameters']['voltage_l_l']['a']['md'] ?? null;

                foreach ($eventData as $event) {
                $eventArraya = $event->getArrayCopy();
                if ($moduleId && isset($eventArraya['module_id']) && $eventArraya['module_id'] == $moduleId) {
                if ($keya && array_key_exists($keya, $eventArraya)) {
                $addValuerunstatus = $eventArraya[$keya];
                }
                break;
                }
                }
                }
                }
                @endphp

                {{-- Show status based on controller status --}}
                @if($controllerStatus === 'offline')
                <strong style="font-size: 1.3rem; font-weight: bold;">—</strong>
                @elseif($addValuerunstatus > 0)
                <span class="status-running blinking">ON</span>
                @else
                <span class="status-stopped">OFF</span>
                @endif
            </td>

            </tr>
            @php $i=$i+1; @endphp
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
    </div>

    <script src="{{url('backend/assets/js/admin-jquery-3.6.0.min.js')}}"></script>

    <script>
    function updateRefreshTime() {
        const now = new Date();
        const options = {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        };
        const formattedTime = now.toLocaleString('en-IN', options);
        document.getElementById('lastRefreshTime').textContent = `Last refreshed: ${formattedTime}`;
    }

    function handleRefresh() {
        // Show loading spinner
        $('#loader').show();

        // Update the refresh time immediately
        updateRefreshTime();

        // Reload the page after a short delay to allow the spinner to show
        setTimeout(() => {
            location.reload();
        }, 500);
    }

    $(document).ready(function() {
        $('#bankSelect, #locationSelect').change(function() {
            let bankName = $('#bankSelect').val();
            let location = $('#locationSelect').val();

            $.ajax({
                url: "{{ route('admin.admin.sites') }}",
                method: "GET",
                data: {
                    bank_name: bankName,
                    location: location
                },
                beforeSend: function() {
                    $('#loader').show();
                },
                success: function(response) {
                    $('#siteTable').html(response.html);
                    // Update refresh time after successful filter
                    updateRefreshTime();
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
    </script>

    <script>
    $(document).ready(function() {
        let siteIds = [];

        $('.site-row').each(function() {
            siteIds.push($(this).data('site-id'));

            // Show loader before AJAX
            $(this).find('.gateway-dot, .controller-dot')
                .removeClass('ONLINE OFFLINE')
                .addClass('loading');
        });

        if (siteIds.length > 0) {
            $.ajax({
                url: '{{ route("admin.site.statuses") }}',
                method: 'POST',
                data: {
                    site_ids: siteIds,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    // (Already handled above per site-row)
                },
                success: function(data) {
                    $.each(data, function(siteId, statuses) {
                        let row = $('.site-row[data-site-id="' + siteId + '"]');

                        let dgStatus = (statuses.dg_status || '').replace(/['"]+/g, '');
                        let ctrlStatus = (statuses.controller_status || '').replace(
                            /['"]+/g, '');

                        row.find('.gateway-dot')
                            .removeClass('loading ONLINE OFFLINE')
                            .addClass(dgStatus);

                        row.find('.controller-dot')
                            .removeClass('loading ONLINE OFFLINE')
                            .addClass(ctrlStatus);
                    });
                },
                error: function() {
                    // fallback: mark all as offline on error
                    $('.gateway-dot, .controller-dot')
                        .removeClass('loading ONLINE')
                        .addClass('OFFLINE');
                }
            });
        }
    });
    </script>

</body>

</html>