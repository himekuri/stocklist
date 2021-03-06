@extends('layouts.app')

@section('content')

    <div class="page-title">
        <h2>カテゴリー編集</h2>
    </div>

    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            {!! Form::model($category, ['route' => ['categories.update', $category->id ],'method' => 'put']) !!}

                <div class="form-group">
                    {!! Form::label('name', '名前') !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('number', '並び順',['class' => 'd-block']) !!}
                    {!! Form::select('number',App\Category::numbers(),['class' => 'form-control'] ) !!}

                </div>
                {!! Form::submit('更新', ['class' => 'orange-btn btn-block']) !!}
            {!! Form::close() !!}
            {{-- 削除ボタン --}}
            <div class="text-center">
                {!! Form::model($category, ['route' => ['categories.destroy', $category->id], 'method' => 'delete']) !!}
                    {!! Form::button('削除する', ['class' => "delete-btn", 'type' => 'submit']) !!}
                {!! Form::close() !!}
            </div>
        </div>

    </div>
@endsection