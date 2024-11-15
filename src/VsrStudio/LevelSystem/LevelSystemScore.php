<?php

namespace VsrStudio\LevelSystem;

use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use pocketmine\player\Player;

class LevelSystemScore {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    // Memperbarui ScoreTag pada event
    public function onPlayerTagUpdate(PlayerTagUpdateEvent $event): void {
        $player = $event->getPlayer();
        $name = $player->getName();
        $data = $this->plugin->getPlayerData($name);

        // Menambahkan tag level dan exp
        $event->addTag(new ScoreTag("{levelsystem.level}", (string)$data["level"]));
        $event->addTag(new ScoreTag("{levelsystem.exp}", (string)$data["exp"]));
    }
}
