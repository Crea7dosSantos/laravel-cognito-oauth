<!DOCTYPE html><!-- Breadcrumb-->
<html prefix="og: http://ogp.me/ns#">

@include('oauth.elements.head')

<body>
    <div>
        <div>
            <main>
                @yield('pageContent')
            </main>
        </div>
    </div><!-- ▼ JS Libraries ▼-->
    @include('oauth.elements.script')
</body>

</html>