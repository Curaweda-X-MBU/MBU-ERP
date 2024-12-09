<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants;
use Carbon\Carbon;

use App\Models\Project\Project;
use App\Models\Project\Recording;
use App\Models\Project\RecordingStock;
use App\Models\Project\RecordingNonstock;
use App\Models\Project\RecordingBw;
use App\Models\Project\RecordingBwList;
use App\Models\Project\RecordingDepletion;
use App\Models\Project\RecordingEgg;
use App\Models\Inventory\ProductWarehouse;
use App\Models\Inventory\StockLog;

class RecordingController extends Controller
{
    public function index(Request $req) {
        try {
            $param = [
                'title' => 'Project > Recording',
                'data' => Recording::with(['project'])->get()
            ];

            return view('project.recording.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Project > Recording > Tambah Baru',
            ];
            if ($req->isMethod('post')) {
                $input = $req->all();
                if (!$req->has('stock') ) return redirect()->back()->with('error', 'Persedian harus diisi');
                if (!$req->has('nonstock') ) return redirect()->back()->with('error', 'Non Persedian harus diisi');
                if (!$req->has('bw') ) return redirect()->back()->with('error', 'Body Weight harus diisi');

                DB::beginTransaction();
                $project = Project::with('kandang')->find($input['project_id']);

                $recording = new Recording();
                $recording->project_id = $input['project_id'];
                $strtotime = strtotime($input['record_datetime']);
                $recordDateInput = date('Y-m-d H:i', $strtotime);
                $recording->record_datetime = $recordDateInput;
                $createdAt = Carbon::parse(date('Y-m-d'));
                $recordDate = Carbon::parse(date('Y-m-d', $strtotime));
                $onTime = $createdAt->isSameDay($recordDate)?true:false;
                $recording->on_time = $onTime;
                $recording->status = array_search('Pengajuan', Constants::RECORDING_STATUS);
                $recording->save();

                $generalStock = ProductWarehouse::where([
                    'product_id' => $input['product_id'],
                    'warehouse_id' => $input['warehouse_id']
                ])->first();
                    
                $arrStocks = $input['stock'];
                foreach ($arrStocks as $key => $value) {
                    RecordingStock::create([
                        'recording_id' => $recording->recording_id,
                        'product_warehouse_id' => $generalStock->product_warehouse_id,
                        'decrease_stock' => $value['decrease_stock'],
                    ]);

                    StockLog::triggerStock([
                        'product_id' => $input['product_id'],
                        'stock_date' => date('Y-m-d', $strtotime),
                        'warehouse_id' => $input['warehouse_id'],
                        'decrease' => $value['decrease_stock'],
                        'stocked_by' => 'Recording',
                        'notes' => 'Project '.$project->kandang->name,
                    ]);
                }

                $arrNonstock = $input['nonstock'];
                foreach ($arrNonstock as $key => $value) {
                    $nonstockVal = $value['value'];
                    $replaceNode = str_replace('.', '', $nonstockVal);
                    $inputValue = str_replace(',', '.', $replaceNode);
                    RecordingNonstock::create([
                        'nonstock_id' => $value['nonstock_id'],
                        'value' => $inputValue
                    ]);
                }

                $recordingBw = new RecordingBw();
                $recordingBw->recording_id = $recording->recording_id;
                $recordingBw->avg_weight = $input['avg_weight'];
                $recordingBw->total_chick = $input['total_chick'];
                $recordingBw->total_calc = $input['total_calc'];
                $recordingBw->value = $input['value'];
                $recordingBw->save();

                $arrBw = $input['bw'];
                foreach ($arrBw as $key => $value) {
                    RecordingBwList::create([
                        'recording_bw_id' => $recordingBw->recording_bw_id,
                        'weight' => str_replace(',', '.', str_replace('.', '', $value['weight'])),
                        'total' => str_replace(',', '.', str_replace('.', '', $value['total'])),
                        'weight_calc' => str_replace(',', '.', str_replace('.', '', $value['weight_calc']))
                    ]);
                }

                $docStock = ProductWarehouse::whereHas('product', function ($query) {
                    $query->where('name', Constrants::RECORDING_DOC);
                })->where('warehouse_id', $input['warehouse_id'])->first();
                $depletionDecrease = $input['death'] + $input['culling'];
                RecordingDepletion::create([
                    'recording_id' => $recording->recording_id,
                    'product_warehouse_id' => $docStock->product_warehouse_id,
                    'decrease' => $depletionDecrease,
                    'death' => $input['death'],
                    'culling' => $input['culling'],
                    'afkir' => $input['afkir'],
                    'total_depletion' => $input['total_delpletion'],
                ]);

                StockLog::triggerStock([
                    'product_id' => $docStock->product_id,
                    'stock_date' => date('Y-m-d', $strtotime),
                    'warehouse_id' => $input['warehouse_id'],
                    'decrease' => $depletionDecrease,
                    'stocked_by' => 'Recording',
                    'notes' => 'Project '.$project->kandang->name,
                ]);

                $eggIncrease = (int) $input['increase']??0;
                $eggDecrease = (int) $input['decrease']??0;
                $eggTotal = $eggIncrease-$eggDecrease;
                if ( $eggTotal !== 0  ) {
                    RecordingEgg::create([
                        'recording_id' => $recording->recording_id,
                        'product_warehouse_id' => $generalStock->product_warehouse_id,
                        'increase' => $eggIncrease,
                        'decrease' => $eggDecrease,
                        'big' => $input['big'],
                        'small' => $input['small'],
                        'crack' => $input['crack'],
                        'dirty' => $input['dirty'],
                        'broken' => $input['broken'],
                        'total_egg' => $eggTotal,
                    ]);
    
                    StockLog::triggerStock([
                        'product_id' => $generalStock->product_id,
                        'stock_date' => date('Y-m-d', $strtotime),
                        'warehouse_id' => $input['warehouse_id'],
                        'increase' => $eggIncrease,
                        'decrease' => $eggDecrease,
                        'stocked_by' => 'Recording',
                        'notes' => 'Project '.$project->kandang->name,
                    ]);
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
}
