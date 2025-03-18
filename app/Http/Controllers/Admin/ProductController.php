<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\Models\Product;
use App\Models\Gacha_product;
use App\Http\Resources\ProductListResource;
use Str;

class ProductController extends Controller
{
    public function index(Request $request) {
        $page_size = 50;
        $id = $request->id ? $request->id : "";
        $rare = $request->rare ? $request->rare : "";
        $name = $request->name ? $request->name : "";
        $min = $request->min ? intval($request->min) : "";
        $max = $request->max ? intval($request->max) : "";
        $page = $request->page ? intval($request->page) : 1;
        
        $products = Product::query();
        if ($id != "") $products = $products->where('id', $id);
        if ($rare != "") $products = $products->where('rare', $rare);
        if ($min != "") $products = $products->where('point', '>=', $min);
        if ($max != "") $products = $products->where('point', '<=', $max);
        if ($name != "") $products = $products->where('name', 'like', '%'.$name.'%');
        $total = $products->count();
        $total = ceil($total / $page_size);
        if ($total == 0) $total = 1;
        if ($page > $total) $page = $total;

        $products = $products->orderBy('point', 'DESC')->orderBy('id', 'ASC')
        ->offset(($page - 1) * $page_size)->limit($page_size)->get();

        $products = ProductListResource::collection($products);
        // return $products_status;
        $search_cond = [
            "id" => $id,
            "rare" => $rare,
            "name" => $name,
            "min" => $min,
            "max" => $max,
            "page" => $page,
        ];

        $products_status = Product::select('products.id', 'products.count', 'temp.sum')
            ->leftJoin(DB::raw('(SELECT product_id, SUM(count) as sum FROM gacha_products GROUP BY product_id) temp'), function($join) {
                $join->on('products.id', 'temp.product_id', '=');
            })
            ->get();

        $hide_cat_bar = 1;
        return inertia('Admin/Product/Index', compact('hide_cat_bar', 'products', 'products_status', 'search_cond', 'total'));
    }
    
    public function create(Request $request) {
        $rules = [
            'name' => 'required',
            'point' => 'required|numeric',
            'rare' => 'required',
            'count' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
        ];
        if ($request->is_update==1) {
            if(!$request->image){
                $rules['image'] = '';
            }
        }
        $validatored = $request->validate($rules);
            
        $data = [
            'name' => $request->name,
            'point' => $request->point,
            'rare' => $request->rare,
            'count' => $request->count,
        ];
        
        if($request->image){
            $image = saveImage('images/products', $request->file('image'), false);
            $data['image'] = $image;
        }
        
        if ($request->is_update==1) {
            $product = Product::find($request->id);
            $product->update($data);
        } else {
            Product::create($data);
        }

        return redirect()->back()->with('message', '保存しました！')->with('title', 'カード 編集')->with('message_id', Str::random(9))->with('type', 'dialog');;
    }

    public function product_destroy($id) {
        Product::where("id", $id)->delete();
        Gacha_product::where("product_id", $id)->delete();
        return redirect()->back()->with('message', '削除しました！')->with('title', '編集')->with('message_id', Str::random(9))->with('type', 'dialog');
    }
}
