<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Item;

use JD\Cloudder\Facades\Cloudder;

class ItemsController extends Controller
{
    public function index()
    {
        // 未ログインの場合はwelcomeページを表示
        if (!\Auth::check()){
            return view('welcome');
        }

        // 認証済みユーザを取得
        $user = \Auth::user();
        // カテゴリー一覧を取得
        $categories = $user->categories()->orderBy('number', 'asc')->get();
        // アイテム一覧を取得
        $items = $user->items()->orderBy('created_at', 'asc')->get();

        // アイテム一覧ビューでそれを表示
        return view('items.index', [
            'items' => $items,
            'categories' => $categories,
        ]);

    }

    public function create()
    {
        $item = new Item;

        // 認証済みユーザを取得
        $user = \Auth::user();
        // カテゴリー一覧を取得
        $categories = $user->categories()->orderBy('number', 'asc')->get();
        // 買い出し先一覧を取得
        $shops = $user->shops()->orderBy('number', 'asc')->get();

        // アイテム作成ビューを表示
        return view('items.create', [
            'item' => $item,
            'categories' => $categories,
            'shops' => $shops,
        ]);
    }

    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|max:10',
            //写真の登録は任意
            'image_url' => 'nullable',
            'image_id' => 'nullable',
            'category_id' =>'required',
            'shop_id' =>'required',
            'status' => 'required',
        ]);

        if ($image = $request->file('image_url')) {
            $image_path = $image->getRealPath();
            Cloudder::upload($image_path, null);
            //直前にアップロードされた画像のpublicIdを取得する。
            $publicId = Cloudder::getPublicId();
            $request->image_url = Cloudder::secureShow($publicId, [
                'width'     => 50,
                'height'    => 50
            ]);
            $request->image_id = $publicId;
        }else{
            $request->image_url = asset('img/sample.png');
        }

        // 認証済みユーザ（閲覧者）のアイテムとして作成（リクエストされた値をもとに作成）
        $request->user()->items()->create([
            'name' => $request->name,
            'image_url' => $request->image_url,
            'image_id' => $request->image_id,
            'category_id' => $request->category_id,
            'shop_id' => $request->shop_id,
            'status' => $request->status,
        ]);

        if ($request->has('continue')) {
            // アイテム作成ビューを表示
            return redirect()->route('items.create');
        }else{
            // アイテム一覧へリダイレクトさせる
            return redirect()->route('items.index');
        }
    }

    public function edit($id)
    {
        // idの値でアイテムを検索して取得
        $item = Item::findOrFail($id);

        if (\Auth::id() !== $item->user_id) {
            return redirect('/');
        }

        // 認証済みユーザを取得
        $user = \Auth::user();
        // カテゴリー一覧を取得
        $categories = $user->categories()->orderBy('number', 'asc')->get();
        // 買い出し先一覧を取得
        $shops = $user->shops()->orderBy('number', 'asc')->get();
        // 在庫状況を取得
        $status = $item->status;

        return view('items.edit', [
            'item' => $item,
            'categories' => $categories,
            'shops' => $shops,
            'status' => $status,
        ]);
    }

    public function update(Request $request, $id)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|max:10',
            //写真の登録は任意
            'image_url' => 'nullable',
            'image_id' => 'nullable',
            'category_id' =>'required',
            'shop_id' =>'required',
            'status' => 'required',
        ]);

        // idの値でアイテムを検索して取得
        $item = Item::findOrFail($id);

        if (\Auth::id() !== $item->user_id) {
            return redirect('/');
        }
        //前の画像をcloudinaryから消去する
        if(isset($item->image_id)){
            Cloudder::destroyImage($item->image_id);
        }
        //画像に初期値を設定
        $publicId = 'sample_nteekx';

        //新たに画像をcloudinaryにアップロードする
        if ($image = $request->file('image_url')) {
            $image_path = $image->getRealPath();
            Cloudder::upload($image_path, null);
            //直前にアップロードされた画像のpublicIdを取得する。
            $publicId = Cloudder::getPublicId();
            $request->image_url = Cloudder::secureShow($publicId, [
                'width'     => 100,
                'height'    => 100
            ]);
            $request->image_id = $publicId;
        }else{
            $request->image_url = asset('img/sample.png');
        }

        $item->name  = $request->name;
        $item->image_url = $request->image_url;
        $item->image_id = $request->image_id;
        $item->category_id = $request->category_id;
        $item->shop_id = $request->shop_id;
        $item->save();

        // アイテム一覧へリダイレクトさせる
        return redirect()->route('items.index');
    }

    public function destroy($id)
    {
        // idの値でアイテムを検索して取得
        $item = Item::findOrFail($id);

        if (\Auth::id() !== $item->user_id) {
            return redirect('/');
        }

        if(isset($item->image_id)){
            Cloudder::destroyImage($item->image_id);
        }

        $item->delete();

        // アイテム一覧へリダイレクトさせる
        return redirect()->route('items.index');
    }

    public function search(Request $request)
    {
        $keyword_name = $request->name;

        // 認証済みユーザを取得
        $user = \Auth::user();
        // カテゴリー一覧を取得
        $categories = $user->categories()->orderBy('number', 'asc')->get();


        // 認証済みユーザを取得
        $user = \Auth::user();
        //キーワードから部分一致するアイテムを取得
        $items = $user->items()->where('name','like', '%' .$keyword_name. '%')->orderBy('created_at', 'asc')->get();

        //検索結果一覧ビューを表示
        return view('items.search', [
            'items' => $items,
            'categories' => $categories,
            'keyword_name' => $keyword_name,
        ]);

    }
}