<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Gacha;
use App\Models\Point;
use App\Models\Favorite;

use App\Models\Product;
use App\Models\Profile;
use App\Models\Gacha_product;
use App\Models\Product_log;

use App\Models\Gacha_record;
use App\Models\Payment;
use App\Models\User;

use App\Http\Resources\ProductListResource;
use App\Http\Resources\FavoriteListResource;
use App\Http\Resources\PointList;
use App\Http\Resources\GachaListResource;
use Str;

use \Exception;

use File;
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use DB;
use App\Models\Coupon;
use App\Models\Coupon_record;
use App\Http\Controllers\PointHistoryController;

class UserController extends Controller
{
    public function index() {
        return inertia('User/Index');
    }

    public function dpexchange() {
        return inertia('User/Dpexchange');
    }

    // Gacha code

    public function gacha($id) {
        $gachas = Gacha::where('id', $id)->where('status', 1)->get();
        if (!count($gachas)) {
            $text = "ガチャカードは存在しません！";
            $hide_cat_bar = 1;
            return inertia('NoProduct', compact('text', 'hide_cat_bar'));
        }
        $gachas = GachaListResource::collection($gachas);
        $hide_cat_bar = 1;
        return inertia('User/Gacha', compact('hide_cat_bar', 'gachas'));
    }

    public function reward($user, $gacha, $number, $token) {
        $user = User::find($user->id);
        $gacha = Gacha::find($gacha->id);

        $point = $user->point - $gacha->point * $number;  // Check User Point   #4
        if ($point<0) return 4;

        $count_rest = $gacha->count_card - $gacha->count;
        $award_products = $gacha->getAward($number, $count_rest, $gacha->count);
        if ($award_products) {} else {
            return 1;  // Check Gacha Product   #3
        }

        $max_point = 0;

        foreach($award_products as $key) {
            $product_item = Product::find($key);
            if ($max_point<$product_item->point) {
                $max_point = $product_item->point;
            }
            if ($product_item->count > 0) {
                $product_item->decrement('count');
                $data = [
                    'product_id' => $product_item->id,
                    'point' => $product_item->point,
                    'rare' => $product_item->rare,
                    'image' => $product_item->image,
                    'name' => $product_item->name,
                    'gacha_record_id' => $token,
                    'user_id' => $user->id,
                    'status' => 1,
                ];
                Product_log::create($data);
                Gacha_product::where('gacha_id', $gacha->id)->where('product_id', $product_item->id)->decrement('count');
            }
        }
        $gacha->increment('count', $number);

        return (object)[
            'result' => 0,
            'max_point' => $max_point
        ];
    }

