<?php

namespace VsrStudio\LevelSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\player\Player;

class Main extends PluginBase {

    private $playerData;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->loadPlayerData();
        $this->getServer()->getPluginManager()->registerEvents(new LevelSystemScore($this), $this);
        $this->getLogger()->info("LevelSystem plugin enabled.");
    }

    public function loadPlayerData(): void {
        $this->playerData = new Config($this->getDataFolder() . "playerdata.yml", Config::YAML);
    }

    public function savePlayerData(): void {
        $this->playerData->save();
    }

    public function getPlayerData(string $name): array {
        return $this->playerData->get($name, ["level" => 1, "exp" => 0]);
    }

    public function addExp(Player $player, int $exp): void {
        $name = $player->getName();
        $data = $this->getPlayerData($name);

        $data["exp"] += $exp;
        if ($data["exp"] >= 100) {
            $data["exp"] = 0;
            $data["level"]++;
        }

        $this->playerData->set($name, $data);
        $this->savePlayerData();
    }

    private function registerScoreHud(): void {
        $plugin = $this->getServer()->getPluginManager()->getPlugin("ScoreHud");

        if ($plugin !== null && $plugin->isEnabled()) {
            $this->getLogger()->info("ScoreHud support enabled for LevelSystem.");
        } else {
            $this->getLogger()->warning("ScoreHud plugin not found or not enabled.");
        }
    }
}

class LevelSystemScore {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerTagUpdate(PlayerTagUpdateEvent $event): void {
        $player = $event->getPlayer();
        $name = $player->getName();
        $data = $this->plugin->getPlayerData($name);

        $event->addTag(new ScoreTag("{levelsystem.level}", (string)$data["level"]));
        $event->addTag(new ScoreTag("{levelsystem.exp}", (string)$data["exp"]));
    }
}
