<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Gacha;
use App\Models\Product;
use App\Models\Gacha_product;

use App\Http\Resources\GachaListResource;
use App\Http\Resources\ProductListResource;
use Illuminate\Support\Facades\DB;
use Str;

class GachaController extends Controller
{
    public function index(Request $request) {
        $cat_id = getCategories()[0]->id;
        if ($request->cat_id) {
            $cat_id = $request->cat_id;
        }
        $GachaObj = Gacha::select('gachas.*', 'temp.ableCount')
            ->leftJoin(DB::raw('(SELECT gacha_id, SUM(count) as ableCount FROM gacha_products WHERE is_last = 0 GROUP BY gacha_id) temp'), function($join) {
                $join->on('gachas.id', 'temp.gacha_id', '=');
            })
            ->where('gachas.category_id', $cat_id);
        if (auth()->user()->getType()=="staff") {
            $GachaObj->where('gachas.status', 0);
        }
        $gachas = $GachaObj->orderBy('gachas.order_level', 'DESC')->orderBy('gachas.id', 'DESC')->get();
        
        $gachas = GachaListResource::collection($gachas);
        return inertia('Admin/Gacha/Index', compact('gachas'));
    }

    public function store(Request $request) {
        $validatored = $request->validate([
            'point' => 'required',
            'count_card' => 'required|numeric',
            'lost_product_type' => '',
            'thumbnail' => 'required|image|mimes:jpeg,jpg,png,gif,svg',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,svg',
            'category_id' => 'required',
        ]);

        $image = saveImage('images/gacha', $request->file('image'), false);
        $thumbnail = saveImage('images/gacha/thumbnail', $request->file('thumbnail'), false);
        $data = [
            'point' => $request->point,
            'count_card' => $request->count_card,
            'lost_product_type' => $request->lost_product_type,
            'thumbnail' => $thumbnail,
            'image' => $image,
            'category_id' => $request->category_id,
            'spin_limit' => $request->count_card,
        ];
        $gacha = Gacha::create($data);
        return redirect(combineRoute(route('admin.gacha.edit', $gacha->id), $request->category_id) ); 
    }

    public function create() {
        return inertia('Admin/Gacha/Create');
    }

    public function edit($id) {
        $gacha = Gacha::find($id);
        if (!$gacha) {
            return redirect()->route('admin.gacha');
        }
        if (auth()->user()->getType()=="staff" && $gacha->status!=0) {
            $text = "権限がありません！";
            $hide_cat_bar = 1;
            return inertia('NoProduct', compact('text', 'hide_cat_bar'));
        }

        $gacha->image = getGachaImageUrl($gacha->image);
        $gacha->thumbnail = getGachaThumbnailUrl($gacha->thumbnail);
        $product_last = $gacha->getProductLast();

        $products = $gacha->getProducts();
        return inertia('Admin/Gacha/Edit', compact('gacha', 'product_last', 'products'));
    }

    public function update(Request $request) {
        $rules = [
            'point' => 'required',
            'count_card' => 'required|numeric',
            'count' => 'required|numeric',
            'lost_product_type' => '',
            'spin_limit' => 'required|numeric|gt:0',
            'thumbnail' => 'required|image|mimes:jpeg,jpg,png,gif,svg',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,svg',
            'category_id' => 'required',
        ];
        
        if (!$request->thumbnail) {
            $rules['thumbnail'] = '';
        } 
        if (!$request->image) {
            $rules['image'] = ''; 
        }
        $validatored = $request->validate($rules);

        $data = [
            'point' => $request->point,
            'count_card' => $request->count_card,
            'count' => $request->count,
            'lost_product_type' => $request->lost_product_type,
            'category_id' => $request->category_id,
            'spin_limit' => $request->spin_limit,
            'buttons' => $request->buttons
        ];

        if ($request->start_time) 
            $data['start_time'] = date('Y-m-d H:i', strtotime($request->start_time));
        else $data['start_time'] = null;

        if ($request->end_time)
            $data['end_time'] = date('Y-m-d H:i', strtotime($request->end_time));
        else $data['end_time'] = null;

        if ($request->thumbnail) {
            $thumbnail = saveImage('images/gacha/thumbnail', $request->file('thumbnail'), false);
            $data['thumbnail'] = $thumbnail;
        }
        if ($request->image) {
            $image = saveImage('images/gacha', $request->file('image'), false);
            $data['image'] = $image;
        }
        $gacha = Gacha::find($request->id);

        if (auth()->user()->getType()=="staff" && $gacha->status!=0) {
            $text = "権限がありません！";
            $hide_cat_bar = 1;
            return inertia('NoProduct', compact('text', 'hide_cat_bar'));
        }


        $gacha->update($data);

        return redirect()->back()->with('message', '保存しました！')->with('title', 'ガチャ 編集')->with('message_id', Str::random(9))->with('type', 'dialog');;
    }

