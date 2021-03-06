@extends('layouts.app')

@section('content')

    <div class="page-title">
        <h2>アイテム編集</h2>
    </div>

    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            {!! Form::model($item, ['route' => ['items.update',$item->id],'method' => 'put','files' => true]) !!}

                <div class="form-group">
                    {!! Form::label('name', '名前') !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('image_url', '画像',['class' => 'd-block']) !!}
                    {!! Form::file('image_url') !!}
                </div>

                @if (count($categories)>0)
                    <div class="form-group">
                        {!! Form::label('category_select', 'カテゴリー',['class' => 'd-block']) !!}
                        @foreach ($categories as $category)
                        {{ Form::radio('category_id',$category->id , false) }}
                        {!! Form::label('category_id', $category->name) !!}
                        @endforeach
                    </div>
                @else
                    <div class="border border-danger rounded p-2 mb-2">先にカテゴリーを設定してください</div>
                @endif

                @if (count($shops)>0)
                    <div class="form-group">
                        {!! Form::label('shop_select', '買い出し先',['class' => 'd-block']) !!}
                        @foreach ($shops as $shop)
                        {{ Form::radio('shop_id', $shop->id, false, ['shop_id' => $shop->id]) }}
                        {!! Form::label('shop_id', $shop->name) !!}
                        @endforeach
                    </div>
                @else
                    <div class="border border-danger rounded p-2 mb-2">先に買い出し先を設定してください</div>
                @endif


                {!! Form::submit('更新', ['class' => 'orange-btn btn-block']) !!}

            {!! Form::close() !!}

            {{-- 削除ボタン --}}
            <div class="text-center">
                {!! Form::model($item, ['route' => ['items.destroy', $item->id], 'method' => 'delete']) !!}
                    {!! Form::button('削除する', ['class' => "delete-btn", 'type' => 'submit']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection