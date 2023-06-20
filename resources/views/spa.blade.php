@php
$config = [
    'appName' => config('app.name'),
    'locale' => $locale = app()->getLocale(),
    'locales' => config('app.locales'),
    'githubAuth' => config('services.github.client_id'),
];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="referrer" content="origin-when-cross-origin">
  <meta name="description" content="">
  <title>{{ config('app.name') }}</title>
  <link rel="shortcut icon" href="/asset/favicon.ico">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <link rel="icon" href="/asset/ukerundesu_logo.png">
  <link rel="stylesheet" href="{{ mix('dist/css/app.css') }}">
</head>
<body>
  <div id="app"></div>

  {{-- Global configuration object --}}
  <script>
    window.config = @json($config);
  </script>

  {{-- Load the application scripts --}}
  <script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
  <script src="{{ mix('dist/js/app.js') }}"></script>
</body>
</html>
