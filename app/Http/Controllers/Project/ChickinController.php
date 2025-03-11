<?php

namespace App\Http\Controllers\Project;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Warehouse;
use App\Models\Inventory\ProductWarehouse;
use App\Models\Project\Project;
use App\Models\Project\ProjectChickIn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChickinController extends Controller
{
    public function index(Request $req)
    {
        try {
            $data = Project::with(['project_chick_in', 'kandang', 'product_category'])
                ->join('project_chickin', 'projects.project_id', '=', 'project_chickin.project_id');
            $request   = $req->all();
            $rows      = $req->has('rows') ? $req->get('rows') : 10;
            $arrAppend = [
                'rows' => $rows,
                'page' => 1,
            ];

            if (isset($request['kandang']['company_id'])) {
                $arrAppend['kandang[company_id]'] = $request['kandang']['company_id'];
            }

            if (isset($request['kandang']['location']['area_id'])) {
                $arrAppend['kandang[location][area_id]'] = $request['kandang']['location']['area_id'];
            }

            if (isset($request['kandang']['location_id'])) {
                $arrAppend['kandang[location_id]'] = $request['kandang']['location_id'];
            }

            foreach ($request as $key => $value) {
                if ($value > 0) {
                    if (! in_array($key, ['rows', 'page'])) {
                        if (is_array($request[$key])) {
                            $data = $data->whereHas($key, function($query) use ($value) {
                                foreach ($value as $relationKey => $relationValue) {
                                    if (is_array($value[$relationKey])) {
                                        // Handle nested relationships (e.g., kandang.location.area_id)
                                        $query->whereHas($relationKey, function($subQuery) use ($relationValue) {
                                            foreach ($relationValue as $subKey => $subValue) {
                                                $subQuery->where($subKey, $subValue);
                                            }
                                        });
                                    } else {
                                        // Direct relationship column filtering
                                        $query->where($relationKey, $relationValue);
                                    }
                                }
                            });
                        } else {
                            $data            = $data->where($key, $value);
                            $arrAppend[$key] = $value;
                        }
                    }
                }
            }

            $data = $data
                ->orderBy('project_chickin.project_chickin_id', 'DESC')
                ->paginate($rows);
            $data->appends($arrAppend);

            $param = [
                'title' => 'Project > Chick-In',
                'data'  => $data,
            ];

            return view('project.chick-in.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function detail(Request $req)
    {
        try {
            $data  = Project::with(['project_chick_in', 'kandang', 'product_category'])->findOrFail($req->id);
            $param = [
                'title' => 'Project > Chick-In > Detail',
                'data'  => $data,
            ];

            return view('project.chick-in.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $project = Project::with(['kandang', 'kandang.warehouse', 'product_category', 'project_chick_in'])->findOrFail($req->id);
            if (! $project->approval_date) {
                return redirect()->back()->with('error', 'Project ini belum disetujui');
            }

            $docInventoryQty      = 0;
            $travelNumber         = '';
            $travelNumberDocument = '';
            $receivedDate         = '';
            $supplierId           = '';
            $supplierName         = '';
            $warehouse            = Warehouse::where('kandang_id', $project->kandang_id)->first();
            if ($warehouse) {
                $productWarehouse = ProductWarehouse::with('stock_availability.purchase_item_reception.purchase_item.purchase.supplier')->whereHas(
                    'product.product_category',
                    function($query) {
                        $query->whereIn('category_code', ['BRO', 'LYR', 'GPS', 'PRS', 'FRS']);
                    }
                )
                    ->where('warehouse_id', $warehouse->warehouse_id)
                    ->first();

                if ($productWarehouse->stock_availability && $productWarehouse->stock_availability[0]->purchase_item_reception) {
                    $receivedItem         = $productWarehouse->stock_availability[0]->purchase_item_reception;
                    $travelNumber         = $receivedItem->travel_number;
                    $receivedDate         = $receivedItem->received_date;
                    $supplierId           = $receivedItem->purchase_item->purchase->supplier_id;
                    $supplierName         = $receivedItem->purchase_item->purchase->supplier->name;
                    $travelNumberDocument = $receivedItem->travel_number_document ?? '';
                }
                if ($productWarehouse) {
                    $docInventoryQty += $productWarehouse->quantity;
                }
            }

            if ($docInventoryQty == 0) {
                return redirect()->back()->with('error', 'Project ini belum melakukan pembelian/penerimaan produk');
            }

            $param = [
                'title'                  => 'Project > chick-in > Tambah',
                'data'                   => $project,
                'chickin_qty'            => $docInventoryQty,
                'travel_number'          => $travelNumber,
                'travel_number_document' => $travelNumberDocument,
                'received_date'          => date('d-M-Y', strtotime($receivedDate)),
                'supplier_id'            => $supplierId,
                'supplier_name'          => $supplierName,
            ];

            if ($req->isMethod('post')) {
                $input      = $req->all();
                $dataInsert = $input['chick_in'] ?? [];
                if (count($dataInsert) > 0) {
                    foreach ($dataInsert as $key => $value) {
                        $dataInsert[$key]['project_id']    = $req->id;
                        $dataInsert[$key]['total_chickin'] = str_replace('.', '', $value['total_chickin']);
                        $dataInsert[$key]['chickin_date']  = date('Y-m-d', strtotime($value['chickin_date']));
                        $document                          = $travelNumberDocument;
                        if (isset($value['travel_letter_document'])) {
                            $docUrl = FileHelper::upload($value['travel_letter_document'], constants::CHICKIN_DOC_PATH);
                            if (! $docUrl['status']) {
                                return redirect()->back()->with('error', $docUrl['message'].' '.$value['travel_letter_document'])->withInput();
                            }
                            $document = $docUrl['url'];
                        }
                        $dataInsert[$key]['travel_letter_document'] = $document;
                    }

                    ProjectChickIn::insert($dataInsert);
                    $project->update([
                        'chickin_status' => array_search('Pengajuan', Constants::PROJECT_CHICKIN_STATUS),
                    ]);
                } else {
                    return redirect()->back()->with('error', 'Data chick in tidak boleh kosong');
                }

                $success = ['success' => 'Data berhasil disimpan'];

                return redirect()->route('project.chick-in.detail', $req->id)->with($success);
            }

            return view('project.chick-in.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $projectChickIn = ProjectChickin::with('project.recording')->find($req->id);
            if (! $projectChickIn) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            }

            $newDate          = Carbon::parse($req->chickin_date);
            $projectRecording = $projectChickIn->project->recording ?? [];
            foreach ($projectRecording as $recording) {
                $newRecordDatetime          = $newDate->copy()->addDays($recording->day);
                $recording->record_datetime = $newRecordDatetime->toDateTimeString();
                $recording->save();
            }

            $projectChickIn->update([
                'chickin_date' => date('Y-m-d', strtotime($req->chickin_date)),
            ]);

            $success = ['success' => 'Data berhasil dirubah'];

            return redirect()->route('project.chick-in.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve(Request $req)
    {
        try {
            if (! $req->has('project_ids')) {
                return redirect()->back()->with('error', 'Pilih Project terlebih dahulu');
            }
            $arrProjectId = $req->input('project_ids');
            for ($i = 0; $i < count($arrProjectId); $i++) {
                $project = Project::with('project_chick_in')->findOrFail($arrProjectId[$i]);
                if (count($project->project_chick_in) === 0) {
                    return redirect()->back()->with('error', 'Data chick in belum diisi');
                }

                $project->update([
                    'chickin_status'        => array_search('Sudah', Constants::PROJECT_CHICKIN_STATUS),
                    'chickin_approval_date' => date('Y-m-d H:i:s'),
                ]);
            }

            $success = ['success' => 'Data berhasil disetujui'];

            return redirect()->route('project.chick-in.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $projectChickIn = ProjectChickIn::where('project_id', $req->id);
            $projectChickIn->delete();
            $project = Project::findOrFail($req->id);
            $project->update([
                'chickin_status'        => array_search('Belum', Constants::PROJECT_CHICKIN_STATUS),
                'chickin_approval_date' => null,
            ]);

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('project.chick-in.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
