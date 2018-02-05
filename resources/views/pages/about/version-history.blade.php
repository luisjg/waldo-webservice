<!DOCTYPE HTML>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Waldo Web Service</title>
    <meta name="description" content="A Web Service that delivers information on CSUN rooms">
    <link rel="icon" href="//www.csun.edu/sites/default/themes/csun/favicon.ico" type="image/x-icon">
    <script src="//use.typekit.net/gfb2mjm.js"></script>
    <script>try{Typekit.load();}catch(e){}</script>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic">
    <link rel="stylesheet" href="{{ url('css/metaphor.css') }}">
    <link rel="stylesheet" href="{{ url('css/tomorrow.css.min') }}">
</head>
<body>
<div class="section section--sm">
    <div class="container type--center">
        <h1 class="giga type--thin">Waldo Web Service</h1>
        <h3 class="h1 type--thin type--gray">Delivering CSUN Room Location Information</h3>
    </div>
</div>

<div class="section" style="min-height: calc(100vh - 130px);">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <p class="header--sm"><strong>DOCUMENTATION</strong></p>
                <ul class="nav">
                    <li class="nav__item"><a class="nav__link" href="{{ url('/#introduction')}}">Introduction</a></li>
                    <li class="nav__item"><a class="nav__link" href="{{ url('/#getting-started')}}">Getting Started</a></li>
                    <li class="nav__item"><a class="nav__link" href="{{ url('/#collections')}}">Collections</a></li>
                    <li class="nav__item"><a class="nav__link" href="{{ url('/#subcollections')}}">Subcollections</a></li>
                </ul>
                <p class="header--sm"><strong>VERSION HISTORY</strong></p>
                <ul class="nav">
                    <li class="nav__item"><a class="nav__link" href="{{ url('/about/version-history') }}">Recent Changes</a></li>
                </ul>
            </div>

            <div class="col-md-9">
                <h2 class="type--header type--thin">Version History</h2>
                <h2>Waldo 1.0.2 <small>Release Date: 02/06/18</small></h2>
                <p>
                    <strong>Improvements: </strong>
                    <ol>
                        <li>Upgrade the underlying code base to the latest version.</li>
                        <li>HTTPS is now enforced through code.</li>
                    </ol>
                </p>
                <h2>Waldo 1.0.1 <small>Release Date: 01/10/18</small></h2>
                <p>
                    <strong>Improvements:</strong>
                    <ol>
                        <li>Speed up transformation between x/y points to lat/long coordinates using <a href="//github.com/proj4php/proj4php">proj4php</a>.</li>
                    </ol>
                </p>
                <h2>Waldo 1.0.0 <small>Release Date: 10/17/17</small></h2>
                <p>
                    <strong>New Features:</strong>
                <ol>
                    <li>Ability to retrieve room location information.</li>
                </ol>
                </p>
            </div>
        </div>
    </div>
</div>
<footer>
<div class="container">
  <div class="row">
    <div class="col-sm-5">
      <div class="row">
        <div class="col-sm-3 footer-seal">
          <img src="//www.csun.edu/faculty/imgs/footer-seal.png" alt="Seal for California State University, Northridge">
        </div>
        <div class="col-sm-9">
          <ul class="list--unstyled">
            <li><strong>Waldo Web Service</strong> <br>© California State University, Northridge</li>
            <li>18111 Nordhoff Street, Northridge, CA 91330</li>
            <li>Phone: (818) 677-1200 / <a href="http://www.csun.edu/contact/" target="_blank">Contact Us</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-sm-7">
      <div class="row">
        <div class="col-sm-4">
          <ul class="list--unstyled">
            <li><a href="//www.csun.edu/emergency/" target="_blank">Emergency Information</a></li>
            <li><a href="//www.csun.edu/afvp/university-policies-procedures/" target="_blank">University Policies &amp; Procedures</a></li>
          </ul>
        </div>
        <div class="col-sm-4">
          <ul class="list--unstyled">
            <li><a href="//www.csun.edu/sites/default/files/900-12.pdf" target="_blank">Terms and Conditions for Use</a></li>
            <li><a href="//www.csun.edu/sites/default/files/500-8025.pdf" target="_blank">Privacy Policy</a></li>
            <li><a href="//www.csun.edu/it/document-viewers" target="_blank">Document Reader</a></li>
          </ul>
        </div>
        <div class="col-sm-4">
          <ul class="list--unstyled">
            <li><a href="//www.calstate.edu/" target="_blank">California State University</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
</footer>
<div class="metalab-footer">
    <div class="metalab-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="metalab-branding">
                        <img src="//www.csun.edu/faculty/imgs/meta-logo-horz.png" alt="CSUN META Lab Logo">
                        <ul class="list--unstyled">
                            <li><a href="//metalab.csun.edu">metalab.csun.edu</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6">
                    <ul class="list--unstyled metalab-tagline">
                        <li>Explore. Learn. Go Beyond.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!--
  __  __   ___   _____     _
 |  \/  | | __| |_   _|   /_\       Explore Learn Go Beyond
 | |\/| | | _|    | |    / _ \      https://www.metalab.csun.edu/
 |_|  |_| |___|   |_|   /_/ \_\
    _       _        _     ___
  _| |_    | |      /_\   | _ )
 |_   _|   | |__   / _ \  | _ \
   |_|     |____| /_/ \_\ |___/
-->
</body>
</html>
