<style>
    .nav-link {
        background: white;
        margin-right: 1rem;
    }
    .nav-link.active {
        background-color: #79AEDD !important;
        color: white !important;
    }

    .nav-tabs {
        margin-bottom: 0 !important;
    }

    .tab-content {
        margin-top: 0 !important;
    }
</style>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a href="{{ route('report.finance.customer-payment') }}" class="nav-link rounded {{ Request::segment(3) == 'customer-payment' ? 'active' : '' }}" type="button">Kontrol Pembayaran Customer</a>
                <a href="{{ route('report.finance.balance-monitoring') }}" class="nav-link rounded {{ Request::segment(3) == 'balance-monitoring' ? 'active' : '' }}" type="button">Monitoring Saldo</a>
            </div>
        </nav>
