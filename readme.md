# Webnoth

Webnoth is an attempt to clone parts of "Battle for Wesnoth" in an web based
environment. For now it renders maps to bitmaps. In the next step, these images
will be used as texture an WebGL scenes and heightmaps will be generated and applied
automatically.

## Installation

- clone the github repo.
- create caches: 

```
php consolenoth.php parse:terrain data/terrain.cfg
php consolenoth.php parse:map data/01_The_Elves_Besieged.map
```

- then have the map rendered:

```
php consolenoth.php render:map 01_The_Elves_Besieged
```