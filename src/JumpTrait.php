<?php
/*
|----------------------------------------------------------------------------
| TopWindow [ Internet Ecological traffic aggregation and sharing platform ]
|----------------------------------------------------------------------------
| Copyright (c) 2006-2019 http://yangrong1.cn All rights reserved.
|----------------------------------------------------------------------------
| Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
|----------------------------------------------------------------------------
| Author: yangrong <yangrong2@gmail.com>
|----------------------------------------------------------------------------
*/

namespace Learn\Jump;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Foundation\Application;

trait JumpTrait
{
    /**
     * 应用实例
     * @var \Illuminate\Contracts\Foundation\Application 
     */
    protected $app;
	
    /**
     * Object Oriented
     * 
     * @param  \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->registerJumpViewPaths();
    }
	
    /**
     * 操作中跳转的快捷方法
     * 
     * @param  string $msg    提示信息
     * @param  string $url    跳转的 URL 地址
     * @param  mixed  $data   返回的数据
     * @param  int    $wait   跳转等待时间
     * @param  string $target 窗口打开方式
     * @param  bool   $throw
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function operation(string $msg = 'CONTINUE_SERVICE', string $url = '', $data = null, int $wait = 1, string $target = '_self', bool $throw = true)
    {
        $url = $this->buildUrl($url);
        $type = $this->getResponseType();
        $result = [];
        $result['code'] = 100;
        $result['status'] = 'OPERATION';
        $result['message'] = $msg;
        $result['data'] = $data;
        $result['url'] = $url;
        $result['wait'] = $wait;
        $result['target'] = $target;
        if ('html' == strtolower($type)) {
            $response = new Response(View::make('jumps::operation', $result));
        } else {
            $response = new JsonResponse($result);
        }
        return $this->throwResponse($response, $throw);
    }
	
    /**
     * 操作成功跳转的快捷方法
     * 
     * @param  string $msg    提示信息
     * @param  string $url    跳转的 URL 地址
     * @param  mixed  $data   返回的数据
     * @param  int    $wait   跳转等待时间
     * @param  string $target 窗口打开方式
     * @param  bool   $throw
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function success(string $msg = 'COMPLETE_SERVICE', string $url = '', $data = null, int $wait = 1, string $target = '_self', bool $throw = true)
    {
        $url = $this->buildUrl($url);
        $type = $this->getResponseType();
        $result = [];
        $result['code'] = 200;
        $result['status'] = 'SUCCESS';
        $result['message'] = $msg;
        $result['data'] = $data;
        $result['url'] = $url;
        $result['wait'] = $wait;
        $result['target'] = $target;
        if ('html' == strtolower($type)) {
            $response = new Response(View::make('jumps::success', $result));
        } else {
            $response = new JsonResponse($result);
        }
        return $this->throwResponse($response, $throw);
    }
	
    /**
     * 操作错误跳转的快捷方法
     * 
     * @param  string $msg    提示信息
     * @param  string $url    跳转的 URL 地址
     * @param  mixed  $data   返回的数据
     * @param  int    $wait   跳转等待时间
     * @param  string $target 窗口打开方式
     * @param  bool   $throw
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function error(string $msg = 'NOT_FOUND_SERVICE', string $url = '', $data = null, int $wait = 5, string $target = '_self', bool $throw = true)
    {
        $url = $this->buildUrl($url);
        $type = $this->getResponseType();
        $result = [];
        $result['code'] = 404;
        $result['status'] = 'ERROR';
        $result['message'] = $msg;
        $result['data'] = $data;
        $result['url'] = $url;
        $result['wait'] = $wait;
        $result['target'] = $target;
        if ('html' == strtolower($type)) {
            $response = new Response(View::make('jumps::error', $result));
        } else {
            $response = new JsonResponse($result);
        }
        return $this->throwResponse($response, $throw);
    }
	
    /**
     * 短消息函数,可以在某个动作处理后友好的提示信息
     *
     * @param  string  $message      
     * @param  string  $url    
     * @param  int     $limittime  
     * @param  bool    $onlymsg  
     * @param  bool    $throw
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function showMsg(string $message = '', string $url = '', int $limittime = 5, bool $onlymsg = false, bool $throw = true)
    {
        if (('-1' !== $url || $url != 'javascript:;') && !preg_match('/close::/', $url)) {
            $url = $this->buildUrl($url);
        }
        $tgobj = '';
        if ($url == '-1') {
            $url = 'javascript:history.go(-1);';
        }
        if ($onlymsg) {
            $template = 'jumps::only_msg';
        } else {
            //当网址为:close::objname 时, 关闭父框架的id=objname元素
            if (preg_match('/close::/', $url)) {
                $tgobj = trim(preg_replace('/close::/', '', $url));
                $url = 'javascript:;';
            }
            $template = 'jumps::show_msg';
        }
        $response = new Response(View::make($template, compact('message', 'url', 'tgobj', 'limittime')));
        return $this->throwResponse($response, $throw);
    }
	
    /**
     * 获取当前的 response 输出类型
     * 
     * @return  string
     */
    protected function getResponseType()
    {
        $request = $this->app['request'];
        return $request->input('_ajax') !== null || $request->expectsJson() ? 'json' : 'html';
    }
	
    /**
     * 注册跳转视图路径
     *
     * @return void
     */
    protected function registerJumpViewPaths()
    {
        View::replaceNamespace('jumps', collect(config('view.paths'))->map(function ($path) {
            return sprintf('%s/jumps', $path);
        })->push(dirname(__DIR__) . '/views')->all());
    }
	
    /**
     * 设置跳转的url地址
     *
     * @param  string  $url 
     * @return string
     */
    protected function buildUrl(string $url = '')
    {
        if ('' == $url) {
            if (!is_null($this->app['request']->server('HTTP_REFERER'))) {
                $url = $this->app['request']->server('HTTP_REFERER');
            } else {
                $url = $this->getResponseType() === 'json' ? '' : 'javascript:history.back(-1);';
            }
        } else {
            if (!strpos($url, '://') && 0 !== strpos($url, '/')) {
                $url = $this->app['url']->to($url);
            }
        }
        return $url;
    }
	
    private function throwResponse($response, bool $throw = true)
    {
        return $throw ? $response->throwResponse() : $response;
    }
}