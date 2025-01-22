<?php

namespace App\Http\Controllers\Project;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Warehouse;
use App\Models\Inventory\ProductWarehouse;
use App\Models\Project\Project;
use App\Models\Project\ProjectChickIn;
use DB;
use Illuminate\Http\Request;

class ChickinController extends Controller
{
    public function index(Request $req)
    {
        try {
            $data  = Project::with(['project_chick_in', 'kandang', 'product_category'])->get();
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

            $docInventoryQty = 0;
            $warehouse       = Warehouse::where('kandang_id', $project->kandang_id)->first();
            if ($warehouse) {
                $productWarehouse = ProductWarehouse::whereHas(
                    'product.product_category',
                    function($query) {
                        $query->whereIn('category_code', ['BRO', 'LYR']);
                    }
                )
                    ->where('warehouse_id', $warehouse->warehouse_id)
                    ->first();

                if ($productWarehouse) {
                    $docInventoryQty += $productWarehouse->quantity;
                }
            }

            if ($docInventoryQty == 0) {
                return redirect()->back()->with('error', 'Project ini belum melakukan pembelian/penerimaan produk');
            }

            $param = [
                'title'       => 'Project > chick-in > Tambah',
                'data'        => $project,
                'chickin_qty' => $docInventoryQty,
            ];

            if ($req->isMethod('post')) {
                $input      = $req->all();
                $dataInsert = $input['chick_in'] ?? [];
                if (count($dataInsert) > 0) {
                    foreach ($dataInsert as $key => $value) {
                        $dataInsert[$key]['project_id']    = $req->id;
                        $dataInsert[$key]['total_chickin'] = str_replace('.', '', $value['total_chickin']);
                        $dataInsert[$key]['chickin_date']  = date('Y-m-d', strtotime($value['chickin_date']));
                        $document                          = '';
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
            $project = Project::with(['kandang', 'product_category'])
                ->with('project_chick_in', function($query) {
                    $query->with('supplier');
                })
                ->findOrFail($req->id);

            $param = [
                'title'       => 'Project > chick-in > Edit',
                'data'        => $project,
                'chickin_qty' => $project->project_chick_in[0]->total_chickin,
            ];

            if ($req->isMethod('post')) {
                $input      = $req->all();
                $dataInsert = $input['chick_in'] ?? [];
                if (count($dataInsert) > 0) {
                    foreach ($dataInsert as $key => $value) {
                        $dataInsert[$key]['project_id']    = $req->id;
                        $dataInsert[$key]['total_chickin'] = str_replace('.', '', $value['total_chickin']);
                        $dataInsert[$key]['chickin_date']  = date('Y-m-d', strtotime($value['chickin_date']));
                        $document                          = '';
                        $existingDoc                       = $project->project_chick_in[$key]->travel_letter_document ?? null;
                        if ($existingDoc && is_string($value['travel_letter_document'] ?? false)) {
                            $document = $existingDoc;
                        } elseif (isset($value['travel_letter_document'])) {
                            $docUrl = FileHelper::upload($value['travel_letter_document'], constants::CHICKIN_DOC_PATH);
                            if (! $docUrl['status']) {
                                return redirect()->back()->with('error', $docUrl['message'].' '.$value['travel_letter_document'])->withInput();
                            }
                            $document = $docUrl['url'];
                        }
                        $dataInsert[$key]['travel_letter_document'] = $document;
                    }

                    DB::transaction(function() use ($req, $dataInsert) {
                        ProjectChickIn::where('project_id', $req->id)->delete();
                        ProjectChickIn::insert($dataInsert);
                    });

                } else {
                    return redirect()->back()->with('error', 'Data chick in tidak boleh kosong');
                }

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('project.chick-in.detail', $req->id)->with($success);
            }

            return view('project.chick-in.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve(Request $req)
    {
        try {
            $project = Project::with('project_chick_in')->findOrFail($req->id);
            if (count($project->project_chick_in) === 0) {
                return redirect()->back()->with('error', 'Data chick in belum diisi');
            }
            if (! $req->first_day_old_chick) {
                return redirect()->back()->with('error', 'Tanggal umur 1 hari ayam harus diisi');
            }
            $project->update([
                'chickin_status'        => array_search('Sudah', Constants::PROJECT_CHICKIN_STATUS),
                'chickin_approval_date' => date('Y-m-d H:i:s'),
                'first_day_old_chick'   => date('Y-m-d H:i:s', strtotime($req->first_day_old_chick)),
            ]);

            $success = ['success' => 'Data berhasil disetujui'];

            return redirect()->route('project.chick-in.detail', $req->id)->with($success);
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
