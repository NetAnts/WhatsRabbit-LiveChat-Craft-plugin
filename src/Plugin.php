<?php

declare(strict_types=1);

namespace NetAnts\WhatsRabbitLiveChat;

use Craft;
use craft\base\Model;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\Cp;
use craft\web\UrlManager;
use NetAnts\WhatsRabbitLiveChat\Model\ApiSettings;
use NetAnts\WhatsRabbitLiveChat\Model\DisplaySettings;
use NetAnts\WhatsRabbitLiveChat\Service\SettingsService;
use yii\base\Event;

class Plugin extends \craft\base\Plugin
{
    public bool $hasCpSettings = true;

    public const PLUGIN_REPO_PROD_URL = 'plugins.whatsrabbit.com';
    public const LIVECHAT_ASSETS_DOMAIN = 'cdn.plugins.whatsrabbit.com';
    private ?SettingsService $service;

    public function __construct($id, $parent = null, array $config = [])
    {
        $this->service = new SettingsService(new Craft());
        parent::__construct($id, $parent, $config);
    }

    public function init(): void
    {
        $this->controllerNamespace = 'NetAnts\\WhatsRabbitLiveChat\\Controller';

        parent::init();
        if ($this->isInstalled) {

            /**
             * Register control panel menu items
             */
            Event::on(
                Cp::class,
                Cp::EVENT_REGISTER_CP_NAV_ITEMS,
                [$this, 'addNavItem'],
            );

            /**
             * Register api route
             */
            Event::on(
                UrlManager::class,
                UrlManager::EVENT_REGISTER_SITE_URL_RULES,
                [$this, 'addRoute'],
            );

            Event::on(
                UrlManager::class,
                UrlManager::EVENT_REGISTER_CP_URL_RULES,
                [$this, 'addCpRoute'],
            );

            /**
             * Register Live-chat hook and files
             */

            $pluginAssetsUrl = getenv('LIVECHAT_ASSETS_DOMAIN') ?: self::LIVECHAT_ASSETS_DOMAIN;


            if ($this->service->getSettings()?->enabled && !Craft::$app->request->isCpRequest) {
                Craft::$app->getView()->registerHtml($this->getLiveChatWidget());
            }
            Craft::$app->getView()->registerCssFile(sprintf('//%s/styles.css', $pluginAssetsUrl));
            Craft::$app->getView()->registerJsFile(sprintf('//%s/polyfills.js', $pluginAssetsUrl));
            Craft::$app->getView()->registerJsFile(sprintf('//%s/main.js', $pluginAssetsUrl));
        }
    }

    public function addNavItem(RegisterCpNavItemsEvent $event): void
    {
        $event->navItems[] = [
            'url' => 'whatsrabbit-live-chat/display-settings/edit',
            'label' => 'What\'sRabbit Live-chat',
            'icon' => '@NetAnts/WhatsRabbitLiveChat/icon.svg'

        ];
    }

    public function addRoute(RegisterUrlRulesEvent $event): void
    {
        $event->rules['whatsrabbit-live-chat'] = 'login/getToken';
    }

    public function addCpRoute(RegisterUrlRulesEvent $event): void
    {
        $event->rules['whatsrabbit-live-chat/display-settings/edit'] = 'whatsrabbit-live-chat/display-settings/edit';
    }


    public function getLiveChatWidget(): string
    {
        $settings = $this->service->getSettings();

        $asset = Craft::$app->assets->getAssetById((int)$settings?->avatarAssetId);

        $displayOptions = [
            'position' => $settings?->position,
            'z-index' => $settings?->zIndex,
            'left' => $settings?->left,
            'right' => $settings?->right,
            'bottom' => $settings?->bottom,
            'top' => $settings?->top,
            'margin' => $settings?->margin,
        ];

        return sprintf(
            '<whatsrabbit-live-chat-widget
                        avatar-url="%s"
                        login-url="%s"
                        whatsapp-url="%s"
                        welcome-title="%s"
                        welcome-description="%s"
                        display-options="%s"
                    ></whatsrabbit-live-chat-widget>',
            $asset?->url,
            '/actions/whatsrabbit-live-chat/login/get-token',
            $settings?->whatsAppUrl,
            $settings?->title,
            $settings?->description,
            htmlspecialchars(json_encode($displayOptions))
        );
    }

    protected function createSettingsModel(): ?Model
    {
        return new ApiSettings();
    }

    // @codeCoverageIgnoreStart
    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'whatsrabbit-live-chat/settings',
            ['settings' => $this->getSettings()]
        );
    }

    public function getPluginInstance(): self
    {
        return parent::getInstance();
    }
    // @codeCoverageIgnoreEnd
}
