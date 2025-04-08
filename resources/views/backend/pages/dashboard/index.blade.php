@extends('backend.layouts.master')

@section('title')
Dashboard Page - Admin Panel
@endsection

@section('styles')
<!-- Start datatable css -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="{{url('backend/assets/css//responsive.bootstrap.min.css')}}">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
<link rel="stylesheet" href="{{url('backend/assets/css/dashboard.css')}}">
@endsection

@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">{{ __('Logins') }}</h4>
                <ul class=" breadcrumbs pull-left">
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
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        {{ $login->site_name }}
                                    </td>

                                    <td>
                                        <?php
                                            $addValuerun = 0;

                                            foreach ($sites as $site) {
                                                $data = json_decode($site->data);
                                                if ($data && isset($data->parameters)) {
                                                    $parameters = $data->parameters->fuel;
                                                    $capacity = $data->capacity;
                                                    
                                                    foreach ($events as $event) {
                                                        if (isset($data->parameters->fuel->md) && $event['admin_id'] == $login->site_id) {
                                                            $moduleId = $data->parameters->fuel->md;
                                                            $fieldValue = $data->parameters->fuel->add;
                                                            
                                                            if ($event['module_id'] == $moduleId && isset($event[$fieldValue])) {
                                                                $addValuerun = (float) $event[$fieldValue];
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
                                                                if ($event['admin_id'] == $login->site_id) {

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
                                                if (isset($parameters->md, $parameters->add)) {
                                                    $moduleId = $parameters->md;
                                                    $fieldValue = $parameters->add;
                                                    $increaseMinutes = isset($parameters->increase_minutes) && is_numeric($parameters->increase_minutes) && (float)$parameters->increase_minutes > 0 
                                                        ? (float)$parameters->increase_minutes 
                                                        : 1; 

                                                    foreach ($events as $event) {
                                                        if (
                                                            isset($event['admin_id'], $event['module_id']) && 
                                                            $event['admin_id'] == $login->site_id && 
                                                            $event['module_id'] == $moduleId
                                                        ) {
                                                            if (!isset($event[$fieldValue]) || !is_numeric($event[$fieldValue])) {
                                                                continue;
                                                            }

                                                            $addValuerun = (float) $event[$fieldValue];

                                                            if ($increaseMinutes > 0) {
                                                                $addValuerun /= $increaseMinutes;
                                                            }
                                                            $addValuerun = number_format($addValuerun, 2, '.', '');
                                                            break 2; 
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    ?>

                                    @php
                                    $present_site = $sites->firstWhere('id', $login->site_id);
                                    $runningHours = DB::table('running_hours')->get()->keyBy('site_id');
                                    @endphp


                                    @if($present_site == true)
                                    <div class="site-form-container d-flex align-items-center gap-3 mb-2">
                                        <form class="site-form d-flex gap-2 align-items-center"
                                            data-site-id="{{ $site->id }}">
                                            @csrf
                                            <input type="hidden" name="site_id" class="site_id"
                                                value="{{ $present_site->id ?? '' }}">

                                            <td>
                                                <input type="text" class="form-control running_hours_admin"
                                                    value="{{ $addValuerun }}" readonly
                                                    style="outline: none; box-shadow: none;">
                                            </td>

                                            @if($site->increase_running_hours_status == 0)
                                            <td>
                                                <button onclick="toggleDivVisibility(this)" style="
                                                    padding: 5px 10px; 
                                                    font-size: 14px; 
                                                    border-radius: 5px; 
                                                    background-color: #007bff; 
                                                    color: white; 
                                                    border: none;
                                                    cursor: pointer;
                                                    transition: 0.3s ease-in-out; 
                                                    margin-bottom: 10px;">
                                                    Show Input and Button
                                                </button>

                                                <div class="inputButtonDiv" style="display: none; margin-top: 10px;">
                                                    <div class="d-flex align-items-center gap-2 w-100">
                                                        <input type="text"
                                                            class="form-control border-1 px-2 py-1 increase_running_hours"
                                                            name="increase_running_hours"
                                                            data-site-id="{{ $present_site->id ?? '' }}"
                                                            style="outline: none; box-shadow: none; background: #e9ecef; border-radius: 5px; width: 70px; font-size: 14px; margin:10px">

                                                        <button style="
                                                            padding: 3px 8px; 
                                                            font-size: 13px; 
                                                            border-radius: 5px; 
                                                            background-color: #28a745; 
                                                            color: white; 
                                                            border: none;
                                                            cursor: pointer;
                                                            transition: 0.3s ease-in-out;">
                                                            Submit
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            @else
                                            <td>

                                            </td>
                                            @endif

                                        </form>
                                    </div>
                                    @else
                                    <div class=" site-form-container d-flex align-items-center gap-3 mb-2">
                                        <input type="hidden" name="site_id" class="site_id" value="{{ $site->id }}">
                                        <td></td>
                                        <td></td>
                                    </div>
                                    @endif
                                    <td>
                                        @php
                                        $siteData = $runningHours[$present_site->id ?? ''] ?? null;
                                        $totalAddrunValue = $addValuerun +
                                        ($siteData->increase_running_hours ?? 0);

                                        $hours = floor($totalAddrunValue); // Get whole hours
                                        $minutes = round(($totalAddrunValue - $hours) * 60);
                                        @endphp

                                        <div>
                                            <span class="text-dark">{{ $hours }} hrs {{ $minutes }} mins</span>
                                        </div>
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($login->created_at)->timezone('Asia/Kolkata')->format('d M\' Y h:i A') }}
                                    </td>
                                    <td>
                                        <span class="dot-{{ $login->status === 'active' ? 'red' : 'green' }}"></span>
                                    </td>
                                    <td>
                                        @if(auth()->user()->hasRole('superadmin'))
                                        <form class="text-center">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $login->site_id }}">

                                            <div class="dropdown">
                                                <button type="button" class="btn btn-primary viewUIButton">User
                                                    UI</button>
                                                <div class="dropdown-menu">
                                                    @if(isset($login) && $login->slug)
                                                    <a href="{{ url('admin/sites/'.$login->slug . '?role=admin') }}"
                                                        class="dropdown-item" target="_blank">
                                                        Admin View
                                                    </a>
                                                    <a href="{{ url('admin/sites/'.$login->slug . '?role=superadmin') }}"
                                                        target="_blank" class="dropdown-item">SuperAdmin View</a>
                                                    @else
                                                    <span class="dropdown-item text-muted">No Site Available</span>
                                                    @endif
                                                </div>
                                            </div>
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

<!-- <script>
$(document).ready(function() {
    if ($('#dataTable').length) {
        $('#dataTable').DataTable({
            responsive: true,
            // pageLength: 25,
            // paging: true
        });
    }
});
</script> -->

<script>
document.getElementById('downloadReport').addEventListener('click', function() {
    const mainContent = document.querySelector('.main-content-inner');
    const originalPadding = mainContent.style.padding;

    mainContent.style.padding = '0px';
    const unwantedElements = document.querySelectorAll(
        '.page-title-area, .float-right, .data-tables .dataTables_wrapper');
    unwantedElements.forEach(element => {
        element.style.display = 'none';
    });

    const tableElement = document.querySelector('#dataTable');

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

    html2pdf().set(options).from(tableElement).save();

    setTimeout(() => {
        unwantedElements.forEach(element => {
            element.style.display = '';
        });

        mainContent.style.padding = originalPadding;
    }, 1000);
});
</script>

<script>
$(document).ready(function() {
    function sendData(element) {
        let site_id = $(element).data("site-id");
        let increase_running_hours = $(element).val();
        increase_running_hours = parseInt(increase_running_hours, 10) || 0;

        if (increase_running_hours > 15) {
            if (!confirm("Are you sure you want to increase this value?")) {
                return;
            }
        }

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
                location.reload();
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                $("#spinner").hide();
            }
        });
    }

    function savePaginationPage() {
        let currentPage = $(".pagination .active a").text();
        localStorage.setItem("currentPage", currentPage);
    }

    function restorePaginationPage() {
        let savedPage = localStorage.getItem("currentPage");
        if (savedPage) {
            $(".pagination a").each(function() {
                if ($(this).text() === savedPage) {
                    $(this)[0].click();
                }
            });
        }
    }

    $(document).on("change", ".increase_running_hours", function() {
        savePaginationPage();
        sendData(this);
    });

    $(document).on("click", "#submitButton", function() {
        let inputField = $(".increase_running_hours");
        savePaginationPage();
        sendData(inputField);
    });

    restorePaginationPage();
});
</script>

<script>
function toggleDivVisibility(button) {
    var row = button.closest("td");
    var div = row.querySelector(".inputButtonDiv");

    if (div.style.display === "none" || div.style.display === "") {
        div.style.display = "block";
        button.innerText = "Hide Input and Button";
    } else {
        div.style.display = "none";
        button.innerText = "Show Input and Button";
    }
}
</script>

<script>
// $(document).ready(function() {
//     if ($('#dataTable').length) {
//         let table = $('#dataTable').DataTable({
//             responsive: true,
//         });

//         function initializeHoverListeners() {
//             let buttons = document.querySelectorAll(".viewUIButton");

//             buttons.forEach(button => {
//                 let dropdownMenu = button.nextElementSibling;
//                 let timeoutId;

//                 button.addEventListener("mouseenter", function() {
//                     clearTimeout(timeoutId);
//                     dropdownMenu.style.display = "block";
//                     setTimeout(() => {
//                         dropdownMenu.style.opacity = "1";
//                         dropdownMenu.style.visibility = "visible";
//                     }, 100);
//                 });

//                 dropdownMenu.addEventListener("mouseenter", function() {
//                     clearTimeout(timeoutId);
//                     dropdownMenu.style.display = "block";
//                     dropdownMenu.style.opacity = "1";
//                     dropdownMenu.style.visibility = "visible";
//                 });

//                 button.addEventListener("mouseleave", function() {
//                     timeoutId = setTimeout(() => {
//                         if (!dropdownMenu.matches(":hover")) {
//                             dropdownMenu.style.opacity = "0";
//                             dropdownMenu.style.visibility = "hidden";
//                             setTimeout(() => {
//                                 dropdownMenu.style.display = "none";
//                             }, 500);
//                         }
//                     }, 1000);
//                 });

//                 dropdownMenu.addEventListener("mouseleave", function() {
//                     timeoutId = setTimeout(() => {
//                         dropdownMenu.style.opacity = "0";
//                         dropdownMenu.style.visibility = "hidden";
//                         setTimeout(() => {
//                             dropdownMenu.style.display = "none";
//                         }, 500);
//                     }, 1000);
//                 });
//             });
//         }

//         // Initialize hover listeners after DataTable initializes
//         initializeHoverListeners();

//         // Reattach hover listeners after table is redrawn (pagination, search, etc.)
//         table.on('draw', function() {
//             initializeHoverListeners();
//         });
//     }
// });

$(document).ready(function() {
    if ($('#dataTable').length) {
        let table = $('#dataTable').DataTable({
            responsive: true,
        });

        function initializeHoverListeners() {
            // Remove previous event handlers to avoid duplicates
            $(document).off('mouseenter mouseleave', '.viewUIButton, .dropdown-menu');

            $(document).on('mouseenter', '.viewUIButton', function() {
                const $dropdown = $(this).siblings('.dropdown-menu');
                $dropdown.stop(true, true).css({
                    display: 'block',
                    opacity: 1,
                    visibility: 'visible'
                });
            });

            $(document).on('mouseleave', '.viewUIButton', function() {
                const $dropdown = $(this).siblings('.dropdown-menu');
                setTimeout(() => {
                    if (!$dropdown.is(':hover')) {
                        $dropdown.stop(true, true).css({
                            opacity: 0,
                            visibility: 'hidden'
                        }).delay(300).fadeOut(200);
                    }
                }, 500);
            });

            $(document).on('mouseleave', '.dropdown-menu', function() {
                const $dropdown = $(this);
                setTimeout(() => {
                    $dropdown.stop(true, true).css({
                        opacity: 0,
                        visibility: 'hidden'
                    }).delay(300).fadeOut(200);
                }, 500);
            });

            $(document).on('mouseenter', '.dropdown-menu', function() {
                $(this).stop(true, true).css({
                    display: 'block',
                    opacity: 1,
                    visibility: 'visible'
                });
            });
        }


        // Initial call
        initializeHoverListeners();

        // Redraw pagination/search
        table.on('draw', function() {
            initializeHoverListeners();
        });

        // Monitor clicks on + to show child rows
        $('#dataTable tbody').on('click', 'td.details-control', function() {
            let tr = $(this).closest('tr');
            let row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(format(row.data())).show(); // format() renders the child row content
                tr.addClass('shown');

                // Delay slightly to ensure DOM is ready before initializing
                setTimeout(() => {
                    initializeHoverListeners();
                }, 200);
            }
        });
    }
});
</script>
@endsection