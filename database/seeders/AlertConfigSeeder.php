<?php

namespace Database\Seeders;

use App\Models\AlertConfig;
use Illuminate\Database\Seeder;

class AlertConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            [
                'alert_type'    => 'import_expiry',
                'days_before'   => 30,
                'notify_emails' => json_encode(['admin@qlxnk.local']),
                'is_active'     => true,
            ],
            [
                'alert_type'    => 'import_expiry',
                'days_before'   => 7,
                'notify_emails' => json_encode(['admin@qlxnk.local', 'manager@qlxnk.local']),
                'is_active'     => true,
            ],
            [
                'alert_type'    => 'export_expiry',
                'days_before'   => 30,
                'notify_emails' => json_encode(['admin@qlxnk.local']),
                'is_active'     => true,
            ],
            [
                'alert_type'    => 'export_expiry',
                'days_before'   => 7,
                'notify_emails' => json_encode(['admin@qlxnk.local', 'manager@qlxnk.local']),
                'is_active'     => true,
            ],
        ];

        foreach ($configs as $config) {
            AlertConfig::firstOrCreate(
                [
                    'alert_type'  => $config['alert_type'],
                    'days_before' => $config['days_before'],
                ],
                $config
            );
        }
    }
}
