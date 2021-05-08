<?php

declare (strict_types=1);

namespace lgdz;

use Exception;
use ReflectionClass;
use lgdz\lib\{
    Helper,
    HttpRequest,
    HttpResponse,
    JwtAuth,
    NumberCompute,
    NumberFormat,
    Password,
    Pinyin,
    Time,
    Tree,
    DbDictionary
};

/**
 * Class Factory
 * @property-read HttpRequest $request;
 * @property-read HttpResponse $response;
 * @property-read Helper $helper;
 * @property-read NumberCompute $num_compute;
 * @property-read NumberFormat $num_format;
 * @property-read Password $password;
 * @property-read Pinyin $pinyin;
 * @property-read Time $time;
 * @property-read Tree $tree;
 * @property-read JwtAuth jwt;
 * @property-read DbDictionary $db_dictionary;
 * @package lgdz
 */
class Factory
{
    public static $__self__ = null;
    public static $container = [];

    public function __construct()
    {
        if (is_null(self::$__self__)) {
            static::$__self__ = $this;
        }
    }

    protected $class = [
        'helper'        => Helper::class,
        'request'       => HttpRequest::class,
        'response'      => HttpResponse::class,
        'jwt'           => JwtAuth::class,
        'num_compute'   => NumberCompute::class,
        'num_format'    => NumberFormat::class,
        'password'      => Password::class,
        'pinyin'        => Pinyin::class,
        'time'          => Time::class,
        'tree'          => Tree::class,
        'db_dictionary' => DbDictionary::class
    ];

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        $class_name = $this->class[$name];
        if (!isset(static::$container['class'][$class_name])) {
            try {
                $class = new ReflectionClass($class_name);
            } catch (\ReflectionException $e) {
                throw new Exception($e->getMessage());
            }
            static::$container['class'][$class_name] = $class->newInstance();
        }
        return static::$container['class'][$class_name];
    }

    public static function container(): self
    {
        if (is_null(self::$__self__)) {
            static::$__self__ = new static;
        }
        return static::$__self__;
    }
}