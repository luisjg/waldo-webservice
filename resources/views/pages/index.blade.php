@extends('layouts.master')

@section('title')
    Documentation
@endsection

@section('description')
    {{ env('APP_NAME') }} Web Service Documentation
@endsection

@section('content')
<h2 id="introduction">Introduction</h2>
<p>
    The information is derived from the Facilities Database maintained by Admin and Finance IT. The web service provides a gateway via a REST-ful API. The information is retrieved by creating a specific URI and giving values to filter the data. The information
    that is returned is a JSON object that contains room location information to a particular room; the format of the JSON object is as follows:
</p>
<pre class="prettyprint">
    <code>
{
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
}
    </code>
</pre>
<br>
<h2 id="getting-started">Getting Started</h2>
<ol>
    <li>
        <strong>Generate the URI</strong>
        <br> Find the usage that fits your need. Browse through subcollections, instances and query types to help you craft your URI.
    </li>
    <li>
        <strong>Provide the Data</strong>
        <br> Use the URI to query your data. See the Usage Example session.
    </li>
    <li>
        <strong>Show the Results</strong>
    </li>
</ol>
<p>Loop through the data to display its information. See the Usage Example session.</p>
<br>
<h2 id="collections">Collections</h2>
<strong>All Rooms Listing</strong>
<ul>
    <li>
        <a href="{{ url('1.0/rooms') }}">{{ url('1.0/rooms') }}</a>
    </li>
</ul>
<br>
<h2 id="subcollections">Subcollections</h2>
<strong>Specific Room retrieval</strong>
<ul>
    <li>
        <a href="{{ url('1.0/rooms?room=JD2211') }}">
            {{ url('1.0/rooms?room=JD2211') }}
        </a>
    </li>
</ul>
<h2 id="code-samples">Code Samples</h2>
<div class="accordion">
    <div class="card">
        <div id="jquery-header" class="card-header">
            <p class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#jquery-body" aria-expanded="true" aria-controls="jquery-body">
                    JQuery
                </button>
            </p>
        </div>
        <div id="jquery-body" class="collapse" aria-labelledby="jquery-header">
            <div class="card-body">
                    <pre>
                        <code class="prettyprint lang-js">
//construct a function to get url and iterate over
$(document).ready(function() {
    //generate a url
    var url = '{!! url('1.0/rooms?room=JD2211') !!}';
    //use the URL as a request
    $.ajax({
        url: url
    }).done(function(data) {
        // print the building name
        console.log(data.rooms[0].building_name);
    });
});
                        </code>
                    </pre>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="php-header" class="card-header">
            <p class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#php-body" aria-expanded="true" aria-controls="php-body">
                    PHP
                </button>
            </p>
        </div>
        <div id="php-body" class="collapse" aria-labelledby="php-header">
            <div class="card-body">
                    <pre>
                        <code class="prettyprint lang-php">
//generate a url
$url = '{!! url('1.0/rooms?room=JD2211') !!}';

//add extra necessary
$arrContextOptions = [
    "ssl" => [
        "verify_peer"=>false,
        "verify_peer_name"=>false
    ]
];

//perform the query
$data = file_get_contents($url, false, stream_context_create($arrContextOptions));

//decode the json
$data = json_decode($data, true);

//iterate over the list of data and print
echo $data['rooms'][0]['building_name'];
                        </code>
                    </pre>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="python-header" class="card-header">
            <p class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#python-body" aria-expanded="true" aria-controls="python-body">
                    Python
                </button>
            </p>
        </div>
        <div id="python-body" class="collapse" aria-labelledby="python-header">
            <div class="card-body">
                    <pre>
                        <code class="prettyprint language-py">
#python 2
import urllib2
import json

#generate a url
url = u'{!! url('1.0/rooms?room=JD2211') !!}'

#open the url
try:
    u = urllib2.urlopen(url)
    data = u.read()
except Exception as e:
    data = {}

#load data with json object
data = json.loads(data)

#iterate over the json object and print
print data['rooms'][0]['building_name']
                        </code>
                    </pre>
            </div>
        </div>
    </div>
    <div class="card">
        <div id="ruby-header" class="card-header">
            <p class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#ruby-body" aria-expanded="true" aria-controls="ruby-body">
                    Ruby
                </button>
            </p>
        </div>
        <div id="ruby-body" class="collapse" aria-labelledby="ruby-header">
            <div class="card-body">
                    <pre>
                        <code class="prettyprint lang-rb">
require 'net/http'
require 'json'

#generate a url
source = '{!! url('1.0/rooms?room=JD2211') !!}'

#prepare the uri
uri = URI.parse(source)

#request the data
response = Net::HTTP.get(uri)

#parse the json
data = JSON.parse(response)

#print the value
puts "#{data['rooms'][0]['building_name']}"
                        </code>
                    </pre>
            </div>
        </div>
    </div>
</div>
@endsection