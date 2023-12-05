<?php

namespace NetAnts\WhatsRabbitLiveChatTest\Service;

use Craft;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use NetAnts\WhatsRabbitLiveChat\Plugin;
use NetAnts\WhatsRabbitLiveChat\Service\SettingsService;
use NetAnts\WhatsRabbitLiveChat\ValueObject\LiveChatConfig;
use PHPUnit\Framework\TestCase;

class SettingsServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private Craft | MockInterface $craft;
    private SettingsService $service;

    protected function setUp(): void
    {
        $this->craft = Mockery::mock(Craft::class);
        $this->service = new SettingsService(
            $this->craft,
        );
    }

    public function testSaveSettings(): void
    {
        $liveChatConfig = LiveChatConfig::createFromRequest([
            'apiKey' => 'some-api-key',
            'apiSecret' => 'some-api-secret',
            'title' => 'Some title',
            'description' => 'Some description',
            'avatarAssetId' => ['some-avatar-id'],
            'whatsAppUrl' => 'https://wa.me',
            'enabled' => true,
            'position' => 'fixed',
            'zIndex' => '10',
            'left' => 'inherit',
            'right' => '0',
            'bottom' => '0',
            'top' => 'inherit',
            'margin' => '20px',
        ]);
        $response = $this->service->saveSettings($liveChatConfig);
        $this->assertTrue($response);
    }

    public function testGetSettings(): void
    {
        $liveChatConfig = LiveChatConfig::createFromRequest([
            'apiKey' => 'some-api-key',
            'apiSecret' => 'some-api-secret',
            'title' => 'Some title',
            'description' => 'Some description',
            'avatarAssetId' => ['some-avatar-id'],
            'whatsAppUrl' => 'https://wa.me',
            'enabled' => true,
            'position' => 'fixed',
            'zIndex' => '10',
            'left' => 'inherit',
            'right' => '0',
            'bottom' => '0',
            'top' => 'inherit',
            'margin' => '20px',
        ]);
        $this->service->saveSettings($liveChatConfig);
        $settings = $this->service->getSettings();
        $this->assertInstanceOf(LiveChatConfig::class, $settings);
    }
}
