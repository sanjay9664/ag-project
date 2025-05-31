<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Add Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    .blinking {
        animation: blink 1s infinite;
    }

    @keyframes blink {
        50% {
            opacity: 0;
        }
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: bold;
    }

    .green-dot {
        height: 12px;
        width: 12px;
        background-color: green;
        border-radius: 50%;
        display: inline-block;
    }

    .red-dot {
        height: 12px;
        width: 12px;
        background-color: red;
        border-radius: 50%;
        display: inline-block;
    }

    .action-btns button {
        margin: 0 2px;
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

    <div class="container mt-4" style="max-width: 100%;">
        <h3 class="text-center mb-4">DG Device Entries</h3>

        <!-- Table -->
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

    <script>
    const tableBody = document.getElementById("tableBody");

    function loadTableFromLocalStorage() {
        const entries = JSON.parse(localStorage.getItem("deviceEntries") || "[]");
        tableBody.innerHTML = "";
        entries.forEach((entry, index) => {
            const updated = new Date().toLocaleString();
            const isDGOn = Math.random() > 0.5;

            const statusHTML = isDGOn ?
                `<div class="status-indicator"><span class="green-dot"></span></div>` :
                `<div class="status-indicator"><span class="red-dot"></span></div>`;

            // Provide default empty string if fields are undefined
            const deviceName = entry.deviceName || "";
            const deviceId = entry.deviceId || "";
            const moduleId = entry.moduleId || "";
            const eventField = entry.eventField || "";
            const siteId = entry.siteId || "";
            const lowerLimit = entry.lowerLimit || "";
            const upperLimit = entry.upperLimit || "";
            const lowLimitMessage = entry.lowerLimitMsg || "";
            const upperLimitMessage = entry.upperLimitMsg || "";
            const email = entry.userEmail || "";

            const row = document.createElement("tr");

            row.innerHTML = `
          <td>${index + 1}</td>
          <td contenteditable="false">${deviceName}</td>
          <td contenteditable="false">${deviceId}</td>
          <td contenteditable="false">${moduleId}</td>
          <td contenteditable="false">${eventField}</td>
          <td contenteditable="false">${siteId}</td>
          <td contenteditable="false">${lowerLimit}</td>
          <td contenteditable="false">${upperLimit}</td>
          <td contenteditable="false">${lowLimitMessage}</td>
          <td contenteditable="false">${upperLimitMessage}</td>
          <td contenteditable="false">${email}</td>
          <td>${updated}</td>
          <td class="blinking">${statusHTML}</td>
          <td class="action-btns">
            <button class="btn btn-sm btn-primary" onclick="editRow(this)">Edit</button>
            <button class="btn btn-sm btn-danger" onclick="deleteRow(${index})">Delete</button>
          </td>
        `;

            tableBody.appendChild(row);
        });
    }

    function editRow(button) {
        const row = button.closest("tr");
        const editableCells = row.querySelectorAll("td[contenteditable]");
        const isEditing = button.textContent === "Save";

        if (isEditing) {
            const entries = JSON.parse(localStorage.getItem("deviceEntries") || "[]");
            const index = row.rowIndex - 1;
            const updatedData = Array.from(editableCells).map(td => td.textContent.trim());

            entries[index] = {
                deviceName: updatedData[0],
                deviceId: updatedData[1],
                moduleId: updatedData[2],
                eventField: updatedData[3],
                siteId: updatedData[4],
                lowerLimit: updatedData[5],
                upperLimit: updatedData[6],
                lowerLimitMsg: updatedData[7],
                upperLimitMsg: updatedData[8],
                userEmail: updatedData[9],
            };

            localStorage.setItem("deviceEntries", JSON.stringify(entries));
            button.textContent = "Edit";
            editableCells.forEach(cell => cell.setAttribute("contenteditable", "false"));
        } else {
            button.textContent = "Save";
            editableCells.forEach(cell => cell.setAttribute("contenteditable", "true"));
        }
    }

    function deleteRow(index) {
        if (confirm("Are you sure you want to delete this entry?")) {
            const entries = JSON.parse(localStorage.getItem("deviceEntries") || "[]");
            entries.splice(index, 1);
            localStorage.setItem("deviceEntries", JSON.stringify(entries));
            loadTableFromLocalStorage();
        }
    }

    window.addEventListener("DOMContentLoaded", loadTableFromLocalStorage);
    </script>

</body>

</html>