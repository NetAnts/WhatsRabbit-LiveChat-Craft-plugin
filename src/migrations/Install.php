<?php

namespace NetAnts\WhatsRabbitLiveChat\migrations;

use craft\db\Migration;

/**
 * m230808_090125_whatsrabbit_livechat_settings migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->dropTableIfExists('{{%whatsrabbit_livechat_settings}}');

        $this->createTable(
            '{{%whatsrabbit_livechat_settings}}',
            [
                'id' => $this->primaryKey(),
                'avatar_asset_id' => $this->integer()->notNull(),
                'title' => $this->string()->notNull(),
                'description' => $this->string()->notNull(),
                'whatsapp_url' => $this->string()->notNull(),
                'enabled' => $this->tinyInteger()->notNull()->defaultValue(1),
                'position' => $this->string()->defaultValue('fixed'),
                'z_index' => $this->string()->defaultValue('10'),
                'left' => $this->string()->defaultValue('inherit'),
                'right' => $this->string()->defaultValue('0'),
                'bottom' => $this->string()->defaultValue('0'),
                'top' => $this->string()->defaultValue('inherit'),
                'margin' => $this->string()->defaultValue('20px'),
            ]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists('{{%whatsrabbit_livechat_settings}}');
        return true;
    }
}
