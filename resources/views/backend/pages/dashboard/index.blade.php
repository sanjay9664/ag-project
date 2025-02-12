@extends('backend.layouts.master')

@section('title')
Dashboard Page - Admin Panel
@endsection

@section('styles')
<!-- Start datatable css -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
<style>
.dot-green {
    height: 20px;
    width: 20px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
    background-color: green;
    margin-left: 20px;
}

.dot-red {
    height: 20px;
    width: 20px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
    background-color: red;
    margin-left: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    /* Ensures consistent width for all columns */
}

th,
td {
    word-wrap: break-word;
    white-space: normal;
    /* Ensure long content breaks to new lines */
    padding: 8px;
    /* Adjust padding */
}

@media only screen and (max-width: 768px) {
    table {
        font-size: 12px;
        /* Adjust font size for mobile */
    }
}

.card-body {
    max-width: 150%;
    margin: auto;
    padding: 10px;
}

.card-body h4 {
    font-size: 18px;
}

.card-body button {
    font-size: 14px;
    padding: 6px 12px;
}

.data-tables table {
    font-size: 12px;
}

.data-tables th,
.data-tables td {
    padding: 5px;
}

.site-form-container input {
    font-size: 12px;
    padding: 5px;
}

.site-form-container {
    gap: 10px;
}

.card {
    transition: all 0.3s ease-in-out;
    border-radius: 12px;
}

/* .card:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
} */

