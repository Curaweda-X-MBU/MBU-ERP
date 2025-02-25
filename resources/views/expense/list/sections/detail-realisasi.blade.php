<style>
    .budget-card {
      cursor: pointer;
      border-radius: 15px;
      color: white;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .budget-card.primary {
      background: linear-gradient(118deg, #76A8D8, #A9D0F5);
    }

    .budget-card.success {
      background: linear-gradient(118deg, #4CA555, #54FF65);
    }

    #budget {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }
</style>

<div class="card">
    <div class="card-header pb-0">
        <h4 class="card-title">{{ $title }} | Realisasi</h4>
    </div>
    <div class="card-header">
        <div style="width: 100%; display: flex; align-items: center; justify-content: end; gap: 0.5rem;">
            @if ($data->parent_expense || $data->child_epense)
            <a href="#" class="btn btn-primary">
                Pengajuan Lain
            </a>
            @endif
            <div class="dropdown dropleft" style="position: static;">
                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow btn-outline-secondary" data-toggle="dropdown">
                    <i data-feather="more-vertical"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="#" class="dropdown-item">
                        <i data-feather='edit-2' class="mr-50"></i>
                        Edit Realisasi
                    </a>
                    <a href="#" class="dropdown-item">
                        <i data-feather='refresh-ccw' class="mr-50"></i>
                        Pengajuan Ulang
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body row">
        {{-- Nominal Pengajuan --}}
        <div class="col-md-6 mt-1">
            <div class="budget-popover d-none">
                <p class="font-bolder">{{ \App\Helpers\Parser::toLocale($data->grand_total) }}</p>
            </div>
            <div class="budget-card primary mt-1" tabindex="0" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="left" data-html="true">
                <h5 class="font-weight-bolder text-white">Nominal Pengajuan</h5>
                <p id="budget" class="mt-1 font-weight-bolder fs-larger" data-visible="false" style="font-size: 1.7rem;">Rp.&nbsp;<span>{{ \App\Helpers\Parser::toLocale($data->grand_total) }}</span></p>
            </div>
        </div>
        {{-- Nominal Realisasi --}}
        <div class="col-md-6 mt-1">
            <div class="budget-popover d-none">
                <p class="font-bolder">{{ \App\Helpers\Parser::toLocale($data->is_realized) }}</p>
            </div>
            <div class="budget-card success mt-1" tabindex="0" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="left" data-html="true">
                <h5 class="font-weight-bolder text-white">Nominal Realisasi</h5>
                <p id="budget" class="mt-1 font-weight-bolder fs-larger" data-visible="false" style="font-size: 1.7rem;">Rp.&nbsp;<span>{{ \App\Helpers\Parser::toLocale($data->is_realized) }}</span></p>
            </div>
        </div>
    </div>
</div>
