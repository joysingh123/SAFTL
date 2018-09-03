<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ URL::to('css/app.css') }}">
 
    <title>CompaniesContact Match Stats</title>
 
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
 
    <!-- Styles -->
    <style>
    html, body {
        background-color: #fff;
        color: #636b6f;
        font-weight: 100;
        margin: 0;
        padding: 5%
    }
</style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">
            CompaniesContact Match Stats
        </h2>
 
        @if (isset($response['status'])  && $response['status'] == "Success")
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
        </button>
        <strong>{{ $response['message'] }}</strong>
        </div>
        @endif
 
    @if ( isset($response['status'])  && $response['status'] == "Fail" )
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
        </button>
        <strong>{{ $response['message'] }}</strong>
    </div>
    @endif
</div>
@if ( isset($response['stats'])  && count($response['stats']) > 0)
<br>
<div class="container">        
  <table class="table">
    <thead>
      <tr>
        <th>Limit</th>
        <th>Found Record For Processing</th>
        <th>New Insertion In MatchTable</th>
        <th>Already Exist In MatchTable</th>
        <th>New Insertion In Company Without Domain</th>
        <th>Already Exist In Company Without Domain</th>
      </tr>
    </thead>
    <tbody>
      <tr>
          <td>{{$response['stats']['Record Processing Limit']}}</td>
          <td>{{$response['stats']['Found Record For Processing']}}</td>
          <td>{{$response['stats']['New In Match']}}</td>
          <td>{{$response['stats']['Already In Match']}}</td>
          <td>{{$response['stats']['New In Domain Not Found']}}</td>
          <td>{{$response['stats']['Already Exist In Domain Not Found']}}</td>
      </tr>
    </tbody>
  </table>
</div>
@endif
</body>
</html>