@extends('layouts.master')
@section('content')
    <h2 id="introduction">Introduction</h2>
    <p>
        The information is derived from the Facilities Database maintained by Admin and Finance IT.
        The web service provides a gateway via a REST-ful API. The information is retrieved by
        creating a specific URI and giving values to filter the data. The information that is
        returned is a JSON object that contains room location information to a particular room;
        the format of the JSON object is as follows:
    </p>
    <pre class="prettyprint"><code>{
  "success": "true",
  "status": 200,
  "api": "waldo",
  "version": "1.0",
  "collection": "rooms",
  "count": "1",
  "rooms": [
    {
      "room_number": "JD2211",
      "building_name": "Jacaranda Hall",
      "latitude": 34.24141145,
      "longitude": -118.529299945
    }
  ]
}</code></pre>
    <br>
    <h2 id="getting-started">Getting Started</h2>
    <ol>
        <li><strong>GENERATE THE URI:</strong> Find the usage that fits your need. Browse through subcollections, instances and query types to help you craft your URI.</li>
        <li><strong>PROVIDE THE DATA:</strong> Use the URI to query your data. See the Usage Example session.</li>
        <li><strong>SHOW THE RESULTS</strong></li>
    </ol>
    <p>Loop through the data to display its information. See the Usage Example session.</p>
    <br>
    <h2 id="collections">Collections</h2>
    <strong>All Rooms Listing</strong>
    <ul>
        <li><a href="{{url('api/1.0/rooms')}}">{{url('api/1.0/rooms')}}</a></li>
    </ul>
    <br>
    <h2 id="subcollections">Subcollections</h2>
    <strong>Specific Room retrieval</strong>
    <ul>
        <li>
            <a href="{{url('api/1.0/rooms?room=JD2211')}}">
                {{url('api/1.0/rooms?room=JD2211')}}
            </a>
        </li>
    </ul>
@endsection