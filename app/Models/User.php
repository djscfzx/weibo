<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Auth;

class User extends Authenticatable
{
  use Notifiable;

  public function statuses() {
    return $this->hasMany(Status::class);
  }

  /*粉丝*/
  public function followers() {
    return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
  }

  /*关注*/
  public function followings() {
    return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
  }

  protected $fillable = [
    'name', 'email', 'password',
  ];

  protected $hidden = [
    'password', 'remember_token',
  ];

  public static function boot() {
    parent::boot();

    static::creating(function ($user) {
      $user->activation_token = str_random(30);
    });
  }

  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  /**
   * 『 生成头像 』
   * 为 gravatar 方法传递的参数 size 指定了默认值 100；
   * 通过 $this->attributes['email'] 获取到用户的邮箱；
   * 使用 trim 方法剔除邮箱的前后空白内容；
   * 用 strtolower 方法将邮箱转换为小写；
   * 将小写的邮箱使用 md5 方法进行转码；
   * 将转码后的邮箱与链接、尺寸拼接成完整的 URL 并返回；
   */
  public function gravatar($size = '100') {
    $hash = md5(strtolower(trim($this->attributes['email'])));
    return "http://www.gravatar.com/avatar/$hash?s=$size";
  }

  public function sendPasswordResetNotification($token) {
    $this->notify(new ResetPassword($token));
  }

  public function follow($user_ids) {
    if ( ! is_array($user_ids)) {
        $user_ids = compact('user_ids');
    }
    $this->followings()->sync($user_ids, false);
  }

  public function unfollow($user_ids) {
    if ( ! is_array($user_ids)) {
        $user_ids = compact('user_ids');
    }
    $this->followings()->detach($user_ids);
  }

  public function isFollowing($user_id) {
    return $this->followings->contains($user_id);
  }

  public function feed() {
    $user_ids = $this->followings->pluck('id')->toArray();
    array_push($user_ids, $this->id);
    return Status::whereIn('user_id', $user_ids)->with('user')->orderBy('created_at', 'desc');
  }
}
