<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Device Input Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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

    .email-group {
        margin-bottom: 10px;
    }
    </style>
</head>

<body>

    <div class="form-container">
        <h4 class="form-title">Device Input Form</h4>
        <select id="savedDataDropdown" class="form-select mb-3">
            <option value="">Select your saved data</option>
            @foreach($data as $index => $entry)
            <option value="{{ $index }}" data-device='@json($entry)'>
                {{ $entry->deviceName ?? 'No Name' }}
            </option>
            @endforeach
        </select>

        <form id="deviceForm" class="mt-3">
            @csrf
            <input type="hidden" id="deviceId" name="id" value="">

            <div class="mb-3">
                <label for="deviceName" class="form-label visually-hidden">Device Name</label>
                <input type="text" class="form-control" id="deviceName" name="deviceName" placeholder="Device Name"
                    required>
            </div>
            <div class="mb-3">
                <label for="deviceUniqueId" class="form-label visually-hidden">Device ID</label>
                <input type="text" class="form-control" id="deviceUniqueId" name="deviceId" placeholder="Device ID"
                    required>
            </div>
            <div class="mb-3">
                <label for="moduleId" class="form-label visually-hidden">Module ID</label>
                <input type="text" class="form-control" id="moduleId" name="moduleId" placeholder="Module ID" required>
            </div>
            <div class="mb-3">
                <label for="eventField" class="form-label visually-hidden">Event Field</label>
                <input type="text" class="form-control" id="eventField" name="eventField" placeholder="Event Field"
                    required>
            </div>
            <div class="mb-3">
                <label for="siteId" class="form-label visually-hidden">Site ID</label>
                <input type="text" class="form-control" id="siteId" name="siteId" placeholder="Site ID" required>
            </div>
            <div class="mb-3">
                <label for="lowerLimit" class="form-label visually-hidden">Lower Limit</label>
                <input type="number" step="any" class="form-control" id="lowerLimit" name="lowerLimit"
                    placeholder="Lower Limit">
            </div>
            <div class="mb-3">
                <label for="upperLimit" class="form-label visually-hidden">Upper Limit</label>
                <input type="number" step="any" class="form-control" id="upperLimit" name="upperLimit"
                    placeholder="Upper Limit">
            </div>
            <div class="mb-3">
                <label for="lowerLimitMsg" class="form-label visually-hidden">Lower Limit Message</label>
                <input type="text" class="form-control" id="lowerLimitMsg" name="lowerLimitMsg"
                    placeholder="Lower Limit Message">
            </div>
            <div class="mb-3">
                <label for="upperLimitMsg" class="form-label visually-hidden">Upper Limit Message</label>
                <input type="text" class="form-control" id="upperLimitMsg" name="upperLimitMsg"
                    placeholder="Upper Limit Message">
            </div>

            <div class="mb-3">
                <label class="form-label">Email Addresses</label>
                <div id="emailFieldsContainer">
                    <div class="input-group email-group mb-2">
                        <input type="email" class="form-control" name="userEmails[]" placeholder="User Email ID"
                            required>
                        <button type="button" class="btn btn-primary" onclick="addEmailField()">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="userPassword" class="form-label visually-hidden">User Password</label>
                <input type="password" class="form-control" id="userPassword" name="userPassword"
                    placeholder="User Password" autocomplete="current-password" required>
            </div>

            <div class="mb-3">
                <label for="owner_email" class="form-label visually-hidden">Owner Email</label>
                <input type="email" class="form-control" id="owner_email" name="owner_email" placeholder="Owner Email">
            </div>

            <div class="text-center my-3">
                <button type="button" class="btn btn-success" onclick="submitDeviceForm()">Submit to Server</button>
                <button type="button" class="btn btn-secondary ms-2" onclick="resetForm()">Reset Form</button>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById("deviceForm");
        const dropdown = document.getElementById("savedDataDropdown");
        const emailFieldsContainer = document.getElementById("emailFieldsContainer");
        const deviceIdHiddenField = document.getElementById("deviceId"); // Renamed to avoid conflict

        // Add email field
        window.addEmailField = function() {
            const emailGroup = document.createElement('div');
            emailGroup.className = 'input-group email-group mb-2';

            emailGroup.innerHTML = `
                    <input type="email" class="form-control" name="userEmails[]" placeholder="Additional Email ID">
                    <button type="button" class="btn btn-danger" onclick="removeEmailField(this)">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                `;

            emailFieldsContainer.appendChild(emailGroup);
        }

        // Remove email field
        window.removeEmailField = function(button) {
            const emailGroups = document.querySelectorAll('.email-group');
            if (emailGroups.length > 1) { // Keep at least one email field
                button.closest('.email-group').remove();
            }
        }

        // Reset form
        window.resetForm = function() {
            form.reset();
            deviceIdHiddenField.value = ''; // Clear the hidden ID field
            // Reset to single email field
            emailFieldsContainer.innerHTML = `
                    <div class="input-group email-group mb-2">
                        <input type="email" class="form-control" name="userEmails[]" placeholder="User Email ID" required>
                        <button type="button" class="btn btn-primary" onclick="addEmailField()">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                `;
        }

        // Handle dropdown selection
        dropdown.addEventListener("change", function() {
            const selected = this.options[this.selectedIndex];
            const deviceData = selected.dataset.device;

            if (deviceData) {
                const parsed = JSON.parse(deviceData);

                // Clear all fields first to ensure a clean slate
                resetForm();

                // Set the device ID for updates
                deviceIdHiddenField.value = parsed.id || ''; // Populate the hidden ID field

                // Fill form fields
                for (const [key, value] of Object.entries(parsed)) {
                    if (key === 'userEmail') {
                        const emails = value.split(',').filter(email => email.trim() !== '');

                        // Clear existing email fields before populating
                        emailFieldsContainer.innerHTML = '';

                        // Create fields for each email
                        emails.forEach((email, index) => {
                            if (index === 0) {
                                // First email goes in the initial field (or a new one if cleared)
                                const firstGroup = document.createElement('div');
                                firstGroup.className = 'input-group email-group mb-2';
                                firstGroup.innerHTML = `
                                        <input type="email" class="form-control" name="userEmails[]" value="${email}" placeholder="User Email ID" required>
                                        <button type="button" class="btn btn-primary" onclick="addEmailField()">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
                                    `;
                                emailFieldsContainer.appendChild(firstGroup);
                            } else {
                                // Add new email fields for subsequent emails
                                addEmailField();
                                const emailInputs = document.querySelectorAll(
                                    'input[name="userEmails[]"]');
                                // Set the value of the newly added input
                                emailInputs[emailInputs.length - 1].value = email;
                            }
                        });
                        // If no emails, ensure one empty field is present
                        if (emails.length === 0) {
                            resetForm(); // This will put back the default single email field
                        }

                    } else {
                        // Handle other form fields by their 'name' attribute
                        const inputField = form.querySelector(`[name="${key}"]`);
                        if (inputField) {
                            inputField.value = value || '';
                        }
                    }
                }
            } else {
                // If no device is selected (e.g., "Select your saved data" is chosen)
                resetForm();
            }
        });

        // Submit form
        window.submitDeviceForm = async function() {
            // Validate at least one email is entered
            const emailInputs = document.querySelectorAll('input[name="userEmails[]"]');
            let hasValidEmail = false;
            emailInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    hasValidEmail = true;
                }
            });

            if (!hasValidEmail) {
                alert("Please enter at least one email address");
                return;
            }

            // Check if all required fields are filled
            if (!form.checkValidity()) {
                alert("Please fill in all required fields.");
                form.reportValidity(); // Show native browser validation messages
                return;
            }


            const formData = new FormData(form);
            const jsonObject = {};

            // Handle multiple emails
            const emails = [];
            emailInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    emails.push(input.value.trim());
                }
            });
            jsonObject.userEmail = emails.join(','); // Join emails with a comma

            // Add other form fields to the JSON object
            formData.forEach((value, key) => {
                if (key !== 'userEmails[]' && key !==
                    '_token'
                ) { // Exclude userEmails[] and CSRF token from direct FormData iteration
                    jsonObject[key] = value;
                }
            });

            // Ensure the 'id' (deviceIdHiddenField) is included in the JSON object for updates
            jsonObject.id = deviceIdHiddenField.value;


            try {
                const isUpdate = jsonObject.id !== '';
                // Assuming your routes are correct
                const url = isUpdate ? "/admin/update-device-events" : "/admin/store-device-events";
                const method = isUpdate ? "PUT" : "POST";

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify(jsonObject)
                });

                const result = await response.json();

                if (response.ok) {
                    alert("✅ Device saved successfully!");
                    window.location.reload(); // Refresh to show updated list
                } else {
                    // Display error message from server if available, otherwise a generic one
                    alert(result.message || "❌ Error saving device");
                    console.error("Error:", result);
                }
            } catch (error) {
                console.error("❌ Network error:", error);
                alert("❌ Already Save .");
            }
        }

    });
    </script>

</body>

</html>