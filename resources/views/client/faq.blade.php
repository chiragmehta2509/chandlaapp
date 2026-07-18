@extends('layouts.client')

@section('title', 'FAQ')

@section('content')
@include('partials.faq-section', [
    'faqDirectGpayHref' => route('client.plans') . '#direct-gpay',
    'faqReferHref' => route('public.home') . '#refer',
])
@endsection
