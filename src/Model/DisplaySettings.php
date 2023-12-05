<?php

namespace NetAnts\WhatsRabbitLiveChat\Model;

use craft\base\Model;

class DisplaySettings extends Model
{
    public ?int $avatarAssetId = null;
    public string $description = '';
    public string $title = '';
    public string $whatsAppUrl = '';
    public bool $enabled = true;

    public string $position = 'fixed';
    public string $zIndex = '10';
    public string $left = 'inherit';
    public string $right = '0';
    public string $bottom = '0';
    public string $top = 'inherit';
    public string $margin = '20px';

    public function rules(): array
    {
        return [
            [['title', 'whatsAppUrl', 'description', 'avatarAssetId'], 'required'],
            [['enabled'], 'boolean'],
            [['position', 'zIndex', 'left', 'right', 'bottom', 'top', 'margin'], 'string']
        ];
    }
}
