<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Add Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    .status-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-weight: bold;
    }

    .dot {
        height: 12px;
        width: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .green {
        background-color: green;
    }

    .red {
        background-color: red;
    }

    .action-btns {
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .action-btns .btn {
        padding: 4px 12px;
        font-size: 14px;
        min-width: 70px;
    }

    table {
        width: 100%;
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
                        <th>Updated</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </div>

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
    </script>
</body>

</html>