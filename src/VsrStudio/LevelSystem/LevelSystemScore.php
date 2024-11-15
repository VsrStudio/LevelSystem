<?php

namespace VsrStudio\LevelSystem;

use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\scoreboard\ScoreHudProvider;
use pocketmine\player\Player;

class LevelSystemScore extends ScoreHudProvider {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function getScore(Player $player): array {
        $name = $player->getName();
        $data = $this->plugin->getPlayerData($name);

        return [
            new ScoreTag("{levelsystem.level}", (string)$data["level"]),
            new ScoreTag("{levelsystem.exp}", (string)$data["exp"])
        ];
    }
}
