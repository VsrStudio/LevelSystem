<?php

namespace VsrStudio\LevelSystem\manager;

use VsrStudio\LevelSystem\manager\LevelManager;
use VsrStudio\LevelSystem\LevelSystem;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use dktapps\pmforms\ModalForm;
use pocketmine\player\Player;

class FormManager {

    public static LevelSystem $plugin;

    public function __construct(LevelSystem $plugin) {
        self::$plugin = $plugin;
    }

    public static function sendMenu(Player $player, Player $target): void {
        $form = new MenuForm($target->getName() . "'s Level Information", "§a==========\n= §e» §aLevel: §e" . LevelManager::getLevelFromCache($target) . "\n§a= §e» §aXP: §e" . LevelManager::getExpFormattedFromCache($target) . "\n§a= §e» §aNext XP: §e" . LevelManager::getNextExpFormattedFromCache($target) . "\n§a==========", [
            new MenuOption("§l§0» §cExit\n§rClick to exit")
        ],
        /**
         * Called when player click/submit the form
         * @param Player $player
         * @param int $data
         */
        function (Player $player, int $data): void {
            if ($data == 0) {
                // gatau dah cuy
            }
        }, 
        /**
         * Called when player closed the form
         * @param Player $player
         */
        function (Player $player): void {
            // gatau dah cuy
        });
        $player->sendForm($form);
    }
}
