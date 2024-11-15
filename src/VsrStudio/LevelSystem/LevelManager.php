<?php

namespace VsrStudio\LevelSystem;

use pocketmine\player\Player;

class LevelManager {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function getLevel(Player $player): int {
        $data = $this->plugin->getPlayerData($player->getName());
        return $data["level"];
    }

    public function getExp(Player $player): int {
        $data = $this->plugin->getPlayerData($player->getName());
        return $data["exp"];
    }

    public function addExp(Player $player, int $amount): void {
        $this->plugin->addExp($player, $amount);
    }
}
