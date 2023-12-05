<?php

namespace NetAnts\WhatsRabbitLiveChatTest\Model;

use Codeception\PHPUnit\TestCase;
use NetAnts\WhatsRabbitLiveChat\Model\DisplaySettings;

class DisplaySettingsTest extends TestCase
{
    public function testRules()
    {
        $settings = new DisplaySettings();
        $rules = $settings->rules();
        $this->assertSame([
            [['title', 'whatsAppUrl', 'description', 'avatarAssetId'], 'required'],
            [['enabled'], 'boolean'],
            [['position', 'zIndex', 'left', 'right', 'bottom', 'top', 'margin'], 'string']
        ], $rules);
    }
}
