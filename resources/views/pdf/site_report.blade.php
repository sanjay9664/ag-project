<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>DGMS Site Overview Report</title>
</head>

<body style="margin: 20px; font-family: Arial, sans-serif; font-size: 12px; color: #333;">

    <!-- Header -->
    <div style="text-align: center; margin-bottom: 20px;">

        <h2 style="margin: 0; color: #1976d2; font-size: 18px;">DG SET MONITORING SYSTEM</h2>
        <p style="margin: 4px 0 0; font-size: 11px; color: #666;">Site Overview Report</p>
    </div>

    <!-- Table -->
    <table cellspacing="0" cellpadding="6"
        style="width: 100%; border-collapse: collapse; font-size: 11px; table-layout: fixed; word-wrap: break-word;">

        <thead>
            <tr style="background: #1976d2; color: #fff; text-align: center;">
                <th style="border: 1px solid #ccc; padding: 6px; width: 5%;">S.No</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 15%;">Site Name</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 10%;">DG Status</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 20%;">Fuel Level</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 10%;">Bank Name</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 10%;">Location</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 10%;">ID</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 10%;">Run Hours</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 10%;">Updated</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 10%;">RMS</th>
                <th style="border: 1px solid #ccc; padding: 6px; width: 10%;">Controller</th>
            </tr>

        </thead>

        <tbody>
            @php $i=1; @endphp
            @foreach($siteData as $site)
            @php
            $sitejsonData = json_decode($site->data, true);
            $updatedAt = null;
            $isRecent = false;
            $formattedUpdatedAt = 'N/A';

            if (!empty($site->updatedAt) && $site->updatedAt !== 'N/A') {
            try {
            $updatedAt = Carbon\Carbon::parse($site->updatedAt)->timezone('Asia/Kolkata');
            $now = Carbon\Carbon::now('Asia/Kolkata');
            $isRecent = $updatedAt->diffInHours($now) < 24; $formattedUpdatedAt=$updatedAt->format("d M Y h:i A");
                } catch (\Exception $e) {
                \Log::error('Date Parsing Error: ' . $e->getMessage());
                }
                }

                $runMd = $sitejsonData['run_status']['md'] ?? null;
                $runKey = $sitejsonData['run_status']['add'] ?? null;
                $runValue = 0;

                foreach ($eventData as $event) {
                $eventArray = $event instanceof \ArrayObject ? $event->getArrayCopy() : (array) $event;
                if ($runMd && isset($eventArray['module_id']) && $eventArray['module_id'] == $runMd) {
                if ($runKey && array_key_exists($runKey, $eventArray)) {
                $runValue = $eventArray[$runKey];
                }
                break;
                }
                }

                $statusText = (is_numeric($runValue) && $runValue > 0) ? 'ON' : 'OFF';
                $statusColor = $statusText === 'ON' ? '#388e3c' : '#d32f2f';

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
                $fuelBarColor = $percentage > 50 ? '#4caf50' : ($percentage > 20 ? '#ff9800' : '#f44336');
                $lowFuelText = $percentage <= 20 ? 'Low Fuel' : '' ; @endphp <tr
                    style="border-bottom: 1px solid #eee; text-align: center;">
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $i }}</td>
                    @php
                        $dgStatus = trim(str_replace('"', '', $site->dg_status));
                        $controllerStatus = trim(str_replace('"', '', $site->controller_status));
                    @endphp

                    <td style="border: 1px solid #ccc; padding: 6px; text-align: left; font-weight: bold; 
                        color: {{ $dgStatus === 'OFFLINE' ? 'red' : 'green' }};">
                        {{ $site->site_name }}
                    </td>

                    <td style="border: 1px solid #ccc; padding: 6px;">
                        <span style="color: {{ $statusColor }}; font-weight: bold;">{{ $statusText }}</span>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 6px; text-align: center;">
                        <div
                            style="width: 120px; height: 12px; border: 1px solid #333; border-radius: 3px; margin: 0 auto; position: relative; background: #f5f5f5; overflow: hidden;">
                            <div style="width: {{ $percentage }}%; height: 100%; background: {{ $fuelBarColor }};">
                            </div>
                            <span style="
                            position: absolute; 
                            top: 50%; 
                            left: 50%; 
                            transform: translate(-50%, -50%);
                            font-size: 9px; 
                            font-weight: bold; 
                            color: {{ $percentage > 0 ? '#000' : '#333' }};
                            white-space: nowrap;">
                                {{ round($percentage) }}%
                            </span>
                        </div>
                        @if($lowFuelText)
                        <div style="margin-top: 2px; font-size: 9px; font-weight: bold; color: #d32f2f;">
                            {{ $lowFuelText }}
                        </div>
                        @endif
                    </td>

                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $sitejsonData['generator'] ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $sitejsonData['group'] ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $sitejsonData['serial_number'] ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">
                        @php
                        $increased_running_hours = DB::table('running_hours')->where('site_id', $site->id)->first();
                        $increaseRunningHours = (float) ($increased_running_hours->increase_running_hours ?? 0);

                        $addValue = 0;
                        $key = $sitejsonData['running_hours']['add'] ?? null;
                        $md = $sitejsonData['running_hours']['md'] ?? null;

                        if ($key && $md && !empty($eventData)) {
                            foreach ($eventData as $event) {
                                if ($event instanceof \ArrayObject) {
                                    $eventArray = $event->getArrayCopy();
                                } else {
                                    $eventArray = (array) $event;
                                }

                                if (isset($eventArray['module_id']) && $eventArray['module_id'] == $md) {
                                    if (array_key_exists($key, $eventArray)) {
                                        $addValue = (float) $eventArray[$key];
                                    }
                                    break;
                                }
                            }
                        }

                        $increaseMinutes = (float) ($sitejsonData['running_hours']['increase_minutes'] ?? 1);
                        $inc_addValue = $increaseMinutes > 0 ? ($addValue / $increaseMinutes) : $addValue;

                        $inc_addValueFormatted = $inc_addValue + $increaseRunningHours;

                        if ($inc_addValueFormatted < 0) { $inc_addValueFormatted=0; } // Convert to total minutes
                            $totalMinutes=round($inc_addValueFormatted * 60); $hours=floor($totalMinutes / 60);
                            $minutes=$totalMinutes % 60; @endphp {{ $hours }} hrs {{ $minutes }} mins </td>

                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $formattedUpdatedAt }}</td>

                    <td
                        style="border: 1px solid #ccc; padding: 6px; font-weight: bold; color: {{ $dgStatus === 'OFFLINE' ? 'red' : ($dgStatus === 'ONLINE' ? 'green' : 'black') }};">
                        {{ $dgStatus }}
                    </td>

                    <td
                        style="border: 1px solid #ccc; padding: 6px; font-weight: bold; color: {{ $controllerStatus === 'OFFLINE' ? 'red' : ($controllerStatus === 'ONLINE' ? 'green' : 'black') }};">
                        {{ $controllerStatus }}
                    </td>
                    </tr>
                    @php $i++; @endphp
                    @endforeach
        </tbody>
    </table>

</body>

</html>