    public function start(Request $request) {
        $id = $request->id;
        $number = $request->number;
        $gacha = Gacha::find($id);
        $user = auth()->user();
        $userLock = Cache::lock('startGacha'.$user->id, 60);
        if (!$userLock->get()) {
            return redirect()->route('main');
        }
        try {
            if (!$gacha || $gacha->count_card == $gacha->count || $gacha->status == 0) {
                $userLock->release();
                return redirect()->route('main');
            }
            $current = date('Y-m-d H:i:s');
            if ($gacha->start_time && $gacha->start_time > $current) {
                $message = '開始時刻までお待ちください。';
                return redirect()->back()->with('message', $message)->with('title', 'ガチャの制限時間')->with('message_id', Str::random(9))->with('type', 'dialog');
            }
            if ($gacha->end_time && $gacha->end_time != '0000-00-00 00:00:00' && $gacha->end_time <= $current) {
                $message = '完了した。';
                return redirect()->back()->with('message', $message)->with('title', 'ガチャの制限時間')->with('message_id', Str::random(9))->with('type', 'dialog');
            }

            $totalSpin = Gacha_record::where('user_id', $user->id)->where('gacha_id', $id)->where('status', 1)->sum('type');
            $remainingSpin = $gacha->spin_limit - $totalSpin;
            if ($remainingSpin < 0) $remainingSpin = 0;

            $count_rest = $gacha->count_card - $gacha->count;
            if ($number > $count_rest) $number = $count_rest;
            if ($number > $remainingSpin) {
                return redirect()->back()->with('message', 'このガチャは'.$gacha->spin_limit.'回までガチャできます。<br/>すでに回したガチャ数は'.$totalSpin.'回です。')->with('title', 'ガチャ回数超過!')->with('message_id', Str::random(9))->with('type', 'dialog');
            }

            $status = $gacha->gacha_limit;

            if ($status == 1) {
                // if ($number > 1) {
                //     $message = '1日1回以上ガチャできません。';
                //     return redirect()->back()->with('message', $message)->with('title', '1日1回ガチャ制限')->with('message_id', Str::random(9))->with('type', 'dialog');
                // }
                $last = Gacha_record::where('user_id', $user->id)->where('gacha_id', $id)->where('status', 1)->whereDay('created_at', now()->day)->first();
                if ($last) {
                    $message = '1日1回以上ガチャできません。';
                    return redirect()->back()->with('message', $message)->with('title', '1日1回ガチャ制限')->with('message_id', Str::random(9))->with('type', 'dialog');
                }
            }

            $gacha_point = $gacha->point * $number;
            $user_point = $user->point;
            if ($user_point< $gacha_point) {
                $userLock->release();
                return redirect()->route('user.point');
            }

            $lock = Cache::lock('startGacha', 60);
            try {
                $lock->block(10);

                $data = [
                    'user_id' => $user->id,
                    'gacha_id' => $gacha->id,
                    'type' => $number,
                ];
                $gacha_record = Gacha_record::create($data);

                $token = $gacha_record->id;
                $result = $this->reward($user, $gacha, $number, $token);

                $lock?->release();
                if (isset($result->max_point)) {
                    $gacha_record->update(['status'=>1]);

                    (new PointHistoryController)->create($user->id, $user->point, -$gacha->point * $number, 'gacha', $token);

                    $dp = $number + $user->dp;
                    $point = $user->point;
                    $point = $point - $gacha->point * $number;
                    $user->update(['dp'=>$dp, 'point'=>$point]);
                    $hide_cat_bar = 1;
                    $video = getVideo($gacha->point * $number, $result->max_point);
                    if (!File::exists('videos/'.$video)) {
                        $video = "default.mp4";
                    }
                    return inertia('User/Video', compact('hide_cat_bar', 'video', 'token'));
                }
                if ($result==1) {
                    $text = "サーバーが混み合っております。少し時間をおいて再度お試しください。";
                    $hide_cat_bar = 1;
                    return inertia('NoProduct', compact('text', 'hide_cat_bar'));
                }

                if ($result==2) {
                    $text = "ガチャ回数を超えました！";
                    $hide_cat_bar = 1;
                    return inertia('NoProduct', compact('text', 'hide_cat_bar'));
                }

                if ($result==3) {
                    $text = "ガチャ時間を超えました！";
                    $hide_cat_bar = 1;
                    return inertia('NoProduct', compact('text', 'hide_cat_bar'));
                }

                if ($result==4) {
                    $text = "ユーザーポイントが足りません。";
                    $hide_cat_bar = 1;
                    return inertia('NoProduct', compact('text', 'hide_cat_bar'));
                }
            } catch (LockTimeoutException $e) {
                $text = "ガチャ時間を超えました！";
                $hide_cat_bar = 1;
                return inertia('NoProduct', compact('text', 'hide_cat_bar'));
            }
        }
        finally {
            $userLock?->release();
        }
    }

    public function result($token) {
        $user = auth()->user();
        $products = Product_log::where('gacha_record_id', $token)
            ->where('status', 1)
            ->get();
        $products = ProductListResource::collection($products);
        $hide_cat_bar = 1;
        $hide_back_btn = 1;
        $show_result_bg = 1;

        $user = auth()->user();
        $profiles = Profile::where('user_id', $user->id)->get();
        $profile = (object)[];
        if (count($profiles)) {
            $profile = $profiles[0];
        }
        $hide_cat_bar = 1;
        return inertia('User/Result', compact('products', 'hide_cat_bar', 'hide_back_btn', 'show_result_bg', 'token', 'profile'));
    }

