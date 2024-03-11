<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <meta name="csrf-token" content="{{ csrf_token() }}">



    {{-- <title inertia>{{ config('app.name', "" ) }}</title> --}}

    {!! \App\Http\Services\PsService::render() !!}

    @routes

    {{ meta_tags() }}

    {{ vite_assets() }}
</head>

<body class="font-sans antialiased" id="mainbody">
    <div id="home_loading__container">
        <div class="home-lds-default">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    @inertia

    @env('local')
    <!-- <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script> -->
    @endenv
</body>
<style>


    #home_loading__container {
        width: 100%;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .home-lds-default {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }

    .home-lds-default div {
        position: absolute;
        width: 6px;
        height: 6px;
        background: #334155;
        border-radius: 50%;
        animation: home-lds-default 1.2s linear infinite;
    }

    .home-lds-default div:nth-child(1) {
        animation-delay: 0s;
        top: 37px;
        left: 66px;
    }

    .home-lds-default div:nth-child(2) {
        animation-delay: -0.1s;
        top: 22px;
        left: 62px;
    }

    .home-lds-default div:nth-child(3) {
        animation-delay: -0.2s;
        top: 11px;
        left: 52px;
    }

    .home-lds-default div:nth-child(4) {
        animation-delay: -0.3s;
        top: 7px;
        left: 37px;
    }

    .home-lds-default div:nth-child(5) {
        animation-delay: -0.4s;
        top: 11px;
        left: 22px;
    }

    .home-lds-default div:nth-child(6) {
        animation-delay: -0.5s;
        top: 22px;
        left: 11px;
    }

    .home-lds-default div:nth-child(7) {
        animation-delay: -0.6s;
        top: 37px;
        left: 7px;
    }

    .home-lds-default div:nth-child(8) {
        animation-delay: -0.7s;
        top: 52px;
        left: 11px;
    }

    .home-lds-default div:nth-child(9) {
        animation-delay: -0.8s;
        top: 62px;
        left: 22px;
    }

    .home-lds-default div:nth-child(10) {
        animation-delay: -0.9s;
        top: 66px;
        left: 37px;
    }

    .home-lds-default div:nth-child(11) {
        animation-delay: -1s;
        top: 62px;
        left: 52px;
    }

    .home-lds-default div:nth-child(12) {
        animation-delay: -1.1s;
        top: 52px;
        left: 62px;
    }

    @keyframes home-lds-default {

        0%,
        20%,
        80%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.5);
        }
    }
</style>

</html>
