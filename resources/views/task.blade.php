@extends('layouts/app')

@section('content')

    tasks #{{ $task_number }}

    {{ $task['image'] }}

    {{ var_export($task['choices']) }}
@stop