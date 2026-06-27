@extends('layouts.guest.guest')

@section('content')
    @include('pages.public.landing-page.sections.hero')
    @include('pages.public.landing-page.sections.why-etherno')
    @include('pages.public.landing-page.sections.portfolio-slider')
    @include('pages.public.partials.instagram-section')
    @include('pages.public.landing-page.sections.testimonials')
    @include('pages.public.landing-page.sections.packages')
    @include('pages.public.landing-page.sections.faq')
    @include('pages.public.landing-page.sections.cta')
@endsection
