<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\PointCreate;

use App\Http\Resources\PointList;

use App\Models\Category;
use App\Models\Point;
use App\Models\User;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Coupon;
use App\Models\Coupon_record;
use App\Models\CouponDiscountRate;
use App\Http\Controllers\PointHistoryController;

use ImageResize;
use Str;

use Illuminate\Support\Facades\Validator;
use DB;

class AdminController extends Controller
{
    public function index(Request $request) {
        // return inertia('Admin/Index');
        $cat_id = getCategories()[0]->id;
        if ($request->cat_id) {
            $cat_id = $request->cat_id;
        }
        return redirect(combineRoute(route('admin.point'), $cat_id));
    }

    public function point_create(Request $request) {
        $hide_cat_bar = 1;
        return inertia('Admin/PointCreate', compact('hide_cat_bar')); 
    }

    public function Point_list(Request $request) {
        // $points = Point::where('category_id', $cat_id)->get();
        $points = Point::orderBy('id', 'ASC')->get();
        $points = PointList::collection($points);
        $hide_cat_bar = 1;
        return inertia('Admin/PointList', compact('points', 'hide_cat_bar'));
    }

    public function point_store(PointCreate $request) {
        $request->validated();
        $name = saveImage('images/point', $request->file('image'), false);
        $data = array('title'=>$request->title, 'point'=>$request->point, 'amount'=>$request->amount, 'image'=>$name, 'category_id'=>$request->category_id);
        Point::create($data);
        return redirect(combineRoute(route('admin.point'), $request->category_id))->with('message', '追加しました！')->with('title', 'ポイント購入')->with('message_id', Str::random(9))->with('type', 'dialog');
        
    }

    public function point_edit(Request $request, $id) {

        $point = Point::find($id);
        $point->image = getPointImageUrl($point->image);
        $hide_cat_bar = 1;
        return inertia('Admin/PointEdit', compact('point', 'hide_cat_bar'));
    }

