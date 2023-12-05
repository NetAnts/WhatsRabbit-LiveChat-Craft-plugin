<?php

declare(strict_types=1);

namespace NetAnts\WhatsRabbitLiveChatTest\Controller;

use Craft;
use craft\test\TestSetup;
use craft\web\Request;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use NetAnts\WhatsRabbitLiveChat\Controller\DisplaySettingsController;
use NetAnts\WhatsRabbitLiveChat\Exception\InvalidDataException;
use NetAnts\WhatsRabbitLiveChat\Model\DisplaySettings;
use NetAnts\WhatsRabbitLiveChat\Service\SettingsService;
use NetAnts\WhatsRabbitLiveChat\ValueObject\LiveChatConfig;
use PHPUnit\Framework\TestCase;
use yii\base\Module;
use yii\web\Response;

class DisplaySettingsControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private SettingsService|MockInterface $settingsService;
    private Craft | MockInterface $craft;
    private DisplaySettingsController $controller;

    protected function setUp(): void
    {
        $id = 'displaySettingsController';
        $module = Mockery::mock(Module::class);
        $config = [];
        $this->craft = Mockery::mock(Craft::class);
        $this->settingsService = Mockery::mock(SettingsService::class);
        $this->settingsService->expects('getSettings')->andReturn(LiveChatConfig::createFromRequest([
            'avatarAssetId' => 'some-asset-id',
            'description' => 'some-description',
            'title' => 'some-title',
            'whatsAppUrl' => 'some-url',
            'enabled' => true,
            'position' => 'fixed',
            'zIndex' => '10',
            'left' => 'inherit',
            'right' => '0',
            'bottom' => '0',
            'top' => 'inherit',
            'margin' => '20px',
        ]));
        $this->controller = new DisplaySettingsController($id, $module, $this->settingsService, $this->craft, $config);
    }


    public function testSavingAction(): void
    {
        $request = Mockery::mock(Request::class);
        $request->expects('getBodyParams')->andReturn([
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
        $request->expects('getValidatedBodyParam')->andReturn(null);
        $request->expects('getPathInfo')->andReturn('/api');
        $this->settingsService->expects('saveSettings')->withAnyArgs()->andReturn(true);
        $this->controller->request = $request;
        $response = $this->controller->actionSave();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertTrue($response->getIsRedirection());
    }



    public function testActionSaveFails(): void
    {
        $request = Mockery::mock(Request::class);
        $request->expects('getBodyParams')->andReturn([
            'title' => 'some title',
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
        $request->expects('getValidatedBodyParam')->andReturn(null);
        $request->expects('getPathInfo')->andReturn('/api');
        $this->settingsService->expects('saveSettings')->withAnyArgs()->andReturn(false);
        $this->controller->request = $request;
        $response = $this->controller->actionSave();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(
            'Something went wrong while saving the plugin settings',
            $this->craft::$app->session->getError()
        );
    }

    public function testModelValidationError()
    {
        $request = Mockery::mock(Request::class);
        $request->expects('getBodyParams')->andReturn([
            'title' => null,
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
        $request->expects('getAcceptsJson')->andReturnTrue();
        $this->controller->request = $request;
        $response = $this->controller->actionSave();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testActionSaveButLiveChatConfigCannotBeCreated(): void
    {
        $request = Mockery::mock(Request::class);
        $request->expects('getBodyParams')->andReturn([
            'apiKey' => 'some-api-key',
            'apiSecret' => 'some-api-secret',
            'title' => 'Some title',
            'description' => 'Some description',
            'avatarAssetId' => ['some-avatar-id'],
            'whatsAppUrl' => true,
            'enabled' => 'true',
            'position' => 'fixed',
            'zIndex' => '10',
            'left' => 'inherit',
            'right' => '0',
            'bottom' => '0',
            'top' => 'inherit',
            'margin' => '20px',
        ]);
        $request->expects('getValidatedBodyParam')->andReturn(null);
        $request->expects('getPathInfo')->andReturn('/api');
        $this->controller->request = $request;
        $this->controller->actionSave();
        $this->assertStringStartsWith(
            'Something went wrong while creating config: NetAnts\WhatsRabbitLiveChat\ValueObject\LiveChatConfig::__construct(): ' .
            'Argument #4 ($whatsAppUrl) must be of type string, bool given, called in',
            $this->craft::$app->session->getError()
        );
    }

    public function testActionEdit(): void
    {
        $response = $this->controller->actionEdit();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testActionEditWithSettingsFromRoute(): void
    {
        $displaySettings = new DisplaySettings([
            'title' => 'Some title',
            'description' => 'Some description',
            'avatarAssetId' => 0,
            'whatsAppUrl' => 'https://wa.me',
            'enabled' => false,
            ]);
        $response = $this->controller->actionEdit($displaySettings);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
    }
}