    public function result_exchange(Request $request) {
        $token = $request->token;
        $checks = $request->checks;
        $user = auth()->user();
        $logs = Product_log::where('gacha_record_id', $token)->where('status', 1)->get();

        $point = $user->point;
        foreach($logs as $log) {
            $key = "id" . $log->id;
            if (isset($checks[$key]) && $checks[$key]) {
                $log->status = 2;
                $log->save();
                if ($product = Product::find($log->product_id)) {
                    $product->increment('count');
                }
                (new PointHistoryController)->create($user->id, $point, $log->point, 'exchange', $log->id);
                $point = $point + $log->point;
            }
        }
        $user->update(['point'=>$point]);

        return redirect()->route('user.gacha.end', ['token'=>$token]);
    }

    public function gacha_end(Request $request) {
        $token = $request->token;
        $point = 0; $number_products = 0;
        if ($token) {
            $user = auth()->user();
            $products = Product_log::where('gacha_record_id', $token)->where('status', 2)
                ->select('point')->get();

            foreach($products as $product) {
                $point = $point + $product->point;
                $number_products = $number_products + 1;
            }
            $gacha_record = Gacha_record::find($token);
            if ($gacha_record) {
                $gachas = Gacha::where('id', $gacha_record->gacha_id)->get();
                if (!count($gachas)) {
                    return redirect()->route('main');
                }
                $gachas = GachaListResource::collection($gachas);
                $hide_cat_bar = 1;
                $hide_back_btn = 1;
                return inertia('User/GachaEnd', compact('point', 'number_products', 'gachas', 'hide_cat_bar', 'hide_back_btn'));
            } else {
                return redirect()->route('main');
            }
        }
        return redirect()->route('main');
    }

    // Gacha Code End

    public function point(Request $request) {
        $points = Point::orderBy('id','ASC')->get();
        $points = PointList::collection($points);
        $hide_cat_bar = 1;
        return inertia('User/Point/Index', compact('points', 'hide_cat_bar'));
    }

    public function purchase_success() {
        $hide_cat_bar = 1;
        $hide_back_btn = 1;
        return inertia('User/Point/Success', compact('hide_cat_bar', 'hide_back_btn'));
    }

    public function favorite() {
        $user = auth()->user();
        $products = Favorite::where('user_id', $user->id)->orderBy('id', 'ASC')->get();
        $products = FavoriteListResource::collection($products);
        $hide_cat_bar = 1;
        // return $products;
        $hide_back_btn = 1;
        return inertia('User/Favorite', compact('products', 'hide_cat_bar', 'hide_back_btn'));
    }

    public function favorite_add(Request $request) {
        $res = ['status'=>0];
        $id = $request->id;
        $value = $request->value;
        if ($id) {
            $user = auth()->user();
            if ($value) {
                $products = Favorite::where('user_id', $user->id)->where('product_id', $id)->get();
                if (!count($products)) {
                    Favorite::create(['user_id'=>$user->id, 'product_id'=>$id]);
                }
            } else {
                Favorite::where('user_id', $user->id)->where('product_id', $id)->delete();
            }
            $res['status'] = 1;
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', 'お気に入り')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function address() {
        $hide_cat_bar = 1;
        $user = auth()->user();
        $profiles = Profile::where('user_id', $user->id)->get();
        return inertia('User/Address', compact('hide_cat_bar', 'profiles'));
    }

    public function address_post(Request $request) {
        $validated = $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'first_name_gana'=>'required',
            'last_name_gana'=>'required',
            'postal_code'=>'required',
            'prefecture'=>'required',
            'address'=>'required',
            'phone' => 'required|numeric|digits:11',
        ]);

        $user = auth()->user();

