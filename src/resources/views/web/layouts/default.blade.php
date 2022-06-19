<!DOCTYPE html><!-- Breadcrumb-->
<html prefix="og: http://ogp.me/ns#">

@include('web.elements.head')

<body>
    <div>
        <div>
            <main>
                @yield('pageContent')
            </main>
        </div>
    </div><!-- ▼ JS Libraries ▼-->
    @include('web.elements.script')
</body>

</html>