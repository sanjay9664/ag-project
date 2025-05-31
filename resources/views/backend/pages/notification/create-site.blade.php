<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Device Input Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f9f9f9;
        font-family: 'Segoe UI', sans-serif;
    }

    .form-container {
        max-width: 600px;
        margin: 40px auto;
        padding: 30px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .form-title {
        font-weight: bold;
        text-align: center;
        margin-bottom: 25px;
    }

    .btn-submit {
        background-color: #2196F3;
        color: white;
        font-weight: bold;
        border-radius: 8px;
        transition: background-color 0.3s;
    }

    .btn-submit:hover {
        background-color: #1976D2;
    }
    </style>
</head>

<body>

    <div class="form-container">
        <h4 class="form-title">Device Input Form</h4>

        <!-- Dropdown to select previous data -->
        <div class="mb-3">
            <label for="savedDataDropdown" class="form-label">Load Previous Entry:</label>
            <select id="savedDataDropdown" class="form-select">
                <option value="">-- Select Saved Entry --</option>
            </select>
        </div>

        <form id="deviceForm">
            <div class="mb-3">
                <input type="text" class="form-control" name="deviceName" placeholder="Device Name" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="deviceId" placeholder="Device ID" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="moduleId" placeholder="Module ID" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="eventField" placeholder="Event Field" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="siteId" placeholder="Site ID" required>
            </div>
            <div class="mb-3">
                <input type="number" step="any" class="form-control" name="lowerLimit" placeholder="Lower Limit"
                    required>
            </div>
            <div class="mb-3">
                <input type="number" step="any" class="form-control" name="upperLimit" placeholder="Upper Limit"
                    required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="lowerLimitMsg" placeholder="Lower Limit Message" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="upperLimitMsg" placeholder="Upper Limit Message" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="userEmail" placeholder="User Email ID" required>
            </div>

            <div class="text-center my-3">
                <button class="btn btn-success" onclick="submitToServer()">Submit All to Server</button>
            </div>

        </form>

    </div>

    <script>
    const form = document.getElementById("deviceForm");
    const dropdown = document.getElementById("savedDataDropdown");

    // Load saved entries into dropdown
    function loadDropdown() {
        dropdown.innerHTML = '<option value="">-- Select Saved Entry --</option>';
        const savedEntries = JSON.parse(localStorage.getItem("deviceEntries") || "[]");
        savedEntries.forEach((entry, index) => {
            const option = document.createElement("option");
            option.value = index;
            option.textContent = `${entry.siteName} (${entry.uuid})`;
            dropdown.appendChild(option);
        });
    }

    // Handle form submit
    async function submitToServer() {
        const entries = JSON.parse(localStorage.getItem("deviceEntries") || "[]");

        for (const entry of entries) {
            try {
                const response = await fetch("/store-device-events", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(entry)
                });

                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Server error');

                console.log("Saved:", result);
            } catch (err) {
                console.error("Failed to save entry:", err.message);
            }
        }

        alert("All entries submitted to the server!");
    }


    // Load selected data into form
    dropdown.addEventListener("change", function() {
        const index = this.value;
        if (!index) return;
        const entries = JSON.parse(localStorage.getItem("deviceEntries") || "[]");
        const selected = entries[index];

        if (selected) {
            for (const [key, value] of Object.entries(selected)) {
                form.elements[key].value = value;
            }
        }
    });

    // Initial load
    loadDropdown();
    </script>

</body>

</html>