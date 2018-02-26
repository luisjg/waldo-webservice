@extends('layouts.master')

@section('content')
<h2 class="type--header type--thin">Version History</h2>
<h2>{{ env('APP_NAME') }} 1.0.2 <small>Release Date: 02/27/18</small></h2>
<p>
    <strong>Improvements:</strong>
    <ol>
        <li>Upgrade the underlying code base to the latest version.</li>
        <li>HTTPS is now enforced through code.</li>
    </ol>
</p>
<hr>
<h2>{{ env('APP_NAME') }} 1.0.1 <small>Release Date: 01/10/18</small></h2>
<p>
    <strong>Improvements:</strong>
    <ol>
        <li>Speed up transformation between x/y points to lat/long coordinates using <a href="//github.com/proj4php/proj4php">proj4php</a>.</li>
    </ol>
</p>
<h2>{{ env('APP_NAME') }} 1.0.0 <small>Release Date: 10/17/17</small></h2>
<p>
    <strong>New Features:</strong>
<ol>
    <li>Ability to retrieve room location information.</li>
</ol>
</p>
@endsection
