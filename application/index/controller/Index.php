<?php
namespace app\index\controller;
use app\index\model\User;
use app\index\model\Activity;
use app\index\model\Story;
use app\index\model\StoryMatch;
use think\Request;

class Index
{

    public function addUser(Request $req)
    {
        $user_id = $req->post('userId');
        if ($user_id) {
            $user = User::get($user_id);
            if ($user) {
                $return = [
                    'code' => 0,
                    'data' => $user
                ];
            } else {
                $return = [
                    'code' => 1,
                    'msg' => '用户不存在'
                ];
            }
        } else {
            $user = new User;
            $user->name = '';
            $result = $user->save();
            if ($result) {
                $return = [
                    'code' => 0,
                    'data' => $user
                ];
            } else {
                $return = [
                    'code' => 1,
                    'msg' => '用户初始化失败'
                ];
            }
        }
        return json_encode($return);
    }

    public function getActivity(Request $req)
    {
        $code = $req->post('code');
        $activity = Activity::get(['code' => $code]);
        if ($activity) {
            $end_time = $activity->end_time;
            $finish = strtotime(date('Y-m-d H:i:s')) > strtotime($end_time) ? true : false;
            $activity->finish = $finish;
            $activity->end_date_text = date('m月d日', strtotime($end_time));
            $return = [
                'code' => 0,
                'data' => $activity
            ];
        } else {
            $return = [
                'code' => 1,
                'msg' => '活动不存在'
            ];
        }
        return json_encode($return);
    }

    public function addStory(Request $req)
    {
        $user_id = $req->post('userId');
        $activity_id = $req->post('activityId');
        $content = $req->post('content');
        $nick_name = $req->post('nickName');
        $story_one = Story::get(['user_id' => $user_id, 'activity_id' => $activity_id]);
        if ($story_one) {
            $return = [
                'code' => 1,
                'msg' => '您已经提交过了，请不要重复提交'
            ];
        } else {
           $story = new Story;
           $story -> data([
               'user_id' => $user_id,
               'activity_id' => $activity_id,
               'content' => $content,
               'nick_name' => $nick_name
           ]);
           $result = $story->save();
            if ($result) {
                $return = [
                    'code' => 0,
                    'data' => ''
                ];
            } else {
                $return = [
                    'code' => 1,
                    'msg' => '提交失败，请重试'
                ];
            }
        }
        return json_encode($return);
    }

    public function getStoryByUserIdAndActivityId(Request $req) {
        $user_id = $req->post('userId');
        $code = $req->post('code');
        $story_one = Story::where(['user_id' => $user_id, 'activity.code' => $code])
                        ->join('activity', 'activity.id = story.activity_id')
                        ->find();
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

    public function matchStory(Request $req) {
        $user_id = $req->post('userId');
        $activity_id = $req->post('activityId');
        if (!$user_id || !$activity_id) {
            $return = [
                'code' => 1,
                'msg' => '参数不完整'
            ]; 
        } else {
            $story_match_one = StoryMatch::get(['user_id' => $user_id, 'activity_id' => $activity_id]);
            if (!$story_match_one) {
                $story_one = Story::where([['user_id','<>', $user_id], ['activity_id', '=', $activity_id]])
                                ->order('match_count', 'asc')
                                ->order('rand()') 
                                ->find();
                if ($story_one) {
                    $story_match_one = new StoryMatch;
                    $story_match_one -> data([
                        'user_id' => $user_id,
                        'activity_id' => $activity_id,
                        'story_id' => $story_one->id
                    ]);
                    $story_match_one -> save();
                    $story_one->match_count = $story_one->match_count + 1;
                    $story_one->save();
                    $story_match_one->otherStory;
                    $return = [
                        'code' => 0,
                        'data' => $story_match_one
                    ];
                } else {
                    $return = [
                        'code' => 1,
                        'msg' => '没有故事可匹配'
                    ];
                }
            } else {
                $story_match_one->otherStory;
                $return = [
                    'code' => 0,
                    'data' => $story_match_one
                ];
            }
        }
        // var_dump($story_match_one->story);
        // var_dump($story_match_one->getLastSql());
        // exit;
        return json_encode($return);
    }
}