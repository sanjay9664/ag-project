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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- PDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<style>
/* Navbar Styling */
/* Navbar Styling (No changes needed) */
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
    color: white;
    white-space: nowrap;
}

.nav-buttons {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.nav-buttons .btn {
    font-size: 0.85rem;
    padding: 5px 12px;
    border-radius: 4px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.nav-buttons .btn:hover {
    transform: translateY(-1px);
}

.btn-refresh {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
}

.btn-logout {
    background-color: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.2);
    color: white;
}

.btn-download {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
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

/* Layout Container (No changes needed) */
.container {
    max-width: 100% !important;
    padding-left: 10px !important;
    padding-right: 10px !important;
}

.dashboard-container {
    width: 100%;
    margin: 0 auto;
    padding: 10px;
}

/* Filter Controls (No changes needed, previous updates are fine) */
.filter-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 15px;
    align-items: center;
    justify-content: flex-end;
}

.filter-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    flex-grow: 1;
    justify-content: flex-end;
}

.filter-select {
    width: 180px;
    border-radius: 4px;
    border: 1px solid #ced4da;
    padding: 6px 12px;
    font-size: 14px;
    height: 38px;
}

.filter-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.action-buttons {
    display: flex;
    gap: 8px;
}

/* Table Styling */
.table-responsive {
    overflow-x: auto; /* Keep horizontal scroll for table on small screens */
    width: 100%;
    -webkit-overflow-scrolling: touch;
}

.table {
    table-layout: auto; /* Changed from fixed to auto for better content fit */
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
}

.table th,
.table td {
    white-space: nowrap; /* Keep content on single line, allowing scroll */
    vertical-align: middle;
    font-size: 15px;
    padding: 6px 8px;
    overflow: hidden; /* Hide overflowing text, rely on nowrap for scroll */
    text-overflow: ellipsis; /* Still show ellipsis for hidden text */
    text-align: center;
}

/* Table Column Widths for larger screens (Original flexible widths) */
.table th:nth-child(1), .table td:nth-child(1) { width: auto; min-width: 40px; } /* S.No */
.table th:nth-child(2), .table td:nth-child(2) { width: auto; min-width: 150px; text-align: left; } /* Site Name */
.table th:nth-child(3), .table td:nth-child(3) { width: auto; min-width: 80px; } /* DG Status */
.table th:nth-child(4), .table td:nth-child(4) { width: auto; min-width: 120px; text-align: center; } /* Fuel Level */
.table th:nth-child(5), .table td:nth-child(5) { width: auto; min-width: 100px; } /* Bank Name */
.table th:nth-child(6), .table td:nth-child(6) { width: auto; min-width: 100px; } /* Location */
.table th:nth-child(7), .table td:nth-child(7) { width: auto; min-width: 120px; text-align: left; font-family: monospace; } /* Id */
.table th:nth-child(8), .table td:nth-child(8) { width: auto; min-width: 120px; } /* Total Run Hours */
.table th:nth-child(9), .table td:nth-child(9) { width: auto; min-width: 150px; } /* Updated Date */
.table th:nth-child(10), .table td:nth-child(10) { width: auto; min-width: 80px; } /* RMS Status */
.table th:nth-child(11), .table td:nth-child(11) { width: auto; min-width: 80px; } /* DG Controller */


/* Status Dots (No changes needed) */
.status-cell {
    text-align: center;
    vertical-align: middle;
}

.status-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: inline-block;
}

.gateway-dot.ONLINE,
.controller-dot.ONLINE {
    background-color: #28a745;
}

.gateway-dot.OFFLINE,
.controller-dot.OFFLINE {
    background-color: #dc3545;
}

