<?php

namespace VsrStudio\LevelSystem;

use VsrStudio\LevelSystem\manager\LevelManager;
use VsrStudio\LevelSystem\LevelSystem;
use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\inventory\CraftItemEvent;

class EventListener implements Listener {

    public array $bonus = [0, 1, "bonus", 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14 ,15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30];

    public function __construct(public LevelSystem $plugin) {
    }

    /**
     * @param TagsResolveEvent $event
     * @return void
     */
    public function onTagsResolve(TagsResolveEvent $event): void {
        $player = $event->getPlayer();
        $tag = $event->getTag();
        $value = "";
        switch ($tag->getName()) {
            case "levelsystem.level":
                $value = LevelManager::getLevelFromCache($player);
            break;
            case "levelsystem.exp":
                $value = LevelManager::getExpFormattedFromCache($player);
            break;
        }
        $tag->setValue((string)$value);
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        LevelManager::addPlayerDataToCache($player, "0", "0", "0");
        LevelManager::getDatas($player, function (?array $data) use ($player): void {
            if ($data == null) {
                LevelManager::createData($player);
            } else {
                LevelManager::addPlayerDataToCache($player, $data["level"], $data["exp"], $data["next_exp"]);
            }
        });
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        LevelManager::removePlayerDataFromCache($player);
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        if ($event->isCancelled()) return;
        $player = $event->getPlayer();
        if ($this->plugin->getConfig()->getNested("level.add_exp.by_break_block.enable", true)) {
            $amount = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_break_block.min", 0), $this->plugin->getConfig()->getNested("level.add_exp.by_break_block.max", 5));
            $message = "§a+" . $amount . " XP";
            $bonus = $this->bonus[array_rand($this->bonus)];
            if ($bonus == "bonus") {
                $amount_bonus = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_bonus.min", 5), $this->plugin->getConfig()->getNested("level.add_exp.by_bonus.max", 10));
                $message = "§a+" . $amount . " §e(Free Bonus +" . $amount_bonus . ") §aXP";
                $amount += $amount_bonus;
            }
            LevelManager::addExp($player, $amount);
            $player->sendTip($message);
        }
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        if ($event->isCancelled()) return;
        $player = $event->getPlayer();
        if ($this->plugin->getConfig()->getNested("level.add_exp.by_place_block.enable", true)) {
            $amount = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_place_block.min", 0), $this->plugin->getConfig()->getNested("level.add_exp.by_place_block.max", 5));
            $message = "§a+" . $amount . " XP";
            $bonus = $this->bonus[array_rand($this->bonus)];
            if ($bonus == "bonus") {
                $amount_bonus = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_bonus.min", 5), $this->plugin->getConfig()->getNested("level.add_exp.by_bonus.max", 10));
                $amount += $amount_bonus;
                $message = "§a+" . $amount . " §e(Free Bonus +" . $amount_bonus . ") §aXP";
            }
            LevelManager::addExp($player, $amount);
            $player->sendTip($message);
        }
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onCraft(CraftItemEvent $event): void {
        if ($event->isCancelled()) return;
        $player = $event->getPlayer();
        if ($this->plugin->getConfig()->getNested("level.add_exp.by_break_block.enable", true)) {
            $amount = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_break_block.min", 0), $this->plugin->getConfig()->getNested("level.add_exp.by_break_block.max", 5));
            $message = "§a+" . $amount . " XP";
            $bonus = $this->bonus[array_rand($this->bonus)];
            if ($bonus == "bonus") {
                $amount_bonus = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_bonus.min", 5), $this->plugin->getConfig()->getNested("level.add_exp.by_bonus.max", 10));
                $amount += $amount_bonus;
                $message = "§a+" . $amount . " §e(Free Bonus +" . $amount_bonus . ") §aXP";
            }
            LevelManager::addExp($player, $amount);
            $player->sendTip($message);
        }
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onPlayerChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        if ($this->plugin->getConfig()->getNested("level.add_exp.by_chat.enable", true)) {
            $amount = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_chat.min", 0), $this->plugin->getConfig()->getNested("level.add_exp.by_chat.max", 5));
            $message = "§a+" . $amount . " XP";
            $bonus = $this->bonus[array_rand($this->bonus)];
            if ($bonus == "bonus") {
                $amount_bonus = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_bonus.min", 5), $this->plugin->getConfig()->getNested("level.add_exp.by_bonus.max", 10));
                $amount += $amount_bonus;
                $message = "§a+" . $amount . " §e(Free Bonus +" . $amount_bonus . ") §aXP";
            }
            LevelManager::addExp($player, $amount);
            $player->sendTip($message);
        }
    }

    /**
     * @param PlayerDeathEvent $event
     * @return void
     */
    public function onPlayerDeath(PlayerDeathEvent $event): void {
        if ($event instanceof EntityDamageByEntityEvent) {
            if (!($player = $event->getDamager()) instanceof Player) return;
            if ($this->plugin->getConfig()->getNested("level.add_exp.by_kill_player.enable", true)) {
                $amount = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_kill_player.min", 0), $this->plugin->getConfig()->getNested("level.add_exp.by_kill_player.max", 5));
                $message = "§a+" . $amount . " XP";
                $bonus = $this->bonus[array_rand($this->bonus)];
                if ($bonus == "bonus") {
                    $amount_bonus = mt_rand($this->plugin->getConfig()->getNested("level.add_exp.by_bonus.min", 5), $this->plugin->getConfig()->getNested("level.add_exp.by_bonus.max", 10));
                    $amount += $amount_bonus;
                    $message = "§a+" . $amount . " §e(Free Bonus +" . $amount_bonus . ") §aXP";
                }
                LevelManager::addExp($player, $amount);
                $player->sendTip($message);
            }
        }
    }
}
