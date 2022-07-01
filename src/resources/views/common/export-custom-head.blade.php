@inject('headContent', 'App\Theming\CustomHtmlHeadContentProvider')

@if(setting('app-custom-head'))
<!-- Custom user content -->
{!! $headContent->forExport() !!}
<!-- End custom user content -->
@endif
