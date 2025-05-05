<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use MongoDB\Client as MongoClient;
use Twilio\Rest\Client as TwilioClient;
use DateTimeZone;

class CheckFuelLevel extends Command
{
    protected $signature = 'fuel:check';
    protected $description = 'Check fuel levels and send WhatsApp alerts using Twilio';

    public function handle()
    {
        $sites = Site::all();

        foreach ($sites as $site) {
            $siteData = json_decode($site->data, true);
            $mdValues = $this->extractMdFields($siteData);

            if (empty($mdValues)) {
                continue;
            }

            $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
            $client = new MongoClient($mongoUri);
            $database = $client->isa_qa;
            $collection = $database->device_events;

            $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

            foreach ($uniqueMdValues as $moduleId) {
                $event = $collection->findOne(
                    ['module_id' => $moduleId],
                    ['sort' => ['createdAt' => -1]]
                );

                if ($event && isset($event['fuel_level']) && $event['fuel_level'] < 25) {
                    $siteName = $siteData['site_name'] ?? $site->name ?? 'Unknown Site';
                    $fuelLevel = $event['fuel_level'] . '%';

                    $message = "🚨 *Fuel Alert!*\nSite: $siteName\nFuel Level: $fuelLevel\n\nPlease refill soon.";

                    $this->sendWhatsAppAlert($message);
                }
            }
        }

        $this->info('Fuel level check complete.');
    }

    private function extractMdFields($data)
    {
        $mdFields = [];
        array_walk_recursive($data, function ($value, $key) use (&$mdFields) {
            if ($key === 'md' && !is_null($value)) {
                $mdFields[] = $value;
            }
        });
        return $mdFields;
    }

    private function sendWhatsAppAlert($message)
    {
        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $twilioNumber = env('TWILIO_WHATSAPP_NUMBER');
            $to = 'whatsapp:+12314892822'; // ✅ Replace this with the real recipient's WhatsApp number

            $twilio = new TwilioClient($sid, $token);
            $twilio->messages->create($to, [
                'from' => $twilioNumber,
                'body' => $message,
            ]);

            \Log::info('WhatsApp alert sent: ' . $message);
        } catch (\Exception $e) {
            \Log::error('Twilio WhatsApp error: ' . $e->getMessage());
        }
    }
}