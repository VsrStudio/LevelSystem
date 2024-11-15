# LevelSystem Plugin for PocketMine-MP

A simple and customizable LevelSystem plugin for PocketMine-MP that allows you to manage player levels and experience. It integrates with the **ScoreHud** plugin to display level and experience data in the scoreboard.

## Features

- Customizable player levels and experience
- Integration with the **ScoreHud** plugin for displaying player level and experience
- Player data stored in `playerData.yml`

## Installation

1. Download the **LevelSystem** plugin.
2. Place the plugin folder inside your `plugins/` directory of your PocketMine-MP server.
3. Ensure that the **ScoreHud** plugin is also installed and enabled on your server.

## Configuration

The plugin uses a `playerData.yml` file to store player levels and experience. The default configuration does not require any changes, but you can modify the file manually if necessary.

## Usage

Once installed and enabled, the **LevelSystem** plugin will automatically manage player levels and experience. You can access and update player data programmatically with the following functions:

- **`getPlayerData($name)`** - Retrieves player data (level and experience).
- **`savePlayerData($name, $data)`** - Saves player data (level and experience).

The **ScoreHud** plugin will display the player's level and experience using the following tags:

- `{levelsystem.level}` - Player's level
- `{levelsystem.exp}` - Player's experience

## Commands

There are no commands available by default. You can interact with the system through code or events.
