<?php

namespace app\http\middleware;

class CheckToken
{
    public function handle($request, \Closure $next)
    {
        // if (!$request->header('Token')) {
        //     $return = [
        //         'code' => 1000,
        //         'msg' => '未登录'
        //     ];
        //     return response(json_encode($return));
        // }
        return $next($request);
    }
}
