<?php

namespace VsrStudio\LevelSystem;

use VsrStudio\LevelSystem\command\LevelCommand;
use VsrStudio\LevelSystem\manager\LevelManager;
use VsrStudio\LevelSystem\manager\FormManager;
use IvanCraft623\RankSystem\tag\Tag;
use IvanCraft623\RankSystem\tag\TagManager;
use IvanCraft623\RankSystem\session\Session;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use CortexPE\Commando\PacketHooker;
use poggit\libasynql\libasynql;
use pocketmine\scheduler\ClosureTask;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class LevelSystem extends PluginBase {
    use SingletonTrait;

    /**
     * @var const PREFIX
     */
    public const PREFIX = "§l§gLevel§aSystem §r§7»§r ";

    /**
     * @var const DEPEND
     */
    public const DEPEND = [
        libasynql::class => ["libasynql", "poggit"],
        TagManager::class => ["RankSystem", "IvanCraft623"],
        PacketHooker::class => ["Commando", "CortexPE"],
        ScoreTag::class => ["ScoreHud", "Ifera"]
    ];

    /**
     * @return void
     */
    public function onEnable(): void {
        self::setInstance($this);
        foreach (self::DEPEND as $class => $data) {
            if (!class_exists($class)) {
                $this->getServer()->getLogger()->error(TextFormat::RED . $data[0] . " depend not found. Please install " . $data[0] . " by " . $data[1]);
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return;
            }
        }
        if (!PacketHooker::isRegistered($this)) PacketHooker::register($this);
        $this->registerRankSystemTag();
        $this->loadScoreHud();
        (new LevelManager($this));
        (new FormManager($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("LevelSystem", new LevelCommand($this, "level", "Get player level information"));
    }

    /**
     * For close database when server or plugin's disabled
     * @return void
     */
    public function onDisable(): void {
        if (LevelManager::getDatabase() !== null) {
            LevelManager::closeDatabase();
        }
    }

    /**
     * For register RankSystem tag
     * @return void
     */
    public function registerRankSystemTag(): void {
        TagManager::getInstance()->registerTag(new Tag("level", function (Session $user): string {
            return (string)LevelManager::getLevelFromCache($user->getPlayer());
        }));
        TagManager::getInstance()->registerTag(new Tag("exp_level", function (Session $user): string {
            return LevelManager::getExpFormattedFromCache($user->getPlayer());
        }));
    }

    /**
     * For load ScoreHud tag
     * @return void
     */
    public function loadScoreHud(): void {
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                if (!$player->isOnline()) continue;
                (new PlayerTagUpdateEvent($player, new ScoreTag("levelsystem.level", LevelManager::getLevelFromCache($player))))->call();
                (new PlayerTagUpdateEvent($player, new ScoreTag("levelsystem.exp", LevelManager::getExpFormattedFromCache($player))))->call();
            }
        }), 20);
    }
}