.status-dot.loading {
    border: 2px solid #ccc;
    border-top: 2px solid #007bff;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Fuel UI (Latest version from previous response) */
.fuel-container {
    position: relative;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.fuel-indicator {
    display: flex;
    align-items: center;
    height: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    overflow: hidden;
    width: 80%;
    margin-left: 0;
    padding: 2px;
    box-sizing: border-box;
    background-color: #f0f0f0;
}

.fuel-level {
    height: 100%;
}

.low-fuel .fuel-level {
    /* background-color: #dc3545 !important; */
}

.normal-fuel .fuel-level {
    /* background-color: #007bff !important; */
}

.fuel-percentage {
    font-size: 0.75rem;
    position: absolute;
    color: white;
    width: 100%;
    text-align: center;
    text-shadow: 0 0 2px rgba(0,0,0,0.5);
}

.fueldata {
    color: red;
    font-weight: bold;
    font-size: 0.70rem;
    text-align: center;
    margin-top: 2px;
    white-space: nowrap;
}

/* DG Status (No changes needed) */
.status-running {
    color: green;
    font-weight: bold;
}

.status-stopped {
    color: red;
    font-weight: bold;
}

.blinking {
    animation: blink 1s step-start infinite;
}

@keyframes blink {
    50% {
        opacity: 0;
    }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    /* Filter Controls - More compact on small screens (no changes from previous) */
    .filter-controls {
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-start;
        padding: 5px;
    }
    
    .filter-group {
        flex-direction: row;
        flex-grow: 1;
        justify-content: flex-start;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 5px;
    }
    
    .filter-select {
        min-width: 120px;
        width: auto;
        flex-shrink: 0;
        font-size: 13px;
        padding: 4px 8px;
        height: 32px;
    }
    
    .action-buttons {
        flex-shrink: 0;
        justify-content: flex-end;
        align-self: flex-start;
    }

    /* Table Adjustments - Columns are now visible */
    .table th,
    .table td {
        font-size: 11px; /* Slightly smaller font to fit more columns */
        padding: 3px 4px; /* Reduced padding */
        white-space: nowrap; /* Keep nowrap to allow horizontal scroll */
        overflow: hidden;
        text-overflow: ellipsis; /* Re-enable ellipsis as nowrap is active */
    }

    /* Make all columns visible, adjust min-widths to fit more columns */
    .table th:nth-child(1), .table td:nth-child(1) { width: auto; min-width: 30px; } /* S.No */
    .table th:nth-child(2), .table td:nth-child(2) { width: auto; min-width: 90px; text-align: left; } /* Site Name */
    .table th:nth-child(3), .table td:nth-child(3) { width: auto; min-width: 50px; } /* DG Status */
    .table th:nth-child(4), .table td:nth-child(4) { width: auto; min-width: 70px; } /* Fuel Level */
    .table th:nth-child(5), .table td:nth-child(5) { width: auto; min-width: 70px; } /* Bank Name */ /* Now visible */
    .table th:nth-child(6), .table td:nth-child(6) { width: auto; min-width: 70px; } /* Location */ /* Now visible */
    .table th:nth-child(7), .table td:nth-child(7) { width: auto; min-width: 100px; text-align: left; font-family: monospace; } /* Id */ /* Now visible */
    .table th:nth-child(8), .table td:nth-child(8) { width: auto; min-width: 80px; } /* Total Run Hours */
    .table th:nth-child(9), .table td:nth-child(9) { width: auto; min-width: 100px; } /* Updated Date */ /* Now visible */
    .table th:nth-child(10), .table td:nth-child(10) { width: auto; min-width: 50px; } /* RMS Status */
    .table th:nth-child(11), .table td:nth-child(11) { width: auto; min-width: 50px; } /* DG Controller */

    /* Fuel UI specific mobile adjustments (no changes from previous) */
    .fuel-percentage {
        font-size: 0.65rem;
    }
    .fueldata {
        font-size: 0.65rem;
    }

    /* Navbar on small mobile (no changes from previous) */
    .navbar-brand.mx-auto {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    /* Filter controls (no changes from previous) */
    .nav-buttons {
        justify-content: center;
        width: 100%;
        margin-top: 10px;
    }
    
    .navbar-brand.mx-auto {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100%;
        text-align: center;
        order: -1;
    }
    
    .container-fluid {
        flex-wrap: wrap;
    }
    
    .navbar-toggler {
        order: 1;
    }
    
    .navbar-brand:first-child {
        order: -2;
    }
    
    /* Further simplify filter controls for very small screens (no changes from previous) */
    .filter-controls {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-group {
        flex-direction: column;
        flex-wrap: wrap;
        overflow-x: hidden;
    }
    .filter-select {
        width: 100% !important;
        margin-bottom: 5px;
    }
    .action-buttons {
        width: 100%;
        justify-content: center;
        margin-top: 5px;
    }

    /* Table Adjustments for very small screens - All columns visible */
    .table th,
    .table td {
        font-size: 10px; /* Even smaller font */
        padding: 2px 3px; /* Reduced padding */
        white-space: nowrap; /* Ensure horizontal scroll */
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Adjust min-widths for very small screens */
    .table th:nth-child(1), .table td:nth-child(1) { width: auto; min-width: 30px; } /* S.No */
    .table th:nth-child(2), .table td:nth-child(2) { width: auto; min-width: 80px; } /* Site Name */
    .table th:nth-child(3), .table td:nth-child(3) { width: auto; min-width: 40px; } /* DG Status */
    .table th:nth-child(4), .table td:nth-child(4) { width: auto; min-width: 60px; } /* Fuel Level */
    .table th:nth-child(5), .table td:nth-child(5) { width: auto; min-width: 60px; } /* Bank Name */ /* Now visible */
    .table th:nth-child(6), .table td:nth-child(6) { width: auto; min-width: 60px; } /* Location */ /* Now visible */
    .table th:nth-child(7), .table td:nth-child(7) { width: auto; min-width: 90px; } /* Id */ /* Now visible */
    .table th:nth-child(8), .table td:nth-child(8) { width: auto; min-width: 70px; } /* Total Run Hours */
    .table th:nth-child(9), .table td:nth-child(9) { width: auto; min-width: 90px; } /* Updated Date */ /* Now visible */
    .table th:nth-child(10), .table td:nth-child(10) { width: auto; min-width: 40px; } /* RMS Status */
    .table th:nth-child(11), .table td:nth-child(11) { width: auto; min-width: 40px; } /* DG Controller */
}

/* Print-specific styles (No changes needed) */
@media print {
    .navbar, .filter-controls, .btn-download {
        display: none !important;
    }
    
    body {
        padding: 20px;
    }
    
    .table {
        width: 100%;
        font-size: 12px;
    }
    
    .table th, .table td {
        padding: 3px 5px;
    }
    
    .table-responsive {
        overflow: visible !important;
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
        $isRecent = $updatedAt->diffInHours($now) < 24;
        $formattedUpdatedAt = $updatedAt->format("d M Y h:i A");
    } catch (\Exception $e) {
        \Log::error('Date Parsing Error: ' . $e->getMessage());
    }
}

$gatewayStatus = $isRecent ? 'online' : 'offline';
$controllerStatus = $isRecent ? 'online' : 'offline';
@endphp

<tr class="site-row" data-site-id="{{ $site->id }}" data-bank="{{ $sitejsonData['generator'] ?? '' }}" data-location="{{ $sitejsonData['group'] ?? '' }}">
    <td>{{ $i }}</td>

    <td style="color: {{ $isRecent ? 'green' : 'red' }};">
        <a href="{{ url('admin/sites/'.$site->slug . '?role=admin') }}"
           style="text-decoration: none; color: inherit; font-weight: bold;"
           target="_blank">
            {{ $site->site_name }}
        </a>
    </td>

     <td>
        <span class="controller-status-text">
            <strong style="font-size: 1.3rem; font-weight: bold;">—</strong>
        </span>
    </td>

    <td class="status-cell">
        @php
        $capacity = $sitejsonData['capacity'] ?? 0;
        $fuelMd = $sitejsonData['parameters']['fuel']['md'] ?? null;
        $fuelKey = $sitejsonData['parameters']['fuel']['add'] ?? null;
        $addValue = 0;

        foreach ($eventData as $event) {
            $eventArray = $event instanceof \ArrayObject ? $event->getArrayCopy() : (array) $event;
            if ($fuelMd && isset($eventArray['module_id']) && $eventArray['module_id'] == $fuelMd) {
                if ($fuelKey && array_key_exists($fuelKey, $eventArray)) {
                    $addValue = $eventArray[$fuelKey];
                }
                break;
            }
        }

        $percentage = is_numeric($addValue) ? floatval($addValue) : 0;
        $percentageDecimal = $percentage / 100;
        $totalFuelLiters = $capacity * $percentageDecimal;
        $fuelClass = $percentage <= 20 ? 'low-fuel' : 'normal-fuel';
        $lowFuelText = $percentage <= 20 ? 'Low Fuel' : '';
        @endphp

        <div class="fuel-container">
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
    document.getElementById('downloadPdf').addEventListener('click', function() {
        $('#loader').show();
        
        // Clone the table to avoid modifying the original
        const element = document.getElementById('siteTable').cloneNode(true);
        
        // Remove any hidden rows from the clone
        $(element).find('tr').each(function() {
            if ($(this).css('display') === 'none') {
                $(this).remove();
            }
        });
        
        // Set PDF options
        const opt = {
            margin: 10,
            filename: 'DGMS_Site_Overview_' + new Date().toISOString().slice(0, 10) + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 2,
                scrollX: 0,
                scrollY: 0,
                windowWidth: document.getElementById('siteTable').scrollWidth
            },
            jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' }
        };
        
        // Generate PDF
        html2pdf().from(element).set(opt).toPdf().get('pdf').then(function(pdf) {
            $('#loader').hide();
            pdf.save(opt.filename);
        });
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

            $(this).find('.controller-status-text')
                .html('<span class="text-muted">Loading...</span>');
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
                    // already handled above in .each()
                },
                success: function(data) {
                    $.each(data, function(siteId, statuses) {
                        let row = $('.site-row[data-site-id="' + siteId + '"]');

                        let dgStatus = '';
                        let ctrlStatus = '';
                        let runStatus = 0;

                        try {
                            dgStatus = JSON.parse(statuses.dg_status || '""');
                        } catch (e) {
                            dgStatus = (statuses.dg_status || '').replace(/['"]+/g, '');
                        }

                        try {
                            ctrlStatus = JSON.parse(statuses.controller_status || '""');
                        } catch (e) {
                            ctrlStatus = (statuses.controller_status || '').replace(
                                /['"]+/g, '');
                        }

                        runStatus = parseInt(statuses.add_value_run_status || 0);

                        let siteName = row.find('.site-name').text().trim();

                        console.log(
                            `Site: ${siteName || siteId}, Controller Status: [${ctrlStatus}], DG Status: [${dgStatus}], Run Status: [${runStatus}]`
                        );

                        row.find('.gateway-dot')
                            .removeClass('loading ONLINE OFFLINE')
                            .addClass(dgStatus);

                        row.find('.controller-dot')
                            .removeClass('loading ONLINE OFFLINE')
                            .addClass(ctrlStatus);

                        let statusText = '';
                        if (!dgStatus || dgStatus === 'OFFLINE') {
                            statusText =
                                '<strong style="font-size: 1.3rem; font-weight: bold;">—</strong>';
                        } else if (runStatus > 0) {
                            statusText = '<span class="status-running blinking">ON</span>';
                        } else {
                            statusText = '<span class="status-stopped">OFF</span>';
                        }

                        row.find('.controller-status-text').html(statusText);
                    });
                },
                error: function() {
                    $('.gateway-dot, .controller-dot')
                        .removeClass('loading ONLINE')
                        .addClass('OFFLINE');

                    $('.controller-status-text')
                        .html('<strong style="font-size: 1.3rem; font-weight: bold;">—</strong>');
                }
            });
        }
    }
    </script>
</body>
</html>