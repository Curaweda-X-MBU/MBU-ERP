<?php

namespace App\Http\Controllers\Project;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
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

                DB::beginTransaction();
                $project = Project::with([
                    'kandang',
                    'purchase_item.product.product_category',
                ])->find($input['project_id']);
                $docProdId = false;
                $eggProdId = false;

                foreach ($project->purchase_item as $key => $value) {
                    if ($value->product->product_category->category_code
                        && $value->product->product_category->category_code === 'BRO') {
                        $docProdId = $value->product_id;
                    }
                    if ($value->product->product_category->category_code
                        && $value->product->product_category->category_code === 'TLR') {
                        $eggProdId = $value->product_id;
                    }
                }

                $recording                  = new Recording;
                $recording->project_id      = $input['project_id'];
                $strtotime                  = strtotime($input['record_datetime']);
                $recordDateInput            = date('Y-m-d H:i', $strtotime);
                $recording->record_datetime = $recordDateInput;
                $createdAt                  = Carbon::parse(date('Y-m-d'));
                $recordDate                 = Carbon::parse(date('Y-m-d', $strtotime));
                $onTime                     = $createdAt->isSameDay($recordDate) ? true : false;
                $recording->on_time         = $onTime;
                $recording->status          = array_search('Disetujui', Constants::RECORDING_STATUS);
                $recording->save();

                $arrStocks = $input['stock'];
                foreach ($arrStocks as $key => $value) {
                    $generalStock = ProductWarehouse::where([
                        'product_id'   => $value['product_id'],
                        'warehouse_id' => $input['warehouse_id'],
                    ])->first();

                    $valDecrease = str_replace('.', '', $value['decrease_stock']);
                    if ($generalStock && $generalStock->quantity < $valDecrease) {
                        DB::rollback();

                        return redirect()->back()->withErrors($validator)->withInput();
                    }

                    StockLog::triggerStock([
                        'product_id'   => $value['product_id'],
                        'stock_date'   => date('Y-m-d', $strtotime),
                        'warehouse_id' => $input['warehouse_id'],
                        'decrease'     => $valDecrease,
                        'stocked_by'   => 'Recording',
                        'notes'        => 'Persediaan Project '.$project->kandang->name,
                    ]);

                    $generalStock = ProductWarehouse::where([
                        'product_id'   => $value['product_id'],
                        'warehouse_id' => $input['warehouse_id'],
                    ])->first();

                    RecordingStock::create([
                        'recording_id'         => $recording->recording_id,
                        'product_warehouse_id' => $generalStock->product_warehouse_id,
                        'decrease'             => $valDecrease,
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

                    $this->insertRecordingDepletionAndEgg($input['depletions'], $input['warehouse_id'], $docProdId, $strtotime, $project, $recording->recording_id);
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
            }

            foreach ($arrProdStockType as $k => $v) {
                StockLog::triggerStock([
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
