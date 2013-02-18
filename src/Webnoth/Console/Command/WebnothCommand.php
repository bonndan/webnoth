<?php

namespace Webnoth\Console\Command;

use Symfony\Component\Console\Command\Command;

/**
 * Base class for Webnoth Commands
 * 
 * 
 */
abstract class WebnothCommand extends Command
{
    /**
     * creates a cache instance
     * 
     * @return \Doctrine\Common\Cache\FilesystemCache
     */
    protected function getCache()
    {
        $cache = new \Doctrine\Common\Cache\FilesystemCache(CACHE_PATH);
        return $cache;
    }
}