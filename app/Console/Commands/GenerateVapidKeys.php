<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateVapidKeys extends Command
{
    protected $signature = 'vapid:generate';
    protected $description = 'Generate VAPID keys for Web Push';

    public function handle()
    {
        try {
            $keys = \Minishlink\WebPush\VAPID::createVAPIDKeys();
            $this->info('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
            $this->info('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
            
            // Save to .env file
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);
            
            if (strpos($envContent, 'VAPID_PUBLIC_KEY=') === false) {
                file_put_contents($envPath, "\nVAPID_PUBLIC_KEY={$keys['publicKey']}\nVAPID_PRIVATE_KEY={$keys['privateKey']}\n", FILE_APPEND);
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to generate VAPID keys');
            return 1;
        }
    }
}