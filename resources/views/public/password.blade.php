@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <x-password-gate
        :title="$project->name"
        subtitle="This project is password protected."
        :action="route('projects.password.check', $project)" />
@endsection