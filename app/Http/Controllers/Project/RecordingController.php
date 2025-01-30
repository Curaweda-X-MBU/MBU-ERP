<?php

namespace App\Http\Controllers\Project;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\ProductCategory;
use App\Models\Inventory\ProductWarehouse;
use App\Models\Inventory\StockLog;
use App\Models\Project\Project;
use App\Models\Project\Recording;
use App\Models\Project\RecordingBw;
use App\Models\Project\RecordingBwList;
use App\Models\Project\RecordingDepletion;
use App\Models\Project\RecordingEgg;
use App\Models\Project\RecordingNonstock;
use App\Models\Project\RecordingStock;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class RecordingController extends Controller
{
    public function index(Request $req)
    {
        try {
            $param = ['title' => 'Project > Recording'];
            $data  = Recording::with('project');
            if ($req->isMethod('post')) {
                $projectId                 = $req->project_id;
                $period                    = $req->period;
                $whereClause               = [];
                $whereClause['project_id'] = $projectId;
                $whereClause['project']    = Project::with('kandang')->find($projectId);
                $whereClause['period']     = $period;
                $param['param']            = $whereClause;

                $filteredWhereClause = array_filter($whereClause, function($value, $key) {
                    if ($key === 'project') {
                        return false;
                    }
                    if ($key === 'period' && $value == 0) {
                        return false;
                    }
                    if ($key === 'project_id' && $value == 0) {
                        return false;
                    }

                    return true;
                }, ARRAY_FILTER_USE_BOTH);

                $data->whereHas('project', function($query) use ($filteredWhereClause) {
                    $query->where($filteredWhereClause);
                });
            }
            $param['data'] = $data->get();

            return view('project.recording.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Project > Recording > Tambah Baru',
            ];

            if ($req->isMethod('post')) {
                $input = $req->all();
                if (! $req->has('stock')) {
                    return redirect()->back()->with('error', 'Persedian harus diisi');
                }

                if (! $req->has('bw')) {
                    return redirect()->back()->with('error', 'Body Weight harus diisi');
                }

                $project = Project::with([
                    'kandang',
                    'recording',
                    'recording.recording_bw',
                    'project_chick_in',
                    'fcr',
                    'fcr.fcr_standard',
                ])->find($input['project_id']);

                $arrDay = [];
                foreach ($project->recording ?? [] as $key => $value) {
                    $existingDay = $value->day;
                    $arrDay[]    = $existingDay;
                    if ($input['day'] == $existingDay) {
                        return redirect()->back()->with('error', 'Error: Umur '.$input['day'].' hari sudah pernah direcord');
                    }
                }

                $arrDaySeq = collect($arrDay)->sort()->values()->all();
                $lastDay   = $input['day'] - 1;
                if ($lastDay > 0 && $arrDaySeq !== range(1, $lastDay)) {
                    return redirect()->back()->with('error', 'Error: Pastikan kamu telah melakukan recording hari sebelumnya');
                }

                $parentProduct    = false;
                $productCategory  = ProductCategory::find($input['product_category_id']);
                $projectWarehouse = ProductWarehouse::whereHas('product', function($query) {
                    $query->whereHas('product_category', function($query) {
                        $query->whereIn('category_code', ['BRO', 'PRS', 'FLS', 'LYR', 'TLR', 'GPS']);
                    });
                })
                    ->where('warehouse_id', $input['warehouse_id'])
                    ->where('quantity', '>', 0)
                    ->first();

                if ($projectWarehouse) {
                    $parentProduct = $projectWarehouse->product_id;
                }

                DB::beginTransaction();

                $recording                  = new Recording;
                $recording->project_id      = $input['project_id'];
                $strtotime                  = strtotime($input['record_datetime']);
                $recording->day             = $input['day'];
                $recordDateInput            = date('Y-m-d H:i', $strtotime);
                $recording->record_datetime = $recordDateInput;
                $createdAt                  = Carbon::today()->subDay();
                $recordDate                 = Carbon::parse(date('Y-m-d', $strtotime));
                $onTime                     = $createdAt->isSameDay($recordDate) ? true : false;
                $recording->on_time         = $onTime;
                $recording->created_by      = auth()->user()->user_id;
                $recording->status          = array_search('Disetujui', Constants::RECORDING_STATUS);
                $recording->save();

                $arrStocks = $input['stock'];
                foreach ($arrStocks as $key => $value) {
                    $generalStock = ProductWarehouse::where([
                        'product_id'   => $value['product_id'],
                        'warehouse_id' => $input['warehouse_id'],
                    ])->first();

                    $valDecrease = str_replace('.', '', $value['decrease_stock']);
                    if ($generalStock->quantity < $valDecrease) {
                        DB::rollback();

                        return redirect()->back()->with('error', 'Pemakaian persediaan melebihi jumlah stok saat ini');
                    }

                    $recordingStock                       = new RecordingStock;
                    $recordingStock->recording_id         = $recording->recording_id;
                    $recordingStock->product_warehouse_id = $generalStock->product_warehouse_id;
                    $recordingStock->decrease             = $valDecrease;
                    $recordingStock->save();

                    $updateStock = StockLog::triggerStock([
                        'product_id'         => $value['product_id'],
                        'stock_date'         => date('Y-m-d', $strtotime),
                        'warehouse_id'       => $input['warehouse_id'],
                        'decrease'           => $valDecrease,
                        'stocked_by'         => 'Recording',
                        'notes'              => 'Persediaan Project '.$project->kandang->name,
                        'recording_stock_id' => $recordingStock->recording_stock_id,
                    ]);
                }

                $arrNonstock = $input['nonstock'] ?? [];
                foreach ($arrNonstock as $key => $value) {
                    $nonstockVal = $value['value'];
                    $replaceNode = str_replace('.', '', $nonstockVal);
                    $inputValue  = str_replace(',', '.', $replaceNode);
                    RecordingNonstock::create([
                        'recording_id' => $recording->recording_id,
                        'nonstock_id'  => $value['nonstock_id'],
                        'value'        => $inputValue,
                    ]);
                }

                $recordingBw               = new RecordingBw;
                $recordingBw->recording_id = $recording->recording_id;
                $recordingBw->avg_weight   = $input['avg_weight'];
                $recordingBw->total_chick  = $input['total_chick'];
                $recordingBw->total_calc   = $input['total_calc'];
                $recordingBw->value        = $input['value'];
                $recordingBw->save();

                $arrBw = $input['bw'];
                foreach ($arrBw as $key => $value) {
                    RecordingBwList::create([
                        'recording_bw_id' => $recordingBw->recording_bw_id,
                        'weight'          => str_replace(',', '.', str_replace('.', '', $value['weight'])),
                        'total'           => str_replace(',', '.', str_replace('.', '', $value['total'])),
                        'weight_calc'     => str_replace(',', '.', str_replace('.', '', $value['weight_calc'])),
                    ]);
                }

                if ($req->has('depletions')) {
                    $this->insertRecordingDepletionAndEgg($input['depletions'], $input['warehouse_id'], $parentProduct, $strtotime, $project, $recording->recording_id);
                }

                if ($req->has('eggs')) {
                    $this->insertRecordingDepletionAndEgg($input['eggs'], $input['warehouse_id'], false, $strtotime, $project, $recording->recording_id);
                }

                DB::commit();
                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('project.recording.index')->with($success);
            }

            return view('project.recording.add', $param);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function insertRecordingDepletionAndEgg($data, $warehouseId, $parentProduct, $strtotime, $project, $recordingId)
    {
        $totalDecrese = 0;
        foreach ($data as $key => $value) {
            $arrProdStockType = [
                [
                    'product_id' => $value['product_id'],
                    'increase'   => $value['total'],
                    'decrease'   => 0,
                ],
            ];

            if ($parentProduct) {
                $arrProdStockType[] = [
                    'product_id' => $parentProduct,
                    'increase'   => 0,
                    'decrease'   => $value['total'],
                ];
                $totalDecrese += $value['total'];
            }

            foreach ($arrProdStockType as $k => $v) {
                $updateStock = StockLog::triggerStock([
                    'product_id'   => $v['product_id'],
                    'stock_date'   => date('Y-m-d', $strtotime),
                    'warehouse_id' => $warehouseId,
                    'increase'     => $v['increase'],
                    'decrease'     => $v['decrease'],
                    'stocked_by'   => 'Recording',
                    'notes'        => 'Project '.$project->kandang->name,
                ]);

            }

            $currentWhStock = ProductWarehouse::where([
                'product_id'   => $value['product_id'],
                'warehouse_id' => $warehouseId,
            ])->first();

            $insertRecord = [
                'recording_id'         => $recordingId,
                'product_warehouse_id' => $currentWhStock->product_warehouse_id,
                'total'                => str_replace('.', '', $value['total']),
                'notes'                => '',
            ];

            if ($parentProduct) {
                RecordingDepletion::create($insertRecord);
            } else {
                RecordingEgg::create($insertRecord);
            }
        }

        $recordings = Recording::with([
            'recording_bw',
            'recording_stock',
            'recording_stock.product_warehouse.product',
        ])->find($recordingId);

        $lastDay            = $recordings->day - 1;
        $lastDailyDepletion = 0;
        $projectChickin     = $project->project_chick_in[0];
        $remainChick        = $projectChickin->total_chickin;
        $fcrStandard        = collect($project->fcr->fcr_standard)->where('day', 0)->first();
        $lastWeight         = $fcrStandard->weight;
        $currentWeight      = $recordings->recording_bw[0]->value;
        $pakanRecord        = collect($recordings->recording_stock)->filter(function($recordingStock) {
            return optional($recordingStock->product_warehouse)
                ->product
                ->name === 'Pakan';
        })->first();
        $cumIntake = ($pakanRecord->decrease * 1000) / $remainChick; // 1kg = 1000gram

        if ($lastDay > 0) {
            $lastRecording = collect($project->recording)->where('day', $lastDay)->first();
            $lastDailyDepletion += $lastRecording->total_depletion;
            $lastWeight = $lastRecording->recording_bw[0]->value;
            $remainChick -= $lastRecording->cum_depletion;
            $cumIntake = $lastRecording->cum_intake + (($pakanRecord->decrease * 1000) / $remainChick);
        }

        $recordings->update([
            'total_chick'          => $remainChick,
            'total_depletion'      => $totalDecrese,
            'cum_depletion'        => $totalDecrese + $lastDailyDepletion,
            'daily_depletion_rate' => $totalDecrese                        / $remainChick                   * 100,
            'cum_depletion_rate'   => ($totalDecrese + $lastDailyDepletion) / $projectChickin->total_chickin * 100,
            'daily_gain'           => $currentWeight - $lastWeight,
            'avg_daily_gain'       => ($currentWeight - $fcrStandard->weight) / $recordings->day,
            'cum_intake'           => $cumIntake,
            'fcr_value'            => $cumIntake / $currentWeight,
        ]);
    }

    public function detail(Request $req)
    {
        try {
            $data  = $this->getById($req->id);
            $param = [
                'title' => 'Project > Recording > Detail',
                'data'  => $data,
            ];

            return view('project.recording.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function getById($recordingId)
    {
        $data = Recording::with([
            'recording_stock.product_warehouse.product.uom',
            'recording_nonstock.nonstock.uom',
            'recording_bw.recordingBwList',
            'recording_depletion.product_warehouse.product',
            'recording_egg.product_warehouse.product.uom',
            'project',
            'project.fcr.fcr_standard',
        ])->findOrFail($recordingId);

        return $data;
    }

    public function edit(Request $req)
    {
        try {
            $data = $this->getById($req->id);
            if ($data->revision_status !== 2) {
                return redirect()->back()->with('error', 'Data recording tidak bisa dirubah, silahkan ajukan perubahan data')->withInput();
            }
            $param = [
                'title' => 'Project > Recording > Ubah',
                'data'  => $data,
            ];

            return view('project.recording.add', $param);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function revisionApproval(Request $req)
    {
        try {
            $id        = $req->id;
            $status    = $req->revision_status;
            $recording = Recording::findOrFail($id);
            $recording->update([
                'revision_status' => $status,
            ]);

            $message = 'Perubahan data berhasil disetujui';
            if ($status == 4) {
                $message = 'Perubahan berhasil ditolak';
            }
            $success = ['success' => $message];

            return redirect()->route('project.recording.index')->with($success);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function revisionSubmission(Request $req)
    {
        try {
            $id        = $req->id;
            $recording = Recording::findOrFail($id);
            $document  = '';
            if ($req->has('document_revision')) {
                $docUrl = FileHelper::upload($req->file('document_revision'), constants::REVISION_DOC_PATH);
                if (! $docUrl['status']) {
                    return redirect()->back()->with('error', $docUrl['message'])->withInput();
                }
                $document = $docUrl['url'];
            }

            $recording->update([
                'revision_status'   => 1,
                'document_revision' => $document,
            ]);

            $success = ['success' => 'Perubahan berhasil diajukan'];

            return redirect()->route('project.recording.index')->with($success);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
