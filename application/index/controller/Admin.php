<?php
namespace app\index\controller;
use app\index\model\User;
use app\index\model\Manager;
use app\index\model\Activity;
use app\index\model\Story;
use think\Request;

class Admin
{
    public function __construct()
    {
    	
    }

    public function getStoryList(Request $req)
    {
        $per_page = $req->get('perPage');
        $curr_page = $req->get('currPage');
        $activity_id = $req->get('activityId');
        $where = [];
        if (!$per_page) {
            $per_page = 10;
        }
        if (!$curr_page) {
            $curr_page = 1;
        }
        if ($activity_id) {
            $where[] = ['story.activity_id', '=', $activity_id];
        }
        $total = Story::where($where)->count();
        $list = Story::where($where)
                    ->page($curr_page, $per_page)
                    ->join('activity', 'story.activity_id = activity.id')
                    ->field('story.id, story.activity_id, story.nick_name, story.content, story.add_time, story.match_count, activity.title as activity_title')
                    ->select();
        $return = [
            'code' => 0,
            'data' => [
                'total' => $total,
                'data' => $list
            ]
        ];
        return json_encode($return);
    }

    public function getStoryDetail(Request $req)
    {
        $id = $req->get('id');
        $story_one = Story::get($id);
        if ($story_one) {
            $return = [
                'code' => 0,
                'data' => $story_one
            ];
        } else {
            $return = [
                'code' => 1,
                'msg' => '数据不存在'
            ]; 
        }
        return json_encode($return);
    }

    public function getActivityList(Request $req)
    {
        header("Access-Control-Allow-Origin: *");
        $per_page = $req->get('perPage');
        $curr_page = $req->get('currPage');
        $title = $req->get('title');
        $where = [];
        if ($title) {
            $where[] = ['title', 'like', '%'.$title.'%'];
        }
        if (!$per_page) {
            $per_page = 10;
        }
        if (!$curr_page) {
            $curr_page = 1;
        }
        $total = Activity::where($where)->count();
        $list = Activity::where($where)->page($curr_page, $per_page)->select();          
        $return = [
            'code' => 0,
            'data' => [
                'total' => $total,
                'data' => $list
            ]
        ];
        
        return json_encode($return);
    }

    public function getActivityDetail(Request $req)
    {
        $code = $req->get('code');
        $activity_one = Activity::get(['code' => $code]);
        if ($activity_one) {
            $return = [
                'code' => 0,
                'data' => $activity_one
            ];
        } else {
            $return = [
                'code' => 1,
                'msg' => '数据不存在'
            ]; 
        }
        return json_encode($return);
    }

    public function login(Request $req)
    {
        $name = $req->post('username');
        $password = md5($req->post('password'));
        $user_one = Manager::get(['username' => $name, 'password' => $password]);
            if ($user_one) {
                $user_one->token = '999';
                $return = [
                    'code' => 0,
                    'data' => $user_one
                ];
            } else {
                $return = [
                    'code' => 1,
                    'msg' => '登录失败，请重新输入'
                ];
            }
        return json_encode($return);
    }

    public function updatePassword(Request $req)
    {
        $id = $req->post('id');
        $password = $req->post('password');
        $password_confirm = $req->post('passwordConfirm');
        if ($password != $password_confirm) {
            $return = [
                'code' => 1,
                'msg' => '两次输入不一样，请重新输入'
            ]; 
        } else {
            $user_one = Manager::get($id);
            if ($user_one) {
                $user_one->password = md5($password);
                $result = $user_one->save();
                if ($result !== false) {
                    $return = [
                        'code' => 0,
                        'data' => ''
                    ];
                } else {
                    $return = [
                        'code' => 1,
                        'msg' => '操作失败，请重试'
                    ];
                }
            } else {
                $return = [
                    'code' => 1,
                    'msg' => '用户不存在'
                ];
            }
        }
        return json_encode($return);
    }

    public function addActivity(Request $req)
    {
        $activity = new Activity;
        $activity->data([
            'title' => $req->post('title'),
            'code' => uniqid(),
            'banner' => $req->post('banner'),
            'content_start' => $req->post('contentStart'),
            'content_end' => $req->post('contentEnd'),
            'content_share' => $req->post('contentShare'),
            'end_time' => $req->post('endTime')
        ]);
        $result = $activity->save();
        if ($result) {
            $return = [
                'code' => 0,
                'data' => $activity
            ];
        } else {
            $return = [
                'code' => 1,
                'msg' => '活动添加失败，请重试'
            ];
        }
        return json_encode($return);
    }

    public function deleteActivity(Request $req)
    {
        $id = $req->post('id');
        $result = Activity::destroy(1);
        if ($result) {
            $return = [
                'code' => 0,
                'data' => ''
            ];
        } else {
            $return = [
                'code' => 1,
                'msg' => '删除失败，请重试'
            ]; 
        }
        return json_encode($return);
    }

    public function updateActivity(Request $req)
    {
        $code = $req->post('code');
        $activity_one = Activity::get(['code' => $code]);
        if ($activity_one) {
            $activity_one->data([
                'title' => $req->post('title'),
                'banner' => $req->post('banner'),
                'content_start' => $req->post('contentStart'),
                'content_end' => $req->post('contentEnd'),
                'content_share' => $req->post('contentShare'),
                'end_time' => $req->post('endTime')
            ]);
            $result = $activity_one->save();
            if ($result !== false) {
                $return = [
                    'code' => 0,
                    'data' => ''
                ];
            } else {
                $return = [
                    'code' => 1,
                    'msg' => '更新失败，请重试'
                ];
            }
        } else {
            $return = [
                'code' => 1,
                'msg' => '数据不存在'
            ]; 
        }
        return json_encode($return);
    }

}