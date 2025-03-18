<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Str;

class SettingController extends Controller
{
    public function index() {
        $maintenance = getOption('maintenance');
        $testOrLive = getOption('testOrLive');
        $is3DSecure = getOption('is3DSecure');

        $hide_cat_bar = 1;
        return inertia('Admin/Settings/Index', compact('maintenance', 'hide_cat_bar', 'testOrLive', 'is3DSecure'));
    }

    public function maintenance_store(Request $request) {
        $maintenance = $request->maintenance;
        setOption('maintenance', $maintenance);
        return redirect()->back()->with('message', '保存しました。')->with('title', '設定')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function payment_store(Request $request) {
        $testOrLive = $request->testOrLive;
        setOption('testOrLive', $testOrLive);
        $is3DSecure = $request->is3DSecure;
        setOption('is3DSecure', $is3DSecure);
        return redirect()->back()->with('message', '保存しました。')->with('title', '設定')->with('message_id', Str::random(9))->with('type', 'dialog');
    }
}
