<!DOCTYPE html>
<html lang="en">
<head>
    @include('customer.component.head')
</head>
<body class="@yield('body_class')">
@include('customer.component.svg')
@include('customer.component.header')
@yield('content_cus')
@include('customer.component.footer')
</body>
</html>
