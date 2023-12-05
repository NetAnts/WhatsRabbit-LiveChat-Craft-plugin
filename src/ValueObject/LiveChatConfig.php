<?php

declare(strict_types=1);

namespace NetAnts\WhatsRabbitLiveChat\ValueObject;

use NetAnts\WhatsRabbitLiveChat\db\Settings;
use NetAnts\WhatsRabbitLiveChat\Exception\InvalidDataException;

class LiveChatConfig
{
    private const REQUIRED_KEYS = [
        'avatarAssetId',
        'title',
        'description',
        'whatsAppUrl'
    ];

    private function __construct(
        public int $avatarAssetId,
        public string $title,
        public string $description,
        public string $whatsAppUrl,
        public bool $enabled,
        public string $loginUrl,
        public string $position,
        public string $zIndex,
        public string $left,
        public string $right,
        public string $bottom,
        public string $top,
        public string $margin,
    ) {
    }

    /**
     * @throws InvalidDataException
     */
    public static function createFromRequest(array $data): self
    {
        foreach (self::REQUIRED_KEYS as $key) {
            if (
                !array_key_exists($key, $data) ||
                empty($data[$key])
            ) {
                throw InvalidDataException::becauseOfMissingData($key);
            }
        }

        return new self(
            (int)$data['avatarAssetId'],
            $data['title'],
            $data['description'],
            $data['whatsAppUrl'],
            (bool)$data['enabled'],
            '/actions/whatsrabbit-live-chat/login/get-token',
            $data['position'],
            $data['zIndex'],
            $data['left'],
            $data['right'],
            $data['bottom'],
            $data['top'],
            $data['margin'],
        );
    }

    public static function createFromDatabase(Settings $settings)
    {
        return new self(
            $settings->avatar_asset_id,
            $settings->title,
            $settings->description,
            $settings->whatsapp_url,
            (bool)$settings->enabled,
            '/actions/whatsrabbit-live-chat/login/get-token',
            $settings->position,
            $settings->z_index,
            $settings->left,
            $settings->right,
            $settings->bottom,
            $settings->top,
            $settings->margin,
        );
    }
}
