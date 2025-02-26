<?php

namespace App\Http\Controllers\Project;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Area;
use App\Models\DataMaster\Company;
// use App\Models\DataMaster\Fcr;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Location;
use App\Models\DataMaster\ProductCategory;
use App\Models\DataMaster\Uom;
use App\Models\Project\Project;
use App\Models\Project\ProjectBudget;
use App\Models\Project\ProjectChickIn;
use App\Models\Project\Recording;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ListController extends Controller
{
    private const VALIDATION_RULES = [
        'product_category_id' => 'required',
        'farm_type'           => 'required',
        'period'              => 'required',
        'fcr_id'              => 'required',
        'standard_mortality'  => 'required',
        // 'target_depletion'    => 'required',
    ];

    private const VALIDATION_MESSAGES = [
        'product_category_id' => 'Kategori Produk tidak boleh kosong',
        'farm_type'           => 'Tipe Kandang tidak boleh kosong',
        'period'              => 'Periode tidak boleh kosong',
        'fcr_id'              => 'FCR tidak boleh kosong',
        'standard_mortality'  => 'Standar Mortalitas tidak boleh kosong',
        // 'target_depletion'    => 'Target Deplesi tidak boleh kosong',
    ];

    public function index(Request $req)
    {
        try {
            $data      = Project::with(['kandang.location', 'product_category']);
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
                ->orderBy('project_id', 'DESC')
                ->paginate($rows);
            $data->appends($arrAppend);

            $param = [
                'title'          => 'Project > List',
                'data'           => $data,
                'type'           => Constants::KANDANG_TYPE,
                'status_chickin' => Constants::PROJECT_CHICKIN_STATUS,
                'status_project' => Constants::PROJECT_STATUS,
            ];

            return view('project.list.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title'              => 'Project > List > Tambah',
                'type'               => Constants::KANDANG_TYPE,
                'recording_interval' => Constants::RECORDING_INTERVAL,
            ];

            if ($req->isMethod('post')) {
                $validator = Validator::make($req->all(), self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
                $input     = $req->all();
                if ($validator->fails()) {
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find($req->input('company_id'))->name;
                    }
                    if (isset($input['area_id'])) {
                        $input['area_name'] = Area::find($req->input('area_id'))->name;
                    }
                    if (isset($input['location_id'])) {
                        $input['location_name'] = Location::find($req->input('location_id'))->name;
                    }
                    if (isset($input['product_category_id'])) {
                        $input['product_category_name'] = ProductCategory::find($req->input('product_category_id'))->name;
                    }
                    if (isset($input['recording'])) {
                        foreach ($input['recording'] as $key => $value) {
                            $input['recording'][$key]['uom_name'] = Uom::find($value['uom_id'])->name;
                        }
                    }

                    return redirect()->back()
                        ->withErrors($validator);
                }
                if (! $req->has('kandang_id')) {
                    return redirect()->back()->with('error', 'Data kandang harus dipilih');
                }

                if (! $req->has('budget')) {
                    return redirect()->back()->with('error', 'Data Anggaran tidak boleh kosong');
                }

                DB::transaction(function() use ($req) {
                    $arrKandang = $req->input('kandang_id');
                    for ($i = 0; $i < count($arrKandang); $i++) {
                        $dtKandang = Kandang::with('user')->find($arrKandang[$i]);
                        $project   = Project::create([
                            'product_category_id' => $req->input('product_category_id'),
                            'kandang_id'          => $arrKandang[$i],
                            'capacity'            => $dtKandang->capacity ?? 0,
                            'farm_type'           => $req->input('farm_type'),
                            'fcr_id'              => $req->input('fcr_id'),
                            'standard_mortality'  => $req->input('standard_mortality'),
                            'period'              => $req->input('period'),
                            'pic'                 => $dtKandang->user->name ?? '',
                            'total_budget'        => $req->input('total_budget'),
                            'chickin_status'      => array_search('Belum', Constants::PROJECT_CHICKIN_STATUS),
                            'project_status'      => array_search('Pengajuan', Constants::PROJECT_STATUS),
                            'created_by'          => Auth::user()->user_id ?? '',
                        ]);

                        $projectId = $project->project_id;
                        if ($req->has('budget')) {
                            $arrBudget = $req->input('budget');
                            foreach ($arrBudget as $key => $value) {
                                $arrBudget[$key]['nonstock_id'] = null;
                                $arrBudget[$key]['product_id']  = null;
                                if ($value['stock_type'] == 1) {
                                    $arrBudget[$key]['product_id'] = $value['product_id'];
                                } else {
                                    $arrBudget[$key]['nonstock_id'] = $value['nonstock_id'];
                                }
                                $arrBudget[$key]['qty']        = str_replace('.', '', str_replace(',', '.', $value['qty']));
                                $arrBudget[$key]['price']      = str_replace('.', '', str_replace(',', '.', $value['price']));
                                $arrBudget[$key]['total']      = $value['total-input'];
                                $arrBudget[$key]['project_id'] = $projectId;
                                unset($arrBudget[$key]['product_category_id']);
                                unset($arrBudget[$key]['stock_type']);
                                unset($arrBudget[$key]['total-input']);
                            }
                            ProjectBudget::insert($arrBudget);
                        }
                    }
                });

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('project.list.index')->with($success);
            }

            return view('project.list.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(Request $req)
    {
        try {
            $project = Project::with(['kandang', 'product_category', 'project_phase', 'project_budget', 'project_recording', 'project_recording.uom'])->findOrFail($req->id);
            $param   = [
                'title'              => 'Project > List > Ubah',
                'data'               => $project,
                'type'               => Constants::KANDANG_TYPE,
                'recording_interval' => Constants::RECORDING_INTERVAL,
            ];

            if ($req->isMethod('post')) {
                $validator = Validator::make($req->all(), self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                if (! $req->has('budget')) {
                    return redirect()->back()->with('error', 'Data Anggaran tidak boleh kosong')->withInput($req->all());
                }

                DB::transaction(function() use ($req, $project) {
                    $project->update([
                        'product_category_id' => $req->input('product_category_id'),
                        'kandang_id'          => $req->input('kandang_id'),
                        'capacity'            => $req->input('capacity'),
                        'farm_type'           => $req->input('farm_type'),
                        'fcr_id'              => $req->input('fcr_id'),
                        'period'              => $req->input('period'),
                        'pic'                 => $req->input('pic'),
                        'total_budget'        => $req->input('total_budget'),
                    ]);

                    $projectId = $project->project_id;
                    if ($req->has('budget')) {
                        ProjectBudget::where('project_id', $projectId)->delete();
                        $arrBudget = $req->input('budget');
                        foreach ($arrBudget as $key => $value) {
                            $arrBudget[$key]['qty']        = str_replace('.', '', str_replace(',', '.', $value['qty']));
                            $arrBudget[$key]['price']      = str_replace('.', '', str_replace(',', '.', $value['price']));
                            $arrBudget[$key]['project_id'] = $projectId;
                        }
                        ProjectBudget::insert($arrBudget);
                    }
                });

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('project.list.detail', $req->id)->with($success);
            }

            return view('project.list.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function copy(Request $req)
    {
        try {
            $project = Project::with(['kandang', 'product_category', 'project_phase', 'project_budget', 'project_recording', 'project_recording.uom'])->findOrFail($req->id);
            $param   = [
                'title'              => 'Project > List > Copy',
                'data'               => $project,
                'type'               => Constants::KANDANG_TYPE,
                'recording_interval' => Constants::RECORDING_INTERVAL,
                'copy'               => true,
            ];

            return view('project.list.copy', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function detail(Request $req)
    {
        try {
            $project = Project::with(['kandang', 'product_category', 'project_phase', 'project_budget', 'fcr', 'fcr.fcr_standard', 'project_budget.product', 'project_budget.nonstock', 'project_recording', 'project_recording.uom', 'closingby'])->findOrFail($req->id);
            $param   = [
                'title' => 'Project > List > Detail',
                'data'  => $project,
                'type'  => Constants::KANDANG_TYPE,
            ];

            return view('project.list.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve(Request $req)
    {
        try {
            if (! $req->has('project_ids')) {
                return redirect()->back()->with('error', 'Pilih project terlebih dahulu');
            }
            $arrProjectId = $req->input('project_ids');
            for ($i = 0; $i < count($arrProjectId); $i++) {
                $project = Project::findOrFail($arrProjectId[$i]);
                $kandang = Kandang::find($project->kandang_id);
                $project->update([
                    'project_status' => array_search('Aktif', Constants::PROJECT_STATUS),
                    'approval_date'  => date('Y-m-d H:i:s'),
                ]);

                if ($kandang) {
                    $kandang->update([
                        'project_status' => true,
                    ]);
                }

                \Log::info('project_status '.$project->project_status);
            }

            $success = ['success' => 'Project berhasil disetujui'];

            return redirect()->route('project.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function closing(Request $req)
    {
        try {
            DB::beginTransaction();
            $project = Project::with(['recording.recording_depletion.product_warehouse'])->findOrFail($req->id);
            $kandang = Kandang::find($project->kandang_id);

            $kandang->update([
                'project_status' => false,
            ]);
            $project->update([
                'project_status' => array_search('Selesai', Constants::PROJECT_STATUS),
                'closing_date'   => date('Y-m-d H:i:s'),
                'closing_by'     => Auth::user()->user_id ?? '',
            ]);
            DB::commit();
            $success = ['success' => 'Closing project berhasil'];

            return redirect()->route('project.list.index')->with($success);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            DB::beginTransaction();
            $project = Project::findOrFail($req->id);
            $kandang = Kandang::find($project->kandang_id);
            $kandang->update([
                'project_status' => false,
            ]);
            $project->delete();
            $projectChickIn = ProjectChickIn::where('project_id', $req->id);
            $projectChickIn->delete();
            $projectRecording = Recording::where('project_id', $req->id);
            $projectRecording->delete();
            DB::commit();
            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('project.list.index')->with($success);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchProject(Request $request)
    {
        $search   = $request->input('q');
        $projects = Project::with(['kandang', 'recording', 'kandang.user', 'kandang.warehouse', 'product_category', 'project_budget', 'project_chick_in', 'fcr', 'fcr.fcr_standard'])
            ->whereHas('kandang', function($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
        $queryParams = $request->query();
        foreach ($queryParams as $key => $value) {
            if ($key === 'project_status_not') {
                $projects->whereNot('project_status', $value);
            } else {
                if ($key === 'location_id') {
                    $projects->whereHas('kandang', function($query) use ($value) {
                        $query->where('kandang.location_id', $value);
                    });
                } elseif ($key !== 'q') {
                    $projects->where($key, $value);
                }
            }
        }

        $projects = $projects->get();

        return response()->json($projects->map(function($project) {
            return ['id' => $project->project_id, 'text' => $project->kandang->name, 'data' => $project];
        }));
    }

    public function searchPeriod(Request $request)
    {
        $search   = $request->input('q');
        $projects = Project::with(['kandang', 'kandang.warehouse', 'product_category'])
            ->whereHas('kandang', function($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
        $queryParams = $request->query();
        foreach ($queryParams as $key => $value) {
            if ($key === 'project_status_not') {
                $projects->whereNot('project_status', $value);
            } else {
                if ($key === 'company_id') {
                    $projects->whereHas('kandang', function($query) use ($value) {
                        $query->where('company_id', $value);
                    });
                } elseif ($key !== 'q') {
                    $projects->where($key, $value);
                }
            }
        }

        $projects = $projects->get();

        return response()->json(
            $projects
                ->map(function($project) {
                    return ['id' => $project->period, 'text' => $project->period];
                })
                ->unique('id')
                ->values()
        );
    }

    public function searchBudget(Request $request)
    {
        $budgets = ProjectBudget::with(['product', 'nonstock'])
            ->where('project_id', $request->project_id)
            ->get();

        return response()->json($budgets);
    }
}
