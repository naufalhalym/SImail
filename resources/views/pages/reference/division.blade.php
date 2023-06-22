@extends('layout.main')

@section('content')
<form action="{{ route('reference.division.store') }}" method="post">
    @csrf
    <x-input-form name="division" :label="__('model.division.division')"/>
    <x-input-form name="description" :label="__('model.division.description')"/>
    <button type="submit" class="btn btn-primary">{{ __('menu.general.save') }}</button>
</form>
@endsection
