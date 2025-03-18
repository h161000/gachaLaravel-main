<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Gacha extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'point',
        'count_card',
        'count',
        'lost_product_type',
        'thumbnail',
        'image',
        'category_id',
        'status',
        'spin_limit',
        'gacha_limit',
        'start_time',
        'end_time',
        'buttons',
    ];

    public function timeStatus() {
        $now = date('Y-m-d H:i:s');
        if ($this->start_time && $this->start_time > $now) return 0;
        if ($this->end_time && $this->end_time <= $now) return 2;
        return 1;
    }
    
    public function getProductLast(){
        $product = Gacha_product::leftJoin('products', function($join) { $join->on('gacha_products.product_id', '=', 'products.id'); })
            ->where('gacha_products.gacha_id', $this->id)
            ->where('gacha_products.is_last', 1)
            ->select('gacha_products.id', 'products.id as product_id', 'products.name', 'products.point', 'products.rare', 'gacha_products.count', 'gacha_products.order', 'products.image')
            ->first();
        
        if ($product) {
            $product->image = getProductImageUrl($product->image);
            return $product;
        }
        return [];
    }

    public function getProducts() {
        $products = Gacha_product::leftJoin('products', function($join) { $join->on('gacha_products.product_id', '=', 'products.id'); })
            ->where('gacha_products.gacha_id', $this->id)
            ->where('gacha_products.is_last', 0)
            ->select('gacha_products.id', 'products.id as product_id', 'products.name', 'products.point', 'products.rare', 'gacha_products.count', 'gacha_products.order', 'products.image')
            ->get();
        
        foreach($products as $product) {
            $product->image = getProductImageUrl($product->image);
        }
        return $products;
    }


    public function getAward($award_total, $rest_total, $current_order) {
        $award_products = []; $arr_select = [];
        $products = Gacha_product::where('gacha_id', $this->id)->where('is_last', 0)->where('count', '>', 0)->where('order', 0)->get();
        
        $to = -1;
        foreach($products as $item) {
            $from = $to + 1; $to = $from + $item->count - 1;
            $item = ['from'=>$from, 'to'=>$to, 'id'=>$item->product_id];
            array_push($arr_select, $item);
        }

        $order_products = Gacha_product::where('gacha_id', $this->id)->where('is_last', 0)->where('count', '>', 0)->where('order', '>', 0)->orderby('order')->get();
        $mid = $to;
        
        foreach ($order_products as $item) {
            $from = $to + 1; $to = $from + $item->count - 1;
            $item = ['from'=>$from, 'to'=>$to, 'id'=>$item->product_id];
            array_push($arr_select, $item);
        }
        if ($rest_total != ($to+1)) {
            return [];
        }

        if ($award_total > $rest_total) {
            return [];
        }

        $rand_ids = [];
        $index = 0;
        
        for($i=0; $i<$award_total; $i++) {
            $current_order ++;
            if ($index < count($order_products) && $order_products[$index]->order == $current_order) {
                array_push($rand_ids, $mid + 1 + $index);
                $index ++;
                continue;
            }
            for($j=0; $j<10000; $j++) {
                $rand_val = rand(0, $mid);
                if (!in_array($rand_val, $rand_ids)) {
                    array_push($rand_ids, $rand_val);
                    break;
                }
            }
        }

        if (count($rand_ids) != $award_total) {
            return [];
        }

        $res_product_ids = [];
        foreach($rand_ids as $rand_id) {
            foreach($arr_select as $item) {
                if($item['from']<=$rand_id && $rand_id<=$item['to']) {
                    array_push($res_product_ids, $item['id']);
                    break;
                }
            }
        }

        if ($award_total == $rest_total) {
            $products_last = Gacha_product::where('gacha_id', $this->id)->where('is_last', 1)->first();
            if ($products_last) {
                array_push($res_product_ids, $products_last->product_id);
            }
        }

        return $res_product_ids;
    }
}

