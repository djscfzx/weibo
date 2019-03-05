<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller {
  public function create() {
    return view('sessions.create');
  }

  public function store(Request $request) {
    $credentials = $this->validate($request, [
      'email' => 'required|email|max:255',
      'password' => 'required'
    ]);

    if(Auth::attempt($credentials)) {
      //登录成功后操作
      session()->flash('success', '欢迎回来━(*｀∀´*)ノ亻!');
      return redirect()->route('users.show', [Auth::user()]);
    } else {
      //登录失败后操作
      session()->flash('danger', '邮箱和密码不匹配哟(＾Ｕ＾)ノ~ＹＯ');
      return redirect()->back()->withInput();
    }
  }
}
