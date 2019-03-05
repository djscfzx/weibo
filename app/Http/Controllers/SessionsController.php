<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller {
  public function __construct() {
    $this->middleware('guest', [
      'only' => ['create']
    ]);
  }

  public function create() {
    return view('sessions.create');
  }

  public function store(Request $request) {
    $credentials = $this->validate($request, [
      'email' => 'required|email|max:255',
      'password' => 'required'
    ]);

    if(Auth::attempt($credentials, $request->has('remember'))) {
      //登录成功后操作
      session()->flash('success', '欢迎回来━(*｀∀´*)ノ亻!');
      $fallback = route('users.show', Auth::user());
      return redirect()->intended($fallback);
    } else {
      //登录失败后操作
      session()->flash('danger', '邮箱和密码不匹配哟(＾Ｕ＾)ノ~ＹＯ');
      return redirect()->back()->withInput();
    }
  }

  public function destroy() {
    Auth::logout();
    session()->flash('success', '退出成功，我会想你的(ノへ￣、)');
    return redirect('login');
  }
}
