@extends('layouts.master')

@section('title')
    Version History
@endsection

@section('description')
    {{ env('APP_NAME') }} Web Service Version History
@endsection

@section('content')
<div class="version-history">
    <h2>Version History</h2>
    <h3>{{ env('APP_NAME') }} 1.0.4 <small>Release Date: 11/06/18</small></h3>
    <strong>Improvements:</strong>
    <ol>
        <li>Update the URLs to be more consistent with the rest of the web services.</li>
        <li>Include sample code usage on the landing page.</li>
        <li>Update the landing pages to include the latest version of <a href="//csun-metalab.github.io/metaphorV2/">Metaphor</a>.</li>
    </ol>
    <h3>{{ env('APP_NAME') }} 1.0.3 <small>Release Date: 04/11/18</small></h3>
    <strong>Improvements:</strong>
    <ol>
        <li>Speed improvements for data retrieval.</li>
    </ol>
    <h3>{{ env('APP_NAME') }} 1.0.2 <small>Release Date: 02/27/18</small></h3>
    <strong>Improvements:</strong>
    <ol>
        <li>Upgrade the underlying code base to the latest version.</li>
        <li>HTTPS is now enforced through code.</li>
    </ol>
    <h3>{{ env('APP_NAME') }} 1.0.1 <small>Release Date: 01/10/18</small></h3>
    <strong>Improvements:</strong>
    <ol>
        <li>Speed up transformation between x/y points to lat/long coordinates using
            <a href="//github.com/proj4php/proj4php">proj4php</a>.</li>
    </ol>
    <h3>{{ env('APP_NAME') }} 1.0.0 <small>Release Date: 10/17/17</small></h3>
    <strong>New Features:</strong>
    <ol>
        <li>Ability to retrieve room location information.</li>
    </ol>
</div>
@endsection
