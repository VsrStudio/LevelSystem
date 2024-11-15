<?php

namespace VsrStudio\LevelSystem;

use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\player\Player;

class LevelSystemScore {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerTagUpdate(PlayerTagUpdateEvent $event): void {
        $player = $event->getPlayer();
        $name = $player->getName();
        $data = $this->plugin->getPlayerData($name);

        $scoreTagLevel = new ScoreTag("{levelsystem.level}", (string)$data["level"]);
        $scoreTagExp = new ScoreTag("{levelsystem.exp}", (string)$data["exp"]);

        $this->plugin->getServer()->getPluginManager()->getPlugin("ScoreHud")
            ->updateScoreTags($player, [$scoreTagLevel, $scoreTagExp]);
    }
}
