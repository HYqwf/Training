<?php
public function _initialize(){
        $URL['PHP_SELF'] = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);   //当前页面名称
        $URL['DOMAIN'] = $_SERVER['SERVER_NAME'];  //域名(主机名)
        $URL['QUERY_STRING'] = $_SERVER['QUERY_STRING'];   //URL 参数
        $URL['URI'] = $URL['PHP_SELF'].($URL['QUERY_STRING'] ? "?".$URL['QUERY_STRING'] : "");
        $this->fromurl = "http://".$URL['DOMAIN'].$URL['PHP_SELF'].($URL['QUERY_STRING'] ? "?showwxpaytitle=1&&".$URL['QUERY_STRING'] : ""); //完整URL地址
    
        $userinfo=cookie('userinfo');
        
        if(!$userinfo){
            //创建微信网页授权URL
            $oauth = & load_wechat('Oauth');
            $code = input('code',0);//从网址中获取CODE参数，如果没有，则为0
            if($code){
                //如果有CODE，则开始获取access_token和openid
                $result = $oauth->getOauthAccessToken();
                if(!$result){
                    //获取失败
                    return '获取失败';
                }else{
                    //获取成功，开始获取用户的基本信息
                    $access_token=$result['access_token'];
                    $openid=$result['openid'];
                    $result_userinfo = $oauth->getOauthUserinfo($access_token, $openid);
                    if(!$result_userinfo){
                        //获取失败
                        return '用户信息获取失败';
                        
                    }else{
                        cookie('userinfo',$result_userinfo,86400);
                    }
                }
            }
            
            $callback=$this->fromurl;
            $state='STATE';
            $scope='snsapi_userinfo';
            $result = $oauth->getOauthRedirect($callback, $state, $scope);
            header("Location:".$result);
        }
    }