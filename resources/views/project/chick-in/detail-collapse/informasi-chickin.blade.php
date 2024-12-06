<div class="card mb-1">
    <div id="headingCollapse2" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
        <span class="lead collapse-title"> Informasi  Chick In </span>
    </div>
    <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100">
                        <thead>
                            <th>No. Surat Jalan</th>
                            <th>Dokumen Surat Jalan</th>
                            <th>Tgl. Chick In</th>
                            <th>Supplier</th>
                            <th>Hatchery</th>
                            <th>Jumlah (Ekor)</th>
                        </thead>
                        <tbody>
                            @if (count($data->project_chick_in) > 0)
                                @foreach ($data->project_chick_in as $item)
                                <tr>
                                    <td>{{ $item->travel_letter_number }}</td>
                                    <td>
                                        @if ($item->travel_letter_document)
                                        <a class="dropdown-item" href="{{ route('file.show', ['filename' => $item->travel_letter_document]) }}" target="_blank">
                                            <i data-feather='download' class="mr-50"></i>
                                            <span>Download</span>
                                        </a>
                                        @endif
                                    </td>
                                    <td>{{ date('d F Y', strtotime($item->chickin_date)) }}</td>
                                    <td>{{ $item->supplier->name }}</td>
                                    <td>{{ $item->hatchery }}</td>
                                    <td>{{ number_format($item->total_chickin, '0', ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center" colspan="6">Belum ada data chick in</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        
    });
</script>