<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DG SET MONITORING SYSTEM</title>
    <link rel="stylesheet" href="{{url('backend/assets/css/admin.css')}}">
    <link rel="stylesheet" href="{{url('backend/assets/css/admin-all-min.css')}}">
    <link rel="stylesheet" href="{{url('backend/assets/css/admin-bootstrap-min.css')}}">
    <link rel="stylesheet" href="{{url('/css/admin-sites.css')}}" class="rel">
    <script src="{{url('backend/assets/js/admin-bootstrap.bundle.js')}}"></script>
    <script src="{{url('backend/assets/js/adminCDN.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- PDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<style>
  .pdf-wrapper {
    width: 1400px; /* Increased width for more content */
    padding: 30px;
    background: #fff;
    color: #000;
    font-size: 14px;
    font-family: 'Arial', sans-serif;
    overflow-x: auto;
    word-break: break-word;
  }

  .pdf-wrapper h3 {
    font-size: 20px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
    white-space: pre-line; /* Supports \n in innerText */
  }

  .pdf-wrapper table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    page-break-inside: auto;
    margin-top: 20px;
  }

  .pdf-wrapper thead {
    background: #f0f0f0;
  }

  .pdf-wrapper th, .pdf-wrapper td {
    border: 1px solid #333;
    padding: 6px 8px;
    font-size: 12px;
    text-align: left;
    word-wrap: break-word;
  }

  .pdf-wrapper tr {
    page-break-inside: avoid;
    page-break-after: auto;
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
                <span class="d-md-none">DGMS</span>
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

                            <button class="btn btn-outline-light btn-download" id="downloadPdf">
                                <i class="fas fa-download me-1"></i> PDF
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
            </div>

            <!-- Filter Controls -->
            <div class="filter-controls">
                <div class="filter-group">
                    <select class="form-select form-select-sm filter-select" id="bankSelect">
                        <option value="" selected>Select Bank</option>
                        @php
                        $uniqueGenerators = collect($siteData)
                        ->map(fn($site) => json_decode($site->data)->generator)
                        ->unique();
                        @endphp

                        @foreach($uniqueGenerators as $generator)
                        <option value="{{ $generator }}">{{ $generator }}</option>
                        @endforeach
                    </select>

                    <select class="form-select form-select-sm filter-select" id="locationSelect">
                        <option value="" selected>Select Location</option>
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

                <div class="action-buttons">
                    <button class="btn btn-primary btn-sm" id="resetFilters">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Sites Table -->
            <div class="table-responsive" id="siteTable">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>S.No</th>
                            <th>Site Name</th>
                            <th>DG Status</th>
                            <th>Fuel Level</th>
                            <!-- <th>Controller Type</th> -->
                            <th>Bank Name</th>
                            <th>Location</th>
                            <th>Id</th>
                            <th>Total Run Hours</th>
                            <th>Updated Date</th>
                            <th>RMS Status</th>
                            <th>DG Controller</th>
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

                            $gatewayStatus = $isRecent ? 'online' : 'offline';
                            $controllerStatus = $isRecent ? 'online' : 'offline';
                            @endphp

                            <tr class="site-row" data-site-id="{{ $site->id }}"
                                data-bank="{{ $sitejsonData['generator'] ?? '' }}"
                                data-location="{{ $sitejsonData['group'] ?? '' }}">
                                <td>{{ $i }}</td>


                                <td class="site-name-cell">
                                    <a href="{{ url('admin/sites/'.$site->slug . '?role=admin') }}"
                                        class="site-name-link" style="text-decoration: none; font-weight: bold;"
                                        target="_blank">
                                        {{ $site->site_name }}
                                    </a>
                                </td>

                                @php
                                $runMd = $sitejsonData['run_status']['md'] ?? null;
                                $runKey = $sitejsonData['run_status']['add'] ?? null;
                                $runValue = 0;

                                foreach ($eventData as $event) {
                                $eventArray = $event instanceof \ArrayObject ? $event->getArrayCopy() : (array) $event;

                                if ($runMd && isset($eventArray['module_id']) && $eventArray['module_id'] == $runMd) {
                                if ($runKey && array_key_exists($runKey, $eventArray)) {
                                $runValue = $eventArray[$runKey];
                                }
                                break;
                                }
                                }

                                $statusText = '';
                                $statusClass = '';

                                if (is_numeric($runValue) && $runValue > 0) {
                                $statusText = 'ON';
                                $statusClass = 'text-success blinking';
                                } else {
                                $statusText = 'OFF';
                                $statusClass = 'text-danger';
                                }
                                @endphp

                                <td>
                                    <span class="controller-status-text {{ $statusClass }}"><strong>{{ $statusText }}</strong></span>
                                </td>

                                <td class="status-cell">
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
                                        class="fuel-container">
                                        <div class="fuel-indicator {{ $fuelClass }}">
                                            <div class="fuel-level" style="width: {{ $percentage }}%;"></div>
                                            <span class="fuel-percentage">{{ $percentage }}%</span>
                                        </div>
                                        @if($lowFuelText)
                                        <span class="fueldata">{{ $lowFuelText }}</span>
                                        @endif
            </div>
            </td>

            <td>{{ $sitejsonData['generator'] ?? 'N/A' }}</td>
            <td>{{ $sitejsonData['group'] ?? 'N/A' }}</td>
            <td>{{ $sitejsonData['serial_number'] ?? 'N/A' }}</td>

            <td>
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

            <td>{{ $formattedUpdatedAt }}</td>


            <td class="status-cell">
                <div class="status-dot controller-dot"></div>
            </td>

            <td class="status-cell">
                <div class="status-dot gateway-dot"></div>
            </td>
            </tr>
            @php $i++; @endphp
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
        $('#loader').show();
        updateRefreshTime();
        setTimeout(() => {
            location.reload();
        }, 500);
    }

    // PDF Download Functionality
