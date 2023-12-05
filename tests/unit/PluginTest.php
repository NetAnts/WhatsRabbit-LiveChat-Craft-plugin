<?php

namespace NetAnts\WhatsRabbitLiveChatTest;

use Codeception\PHPUnit\TestCase;
use Craft;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterUrlRulesEvent;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use NetAnts\WhatsRabbitLiveChat\Model\ApiSettings;
use NetAnts\WhatsRabbitLiveChat\Model\DisplaySettings;
use NetAnts\WhatsRabbitLiveChat\Plugin;
use NetAnts\WhatsRabbitLiveChat\Service\SettingsService;
use NetAnts\WhatsRabbitLiveChat\ValueObject\LiveChatConfig;

//use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private Plugin $plugin;
    protected UnitTester $tester;

    protected function setUp(): void
    {
        $this->plugin = new Plugin('whatsrabbit-live-chat');
    }

    public function testCanCreate(): void
    {
        $this->assertInstanceOf(Plugin::class, $this->plugin);
    }

    public function testInit(): void
    {
        // Given
        $controllerNamespace = 'NetAnts\\WhatsRabbitLiveChat\\Controller';

        // When
        $this->plugin->init();

        // Then
        $this->assertSame($controllerNamespace, $this->plugin->controllerNamespace);
    }

    public function testInitAddsHtml(): void
    {
        $this->plugin->isInstalled = true;
        $settingsService = Mockery::mock(SettingsService::class);
        $settingsService->expects('getSettings')->twice()->andReturn(LiveChatConfig::createFromRequest([
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
        ]));
        $settingsServiceProperty = new \ReflectionProperty(Plugin::class, 'service');
        $settingsServiceProperty->setAccessible(true);
        $settingsServiceProperty->setValue($this->plugin, $settingsService);

        // When
        $this->plugin->init();

        // Then
        $this->assertStringContainsString('<whatsrabbit-live-chat-widget', Craft::$app->getView()->getBodyHtml());
    }

    public function testAddNavItem(): void
    {
        $event = Mockery::mock(RegisterCpNavItemsEvent::class);
        $event->navItems = [];

        $this->plugin->addNavItem($event);

        $this->assertCount(1, $event->navItems);
        $expectedNavItem = [
            'url' => 'whatsrabbit-live-chat/display-settings/edit',
            'label' => 'What\'sRabbit Live-chat',
            'icon' => '@NetAnts/WhatsRabbitLiveChat/icon.svg',
        ];

        $this->assertSame($expectedNavItem, $event->navItems[0]);
    }

    public function testAddRoute(): void
    {
        $event = Mockery::mock(RegisterUrlRulesEvent::class);
        $event->rules = [];

        $this->plugin->addRoute($event);

        $this->assertCount(1, $event->rules);
        $expectedRules = [
            'whatsrabbit-live-chat' => 'login/getToken'
        ];
        $this->assertSame($expectedRules, $event->rules);
    }

    public function testAddCpRoute(): void
    {
        $event = Mockery::mock(RegisterUrlRulesEvent::class);
        $event->rules = [];

        $this->plugin->addCpRoute($event);

        $this->assertCount(1, $event->rules);
        $expectedRules = [
            'whatsrabbit-live-chat/display-settings/edit' => 'whatsrabbit-live-chat/display-settings/edit'
        ];
        $this->assertSame($expectedRules, $event->rules);
    }


    public function testCreateSettingsModel(): void
    {
        $settings = $this->plugin->getSettings();
        $this->assertInstanceOf(ApiSettings::class, $settings);
    }

    public function testGetLiveChatWidget(): void
    {
        $context = [];
        $settings = [
            'avatarAssetId' => [0],
        ];
        $this->plugin->setSettings($settings);
        $response = $this->plugin->getLiveChatWidget($context);
        $expectedHtml = '<whatsrabbit-live-chat-widget
                                    avatar-url=""
                                    login-url="/actions/whatsrabbit-live-chat/login/get-token"
                                    whatsapp-url=""
                                    welcome-title=""
                                    welcome-description=""
                                    display-options="{&quot;position&quot;:null,&quot;z-index&quot;:null,&quot;left&quot;:null,' .
                                    '&quot;right&quot;:null,&quot;bottom&quot;:null,&quot;top&quot;:null,&quot;margin&quot;:null}"
                                ></whatsrabbit-live-chat-widget>';
        $this->assertSame(preg_replace("(\s+)", "\s", $expectedHtml), preg_replace("(\s+)", "\s", $response));
    }
}
