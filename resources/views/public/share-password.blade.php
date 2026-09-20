@extends('layouts.app')

@section('title', 'Protected document')

@section('content')
    <x-password-gate
        title="Protected document"
        subtitle="This invoice is password protected."
        :action="route('shares.password.check', $shareLink)" />
@endsection