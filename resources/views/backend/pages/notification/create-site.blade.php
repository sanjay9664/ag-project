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
        <select id="savedDataDropdown" class="form-select">
            <option value="">Select your Save Privious data</option>
            @foreach($data as $index => $entry)
            <option value="{{ $index }}" data-device='@json($entry)'>
                {{ $entry->deviceName ?? 'No Name' }}
            </option>
            @endforeach
        </select>
        <form id="deviceForm" class="mt-3">
            @csrf
            <div class=" mb-3"><input type="text" class="form-control" name="deviceName" placeholder="Device Name"
                    required>
            </div>
            <div class="mb-3"><input type="text" class="form-control" name="deviceId" placeholder="Device ID" required>
            </div>
            <div class="mb-3"><input type="text" class="form-control" name="moduleId" placeholder="Module ID" required>
            </div>
            <div class="mb-3"><input type="text" class="form-control" name="eventField" placeholder="Event Field"
                    required>
            </div>
            <div class="mb-3"><input type="text" class="form-control" name="siteId" placeholder="Site ID" required>
            </div>
            <div class="mb-3"><input type="number" step="any" class="form-control" name="lowerLimit"
                    placeholder="Lower Limit">
            </div>
            <div class="mb-3"><input type="number" step="any" class="form-control" name="upperLimit"
                    placeholder="Upper Limit">
            </div>
            <div class="mb-3"><input type="text" class="form-control" name="lowerLimitMsg"
                    placeholder="Lower Limit Message">
            </div>
            <div class="mb-3"><input type="text" class="form-control" name="upperLimitMsg"
                    placeholder="Upper Limit Message">
            </div>
            <div class="mb-3"><input type="email" class="form-control" name="userEmail" placeholder="User Email ID"
                    required>
            </div>

            <div class="text-center my-3">
                <button class="btn btn-success" onclick="submitSingleEntry(event)">Submit to Server</button>
            </div>
        </form>
    </div>

    <script>
    const form = document.getElementById("deviceForm");
    const dropdown = document.getElementById("savedDataDropdown");

    async function submitSingleEntry(event) {
        event.preventDefault();

        const formData = new FormData(form);
        const jsonObject = {};
        formData.forEach((value, key) => jsonObject[key] = value);

        try {
            const response = await fetch("/admin/store-device-events", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(jsonObject)
            });

            const result = await response.json();

            if (response.ok) {
                alert("✅ Device event saved successfully!");
                window.location.href =
                    "/admin/notification-list";
            } else {
                alert(result.message || "❌ Error saving device event");
                console.error("Validation/server error:", result);
            }
        } catch (error) {
            console.error("❌ Network error:", error);
            alert("❌ Failed to send data to server");
        }
    }



    async function loadDropdownFromServer() {
        try {
            const res = await fetch('/api/device-events');
            const data = await res.json();

            dropdown.innerHTML = `<option value="">-- Select Saved Entry --</option>`;
            data.forEach((entry, index) => {
                const option = document.createElement("option");
                option.value = index;
                option.textContent = entry.deviceName || 'No Name';
                option.dataset.device = JSON.stringify(entry);
                dropdown.appendChild(option);
            });
        } catch (err) {
            console.error("Dropdown load error:", err);
        }
    }


    dropdown.addEventListener("change", function() {
        const selected = this.options[this.selectedIndex];
        const deviceData = selected.dataset.device;

        if (deviceData) {
            const parsed = JSON.parse(deviceData);
            for (const [key, value] of Object.entries(parsed)) {
                if (form.elements[key]) {
                    form.elements[key].value = value;
                }
            }
        }
    });


    loadDropdownFromServer();
    </script>


</body>

</html>