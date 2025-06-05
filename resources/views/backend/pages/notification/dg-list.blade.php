<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>Dashboard Add Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    /* Status indicator styling */
    .status-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    /* Dot for status */
    .dot {
        height: 12px;
        width: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .green {
        background-color: #28a745;
        /* success green */
    }

    .red {
        background-color: #dc3545;
        /* danger red */
    }

    /* Action buttons */
    .action-btns {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-btns .btn {
        padding: 4px 10px;
        font-size: 13px;
        min-width: 70px;
        border-radius: 4px;
        transition: all 0.2s ease-in-out;
    }

    .action-btns .btn:hover {
        transform: scale(1.05);
    }

    /* Full-width table with wrapping and responsive font */
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        word-wrap: break-word;
        font-size: 14px;
    }

    /* Column and row alignment */
    th,
    td {
        text-align: center;
        vertical-align: middle;
        padding: 8px;
        word-break: break-word;
    }

    /* Table header styling */
    thead th {
        background-color: #343a40;
        color: #fff;
    }

    /* Responsive tweaks */
    @media (max-width: 768px) {
        table {
            font-size: 12px;
        }

        .action-btns .btn {
            font-size: 11px;
            min-width: 60px;
        }

        .status-indicator {
            font-size: 0.8rem;
        }

        .dot {
            height: 10px;
            width: 10px;
        }
    }

    @media (max-width: 576px) {
        table {
            font-size: 11px;
        }

        th,
        td {
            padding: 6px;
        }

        .action-btns {
            flex-direction: column;
            gap: 4px;
        }

        .action-btns .btn {
            width: 100%;
        }
    }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-4">
        <h3 class="text-center mb-4">Notifications List Entries</h3>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Device Name</th>
                        <th>Device ID</th>
                        <th>Module ID</th>
                        <th>Event Field</th>
                        <th>Site ID</th>
                        <th>Lower Limit</th>
                        <th>Upper Limit</th>
                        <th>Low Limit Message</th>
                        <th>Upper Limit Message</th>
                        <th>User Email ID</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <!-- <th></th> -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <!-- <tbody id="tableBody"></tbody> -->
                <tbody>
                    @foreach($data as $val => $device)
                    <tr>
                        <td>{{++$val}}</td>
                        <td>{{$device->deviceName}}</td>
                        <td>{{$device->deviceId}}</td>
                        <td>{{$device->moduleId}}</td>
                        <td>{{$device->eventField}}</td>
                        <td>{{$device->siteId}}</td>
                        <td>{{$device->lowerLimit}}</td>
                        <td>{{$device->upperLimit}}</td>
                        <td>{{$device->lowerLimitMsg}}</td>
                        <td>{{$device->upperLimitMsg}}</td>
                        <td>{{$device->userEmail}}</td>
                        <td>{{$device->created_at}}</td>
                        <td>{{$device->updated_at}}</td>
                        <!-- <td></td> -->
                        <td class="action-btns">
                            <button class="btn btn-warning btn-sm"
                                onclick="editRow('{{ $device->deviceId }}', this)">Edit</button>
                            <button class="btn btn-danger btn-sm"
                                onclick="deleteRow('{{ $device->deviceId }}', this)">Delete</button>
                        </td>


                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    const tableBody = document.getElementById("tableBody");

    async function loadTableFromServer() {
        try {
            const response = await fetch("/admin/device-events");
            const entries = await response.json();

            tableBody.innerHTML = "";

            entries.forEach((entry, index) => {
                const updated = entry.updated_at ?
                    new Date(entry.updated_at).toLocaleString() :
                    "N/A";

                // You can replace this with real logic based on entry values
                const isDGOn = Math.random() > 0.5;
                const statusHTML = `
            <div class="status-indicator">
              <span class="dot ${isDGOn ? 'green' : 'red'}"></span>
            </div>
          `;

                const row = document.createElement("tr");
                row.innerHTML = `
            <td>${index + 1}</td>
            <td>${entry.deviceName || ""}</td>
            <td>${entry.deviceId || ""}</td>
            <td>${entry.moduleId || ""}</td>
            <td>${entry.eventField || ""}</td>
            <td>${entry.siteId || ""}</td>
            <td>${entry.lowerLimit ?? ""}</td>
            <td>${entry.upperLimit ?? ""}</td>
            <td>${entry.lowerLimitMsg || ""}</td>
            <td>${entry.upperLimitMsg || ""}</td>
            <td>${entry.userEmail || ""}</td>
            <td>${updated}</td>
            <td>${statusHTML}</td>
            <td class="action-btns">
              <button class="btn btn-warning btn-sm">Edit</button>
              <button class="btn btn-danger btn-sm">Delete</button>
            </td>
          `;
                tableBody.appendChild(row);
            });
        } catch (error) {
            console.error("Failed to load device events:", error);
            tableBody.innerHTML = `
          <tr>
            <td colspan="14" class="text-danger">Error loading data.</td>
          </tr>
        `;
        }
    }

    // Call on page load
    loadTableFromServer();

    function editRow(deviceId, btn) {
        const row = btn.closest("tr");
        const tds = row.querySelectorAll("td:not(:last-child)");

        // Store original values and replace with inputs
        tds.forEach((td, index) => {
            // Skip S.No column (index 0)
            if (index === 0) return;

            const oldText = td.innerText.trim();
            td.setAttribute("data-old", oldText);
            td.innerHTML = `<input type="text" class="form-control form-control-sm" value="${oldText}">`;
        });

        // Change buttons
        const btnGroup = row.querySelector(".action-btns");
        btnGroup.innerHTML = `
        <button class="btn btn-success btn-sm" onclick="saveRow('${deviceId}', this)">Save</button>
        <button class="btn btn-secondary btn-sm" onclick="cancelEdit(this)">Cancel</button>
    `;
    }

    function cancelEdit(btn) {
        const row = btn.closest("tr");
        const tds = row.querySelectorAll("td:not(:last-child)");

        tds.forEach((td, index) => {
            const old = td.getAttribute("data-old");
            if (old !== null) td.innerText = old;
        });

        const btnGroup = row.querySelector(".action-btns");
        const deviceId = row.querySelector("td:nth-child(2)").innerText.trim(); // Assuming deviceId is in second column

        btnGroup.innerHTML = `
        <button class="btn btn-warning btn-sm" onclick="editRow('${deviceId}', this)">Edit</button>
        <button class="btn btn-danger btn-sm" onclick="deleteRow('${deviceId}', this)">Delete</button>
    `;
    }

    function saveRow(deviceId, btn) {
        const row = btn.closest('tr');
        const inputs = row.querySelectorAll('td:not(:last-child) input');

        // Create data object based on your table structure
        const updatedData = {};
        const fieldNames = ['deviceName', 'deviceId', 'moduleId', 'eventField', 'siteId',
            'lowerLimit', 'upperLimit', 'lowerLimitMsg', 'upperLimitMsg', 'userEmail'
        ];

        inputs.forEach((input, index) => {
            if (index > 0) { // Skip S.No
                updatedData[fieldNames[index - 1]] = input.value;
            }
        });

        // Simple validation
        if (!updatedData.deviceId || !updatedData.deviceName) {
            alert('Device ID and Name are required!');
            return;
        }

        $.ajax({
            url: `/update-device-events/${deviceId}`,
            method: "POST",
            data: {
                _method: "PUT",
                _token: document.querySelector('meta[name="csrf-token"]').content,
                ...updatedData
            },
            success: function(response) {
                // Update the row with new values
                inputs.forEach((input, index) => {
                    if (index > 0) {
                        row.children[index].innerHTML = input.value;
                    }
                });

                // Restore buttons
                const btnGroup = row.querySelector(".action-btns");
                btnGroup.innerHTML = `
                <button class="btn btn-warning btn-sm" onclick="editRow('${deviceId}', this)">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteRow('${deviceId}', this)">Delete</button>
            `;

                alert("Updated successfully!");
            },
            error: function(xhr) {
                alert("Failed to update: " + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }
    </script>
</body>

</html>