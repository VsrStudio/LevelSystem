<?php

namespace VsrStudio\LevelSystem\command;

use VsrStudio\LevelSystem\manager\FormManager;
use VsrStudio\LevelSystem\LevelSystem;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LevelCommand extends BaseCommand {

    protected function prepare(): void {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->setPermission("levelsystem.command.level");
    }

    public function onRun(CommandSender $sender, string $alias_used, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Use this command in-game only!");
            return;
        }
        $target = LevelSystem::getInstance()->getServer()->getPlayerExact($args["player"]);
        if ($target == null) {
            $sender->sendMessage(LevelSystem::PREFIX . "§cPlayer by name: §e" . $args["player"] . " §cnot found!");
            return;
        }
        FormManager::sendMenu($sender, $target);
    }
}
