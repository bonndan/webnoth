# Webnoth

Webnoth is an experiment to render "Battle for Wesnoth" map in a 3D web based
environment.

![webnoth_lightmap](https://github.com/bonndan/webnoth/raw/master/doc/webnoth_lightmap.png "Attempt with heightmap and lightmap")


## Installation

- clone the github repo.
- install composer


##Usage

First create a cache of the terrain configuration 

```
php consolenoth.php parse:terrain data/terrain.cfg
```

then parse a map (will be cached as well)

```
php consolenoth.php parse:map data/01_The_Elves_Besieged.map
```

then have the map rendered:

```
php consolenoth.php render:map 01_The_Elves_Besieged
```

all results will be stored in the ./cache directory.