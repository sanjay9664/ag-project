<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Device Input Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
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
            <input type="hidden" id="deviceId" name="id" value="" />

            <div class="mb-3">
                <label for="deviceName" class="form-label visually-hidden">Device Name</label>
                <input type="text" class="form-control" id="deviceName" name="deviceName" placeholder="Device Name"
                    required autocomplete="off" />
            </div>

            <div class="mb-3">
                <label for="deviceUniqueId" class="form-label visually-hidden">Device ID</label>
                <input type="text" class="form-control" id="deviceUniqueId" name="deviceId" placeholder="Device ID"
                    required autocomplete="off" />
            </div>

            <div class="mb-3">
                <label for="moduleId" class="form-label visually-hidden">Module ID</label>
                <input type="text" class="form-control" id="moduleId" name="moduleId" placeholder="Module ID" required
                    autocomplete="off" />
            </div>

            <div class="mb-3">
                <label for="eventField" class="form-label visually-hidden">Event Field</label>
                <input type="text" class="form-control" id="eventField" name="eventField" placeholder="Event Field"
                    required autocomplete="off" />
            </div>

            <div class="mb-3">
                <label for="siteId" class="form-label visually-hidden">Site ID</label>
                <input type="text" class="form-control" id="siteId" name="siteId" placeholder="Site ID" required
                    autocomplete="off" />
            </div>

            <div class="mb-3">
                <label for="lowerLimit" class="form-label visually-hidden">Lower Limit</label>
                <input type="number" step="any" class="form-control" id="lowerLimit" name="lowerLimit"
                    placeholder="Lower Limit" autocomplete="off" />
            </div>

            <div class="mb-3">
                <label for="upperLimit" class="form-label visually-hidden">Upper Limit</label>
                <input type="number" step="any" class="form-control" id="upperLimit" name="upperLimit"
                    placeholder="Upper Limit" autocomplete="off" />
            </div>

            <div class="mb-3">
                <label for="lowerLimitMsg" class="form-label visually-hidden">Lower Limit Message</label>
                <input type="text" class="form-control" id="lowerLimitMsg" name="lowerLimitMsg"
                    placeholder="Lower Limit Message" autocomplete="off" />
            </div>

            <div class="mb-3">
                <label for="upperLimitMsg" class="form-label visually-hidden">Upper Limit Message</label>
                <input type="text" class="form-control" id="upperLimitMsg" name="upperLimitMsg"
                    placeholder="Upper Limit Message" autocomplete="off" />
            </div>

            <div class="mb-3">
                <label class="form-label">Email Addresses</label>
                <div id="emailFieldsContainer">
                    <div class="input-group email-group mb-2">
                        <input type="email" class="form-control" name="userEmails[]" placeholder="User Email ID"
                            required autocomplete="email" />
                        <button type="button" class="btn btn-primary" onclick="addEmailField()">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="userPassword" class="form-label visually-hidden">User Password</label>
                <input type="password" class="form-control" id="userPassword" name="userPassword"
                    placeholder="User Password" autocomplete="current-password" required />
            </div>

            <div class="mb-3">
                <label for="owner_email" class="form-label visually-hidden">Owner Email</label>
                <input type="email" class="form-control" id="owner_email" name="owner_email" placeholder="Owner Email"
                    autocomplete="email" />
            </div>

            <div class="text-center my-3">
                <button type="button" class="btn btn-success" onclick="submitDeviceForm()">Submit to Server</button>
                <button type="button" class="btn btn-secondary ms-2" onclick="resetForm()">Reset Form</button>
            </div>
        </form>
    </div>


    <!-- Include EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.emailjs.com/dist/email.min.js"></script>
    <script type="text/javascript">
    (function() {
        emailjs.init("XV9V8o3w_jtgjHR7J");
    })();

    // Function to send email to owner
    const sendMailToOwner = (to_email, site_name) => {
        emailjs.send(
            'service_l362y9i',
            'template_nd323pd', {
                to_email,
                site_name
            },
            'XV9V8o3w_jtgjHR7J'
        ).then(response => {
            console.log('✅ Email sent!', response.status, response.text);
        }).catch(err => {
            console.error('❌ Email error:', err);
        });
    };


    // Example: Fetch from Laravel API and send email to each owner
    fetch('/api/fetch-owner-emails') // ✅ Replace with actual route
        .then(res => res.json())
        .then(data => {
            if (data.status && Array.isArray(data.owners)) {
                data.owners.forEach(owner => {
                    sendMailToOwner(owner.owner_email, owner.deviceName);
                });
            }
        })
        .catch(err => console.error('❌ Fetch error:', err));
    </script>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('deviceForm');
        const dropdown = document.getElementById('savedDataDropdown');
        const emailFieldsContainer = document.getElementById('emailFieldsContainer');
        const deviceIdHiddenField = document.getElementById('deviceId');

        // Add email field function
        window.addEmailField = function() {
            const emailGroup = document.createElement('div');
            emailGroup.className = 'input-group email-group mb-2';
            emailGroup.innerHTML = `
                    <input type="email" class="form-control" name="userEmails[]" placeholder="Additional Email ID" autocomplete="email" />
                    <button type="button" class="btn btn-danger" onclick="removeEmailField(this)">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                `;
            emailFieldsContainer.appendChild(emailGroup);
        };

        // Remove email field function
        window.removeEmailField = function(button) {
            const emailGroups = document.querySelectorAll('.email-group');
            if (emailGroups.length > 1) {
                button.closest('.email-group').remove();
            }
        };

        // Reset form function
        window.resetForm = function() {
            form.reset();
            deviceIdHiddenField.value = '';
            emailFieldsContainer.innerHTML = `
                    <div class="input-group email-group mb-2">
                        <input type="email" class="form-control" name="userEmails[]" placeholder="User Email ID" required autocomplete="email" />
                        <button type="button" class="btn btn-primary" onclick="addEmailField()">
                            <i class="bi bi-plus"></i> Add
                        </button>
                    </div>
                `;
        };

        // Dropdown change handler
        dropdown.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const deviceData = selected.dataset.device;

            if (deviceData) {
                const parsed = JSON.parse(deviceData);
                resetForm();

                // Fill hidden id field for update
                deviceIdHiddenField.value = parsed.id || '';

                // Fill other form fields
                for (const [key, value] of Object.entries(parsed)) {
                    if (key === 'userEmail') {
                        // Emails may be saved as JSON array or comma separated string, handle both
                        let emails = [];
                        if (typeof value === 'string') {
                            try {
                                emails = JSON.parse(value);
                                if (!Array.isArray(emails)) emails = value.split(',').map(e => e
                                    .trim());
                            } catch {
                                emails = value.split(',').map(e => e.trim());
                            }
                        } else if (Array.isArray(value)) {
                            emails = value;
                        }

                        emailFieldsContainer.innerHTML = '';
                        emails.forEach((email, idx) => {
                            if (idx === 0) {
                                const firstGroup = document.createElement('div');
                                firstGroup.className = 'input-group email-group mb-2';
                                firstGroup.innerHTML = `
                                        <input type="email" class="form-control" name="userEmails[]" value="${email}" placeholder="User Email ID" required autocomplete="email" />
                                        <button type="button" class="btn btn-primary" onclick="addEmailField()">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
                                    `;
                                emailFieldsContainer.appendChild(firstGroup);
                            } else {
                                addEmailField();
                                const emailInputs = document.querySelectorAll(
                                    'input[name="userEmails[]"]');
                                emailInputs[emailInputs.length - 1].value = email;
                            }
                        });

                        if (emails.length === 0) resetForm();
                    } else {
                        const inputField = form.querySelector(`[name="${key}"]`);
                        if (inputField) inputField.value = value ?? '';
                    }
                }
            } else {
                resetForm();
            }
        });

        // Submit form function
      window.submitDeviceForm = async function () {
    const emailInputs = document.querySelectorAll('input[name="userEmails[]"]');
    let emails = [];

    emailInputs.forEach((input) => {
        const trimmed = input.value.trim();
        if (trimmed !== '') {
            emails.push(trimmed);
        }
    });

    if (emails.length === 0) {
        alert('Please enter at least one email address');
        return;
    }

    const formData = new FormData(form);
    const jsonObject = {};

    formData.forEach((value, key) => {
        if (key !== 'userEmails[]' && key !== '_token') {
            jsonObject[key] = value;
        }
    });

    // Include email array
    jsonObject.userEmail = emails;

    // Include hidden ID for update detection
    jsonObject.id = deviceIdHiddenField.value;

    // Basic validation check
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    try {
        const isUpdate = jsonObject.id !== '';
        const url = isUpdate
            ? '/admin/update-device-events'
            : '/admin/store-device-events';

        const method = isUpdate ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
            },
            body: JSON.stringify(jsonObject),
        });

        const result = await response.json();

        if (response.ok) {
            alert('✅ Device saved successfully!');
            window.location.reload();
        } else {
            if (result.errors) {
                const errorMessages = Object.values(result.errors)
                    .flat()
                    .join('\n');
                alert(`❌ Validation Errors:\n${errorMessages}`);
            } else {
                alert(result.message || '❌ Something went wrong.');
            }
            console.error(result);
        }
    } catch (error) {
        console.error('❌ Network error:', error);
        alert('❌ Failed to save device. See console for details.');
    }
};

    });
    </script>
</body>

</html>