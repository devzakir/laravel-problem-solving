@extends('layouts.email_layout')

@section('content')

<div class="row ">
	<div class="col-md-12 col-sm-12 notification-title">
	<p>UKERUNDESUへの仮登録が完了しました。</p>
	<p>下記URLより本登録を行ってください。</p>
	<a href="{{ $url }}">{{ $url }}</a>
	<p>※URLが改行されて2行に表示されている場合はつなげて1行にしアクセスしてください。</p>
	</div>
</div>
@endsection