//  document.getElementById('downloadPdf').addEventListener('click', function () {
//     document.getElementById('loader').style.display = 'block';

//     const originalTable = document.getElementById('siteTable');
//     const clonedTable = originalTable.cloneNode(true);

//     $(clonedTable).find('tr').each(function () {
//         if ($(this).css('display') === 'none') {
//             $(this).remove();
//         }
//     });

//     const wrapper = document.createElement('div');
//     wrapper.classList.add('pdf-wrapper');

//     const now = new Date();
//     const formattedDateTime = now.toLocaleString('en-IN', {
//         day: '2-digit',
//         month: 'short',
//         year: 'numeric',
//         hour: '2-digit',
//         minute: '2-digit',
//         hour12: true
//     });

//     const heading = document.createElement('h3');
//     heading.innerText = `DGMS Site Overview Report\nGenerated on: ${formattedDateTime}`;
//     wrapper.appendChild(heading);
//     wrapper.appendChild(clonedTable);

//     const opt = {
//         margin: 10,
//         filename: 'DGMS_Site_Overview_' + now.toISOString().slice(0, 10) + '.pdf',
//         image: { type: 'jpeg', quality: 0.98 },
//         html2canvas: {
//             scale: 2,
//             scrollX: 0,
//             scrollY: 0,
//             windowWidth: wrapper.scrollWidth,
//             windowHeight: wrapper.scrollHeight,
//             useCORS: true
//         },
//         jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' }
//     };