    public function point_update(Request $request) {
        if($request->image) {
             $validatored = $request->validate([
                'title' => 'required',
                'point' => 'required',
                'amount' => 'required|numeric|min:100',
                'image' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            ]);
            $name = saveImage('images/point', $request->file('image'), false);
            $data = array('title'=>$request->title, 'amount'=>$request->amount, 'point'=>$request->point, 'image'=>$name, 'category_id'=>$request->category_id);
            
        } else {
            $validatored = $request->validate([
                'title' => 'required',
                'point' => 'required',
                'amount' => 'required|numeric|min:100',
            ]);
            $data = array('title'=>$request->title, 'amount'=>$request->amount, 'point'=>$request->point, 'category_id'=>$request->category_id);
        }
        
        $point = Point::find($request->id);
        $point->update($data);
        return redirect(combineRoute(route('admin.point'), $request->category_id))->with('message', '編集しました！')->with('title', 'ポイント購入管理')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function point_destroy($id = null) {
        Point::where("id", $id)->delete();
        return redirect()->back()->with('message', '削除しました！')->with('title', 'ポイント購入管理')->with('message_id', Str::random(9))->with('type', 'dialog');
    }


    public function delivery_list() {
        return inertia('Admin/DeliveryList');
    }

    public function category() {
        return inertia('Admin/CategoryList');
    }

    public function banner() {
        $hide_cat_bar = 1;
        $banners = DB::table('banner')->orderBy('order')->get();
        return inertia('Admin/Banner', compact('hide_cat_bar', 'banners'));
    }

    public function banner_create(Request $request) {
        $file = $request->file('file');
        if ($file) $image = '/images/banner/'.saveImage('images/banner', $file, false);
        else $image = "";
        DB::table('banner')->insert([
            'link_url' => $request->link_url,
            'image' => $image
        ]);
        return redirect()->back()->with('message', '追加しました！')->with('title', 'バナー設定')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function banner_store(Request $request) {
        $files = $request->file('files');
        $order_level = 0;
        foreach($request->banners as $item) {
            $data = [
                'order'=>$order_level,
                'link_url'=>$item['link_url']
            ];
            if (isset($files[$order_level])) {
                $image = saveImage('images/banner', $files[$order_level], false);
                $data['image'] = $image;
            }
            DB::table('banner')->where('id', $item['id'])->update($data);
            $order_level += 1;
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', 'バナー編集')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function banner_destroy($id = null) {
        $banner = DB::table('banner')->find($id);
        if ($banner && $banner->image != '' && file_exists(public_path($banner->image))) unlink(public_path($banner->image));
        DB::table('banner')->where("id", $id)->delete();
        return redirect()->back()->with('message', '削除しました！')->with('title', 'バナー編集')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function users (Request $request) {
        $page_size = 30;
        $keyword = $request->keyword ? $request->keyword : "";
        $page = $request->page ? intval($request->page) : 1;
        $order_by = $request->order_by ? $request->order_by : "id";

        $search_cond = [
            'page' => $page,
            'keyword' => $keyword,
            'order_by' => $order_by,
        ];

        $direction = $order_by == 'id' ? 'asc' : 'desc';
        
        if ($order_by == 'amount') {
            $purchases = DB::table('payments')->leftJoin('points', 'payments.point_id', '=', 'points.id')
            ->select('payments.user_id', DB::raw('sum(points.amount) as amount'))
            ->where('payments.status', 1)
            ->groupBy('payments.user_id');
        }
        
        $users = DB::table('users')->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
        ->select('users.id', DB::raw('concat(profiles.first_name, " ", profiles.last_name) as name'), 'users.email', 'users.phone', 'profiles.address');

        if ($order_by == 'amount') {
            $users = $users->leftJoinSub($purchases, 'purchases', function($join) {
                $join->on('users.id', '=', 'purchases.user_id');
            });
        }
        
        $users = $users->where('users.email', 'like', '%'.$keyword.'%')
        ->orWhere('users.phone', 'like', '%'.$keyword.'%')
        ->orWhere(DB::raw('concat(profiles.first_name, " ", profiles.last_name)'), 'like', '%'.$keyword.'%')
        ->orWhere('profiles.address', 'like', '%'.$keyword.'%');

        $total = $users->count();
        $total = ceil($total / $page_size);
        if ($total == 0) $total = 1;
        if ($page > $total) $page = $total;

        $order_by = ($order_by == 'amount' ? 'purchases.' : 'users.').$order_by;

        $users = $users->orderBy($order_by, $direction)->offset(($page - 1) * $page_size)->limit($page_size)->get();

        $hide_cat_bar = 1;

        return inertia('Admin/Users/Index', compact('users', 'total', 'search_cond', 'hide_cat_bar'));
    }

    public function user_detail($id) {
        $user = User::find($id);
        $profile = Profile::where('user_id', $id)->first();
        $payments = DB::table('payments')->leftJoin('points', 'payments.point_id', '=', 'points.id')
        ->where('payments.user_id', $id)
        ->where('payments.status', 1)
        ->select('payments.order_id', 'points.point', 'points.amount', 'payments.updated_at')
        ->orderBy('payments.updated_at', 'desc')
        ->get();
        
        $coupons = DB::table('coupon_records')->leftJoin('coupons', 'coupon_records.coupon_id', '=', 'coupons.id')
        ->where('coupon_records.user_id', $id)
        ->select('coupons.title', 'coupons.code', 'coupons.point', 'coupon_records.updated_at')
        ->orderBy('coupon_records.updated_at', 'desc')
        ->get();
        $hide_cat_bar = 1;
        
        return inertia('Admin/Users/Detail', compact('user', 'profile', 'payments', 'coupons', 'hide_cat_bar'));
    }

    public function user_update(Request $request) {
        if (isset($request->id)) {
            
            $user = User::find($request->id);
            if ($user) {
                if (isset($request->point)) {
                    if ($user->point != $request->point) {
                        (new PointHistoryController)->create($user->id, $user->point, $request->point - $user->point, 'admin', 0);
                    }
                    $user->update(['point' => $request->point]);
                    return redirect()->back()->with('message', '保存しました！')->with('message_id', Str::random(9))->with('type', 'dialog');
                }
            }
        }
    }

    public function purchase_log($id, Request $request) {
        $page_size = 50;

        $payments = DB::table('payments')->leftJoin('points', 'payments.point_id', '=', 'points.id')
        ->where('payments.user_id', $id)
        ->where('payments.status', 1);
        
        $total_amount = $payments->sum('points.amount');
        $total_point = $payments->sum('points.point');
        $page = $request->page ? intval($request->page) : 1;
        $total = $payments->count();
        $total = ceil($total / $page_size);
        if ($total == 0) $total = 1;
        if ($page > $total) $page = $total;
        
        $search_cond = [
            'page' => $page
        ];
        
        $hide_cat_bar = 1;
        
        $payments = $payments
            ->select('payments.order_id', 'payments.access_id', 'points.point', 'points.amount', 'payments.updated_at')
            ->orderBy('payments.updated_at', 'desc')->offset(($page - 1) * $page_size)->limit($page_size)->get();

        return inertia('Admin/Users/PurchaseLog', compact('payments', 'total', 'search_cond', 'hide_cat_bar', 'total_amount', 'total_point', 'id'));
    }

    public function gacha_log($id, Request $request) {
        $page_size = 20;

        $gachas = DB::table('gacha_records')->leftjoin('gachas', 'gacha_records.gacha_id', '=', 'gachas.id')
        ->select(DB::raw('sum(gachas.point * gacha_records.type) as total_point'), DB::raw('sum(gacha_records.type) as total_gacha')) 
        ->where('gacha_records.user_id', $id)
        ->where('gacha_records.status', 1)->get();

        $total_point = $gachas[0]->total_point ?? 0;
        $total_gacha = $gachas[0]->total_gacha ?? 0;
        

        $total_exchanged =DB::table('products')->where('status', 2)->where('user_id', $id)->sum('point');

        $products = DB::table('products')->leftJoin('gacha_records', 'products.gacha_record_id', '=', 'gacha_records.id')
        ->where('products.user_id', $id);
        
        $hide_cat_bar = 1;
        
        $gachas = DB::table('gacha_records')->leftjoin('gachas', 'gacha_records.gacha_id', '=', 'gachas.id')
        ->where('gacha_records.user_id', $id)
        ->where('gacha_records.status', 1);
        
        $page = $request->page ? intval($request->page) : 1;
        $total = $gachas->count();
        $total = ceil($total / $page_size);
        if ($total == 0) $total = 1;
        if ($page > $total) $page = $total;
        
        $search_cond = [
            'page' => $page
        ];
        
        $gachas = $gachas
        ->leftJoin('categories', 'gachas.category_id', '=', 'categories.id')
        ->select('gacha_records.id', 'gacha_records.gacha_id', 'gacha_records.created_at', 'gacha_records.type', 'gachas.point', 'categories.title as category')
        ->orderBy('gacha_records.created_at')->offset(($page - 1) * $page_size)->limit($page_size)->get();
        
        $status = [
            '0' => '',
            '1' => '未選択',
            '2' => 'ptに変換',
            '3' => '発送待ち',
            '4' => '発送済み'
        ];

        foreach($gachas as $gacha) {
            $gacha->products = DB::table('products')->where('gacha_record_id', $gacha->id)->orderBy('is_last')->orderBy('is_lost_product', 'desc')->orderBy('status')->get()->toArray();
            foreach($gacha->products as $product) {
                $product->status = $status[$product->status];
                $product->type = $product->is_last > 0 ? '切り番' : ($product->is_lost_product ? 'ハズレ' : '当たり');
                $product->id = $product->is_last == 0 && $product->is_lost_product == 1 ? $product->marks : 0;
            }
        }
        
        return inertia('Admin/Users/GachaLog', compact('gachas', 'total', 'search_cond', 'hide_cat_bar', 'total_exchanged', 'total_point', 'total_gacha', 'id'));
    }
}