.icon-container {
    background: rgba(255, 255, 255, 0.2);
    padding: 15px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.fa-users,
.fa-user-check,
.fa-chart-line {
    color: white;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745, #1e7e34);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545, #a71d2a);
}

@media (max-width: 768px) {
    .icon-container {
        padding: 10px;
    }

    .fa-3x {
        font-size: 2rem;
    }
}
</style>
@endsection

@section('admin-content')
<!-- page title area start -->





<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">{{ __('Logins') }}</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li><span>{{ __('All Logins') }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    @if(auth()->user()->hasRole('superadmin'))
                    <h4 class="header-title float-left ">{{ __('Login Logs') }}</h4>
                    <button id="downloadReport" class="btn btn-primary float-right">Download Report</button>
                    <div class="clearfix"></div>
                    <div class="data-tables">
                        @include('backend.layouts.partials.messages')
                        <table id="dataTable" class="text-center">
                            <thead class="bg-light text-capitalize">
                                <tr>
                                    <th width="5%">{{ __('Sl') }}</th>
                                    <th width="15%">Site Name</th>
                                    <!-- <th width="15%">{{ __('User Name') }}</th> -->
                                    <th width="15%">Fuel Indicator</th>
                                    <th width="15%">DG Running Status</th>
                                    <th width="15%">Actual hours from controller</th>
                                    <th width="15%">Increase Running Hours</th>
                                    <th width="15%">Total Running Hours</th>
                                    <th width="10%">{{ __('Login Time') }}</th>
                                    <th width="10%">Login Status</th>
                                    <th width="10%">{{ __('Login as User') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logins as $login)
                                <tr>

                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @php
                                        $site = $sites->firstWhere('email', $login->email);
                                        @endphp
                                        {{ $site ? $site->site_name : '_' }}
                                    </td>
                                    <!-- <td>{{ $login->name }}</td> -->
                                    <td>
                                        <?php
                                            $addValuerun = 0;

                                            foreach ($sites as $site) {
                                                $data = json_decode($site->data);
                                                if ($data && isset($data->parameters)) {
                                                    $parameters = $data->parameters->fuel;
                                                    $capacity = $data->capacity;

                                                    foreach ($events as $event) {
                                                        if (isset($data->parameters->fuel->md) && $event['admin_id'] == $login->user_id) {
                                                            $moduleId = $data->parameters->fuel->md;
                                                            $fieldValue = $data->parameters->fuel->add;

                                                            if ($event['module_id'] == $moduleId && isset($event[$fieldValue])) {
                                                                $addValuerun = (float) $event[$fieldValue]; // Ensure it's numeric
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        ?>
                                        <!-- Display result -->
                                        <b>{{ $addValuerun }} % /
                                            {{ $addValuerun * $capacity / 100 }} L</b>
                                    </td>
                                    <td>
                                        <?php
                                            $isRunning = false;
                                            $siteFound = false;

                                            foreach ($sites as $site) {
                                                if ($site->email == $login->email) {
                                                    $siteFound = true;
                                                    $data = json_decode($site->data);

                                                    if ($data && isset($data->electric_parameters->voltage_l_l)) {
                                                        foreach ($data->electric_parameters->voltage_l_l as $value) {

                                                            $moduleId = (int) $value->md;
                                                            $addValue = $value->add;

                                                            foreach ($events as $event) {
                                                                if ($event['admin_id'] == $login->user_id) {

                                                                    $eventModuleIds = (array) $event['module_id']; 

                                                                    if (in_array($moduleId, $eventModuleIds)) {
                                                                        if (isset($event[$addValue]) && $event[$addValue] > 0) {
                                                                            $isRunning = true;
                                                                            break 3;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    break;
                                                }
                                            }
                                        ?>

                                        {{ $siteFound ? ($isRunning ? 'Running' : 'Stop') : '_' }}
                                    </td>

                                    <?php
                                        $addValuerun = 0;

                                        foreach ($sites as $site) {
                                            $data = json_decode($site->data);

                                            if ($data && isset($data->running_hours)) {
                                                $parameters = $data->running_hours;

                                                // Ensure md and add fields exist
                                                if (isset($parameters->md, $parameters->add)) {
                                                    $moduleId = $parameters->md;
                                                    $fieldValue = $parameters->add;

                                                    foreach ($events as $event) {
                                                        if ($event['admin_id'] == $login->user_id && isset($event['module_id']) && $event['module_id'] == $moduleId) {
                                                            if (isset($event[$fieldValue]) && is_numeric($event[$fieldValue])) {
                                                                $addValuerun = (float) $event[$fieldValue]; // Ensure it's numeric
                                                                break 2; // Exit both loops after finding the value
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    ?>

                                    @php
                                    $present_site = $sites->firstWhere('email', $login->email);


                                    $runningHours = DB::table('running_hours')->get()->keyBy('site_id');
                                    @endphp


                                    @if($present_site == true)
                                    <div class="site-form-container d-flex align-items-center gap-3 mb-2">
                                        <form class="site-form d-flex gap-2 align-items-center"
                                            data-site-id="{{ $site->id }}">
                                            @csrf
                                            <input type="hidden" name="site_id" class="site_id"
                                                value="{{ $present_site->id ?? '' }}">

                                            <!-- @php
                                            $siteData = $runningHours[$present_site->id ?? ''] ?? null;
                                            $totalAddrunValue = $addValuerun +
                                            ($siteData->increase_running_hours ?? 0);
                                            @endphp -->

                                            <td>
                                                <input type="text" class="form-control running_hours_admin"
                                                    value="{{ $addValuerun }}" readonly
                                                    style="outline: none; box-shadow: none;">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2 w-100">
                                                    <!-- Input Field -->
                                                    <input type="text"
                                                        class="form-control border-1 px-2 py-1 increase_running_hours"
                                                        id="increaseRunningHours" name="increase_running_hours"
                                                        data-site-id="{{ $present_site->id ?? '' }}"
                                                        style="outline: none; box-shadow: none; background: #e9ecef; border-radius: 5px; width: 50px; font-size: 14px; margin:10px">

                                                    <!-- Submit Button -->
                                                    <button id="submitButton" style="
        padding: 3px 8px; 
        font-size: 13px; 
        border-radius: 5px; 
        background-color: #007bff; 
        color: white; 
        border: none;
        cursor: pointer;
        transition: 0.3s ease-in-out;">
                                                        Submit
                                                    </button>
                                                </div>


                                            </td>

                                        </form>
                                    </div>
                                    @else
                                    <div class=" site-form-container d-flex align-items-center gap-3 mb-2">
                                        <input type="hidden" name="site_id" class="site_id" value="{{ $site->id }}">
                                        <td></td>
                                        <td></td>
                                    </div>
                                    @endif

                                    <!-- <td>
                                        @php
                                        $site = $sites->firstWhere('email', $login->email);
                                        @endphp
                                        {{ $site ? $site->site_name : '_' }}
                                    </td> -->
                                    <td>
                                        @php
                                        $siteData = $runningHours[$present_site->id ?? ''] ?? null;
                                        $totalAddrunValue = $addValuerun +
                                        ($siteData->increase_running_hours ?? 0);
                                        @endphp

                                        <div>
                                            <!-- <span class="fw-bold text-primary">Total Running Hours:</span> -->
                                            <span class="text-dark">{{ $totalAddrunValue }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($login->created_at)->timezone('Asia/Kolkata')->format('d M\' Y h:i A') }}
                                    </td>
                                    <td>
                                        <span class="dot-{{ $login->status === 'active' ? 'green' : 'red' }}"></span>
                                    </td>
                                    <td>
                                        @if(auth()->user()->hasRole('superadmin'))
                                        <form action="{{ route('admin.login.submit') }}" method="POST"
                                            class="text-center">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $login->user_id }}">
                                            <button type="submit" class="btn btn-primary text-white btn-sm px-2 w-100 "
                                                style="font-size: 12px; padding: 10px 8px;">
                                                Login
                                            </button>
                                        </form>

                                        @endif
                                    </td>

                                </tr>
                                @endforeach

                            </tbody>

                        </table>
                    </div>
                    @else
                    <div class="main-content-inner">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-6 mt-4">
                                        <div class="card shadow-lg border-0">
                                            <a href="{{ route('admin.sites.index') }}" class="text-decoration-none">
                                                <div
                                                    class="card-body d-flex justify-content-between align-items-center p-4">
                                                    <div class="icon-container">
                                                        <i class="fa fa-users fa-3x text-primary"></i>
                                                    </div>
                                                    <div class="text-end">
                                                        <h6 class="text-muted fw-bold">Total Sites</h6>
                                                        <h2 class="text-dark fw-bolder">{{ $total_sites }}</h2>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- data table end -->

    </div>
</div>
@endsection

@section('scripts')
<!-- Start datatable js -->
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module">
</script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
if ($('#dataTable').length) {
    $('#dataTable').DataTable({
        responsive: true
    });
}
</script>
<script>
document.getElementById('downloadReport').addEventListener('click', function() {
    // Store the current padding of the main-content-inner for restoration
    const mainContent = document.querySelector('.main-content-inner');
    const originalPadding = mainContent.style.padding;


    // Temporarily adjust the padding of the main-content-inner (if necessary for better report layout)
    mainContent.style.padding = '0px';

    // Hide all elements that are not part of the table
    const unwantedElements = document.querySelectorAll(
        '.page-title-area, .float-right, .data-tables .dataTables_wrapper');
    unwantedElements.forEach(element => {
        element.style.display = 'none';
    });

    // Select the table for the report
    const tableElement = document.querySelector('#dataTable');

    // Define options for the PDF export
    const options = {
        margin: 0.1,
        filename: 'login_report.pdf',
        image: {
            type: 'jpeg',
            quality: 0.98
        },
        html2canvas: {
            scale: 2,
            useCORS: true,
            scrollY: 0
        },
        jsPDF: {
            unit: 'in',
            format: 'letter',
            orientation: 'landscape'
        }
    };

    // Generate and download the PDF
    html2pdf().set(options).from(tableElement).save();

    // Revert the layout and hidden elements back to their original state after the download
    setTimeout(() => {
        unwantedElements.forEach(element => {
            element.style.display = '';
        });

        // Restore the original padding of the main-content-inner
        mainContent.style.padding = originalPadding;
    }, 1000); // Ensure it happens after the PDF is generated
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function sendData(element) {
        let site_id = $(element).data("site-id"); // Get site ID from the input field
        let increase_running_hours = $(element).val(); // Get the entered value
        increase_running_hours = parseInt(increase_running_hours, 10) ||
            0; // Convert to integer, default to 0

        // Check if value is greater than 15
        if (increase_running_hours > 15) {
            if (!confirm("Are you sure you want to increase this value?")) {
                return;
            }
        }

        // Show spinner before sending request
        $("#spinner").show();

        $.ajax({
            url: "{{ route('admin.savedashboarddata') }}",
            type: "POST",
            data: {
                site_id: site_id,
                increase_running_hours: increase_running_hours,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                alert("Data saved successfully!");
                location.reload(); // Refresh the page after successful save
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                $("#spinner").hide(); // Hide spinner on error
            }
        });
    }



    // Trigger AJAX when user changes the input field
    $(document).on("change", ".increase_running_hours", function() {
        sendData(this); // Pass the specific input field
    });

    $(document).on("click", "#submitButton", function() {
        let inputField = $(".increase_running_hours"); // Select the input field
        sendData(inputField); // Call sendData function
    });
});
</script>


@endsection