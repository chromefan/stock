<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * Memcache缓存类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class CacheMemcache
{//类定义开始
	/**
	 * memcache 操作句柄
	 *
	 * @var obj
	 */
	private $handler;

	/**
	 * 单例实例
	 *
	 * @var obj
	 */
	private static $instance;
	/**
	 * 缓存有效期
	 *
	 * @var int
	 */
	protected $expire;

	/**
	 * Memcache服务器配置
	 *
	 * @var array
	 */
	protected $options = array();

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    private function __construct($options)
    {
        if ( !extension_loaded('memcache') ) {
            throw_exception(L('_NOT_SUPPERT_').':memcache');
        }
        if(empty($options)) {
            $options = array(
                'timeout' => false,
                'persistent' => false,
                'servers'=>array(
                    array('ip'=>'192.168.1.151','port'=>11212),
                    array('ip'=>'192.168.1.152','port'=>11212)
                ),
            );
        }
        $func = 'addServer';	//$options['persistent'] ? 'pconnect' : 'connect';
        $this->expire = isset($options['expire'])?$options['expire']:C('MEMCACHE_CACHE_TIME');
        $this->handler  = new Memcache;

        if($options['timeout'] === false)
        {
            foreach($options['servers'] as $server)
            {
                $this->handler->$func($server['ip'], $server['port'], $options['persistent']);
            }
        }
    }

    /**
     * 单例模式得到唯一实例
     *
     * @param array $config memcache 配置文件
     * @return unknown
     */
    public static function getInstance($config = array())
    {
        if(self::$instance == null)
        {
            self::$instance = new CacheMemcache($config);
        }

        return self::$instance;
    }

    /**
     +----------------------------------------------------------
     * 读取缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function get($name)
    {
        return $this->handler->get($name);
    }

    /**
     +----------------------------------------------------------
     * 写入缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function set($name, $value, $ttl = null)
    {
        $compress = is_bool($value) || is_int($value) || is_float($value) ? 0 : MEMCACHE_COMPRESSED;
        if(isset($ttl) && is_int($ttl))
            $expire = $ttl;
        else
            $expire = $this->expire;
        return $this->handler->set($name, $value, $compress, $expire);
    }

    /**
     +----------------------------------------------------------
     * 删除缓存
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function rm($name, $ttl = false)
    {
        return $ttl === false ?
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

    /**
     +----------------------------------------------------------
     * 清除缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function clear()
    {
        return $this->handler->flush();
    }

    /**
     * 命名空间key
     *
     * @return unknown
     */
    public function ns_key($name)
    {
    	$ns_key = $this->handler->get($name);
    	if ($ns_key === false) {
    		$ns_key = time();
    		$this->handler->set($name, $ns_key, 864000);//10天
    	}
        return $ns_key;
    }
}//类定义结束
?>