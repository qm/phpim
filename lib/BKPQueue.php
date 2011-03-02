<?php

 /**@package: BKPQueue
   *@description: muti-memcahceq with reduce support
   *@author: slabtop@yahoo.com
   */


class BKPQueue {
  
  private $handle;  
  private $server;

  public function __construct($cluster = 'default') {
    
    $this->handle = new Memcache();
    $this->server = new Memcached();

    foreach ($GLOBALS['MQ_SERVER'][$cluster] as $mq) {
      list($ip,$port) = explode(':',$mq);
      $this->handle->addServer($ip,$port,true);
      $this->server->addServer($ip,$port);
    }

   }

  function push($item,$value) {
    return memcache_set($this->handle,$item,$value,0,0);
  }


  function pop($item) {
    $value =  memcache_get($this->handle,$item);
    if(!$value) 
      $value = $this->server->get($item);
     
    return $value;
  }

  function close() {
    return memcache_close($this->handle);
  }
  
  function reduce($item,$first,$callback) {
    $result = $first;
    while(($curr = $this->pop($item)) && isset($curr))
      $result = call_user_func($callback,$curr,$result);
    return $result;
  }

  function getServerByKey($item) {
	return $this->server->getServerByKey($item);
  }

  function stat() {
    echo '<pre>';
    var_dump($this->server->getStats());
    echo '<pre />';
  }
}
