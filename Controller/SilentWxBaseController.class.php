<?php

/**
 * author: Jayin <tonjayin@gmail.com>
 */

namespace Wechat\Controller;

use Common\Controller\Base;
use Think\Exception;

/**
 * 静默授权（只获取用户openid）
 */
class SilentWxBaseController extends Base {

    /**
     * 授权获取的用户openid
     * @var string|null
     */
    public $wx_openid = null;
    public $open_app_id;

    const __WECHAT_TOKEN_NAME = 'wechattoken';

    protected function _initialize() {
        parent::_initialize();
        //检测是否微信浏览器
        $is_wechat = strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
        if ($is_wechat) {
            $open_app_id = $this->open_app_id = $this->getOpenAppId();
            if (empty($open_app_id)) {
                $this->error('缺少参数 open_app_id(开放平台 app_id)');

                return;
            }
            $open_app = $this->getOpenApp($open_app_id);

            if (!I('get.openid')) {
                //没有登录
                if (session('wx_openid')) {
                    $this->wx_openid = session('wx_openid');
                } else {
                    //没有微信用户资料
                    $return_url = $this->createReturnURL($open_app_id);
                    $param = "url=" . urlencode($return_url);
                    $oauthUrl = 'http://open.ztbopen.cn/oauth2_base/' . $open_app['open_alias'] . '.html?' . $param;
                    redirect($this->signEncode($oauthUrl, $open_app['open_secret_key']));
                }
            } else {
                //签名验证
                $this->signDecode(get_url(), $open_app['open_secret_key']);

                //token校验
                if (C("WECHAT_TOKEN_ON")) {
                    $received_token = I('get.' . self::__WECHAT_TOKEN_NAME);

                    if ($received_token != session(self::__WECHAT_TOKEN_NAME)) {
                        throw new Exception('Token校验失败');
                    }

                    session(self::__WECHAT_TOKEN_NAME, null);
                }

                //保存openid
                $wx_openid = I('get.openid');
                session('wx_openid', $wx_openid);
                $this->wx_openid = session('wx_openid');
            }
        }
    }

    /**
     * 生成认证签名
     *
     * @param $url
     * @param $secret_key
     * @return string|bool
     */
    public function signEncode($url, $secret_key) {
        $url_arr = explode('?', $url);
        if (empty($url_arr[1])) {
            $this->error('参数错误');

            return false;
        } else {
            $param_str = $url_arr[1] . "&time=" . time(); //加上签名的时间戳
            $sign = md5(urlencode(trim($param_str)) . $secret_key); //生成签名
            return $url_arr[0] . "?" . $param_str . "&sign=" . $sign;
        }
    }

    /**
     * 签名认证
     *
     * @param string $url 带有签名的url
     * @param string $secret_key 签名私钥
     * @return bool
     */
    public function signDecode($url, $secret_key) {
        $url_arr = explode('?', $url);
        if (empty($url_arr[1])) {
            $this->error('参数错误');

            return false;
        } else {
            $param_sign = explode('&sign=', $url_arr[1]);
            $param = $param_sign[0]; //对于获取到的参数浏览器可能会decode
            if (empty($param_sign[1])) {
                $this->error('签名失败');

                return false;
            } else {
                $sign = $param_sign[1];
            }
            if (md5(urlencode(trim($param)) . $secret_key) == $sign) {
                //签名成功，可以继续操作
                return true;
            } else {
                $this->error('签名失败');

                return false;
            }
        }
    }

    /**
     * 构建授权完成后的跳转链接(带有open_app_id参数)
     *
     * @param string $open_app_id 微信第三方平台的app_id
     * @return string
     */
    public function createReturnURL($open_app_id) {
        $current_url = get_url();
        if (strpos($current_url, '?') !== false) {
            $current_url .= '&open_app_id=' . $open_app_id;
        } else {
            $current_url .= '?open_app_id=' . $open_app_id;
        }

        //生成token
        if (C("WECHAT_TOKEN_ON")) {
            $new_token = md5($open_app_id . time());
            session(self::__WECHAT_TOKEN_NAME, $new_token);
            $current_url .= '&' . self::__WECHAT_TOKEN_NAME . '=' . $new_token;
        }

        return $current_url;
    }

    /**
     * 微信环境下，获取的开放平台应用ID(open_app_id)
     *
     * @return mixed
     */
    protected function getOpenAppId() {
        $open_app_id = I('get.open_app_id', session('__open_app_id'));
        $db = M('WechatApp');

        if (empty($open_app_id)) {
            //没有指定就检测有无默认配置
            $app = $db->where(['default' => 1])->find();
            $open_app_id = $app['open_app_id'];
        }
        session('__open_app_id', $open_app_id);

        return $open_app_id;
    }

    /**
     * 获取给定的开放平台应用配置
     *
     * @param $open_app_id
     * @return array
     * @throws Exception
     */
    protected function getOpenApp($open_app_id) {
        $db = M('WechatApp');
        if (!empty($open_app_id)) {
            $app = $db->where(['open_app_id' => $open_app_id])->find();
        } else {
            $app = $db->where(['default' => 1])->find();
        }
        if (empty($app)) {
            throw new Exception('请指定默认的开放应用 open_app_id');
        }

        return $app;
    }

}