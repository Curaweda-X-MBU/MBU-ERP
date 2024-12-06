    
    <!-- BEGIN: Page Vendor JS-->
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }

            $(function () {
                $('#toggleSidebar').click(function() {
                    setTimeout(function() {
                        $('table').DataTable().columns.adjust().draw(); 
                    }, 300);
                });

                $.fn.select2.defaults.set("language", {
                    noResults: function() {
                        return "Data tidak ditemukan";
                    },
                    errorLoading: function() {
                        return "Hasil tidak dapat dimuat";
                    },
                    searching: function() {
                        return "Mencari...";
                    },
                });
            });

        })
    </script>