    public function sorting(Request $request) {
        $cat_id = getCategories()[0]->id;
        if ($request->cat_id) {
            $cat_id = $request->cat_id;
        }
        $GachaObj = Gacha::where('category_id', $cat_id);
        if (auth()->user()->getType()=="staff") {
            $GachaObj->where('status', 0);
        }
        $gachas = $GachaObj->orderBy('order_level', 'DESC')->orderBy('id', 'DESC')->get();

        $gachas = GachaListResource::collection($gachas);
        return inertia('Admin/Gacha/Sorting', compact('gachas'));
    }

    public function sorting_store(Request $request) {
        $data = $request->all();
        $order_level = 1;
        $data['gachas'] = array_reverse($data['gachas']);
        foreach($data['gachas'] as $key=>$item) {
            Gacha::where('id', $item['id'])->update([
                'order_level'=>$order_level
            ]);
            $order_level += 1;
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', 'ガチャ編集')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function product_destroy($id) {
        Gacha_product::where("id", $id)->delete();
        return redirect()->back()->with('message', '削除しました！')->with('title', '編集')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function product_create(Request $request) {
        $rules = [
            'gacha_id' => 'required|numeric', 
            'product_id' => 'required|numeric',
            'is_last' => 'required|numeric',
            'order' => 'required|numeric',
            'count' => 'required|numeric'
        ];
        
        $validatored = $request->validate($rules);
            
        $data = [
            'gacha_id' => $request->gacha_id,
            'product_id' => $request->product_id,
            'order' => $request->order,
            'is_last' => $request->is_last,
            'count' => $request->count
        ];
        
        $product = Product::find($request->product_id);
        if ($product == null) {
            return redirect()->back()->with('message', '入力したIDのカードが存在しません。')->with('message_id', Str::random(9))->with('type', 'dialog')->with('data', ['close' => false]);
        }
        $total = Gacha_product::where('product_id', $product->id)->sum('count');

        if ($total + $request->count > $product->count) {
            return redirect()->back()->with('message', 'カードの在庫が不足しています。<br>追加できるカード数は'.($product->count - $total).'枚です。')->with('message_id', Str::random(9))->with('type', 'dialog')->with('data', ['close' => false]);
        }

        if ($request->is_update==1) {
            $product = Gacha_product::where('id', $request->id);
            $result = $product->update($data);
            if (!$result) {
                return redirect()->back()->with('message', '失敗しました！')->with('title', 'ガチャ 編集')->with('message_id', Str::random(9))->with('type', 'dialog');
            }
        } else {
            Gacha_product::create($data);
        }
        return redirect()->back()->with('message', '保存しました！')->with('title', 'ガチャ 編集')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function to_public (Request $request) {
        $gacha_id = $request->gacha_id;
        if (!$gacha_id) {
            return redirect()->back();
        }

        $gacha = Gacha::find($gacha_id);
        if ( !$gacha ) {
            return redirect()->back();
        }
       

        $gacha->status = $request->to_status;
        $gacha->save();

        $string = "非公開にしました！";
        if ($request->to_status) {
            $string = "公開にしました！";
        }
        return redirect()->back()->with('message', $string)->with('title', 'ガチャ 編集')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function gacha_limit (Request $request) {
        $gacha_id = $request->gacha_id;
        if (!$gacha_id) {
            return redirect()->back();
        }

        $gacha = Gacha::find($gacha_id);
        if ( !$gacha ) {
            return redirect()->back();
        }

        $gacha->gacha_limit = $request->to_status;
        $gacha->save();

        $string = "1日1回制限設定をキャンセルしました。";
        if ($request->to_status) {
            $string = "1日1回制限設定を完了しました。";
        }
        return redirect()->back()->with('message', $string)->with('title', 'ガチャ 編集')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    public function destroy($id) {
        Gacha_product::where('gacha_id', $id)->delete();
        Gacha::where('id', $id)->delete();
        return redirect()->back()->with('message', '削除しました！')->with('title', 'ガチャ')->with('message_id', Str::random(9))->with('type', 'dialog');
    }

    
}
