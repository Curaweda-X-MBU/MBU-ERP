<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
</style>

<div class="card">
    <div class="card-body">
        @include('report.sections.keuangan-collapse.data-keuangan')
        <section id="collapsible">
            <div class="row">
                <div class="col-sm-12">
                    <div class=" collapse-icon">
                        <div class=" p-0">
                            <div class="collapse-default">
                                @include('report.sections.keuangan-collapse.hpp-pembelian')
                                @include('report.sections.keuangan-collapse.laba-rugi')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