        $profiles = Profile::where('user_id', $user->id)->get();
        if (count($profiles)>0) {
            $profiles[0]->update($validated);
        } else {
            $validated['user_id'] = $user->id;
            Profile::create($validated);
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', '個人情報登録')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function products() {
        $user = auth()->user();
        $this->auto_product_point_exchange($user);
        $products = Product_log::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->get();
        $products = ProductListResource::collection($products);

        $user = auth()->user();
        $profiles = Profile::where('user_id', $user->id)->get();
        $profile = [];
        if (count($profiles)) {
            $profile = $profiles[0];
        }

        $hide_cat_bar = 1;

        return inertia('User/Product/Index', compact('products', 'hide_cat_bar', 'profile'));
    }

    public function product_point_exchange(Request $request) {
        $checks = $request->checks;
        $user = auth()->user();
        $logs = Product_log::where('user_id', $user->id)->where('status', 1)->get();

        $point = $user->point;
        foreach($logs as $log) {
            $key = "id" . $log->id;
            if (isset($checks[$key]) && $checks[$key]) {
                $log->status = 2;
                $log->save();
                if ($product = Product::find($log->product_id)) {
                    $product->increment('count');
                }
                (new PointHistoryController)->create($user->id, $point, $log->point, 'exchange', $log->id);
                $point = $point + $log->point;
            }
        }
        $user->update(['point'=>$point]);

        return redirect()->back()->with('message', '変換しました！')->with('title', 'ポイント変換')->with('message_id', Str::random(9))->with('type', 'dialog')->with('data', ['user' => $user]);
    }

    public function auto_product_point_exchange($user) {
        $logs = Product_log::where('user_id', $user->id)->where('status', 1)->whereRaw('updated_at < NOW() - INTERVAL 7 DAY')->get();

        $point = $user->point;
        foreach($logs as $log) {
            $key = "id" . $log->id;
            if (isset($checks[$key]) && $checks[$key]) {
                $log->status = 2;
                $log->save();
                if ($product = Product::find($log->product_id)) {
                    $product->increment('count');
                }
                (new PointHistoryController)->create($user->id, $point, $log->point, 'exchange', $log->id);
                $point = $point + $log->point;
            }
        }
        $user->update(['point'=>$point]);
    }

    public function product_delivery_post(Request $request) {
        $user = auth()->user();
        $checks = $request->checks;
        $products = Product_log::where('user_id', $user->id)->where('status', 1)->get();
        foreach($products as $product) {
            $key = "id" . $product->id;
            if (isset($checks[$key]) && $checks[$key]) {
                $product->status = 3;
                $product->save();
            }
        }
        return redirect()->back();
    }

    public function delivery_wait(Request $request) {
        $user = auth()->user();
        $this->auto_product_point_exchange($user);
        $products = Product_log::where('user_id', $user->id)
            ->where('status', 3)
            ->orderBy('id', 'ASC')
            ->get();
        $products = ProductListResource::collection($products);
        $hide_cat_bar = 1;
        return inertia('User/Product/Wait', compact('products', 'hide_cat_bar'));
    }


    public function delivered(Request $request) {
        $user = auth()->user();
        $this->auto_product_point_exchange($user);
        $products = Product_log::where('user_id', $user->id)
            ->where('status', 4)
            ->orderBy('id', 'ASC')
            ->get();
        $products = ProductListResource::collection($products);
        $hide_cat_bar = 1;
        return inertia('User/Product/Delivered', compact('products', 'hide_cat_bar'));
    }


    public function dp_detail($id) {
        $user = auth()->user();
        $products = Product::where('id', $id)->where('status', 0)->where('is_lost_product', 2)->get();
        if (!count($products)) {
            return redirect()->route('main.dp');
        }
        $product = $products[0];
        $favorite = Favorite::where('user_id', $user->id)->where('product_id', $product->id)->count();

        $products = ProductListResource::collection($products);
        $productStatusTxt = getProductStatusTxt();

        $profiles = Profile::where('user_id', $user->id)->get();
        $profile = [];
        if (count($profiles)) {
            $profile = $profiles[0];
        }

        $hide_cat_bar = 1;
        return inertia('User/Dp/Detail', compact('products', 'favorite', 'hide_cat_bar', 'productStatusTxt', 'profile'));
    }

