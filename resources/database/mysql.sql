-- #!mysql
-- # { init_data
CREATE TABLE IF NOT EXISTS player_level (
  name TEXT PRIMARY KEY,
  level INT DEFAULT 1,
  exp INT DEFAULT 0,
  next_exp INT DEFAULT 100
);
-- # }
-- # { set_data
-- #    :name string
-- #    :level int
-- #    :exp int
-- #    :next_exp int
INSERT OR REPLACE INTO player_level (name, level, exp, next_exp) VALUES (:name, :level, :exp, :next_exp);
-- # }
-- # { get_data
-- #    :name string
SELECT * FROM player_level WHERE name = :name;
-- # }
-- # { update_data
-- #    :name string
-- #    :level int
-- #    :exp int
-- #    :next_exp int
UPDATE player_level SET level = :level, exp = :exp, next_exp = :next_exp WHERE name = :name;
-- # }
