<?php

namespace VsrStudio\LevelSystem;

use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\player\Player;
use Ifera\ScoreHud\ScoreHud;

class Main extends PluginBase {

    private $playerData;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerTagUpdate(PlayerTagUpdateEvent $event): void {
        $player = $event->getPlayer();
        $name = $player->getName();
        $data = $this->getPlayerData($name);

        $levelTag = new ScoreTag("{levelsystem.level}", (string) $data["level"]);
        $expTag = new ScoreTag("{levelsystem.exp}", (string) $data["exp"]);

        $scoreHud = $this->getServer()->getPluginManager()->getPlugin("ScoreHud");

        if ($scoreHud !== null && $scoreHud->isEnabled()) {
            $scoreHud->getScoreHudManager()->updatePlayerTags($player, [$levelTag, $expTag]);

            $this->getLogger()->info("Scoreboard pemain {$name} diperbarui.");
        }
    }

    public function getPlayerData(string $name): array {
        return $this->playerData->get($name, ["level" => 1, "exp" => 0]);
    }
}
