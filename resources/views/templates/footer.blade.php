        <footer class="footer footer-static footer-light no-print">
                <div class="float-right">Version {{ env('APP_VERSION') }}</div>
                <p class="clearfix mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2024 Mitra Berlian Unggas<span class="d-none d-sm-inline-block">, All rights Reserved</span></span></p>
        </footer>
        <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
        <script>
                $.extend(true, $.fn.dataTable.defaults, {
                    language: {
                        url: "{{ asset('app-assets/vendors/js/tables/datatable/id.json') }}"
                    }
                });
        </script>