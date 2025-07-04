<?php
namespace plugin\control\event;

use app\expose\enum\EventName;
use app\expose\helper\Uploads;
use app\expose\utils\wechat\modules\Reply;
use plugin\user\app\model\PluginUser;
use plugin\user\app\model\PluginUserWechat;
use support\Log;
use support\Redis;
use think\facade\Db;
use Webman\Event\Event;

class WechatOfficialAccount
{
    use Reply;
    public function login($data)
    {
        Db::startTrans();
        try {
            $UserWechat=PluginUserWechat::where('openid',$data['FromUserName'])->find();
            if($UserWechat)
            {
                if($UserWechat->uid){
                    $User=PluginUser::where('id',$UserWechat->uid)->find();
                    Redis::set($data['EventKey'].'_callback',$User->id,'EX',60);
                    Db::commit();
                    Event::emit(EventName::USER_LOGIN['value'], $User);
                    return $this->replyText($data,'欢迎登录：'.$User->nickname);
                }
                $User=new PluginUser();
                if($UserWechat->nickname){
                    $User->nickname=$UserWechat->nickname;
                }
                if($UserWechat->headimg){
                    try {
                        $User->headimg=Uploads::download($UserWechat->headimg);
                    } catch (\Throwable $th) {
                    }
                }
                if(isset($data['params'])&&isset($data['params']['puid'])){
                    $User->puid=$data['params']['puid'];
                }
                $User->activation_time=date('Y-m-d H:i:s');
                $User->save();
            }else{
                $User=new PluginUser();
                if(isset($data['params'])&&isset($data['params']['puid'])){
                    $User->puid=$data['params']['puid'];
                }
                $User->activation_time=date('Y-m-d H:i:s');
                $User->save();
                $UserWechat=new PluginUserWechat();
                $UserWechat->openid=$data['FromUserName'];
            }
            $UserWechat->subscribe=1;
            $UserWechat->uid=$User->id;
            $UserWechat->save();
            Redis::set($data['EventKey'].'_callback',$User->id,'EX',60);
            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            Log::error('微信扫码登录失败：'.$th->getMessage(),$th->getTrace());
            return $this->replyText($data,'登录失败');
        }
        Event::emit(EventName::USER_REGISTER['value'], $User);
        return $this->replyText($data,'注册成功');
    }
}