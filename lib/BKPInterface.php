<?php

/*@package: BKPInterface
 *@description: rpc interface for backend IM server.
 *@author: slabtop@yahoo.com
 */

class BKPInterface {
   
  public static function sendJson($url, array $options, $timeout = 500, $debug = 0, $method = 'post') {
    
    if(empty($options) || empty($url))
      return false;
    else $options['cliendid'] = defined(ICE_CLIEND_ID) ? ICE_CLIEND_ID : 410001;
    
    $request_url = $url.'?jsonStr='.urlencode(json_encode($options));
    
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL,$request_url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
    curl_setopt ($ch, CURLOPT_TIMEOUT_MS, $timeout);
    $handles = curl_exec($ch);
    curl_close($ch);

    if($debug) self::dump(array('options'=>$options,'result'=>$handles,'url'=>$request_url));

    if($handles === false) return false;
    else {
      $json = json_decode($handles,true);
      if(isset($json['result']) && !empty($json['result']))
        return $json['result'];
      else
        return false;
    }
    
  }

  public static function dump($info) {
    echo '<pre>';
    var_dump($info);
    echo '<pre />';
  }

}