//     html2pdf()
//         .from(wrapper)
//         .set(opt)
//         .toPdf()
//         .get('pdf')
//         .then(function (pdf) {
//             document.getElementById('loader').style.display = 'none';
//             pdf.save(opt.filename);
//         })
//         .catch(function (error) {
//             document.getElementById('loader').style.display = 'none';
//             alert('PDF generation failed: ' + error.message);
//         });
// });
document.getElementById('downloadPdf').addEventListener('click', function () {
    document.getElementById('loader').style.display = 'block';

    const originalTable = document.getElementById('siteTable');
    const clonedTable = originalTable.cloneNode(true);

    // Remove hidden rows
    $(clonedTable).find('tr').each(function () {
        if ($(this).css('display') === 'none') {
            $(this).remove();
        }
    });

    // Build wrapper
    const wrapper = document.createElement('div');
    wrapper.classList.add('pdf-wrapper');

    const now = new Date();
    const formattedDateTime = now.toLocaleString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });

    const heading = document.createElement('h3');
    heading.innerText = `DGMS Site Overview Report\nGenerated on: ${formattedDateTime}`;
    wrapper.appendChild(heading);
    wrapper.appendChild(clonedTable);

    document.body.appendChild(wrapper); // attach to DOM for rendering

    const opt = {
        margin: [10, 10, 10, 10],
        filename: 'DGMS_Site_Overview_' + now.toISOString().slice(0, 10) + '.pdf',
        image: { type: 'jpeg', quality: 1 }, // highest quality
        html2canvas: {
            scale: 4, // very high resolution
            useCORS: true,
            scrollX: 0,
            scrollY: 0
        },
        jsPDF: {
            unit: 'px',
            format: [1400, 1000], // wider than A3
            orientation: 'landscape'
        }
    };

    // Delay helps rendering accuracy
    setTimeout(() => {
        html2pdf()
            .set(opt)
            .from(wrapper)
            .save()
            .then(() => {
                document.getElementById('loader').style.display = 'none';
                document.body.removeChild(wrapper);
            })
            .catch((error) => {
                document.getElementById('loader').style.display = 'none';
                alert('PDF generation failed: ' + error.message);
                document.body.removeChild(wrapper);
            });
    }, 300);
});


    // Reset filters
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('bankSelect').value = '';
        document.getElementById('locationSelect').value = '';
        filterSites();
    });

    $(document).ready(function() {
        $('#bankSelect, #locationSelect').change(filterSites);

        // Initialize status dots
        updateStatusDots();
    });

    function filterSites() {
        const bankValue = $('#bankSelect').val().toLowerCase();
        const locationValue = $('#locationSelect').val().toLowerCase();

        $('.site-row').each(function() {
            const rowBank = $(this).data('bank').toString().toLowerCase();
            const rowLocation = $(this).data('location').toString().toLowerCase();

            const bankMatch = bankValue === '' || rowBank.includes(bankValue);
            const locationMatch = locationValue === '' || rowLocation.includes(locationValue);

            if (bankMatch && locationMatch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function updateStatusDots() {
        let siteIds = [];

        $('.site-row').each(function() {
            siteIds.push($(this).data('site-id'));

            $(this).find('.gateway-dot, .controller-dot')
                .removeClass('ONLINE OFFLINE')
                .addClass('loading');

            // Don't change the controller status text
            // Don't show "Loading..."

            $(this).find('.site-name-link')
                .css('color', '#6c757d');
        });

        if (siteIds.length > 0) {
            $.ajax({
                url: '{{ route("admin.site.statuses") }}',
                method: 'POST',
                data: {
                    site_ids: siteIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $.each(data, function(siteId, statuses) {
                        let row = $('.site-row[data-site-id="' + siteId + '"]');

                        let dgStatus = (statuses.dg_status || '').replace(/['"]+/g, '');
                        let ctrlStatus = (statuses.controller_status || '').replace(/['"]+/g, '');

                        // Update dot status
                        row.find('.gateway-dot')
                            .removeClass('loading ONLINE OFFLINE')
                            .addClass(dgStatus);

                        row.find('.controller-dot')
                            .removeClass('loading ONLINE OFFLINE')
                            .addClass(ctrlStatus);

                        // Update site name color
                        let color = (ctrlStatus === 'ONLINE') ? 'green' : 'red';
                        row.find('.site-name-link').css('color', color);

                        // Show "—" only if DG is OFFLINE
                        if (dgStatus === 'OFFLINE') {
                            row.find('.controller-status-text').html(
                                '<strong style="font-size: 1.3rem; font-weight: bold;">—</strong>'
                            );
                        }
                        // Else: do nothing, leave the ON/OFF text from Blade
                    });
                },
                error: function() {
                    $('.gateway-dot, .controller-dot')
                        .removeClass('loading ONLINE')
                        .addClass('OFFLINE');

                    $('.controller-status-text').html(
                        '<strong style="font-size: 1.3rem; font-weight: bold;">—</strong>'
                    );

                    $('.site-name-link').css('color', 'red');
                }
            });
        }
    }
    </script>
</body>

</html>