<?php

namespace VsrStudio\LevelSystem\manager;

use VsrStudio\LevelSystem\LevelSystem;
use poggit\libasynql\libasynql;
use poggit\libasynql\DataConnector;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;

class LevelManager {

    /**
     * @var DataConnector $database
     */
    public static DataConnector $database;

    /**
     * @var LevelSystem $plugin
     */
    public static LevelSystem $plugin;

    /**
     * @var array $level_cache
     */
    public static array $level_cache;

    /**
     * @param LevelSystem $plugin
     */
    public function __construct(LevelSystem $plugin) {
        self::$database = libasynql::create($plugin, $plugin->getConfig()->get("database"), [
            "mysql" => "database/mysql.sql",
            "sqlite" => "database/sqlite.sql"
        ]);
        self::$database->executeGeneric("init_data");
        self::$database->waitAll();
        self::$plugin = $plugin;
    }

    /**
     * @return void
     */
    public static function closeDatabase(): void {
        self::$database->close();
    }

    /**
     * @return DataConnector
     */
    public static function getDatabase(): DataConnector {
        return self::$database;
    }

    /**
     * @param Player $player
     * @param int $level
     * @param int $exp
     * @param int $next_exp
     * @return void
     */
    public static function addPlayerDataToCache(Player $player, string|int $level, string|int $exp, string|int $next_exp): void {
        self::$level_cache[$player->getName()] = [
            "level" => $level,
            "exp" => $exp,
            "next_exp" => $next_exp
        ];
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function removePlayerDataFromCache(Player $player): void {
        if (isset(self::$level_cache[$player->getName()])) unset(self::$level_cache[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public static function issetDataPlayerFromCache(Player $player): bool {
        return isset(self::$level_cache[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return int
     */
    public static function getLevelFromCache(Player $player): int {
        $level = 0;
        if (isset(self::$level_cache[$player->getName()])) {
            $level = self::$level_cache[$player->getName()]["level"];
        }
        return $level;
    }

    /**
     * @param Player $player
     * @return int
     */
    public static function getExpFromCache(Player $player): int {
        $exp = 0;
        if (isset(self::$level_cache[$player->getName()])) {
            $next_exp = self::$level_cache[$player->getName()]["exp"];
        }
        return $exp;
    }

    /**
     * @param Player $player
     * @return int
     */
    public static function getExpFormattedFromCache(Player $player): string {
        $exp = 0;
        if (isset(self::$level_cache[$player->getName()])) {
            $exp = number_format(self::$level_cache[$player->getName()]["exp"]);
        }
        return $exp;
    }

    /**
     * @param Player $player
     * @return int
     */
    public static function getNextExpFromCache(Player $player): int {
        $next_exp = 0;
        if (isset(self::$level_cache[$player->getName()])) {
            $next_exp = self::$level_cache[$player->getName()]["next_exp"];
        }
        return $next_exp;
    }

    /**
     * @param Player $player
     * @return int
     */
    public static function getNextExpFormattedFromCache(Player $player): string {
        $next_exp = 0;
        if (isset(self::$level_cache[$player->getName()])) {
            $next_exp = number_format(self::$level_cache[$player->getName()]["next_exp"]);
        }
        return $next_exp;
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function createData(Player $player): void {
        self::$database->executeInsert("set_data", [
            "name" => $player->getName(),
            "level" => 1,
            "exp" => 0,
            "next_exp" => 1000
        ]);
        self::$level_cache[$player->getName()] = [
            "level" => 1,
            "exp" => 0,
            "next_exp" => 1000
        ];
    }

    /**
     * @param Player $player
     * @param int $data
     */
    public static function addExp(Player $player, int $amount): void {
        self::$database->executeSelect("get_data", [
            "name" => $player->getName()
        ],
        /**
         * @param array $rows
         */
        function (array $rows) use ($player, $amount) {
            if (empty($rows)) return;
            $level = $rows[0]["level"];
            $exp = $rows[0]["exp"] + $amount;
            $next_exp = $rows[0]["next_exp"];
            if ($exp >= $next_exp) {
                $level++;
                $next_exp = $level + 1 * 1000 + mt_rand(1, 10) + mt_rand(10, 100) + mt_rand(1, 50);
                $player->sendTitle("§aLevel Up!", "§e" . $rows[0]["level"] . " §b----> §e" . $level);
                $player->sendToastNotification("§l§aLEVELUP!", "§eYou have leveled up to level " . $level);
                self::sendReward($player);
                if (self::$plugin->getConfig()->getNested("level.announcement_when_up_level.enable")) {
                    $message = str_replace(["{player}", "{level}", "{old_level}"], [$player->getName(), $level, $rows[0]["level"]], self::$plugin->getConfig()->getNested("level.announcement_when_up_level.message"));
                    self::$plugin->getServer()->broadcastMessage(LevelSystem::PREFIX . $message);
                }
            }
            self::$database->executeChange("update_data", [
                "name" => $player->getName(),
                "level" => $level,
                "exp" => $exp,
                "next_exp" => $next_exp
            ]);
            self::$level_cache[$player->getName()] = [
                "level" => $level,
                "exp" => $exp,
                "next_exp" => $next_exp
            ];
        });
    }

    /**
     * @param Player $player
     * @param string $type_data
     * @param callable $callback
     * @return void
     */
    public static function getData(Player $player, string $type_data, callable $callback): void {
        if (!in_array($type_data, ["level", "exp", "next_exp"])) return;
        self::$database->executeSelect("get_data", [
            "name" => $player->getName()
        ],
        /**
         * @param array $rows
         */
        function (array $rows) use ($callback): void {
            if (empty($rows)) {
                $callback(null);
                return;
            } else {
                $callback($rows[0][$type_data]);
            }
        });
    }

    /**
     * @param Player $player
     * @param callable $callback
     * @return void
     */
    public static function getDatas(Player $player, callable $callback): void {
        self::$database->executeSelect("get_data", [
            "name" => $player->getName()
        ],
        /**
         * @param array $rows
         */
        function (array $rows) use ($callback): void {
            if (empty($rows)) {
                $callback(null);
                return;
            } else {
                $callback($rows[0]);
            }
        });
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function sendReward(Player $player): void {
        foreach (self::$plugin->getConfig()->getNested("level.reward_command", []) as $command) {
            self::$plugin->getServer()->dispatchCommand(new ConsoleCommandSender(self::$plugin->getServer(), self::$plugin->getServer()->getLanguage()), str_replace("{player}", $player->getName(), $command));
        }
        $player->sendMessage(LevelSystem::PREFIX . "§aThe reward successfully sent to you!");
    }
}
