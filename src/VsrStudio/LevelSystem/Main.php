<?php

namespace VsrStudio\LevelSystem;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\Player;

class Main extends PluginBase {

    private $playerData;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->playerData = new Config($this->getDataFolder() . "playerData.yml", Config::YAML);
        $this->registerScoreHud();
    }

    public function getPlayerData(string $name): array {
        return $this->playerData->get($name, ["level" => 1, "exp" => 0]);
    }

    public function savePlayerData(string $name, array $data): void {
        $this->playerData->set($name, $data);
        $this->playerData->save();
    }

    private function registerScoreHud(): void {
        $plugin = $this->getServer()->getPluginManager()->getPlugin("ScoreHud");
        
        if ($plugin !== null && $plugin->isEnabled()) {
            if (method_exists($plugin, 'registerProvider')) {
                $plugin->registerProvider(new LevelSystemScore($this));
                $this->getLogger()->info("ScoreHud support enabled for LevelSystem.");
            } else {
                $this->getLogger()->warning("registerProvider method not found in ScoreHud.");
            }
        } else {
            $this->getLogger()->warning("ScoreHud plugin not found or not enabled.");
        }
    }
}
