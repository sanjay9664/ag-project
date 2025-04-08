<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-primary">
            <tr>
                <th>S.No</th>
                <th>Site Name</th>
                <th>Bank Name</th>
                <th>Location</th>
                <th>Id</th>
                <th>Fuel Level</th>
                <th>Total Run Hours</th>
                <th>Updated Date</th>
                <th>DG Status</th>
            </tr>
        </thead>
        <tbody>
            @php $i=1; @endphp
            @foreach ($siteData as $site)
            @php
            $sitejsonData = json_decode($site->data, true);
            $updatedAt = null;
            $isRecent = false;

            if (!empty($site->updatedAt) && $site->updatedAt !== 'N/A') {
            try {
            $updatedAt = Carbon\Carbon::parse($site->updatedAt);
            $now = Carbon\Carbon::now();

            $isRecent = $updatedAt->diffInHours($now) < 24; } catch (\Exception $e) { \Log::error('Date Parsing Error: ' . $e->getMessage());
                        }
                }
            @endphp
            <tr data-site-id="{{ $site->id }}">
                <td>{{ $i }}</td>
                <td style="color: {{ $isRecent ? 'green' : 'red' }};">
                    <a href="{{ url('admin/sites/'.$site->slug . '?role=admin') }}"
                        style="text-decoration: none; color: inherit;font-weight: bold;" target="_blank">
                        {{ $site->site_name }}
                    </a>
                </td>
                <td>{{ $sitejsonData['generator'] ?? 'N/A' }}</td>
                <td>{{ $sitejsonData['group'] ?? 'N/A' }}</td>
                <td>{{ $sitejsonData['serial_number'] ?? 'N/A' }}</td>
                <td>
                      @php
                                $capacity = $sitejsonData[' capacity'] ?? 0;
                $fuelMd=$sitejsonData['parameters']['fuel']['md'] ?? null;
                $fuelKey=$sitejsonData['parameters']['fuel']['add'] ?? null; $addValue='_' ; foreach ($eventData as
                $event) { $eventArray=$event->getArrayCopy();
                if ($fuelMd && isset($eventArray['module_id']) && $eventArray['module_id'] == $fuelMd) {
                if ($fuelKey && array_key_exists($fuelKey, $eventArray)) {
                $addValue = $eventArray[$fuelKey];
                }
                break;
                }
                }

                $percentage = is_numeric($addValue) ? $addValue : 0;
                $percentageDecimal = $percentage / 100;
                $totalFuelLiters = $capacity * $percentageDecimal;
                $fuelClass = $percentage <= 20 ? 'low-fuel' : 'normal-fuel' ; $lowFuelText=$percentage <=20 ? 'Low Fuel'
                    : '' ; @endphp <div class="fuel-container" style="position: relative; width: 100%;">
                    <div class="fuel-indicator {{ $fuelClass }}" style="display: flex; align-items: center;">
                        <div class="fuel-level">
                        </div>
                        <span class="fuel-percentage">{{ $percentage }}%</span>
                    </div>

                    @if($lowFuelText)
                    <span class="fueldata">{{ $lowFuelText }}</span>
                    @endif
</div>
</td>

<td class="running-hours">
    @php
    $increased_running_hours = DB::table('running_hours')->where('site_id', $site->id)->first();
    $increaseRunningHours = (float) ($increased_running_hours->increase_running_hours ?? 0);
    $addValue = 0;
    $key = $sitejsonData['running_hours']['add'] ?? null;
    $md = $sitejsonData['running_hours']['md'] ?? null;

    if ($key && $md) {
    foreach ($eventData as $event) {
    $eventArray = $event->getArrayCopy();
    if (isset($eventArray['module_id']) && $eventArray['module_id'] == $md) {
    if (array_key_exists($key, $eventArray)) {
    $addValue = (float) $eventArray[$key];
    }
    break;
    }
    }
    }

    $increaseMinutes = $sitejsonData['running_hours']['increase_minutes'] ?? 1;
    $inc_addValue = $increaseMinutes > 0 ? $addValue / $increaseMinutes : $addValue;
    $inc_addValueFormatted = number_format($inc_addValue, 2) + $increaseRunningHours;
    $hours = floor($inc_addValueFormatted);
    $minutes = round(($inc_addValueFormatted - $hours) * 60);
    @endphp
    {{ $hours }} hrs {{ $minutes}} mins
</td>
<td class="last-updated">{{$site->updatedAt}}</td>
<td>
    @php
    $addValuerunstatus = 0;
    if (isset($sitejsonData['electric_parameters']['voltage_l_l']['a'])) {
    $keya = $sitejsonData['electric_parameters']['voltage_l_l']['a']['add'] ?? null;
    $moduleId = $sitejsonData['electric_parameters']['voltage_l_l']['a']['md'] ?? null;

    foreach ($eventData as $event) {
    $eventArraya = $event->getArrayCopy();
    if ($moduleId && isset($eventArraya['module_id']) && $eventArraya['module_id'] == $moduleId) {
    if ($keya && array_key_exists($keya, $eventArraya)) {
    $addValuerunstatus = $eventArraya[$keya];
    }
    break;
    }
    }
    }
    @endphp
    @if($addValuerunstatus > 0)
    <span class="status-running blinking">ON</span>
    @else
    <span class="status-stopped">OFF</span>
    @endif
</td>
</tr>
@php $i++; @endphp
@endforeach
</tbody>
</table>
</div>