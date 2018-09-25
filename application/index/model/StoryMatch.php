<?php
namespace app\index\model;

use think\Model;

class StoryMatch extends Model
{
	public function otherStory() {
        return $this->belongsTo('Story', 'story_id');
    }
}