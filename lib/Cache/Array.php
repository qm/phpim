<?php
  /**
   *@package Cache_Array
   *@description memcache with arrayaccess
   *@author slabtop@yahoo.com
   *
   *@example 
   * $mc = new Cache_Array($cluster='op'); 
   * or you can get singleton instance with:
   * $mc = Cache_Array::instance($cluster = 'op');
   * 
   * Get value from key:
   *  $value = $mc['key'];
   *
   * Modify or Add new key:
   *  $mc['key'] = $value;
   * set expire with setExpire($expire_time,$dynamic_flag) after get instance above;
   *
   * method stat() will show memcached stats.
   */

class Cache_Array implements arrayaccess {
  
  private $handle;
  private static $cache = array();
  public static $expire; //memcache item expire time
  
  public  function __construct ($cluster = 'default',$expire = 300) {
    $this->handle = new Memcache;
    
    foreach($GLOBALS['MEMCACHED_SERVERS'][$cluster] as $mc) {
      list($ip, $port, $weight) = explode(':', $mc);
      $this->handle->addServer($ip,$port,true,$weight,30);
    }
    
    $this->handle->setCompressThreshold(5000);

    self::$expire = $expire;
      
  }

  public static function instance($cluster = 'default') {
    if(isset(self::$cache[$cluster]))
      return self::$cache[$cluster];
    else
      return self::$cache[$cluster] = new Cache_Array($cluster);
  }

  public function offsetExists($item) {
    return $this->handle->get($item);
  }

  public function offsetGet($item) {
    return $this->handle->get($item);
  }

  public function offsetSet($item,$value) {
    return $this->handle->set($item,$value,0,self::$expire);
  }

  public function offsetUnset($item) {
    return $this->handle->delete($item);
  }

  public function setExpire($time,$dynamic = 0) {
    if($dynamic)
      self::$expire = rand($time/2,$time*1.5);
    else
      self::$expire = $time;
  }

  public function close() {
    $this->handle->close();
  }

  public static function sclose($cluster = 'default'){
    self::$cache[$cluster]->close();
    unset(self::$cache[$cluster]);
  }
  
  public function stat() {
    echo '<pre>';
    var_dump(self::$cache);
    var_dump($this->handle->getStats());
    echo '<pre/>';
  }


}

  
