@if (session('error'))
    <div class="alert alert-danger">
        <div class="alert-body">
            <strong>{{ session('error') }}</strong>
        </div>
    </div>
@endif
@if (session('success'))
    <div class="alert alert-success">
        <div class="alert-body">
            <strong>{{ session('success') }}</strong>
        </div>
    </div>
@endif