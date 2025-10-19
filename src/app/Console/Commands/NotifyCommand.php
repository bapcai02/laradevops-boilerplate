<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradev:notify {type : success|fail|started} {--message= : Custom message} {--environment= : Environment name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deployment notifications to configured channels';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->argument('type');
        $customMessage = $this->option('message');
        $environment = $this->option('environment') ?? config('notify.environment.name');

        if (!in_array($type, ['success', 'fail', 'started'])) {
            $this->error('Type must be one of: success, fail, started');
            return 1;
        }

        $this->info("Sending {$type} notification for {$environment}...");

        $notifications = $this->getEnabledChannels();
        
        if (empty($notifications)) {
            $this->warn('No notification channels are enabled.');
            return 0;
        }

        $template = config("notify.templates.{$type}");
        $message = $customMessage ?? str_replace('{environment}', $environment, $template['message']);

        $payload = $this->buildPayload($type, $message, $environment);

        $successCount = 0;
        $totalCount = count($notifications);

        foreach ($notifications as $channel => $config) {
            try {
                $this->sendNotification($channel, $config, $payload);
                $successCount++;
                $this->info("✓ Sent to {$channel}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$channel}: " . $e->getMessage());
                Log::error("Notification failed for {$channel}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$successCount}/{$totalCount} notifications successfully.");
        return $successCount === $totalCount ? 0 : 1;
    }

    /**
     * Get enabled notification channels
     */
    private function getEnabledChannels()
    {
        $channels = config('notify.channels', []);
        $enabled = [];

        foreach ($channels as $channel => $config) {
            if (isset($config['enabled']) && $config['enabled']) {
                $enabled[$channel] = $config;
            }
        }

        return $enabled;
    }

    /**
     * Build notification payload
     */
    private function buildPayload($type, $message, $environment)
    {
        $template = config("notify.templates.{$type}");
        $env = config('notify.environment');

        return [
            'type' => $type,
            'title' => $template['title'],
            'message' => $message,
            'environment' => $environment,
            'app_name' => config('app.name'),
            'app_url' => $env['url'],
            'version' => $env['version'],
            'timestamp' => now()->toISOString(),
            'color' => $template['color'] ?? 'good',
        ];
    }

    /**
     * Send notification to specific channel
     */
    private function sendNotification($channel, $config, $payload)
    {
        switch ($channel) {
            case 'slack':
                $this->sendSlackNotification($config, $payload);
                break;
            case 'telegram':
                $this->sendTelegramNotification($config, $payload);
                break;
            case 'discord':
                $this->sendDiscordNotification($config, $payload);
                break;
            case 'webhook':
                $this->sendWebhookNotification($config, $payload);
                break;
            default:
                throw new \Exception("Unsupported channel: {$channel}");
        }
    }

    /**
     * Send Slack notification
     */
    private function sendSlackNotification($config, $payload)
    {
        $webhookUrl = $config['webhook_url'];
        if (!$webhookUrl) {
            throw new \Exception('Slack webhook URL not configured');
        }

        $slackPayload = [
            'channel' => $config['channel'],
            'username' => $config['username'],
            'icon_emoji' => $config['icon_emoji'],
            'attachments' => [
                [
                    'color' => $payload['color'],
                    'title' => $payload['title'],
                    'text' => $payload['message'],
                    'fields' => [
                        [
                            'title' => 'Environment',
                            'value' => $payload['environment'],
                            'short' => true,
                        ],
                        [
                            'title' => 'Application',
                            'value' => $payload['app_name'],
                            'short' => true,
                        ],
                        [
                            'title' => 'Version',
                            'value' => $payload['version'],
                            'short' => true,
                        ],
                        [
                            'title' => 'URL',
                            'value' => $payload['app_url'],
                            'short' => true,
                        ],
                    ],
                    'footer' => 'Laravel DevOps',
                    'ts' => now()->timestamp,
                ],
            ],
        ];

        $response = Http::post($webhookUrl, $slackPayload);
        if (!$response->successful()) {
            throw new \Exception('Slack API error: ' . $response->body());
        }
    }

    /**
     * Send Telegram notification
     */
    private function sendTelegramNotification($config, $payload)
    {
        $botToken = $config['bot_token'];
        $chatId = $config['chat_id'];

        if (!$botToken || !$chatId) {
            throw new \Exception('Telegram bot token or chat ID not configured');
        }

        $text = "*{$payload['title']}*\n\n";
        $text .= "{$payload['message']}\n\n";
        $text .= "🌍 *Environment:* {$payload['environment']}\n";
        $text .= "📱 *Application:* {$payload['app_name']}\n";
        $text .= "🔢 *Version:* {$payload['version']}\n";
        $text .= "🔗 *URL:* {$payload['app_url']}\n";
        $text .= "⏰ *Time:* " . now()->format('Y-m-d H:i:s');

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        $response = Http::post($url, $data);
        if (!$response->successful()) {
            throw new \Exception('Telegram API error: ' . $response->body());
        }
    }

    /**
     * Send Discord notification
     */
    private function sendDiscordNotification($config, $payload)
    {
        $webhookUrl = $config['webhook_url'];
        if (!$webhookUrl) {
            throw new \Exception('Discord webhook URL not configured');
        }

        $discordPayload = [
            'username' => $config['username'],
            'avatar_url' => $config['avatar_url'] ?? null,
            'embeds' => [
                [
                    'title' => $payload['title'],
                    'description' => $payload['message'],
                    'color' => $this->getColorCode($payload['color']),
                    'fields' => [
                        [
                            'name' => 'Environment',
                            'value' => $payload['environment'],
                            'inline' => true,
                        ],
                        [
                            'name' => 'Application',
                            'value' => $payload['app_name'],
                            'inline' => true,
                        ],
                        [
                            'name' => 'Version',
                            'value' => $payload['version'],
                            'inline' => true,
                        ],
                        [
                            'name' => 'URL',
                            'value' => $payload['app_url'],
                            'inline' => false,
                        ],
                    ],
                    'footer' => [
                        'text' => 'Laravel DevOps',
                    ],
                    'timestamp' => $payload['timestamp'],
                ],
            ],
        ];

        $response = Http::post($webhookUrl, $discordPayload);
        if (!$response->successful()) {
            throw new \Exception('Discord API error: ' . $response->body());
        }
    }

    /**
     * Send generic webhook notification
     */
    private function sendWebhookNotification($config, $payload)
    {
        $url = $config['url'];
        $method = strtoupper($config['method'] ?? 'POST');
        $headers = $config['headers'] ?? [];

        if (!$url) {
            throw new \Exception('Webhook URL not configured');
        }

        $response = Http::withHeaders($headers)->send($method, $url, $payload);
        if (!$response->successful()) {
            throw new \Exception('Webhook error: ' . $response->body());
        }
    }

    /**
     * Convert color name to hex code
     */
    private function getColorCode($color)
    {
        $colors = [
            'good' => 0x00ff00,
            'warning' => 0xffaa00,
            'danger' => 0xff0000,
        ];

        return $colors[$color] ?? 0x00ff00;
    }
}
