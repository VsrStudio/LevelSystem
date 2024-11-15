<?php

namespace VsrStudio\LevelSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\event\server\PluginEnableEvent;
use pocketmine\plugin\Plugin;

class Main extends PluginBase implements Listener {

    private Config $playerData;

    public function onEnable(): void {
        $this->getLogger()->info("LevelSystem diaktifkan!");
        $this->saveResource("playerData.yml");
        $this->playerData = new Config($this->getDataFolder() . "playerData.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->registerScoreHud();
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $name = $player->getName();
        if (!$this->playerData->exists($name)) {
            $this->playerData->set($name, ["level" => 1, "exp" => 0]);
            $this->playerData->save();
        }
        $player->sendMessage("Selamat datang, levelmu: " . $this->playerData->get($name)["level"]);
    }

    public function addExp(Player $player, int $amount): void {
        $name = $player->getName();
        $data = $this->playerData->get($name);
        $data["exp"] += $amount;

        // Logika level up
        $requiredExp = $data["level"] * 100;
        if ($data["exp"] >= $requiredExp) {
            $data["exp"] -= $requiredExp;
            $data["level"]++;
            $player->sendMessage("Selamat! Kamu naik ke level " . $data["level"]);
        }

        $this->playerData->set($name, $data);
        $this->playerData->save();
    }

    public function getPlayerData(string $name): array {
        return $this->playerData->get($name, ["level" => 1, "exp" => 0]);
    }

    private function registerScoreHud(): void {
        $plugin = $this->getServer()->getPluginManager()->getPlugin("ScoreHud");
        if ($plugin instanceof Plugin && $plugin->isEnabled()) {
            $plugin->getScoreHudManager()->registerProvider(new LevelSystemScore($this));
            $this->getLogger()->info("ScoreHud support diaktifkan untuk LevelSystem.");
        }
    }
}