    public function dp_detail_post(Request $request) {
        $id = $request->id;
        if (!$id) {
            return redirect()->route('main.dp');
        }
        $products = Product::where('id', $id)->where('status', 0)->where('is_lost_product', 2)->get();
        if (!count($products)) {
            return redirect()->route('main.dp');
        }

        $user = auth()->user();
        if ($user->dp<$products[0]->dp) {
            return redirect()->back()->with('message', 'DPが足りてないです！')->with('title', 'TP交換所 – 詳細')->with('message_id', Str::random(9))->with('type', 'dialog');
        }

        // $product = Product::where('id', $id)->where('status', 0)->where('is_lost_product', 2)->update(['status'=>1, 'user_id'=>$user->id]);
        $product = Product::find($id);
        $data = [
            'name' => $product->name,
            'point' => $product->point,
            'dp' => $product->dp,
            'image' => $product->image,
            'category_id' => $product->category_id,
            'rare' => $product->rare,
            'product_type' => $product->product_type,
            'status_product' => $product->status_product,
            'is_lost_product' => 2,
            'status' => 3,
            'user_id'=>$user->id
        ];
        Product::create($data);

        $dp = $user->dp - $product->dp;
        $user->update(['dp'=>$dp]);
        return redirect()->route('user.dp.detail.success');
    }

    public function dp_detail_success(Request $request) {
        $hide_cat_bar = 1;$hide_back_btn = 1;
        return inertia('User/Dp/Success', compact('hide_cat_bar', 'hide_back_btn'));
    }

    public function coupon() {
        $user = auth()->user();
        $hide_cat_bar = 1;
        $coupons = DB::table('coupon_records')->leftJoin('coupons', 'coupons.id', '=', 'coupon_records.coupon_id')
            ->select('coupons.title', 'coupons.point', 'coupon_records.updated_at')
            ->where('coupon_records.user_id', $user->id)
            ->orderBy('coupon_records.updated_at', 'desc')->get();
        foreach($coupons as $coupon) {
            $coupon->acquired_time = date('Y年n月j日 H:i', strtotime($coupon->updated_at));
        }
        return inertia('User/Coupon', compact('hide_cat_bar', 'coupons'));
    }

    public function coupon_post(Request $request) {
        $user = auth()->user();
        $request->validate([
            'code' => 'required'
        ]);
        $coupon = Coupon::where('code', $request->code)->first();
        if ($coupon) {
            if ($coupon->expiration <= date('Y-m-d H:i:s')) {
                return redirect()->back()->with('message', '有効期間を超えました。')->with('title', '取得エラー')->with('message_id', Str::random(9))->with('type', 'dialog');
            }
            $record = Coupon_record::where(['coupon_id' => $coupon->id, 'user_id' => $user->id])->first();
            if ($record) {
                return redirect()->back()->with('message', 'すでにこのコードを利用しました。')->with('title', '取得エラー')->with('message_id', Str::random(9))->with('type', 'dialog');
            }
            $records = Coupon_record::where(['coupon_id' => $coupon->id])->count();
            if ($records == $coupon->user_limit) {
                return redirect()->back()->with('message', '利用可能な人数を超えました。')->with('title', '取得エラー')->with('message_id', Str::random(9))->with('type', 'dialog');
            }
            $coupon_record = Coupon_record::create([
                'coupon_id' => $coupon->id,
                'user_id' => $user->id
            ]);
            (new PointHistoryController)->create($user->id, $user->point, $coupon->point, 'coupon', $coupon_record->id);

            $user->update(['point' => $user->point + $coupon->point]);
            $coupon->acquired_time = date('Y年m月d日 H時i分', strtotime($coupon->updated_at));
            return redirect()->back()->with('message', '取得に成功しました。')->with('title', '取得成功')->with('message_id', Str::random(9))->with('type', 'dialog')->with('data', ['coupon' => $coupon, 'user' => $user]);
        }
        else {
            return redirect()->back()->with('message', '有効なコードを入力してください。')->with('title', '取得エラー')->with('message_id', Str::random(9))->with('type', 'dialog');
        }
    }
}
