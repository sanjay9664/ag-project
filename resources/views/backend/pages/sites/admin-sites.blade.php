<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DG SET MONITORING SYSTEM</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <link rel="stylesheet" href="{{url('backend/assets/css/admin.css')}}">
    <link rel="stylesheet" href="{{url('backend/assets/css/admin-all-min.css')}}">
    <link rel="stylesheet" href="{{url('backend/assets/css/admin-bootstrap-min.css')}}">
    <!-- <link rel="stylesheet" href=""> -->
    <script src="{{url('backend/assets/js/admin-bootstrap.bundle.js')}}"></script>
    <script src="{{url('backend/assets/js/adminCDN.js')}}"></script>
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

                        <form id="admin-logout-form" action="{{ route('admin.logout.submit') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>

                        <button class="btn btn-outline-light btn-sm mx-1"
                            onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                            Log Out
                        </button>

                        <button class="btn btn-outline-light btn-sm mx-1" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
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
                        @foreach($siteData as $site)
                        <option value="{{ json_decode($site->data)->group }}">{{ json_decode($site->data)->group }}
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

                        if (!empty($site->updatedAt) && $site->updatedAt !== 'N/A') {
                        try {
                        $updatedAt = Carbon\Carbon::parse($site->updatedAt);
                        $now = Carbon\Carbon::now();

                        $isRecent = $updatedAt->diffInHours($now) < 24; } catch (\Exception $e) { \Log::error('Date
                            Parsing Error: ' . $e->getMessage());
                                }
                            }
                        @endphp

                        <tr data-site-id="{{ $site->id }}">
                            <td>{{ $i }}</td>
                            <td style="color: {{ $isRecent ? 'green' : 'red' }};">
                                <a href="{{ url('admin/sites/'.$site->slug . '?role=admin') }}"
                                    style="text-decoration: none; color: inherit;font-weight: bold;" target="_blank">
                                    {{ $site->site_name }}
                                </a>
                            </td>
                            <td>{{ $sitejsonData['generator'] ?? 'N/A' }}</td>
                            <td>{{ $sitejsonData['group'] ?? 'N/A' }}</td>
                            <td>{{ $sitejsonData['serial_number'] ?? 'N/A' }}</td>
                            <td>
                      @php
                                $capacity = $sitejsonData[' capacity'] ?? 0;
                            $fuelMd=$sitejsonData['parameters']['fuel']['md'] ?? null;
                            $fuelKey=$sitejsonData['parameters']['fuel']['add'] ?? null; $addValue='_' ; foreach
                            ($eventData as $event) { $eventArray=$event->getArrayCopy();
                            if ($fuelMd && isset($eventArray['module_id']) && $eventArray['module_id'] == $fuelMd) {
                            if ($fuelKey && array_key_exists($fuelKey, $eventArray)) {
                            $addValue = $eventArray[$fuelKey];
                            }
                            break;
                            }
                            }

                            $percentage = is_numeric($addValue) ? $addValue : 0;
                            $percentageDecimal = $percentage / 100;
                            $totalFuelLiters = $capacity * $percentageDecimal;
                            $fuelClass = $percentage <= 20 ? 'low-fuel' : 'normal-fuel' ; $lowFuelText=$percentage <=20
                                ? 'Low Fuel' : '' ; @endphp <div class="fuel-container"
                                style="position: relative; width: 100%;">
                                <div class="fuel-indicator {{ $fuelClass }}"
                                    style="display: flex; align-items: center;">
                                    <div class="fuel-level">
                                    </div>
                                    <span class="fuel-percentage">{{ $percentage }}%</span>
                                </div>

                                @if($lowFuelText)
                                <span class="fueldata">{{ $lowFuelText }}</span>
                                @endif
            </div>


            </td>



            <td class="running-hours">
                @php
                $increased_running_hours = DB::table('running_hours')->where('site_id',
                $site->id)->first();
                $increaseRunningHours = (float) ($increased_running_hours->increase_running_hours ??
                0);
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
                $inc_addValueFormatted = number_format($inc_addValue, 2) + $increaseRunningHours;
                @endphp
                {{ $inc_addValueFormatted }} Hrs
            </td>

            <td class="last-updated">
                {{$site->updatedAt}}
            </td>

            <td>
                @php
                $addValuerunstatus = 0;
                if (isset($sitejsonData['electric_parameters']['voltage_l_l']['a'])) {
                $keya = $sitejsonData['electric_parameters']['voltage_l_l']['a']['add'] ?? null;
                $moduleId = $sitejsonData['electric_parameters']['voltage_l_l']['a']['md'] ?? null;

                foreach ($eventData as $event) {
                $eventArraya = $event->getArrayCopy();
                if ($moduleId && isset($eventArraya['module_id']) && $eventArraya['module_id'] ==
                $moduleId) {
                if ($keya && array_key_exists($keya, $eventArraya)) {
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
            @php $i=$i+1; @endphp
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
    </div>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="{{url('backend/assets/js/admin-jquery-3.6.0.min.js')}}"></script>

    <script>
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
                    // $('#siteTable').html(response);
                    $('#siteTable').html(response.html);
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

</body>

</html>