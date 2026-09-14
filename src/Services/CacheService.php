<?php
// /**
//  * CacheService class file
//  *
//  * PHP Version 7.2.19
//  *
//  * @category Service
//  * @package  LumePack\Foundation\Services
//  * @author   KDSaltyPony <kallofdragon@gmail.com>
//  * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
//  * @link     none
//  */
// namespace LumePack\Foundation\Services;

// use Illuminate\Contracts\Cache\Repository as CacheRepository;
// use Illuminate\Support\Facades\Cache;

// /**
//  * CacheService
//  *
//  * @category Service
//  * @package  LumePack\Foundation\Services
//  * @author   KDSaltyPony <kallofdragon@gmail.com>
//  * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
//  * @link     none
//  */
// class CacheService
// {
//     /**
//      * Get the cache store
//      *
//      * @return CacheRepository
//      */
//     public static function store(string $name = null): CacheRepository
//     {
//         return Cache::store($name);
//     }

//     /**
//      * Create a key to store elements in cache
//      *
//      * @return string
//      */
//     protected static function key(string $method, string $type): string
//     {
//         return "{$method}:{$type}:{$this->id}";
//     }
// }
