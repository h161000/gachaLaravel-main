<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Product_log;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\DeliveryProductResource;

use Str;
use Carbon\Carbon;
use Mail;


class DeliveryController extends Controller
{
    public function admin (Request $request) {
        $products = Product_log::select(
            'product_logs.id',
            'product_logs.name',
            'product_logs.point',
            'product_logs.image',
            'product_logs.updated_at',
            'product_logs.rare',
            'product_logs.user_id',
            'profiles.address',
        )->leftJoin('profiles', function($join) { $join->on('product_logs.user_id', '=', 'profiles.user_id'); })
        ->where('product_logs.status', 3)
        ->orderBy('product_logs.updated_at', 'ASC')->get();
        $products = DeliveryProductResource::collection($products);
        $hide_cat_bar = 1;
        return inertia('Admin/Delivery/Index', compact('hide_cat_bar', 'products')) ; 
    }

    public function getProductData(Request $request) {
        $id = $request->id;
        $product = Product_log::find($id);
        $res = ['status' =>1 ];
        if($product) {
            $user = User::find($product->user_id);
            $profile = Profile::where('user_id', $product->user_id)->first();
            $res['user'] = $user;
            $res['profile'] = $profile;
        } else {
            $res = ['status' =>0 ];
        }
        return $res;
    }

    public function deliver_post(Request $request) {
        $id = $request->id;
        $product = Product_log::find($id);
        $product->status = 4; // into delivered status
        $product->save();
        return redirect()->back()->with('message', '発送済みにしました！')->with('title', '発送')->with('message_id', Str::random(9))->with('type', 'dialog');
    }


    public function completed (Request $request) {
        $products = Product_log::select(
            'product_logs.id',
            'product_logs.name',
            'product_logs.point',
            'product_logs.image',
            'product_logs.updated_at',
            'product_logs.rare',
            'product_logs.user_id',
            'profiles.address',
        )->leftJoin('profiles', function($join) { $join->on('product_logs.user_id', '=', 'profiles.user_id'); })
        ->where('product_logs.status', 4)
        ->orderBy('product_logs.updated_at', 'ASC')->get();
        $products = DeliveryProductResource::collection($products);
        $hide_cat_bar = 1;
        
        return inertia('Admin/Delivery/Completed', compact('hide_cat_bar', 'products')) ; 
    }

    public function unDeliver_post(Request $request) {
        $id = $request->id;
        $product = Product_log::find($id);
        $product->status = 3;   // into waiting status
        $product->save();
        return redirect()->back()->with('message', '未発送にしました！')->with('title', '発送')->with('message_id', Str::random(9))->with('type', 'dialog');
    }


    public function csv_delivery(Request $request) {
        $hide_cat_bar = 1;
        return inertia('Admin/Delivery/CSV', compact('hide_cat_bar')) ; 
    }

    public function make_delivery_csv() {
        $products = Product_log::select(
            'product_logs.updated_at',
            'product_logs.user_id',
            'product_logs.name',
            'product_logs.rare',
            'product_logs.point',
            'product_logs.image',
            
            'profiles.first_name',
            'profiles.last_name',
            'profiles.first_name_gana',
            'profiles.last_name_gana',
            'profiles.postal_code',
            'profiles.prefecture',
            'profiles.address',
            'profiles.phone',
        )->leftJoin('profiles', function($join) { $join->on('product_logs.user_id', '=', 'profiles.user_id'); })
        ->where('product_logs.status', 3)
        ->orderBy('product_logs.updated_at', 'ASC')->get();

        $columnNames = [
            '更新日時',
            'ユーザーID',
            '商品名',
            'レアリティ',
            '商品画像URL',
            'PT',
            '名前',
            '名前(カナ)',
            '郵便番号',
            '住所',
            '電話番号',
        ];
        
        $outputs = '';
        foreach ($columnNames as $item) {
            $outputs .= $item . ',';
        }
        $outputs = rtrim($outputs, ',') . "\n";

        
        foreach ($products as $item) {
            $update_at = $item->updated_at->format('Y年n月j日 H:i');
            $arrInfo = [
                $update_at,
                $item->user_id,
                $item->name,
                $item->rare,
                'https://raffle-pro.com' . getProductImageUrl($item->image),
                $item->point,
                $item->first_name . ' '. $item->last_name,
                $item->first_name_gana . ' '. $item->last_name_gana,
                
                "〒" . $item->postal_code,
                $item->prefecture .' '.$item->address,
                " ". $item->phone,
            ];
            foreach ($arrInfo as $item) {
                $outputs .= $item . ',';
            }
            $outputs = rtrim($outputs, ',') . "\n";
        }
        $txt2 = pack('C*',0xEF,0xBB,0xBF). $outputs;
        $fileName = date('Y_m_d') .'_'. uniqid() . '.csv';

        if (!file_exists('delivery_csv')) {
            mkdir('delivery_csv', 0777, true);
        }

        $save_path = 'delivery_csv/' . $fileName;
        file_put_contents($save_path, $txt2);

        return $save_path;
    }

    public function csv_delivery_get() {
        
        $save_path = $this->make_delivery_csv();

        return response()->download($save_path);
    }

    public function csv_delivery_post(Request $request) {
        $rules = [
            'email' => 'required|email',
        ];
        $validatored = $request->validate($rules);

        $email = $validatored['email'];
        
        $save_path = $this->make_delivery_csv();

        $info = array(
            'name' => "Raffle-pro"
        );
        Mail::send('delivery_list', $info, function ($message) use ($save_path, $email)
        {
            $message->to($email)
                ->subject('発送依頼一覧');
            $message->attach(getcwd(). "/" . $save_path);
            $message->from(env('MAIL_FROM_ADDRESS'), 'Raffle-pro');
        });

        return redirect()->back()->with('message', '送信しました！')->with('title', '発送依頼一覧')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

}
