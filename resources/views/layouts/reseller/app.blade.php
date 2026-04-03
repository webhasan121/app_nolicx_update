@extends('layouts.app')

@section('navigations')
    {{-- @livewire('layout.navigation', []) --}}
    @livewire('layout.navigation', ['get' => 'reseller'], key('reseller'))
@endsection

@section('page-header')
    Reseller Dashboard
@endsection

{{$slot}}