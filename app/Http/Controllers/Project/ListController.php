<?php

namespace App\Http\Controllers\Project;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Area;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Fcr;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Location;
use App\Models\DataMaster\ProductCategory;
use App\Models\DataMaster\Uom;
use App\Models\Project\Project;
use App\Models\Project\ProjectBudget;
use App\Models\Project\ProjectChickIn;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ListController extends Controller
{
    private const VALIDATION_RULES = [
        'product_category_id' => 'required',
        'kandang_id'          => 'required',
        'capacity'            => 'required',
        'farm_type'           => 'required',
        'period'              => 'required',
        'pic'                 => 'required',
        'fcr_id'              => 'required',
        'target_depletion'    => 'required',
    ];

    private const VALIDATION_MESSAGES = [
        'product_category_id' => 'Kategori Produk tidak boleh kosong',
        'kandang_id'          => 'Kandang tidak boleh kosong',
        'capacity'            => 'Kapasitas tidak boleh kosong',
        'farm_type'           => 'Tipe Kandang tidak boleh kosong',
        'period'              => 'Periode tidak boleh kosong',
        'pic'                 => 'Penaggung jawab tidak boleh kosong',
        'fcr_id'              => 'FCR tidak boleh kosong',
        'target_depletion'    => 'Target Deplesi tidak boleh kosong',
    ];

    public function index(Request $req)
    {
        try {
            $data  = Project::with(['kandang', 'product_category'])->get();
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
                    if (isset($input['kandang_id'])) {
                        $input['kandang_name'] = Kandang::find($req->input('kandang_id'))->name;
                    }
                    if (isset($input['fcr_id'])) {
                        $fcr               = Fcr::with('uom')->find($req->input('fcr_id'));
                        $input['fcr_name'] = $fcr->name.' - '.$fcr->value.' '.$fcr->uom->name;
                    }
                    if (isset($input['recording'])) {
                        foreach ($input['recording'] as $key => $value) {
                            $input['recording'][$key]['uom_name'] = Uom::find($value['uom_id'])->name;
                        }
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                if (! $req->has('budget')) {
                    return redirect()->back()->with('error', 'Data Anggaran tidak boleh kosong')->withInput($input);
                }

                DB::transaction(function() use ($req) {
                    $project = Project::create([
                        'product_category_id' => $req->input('product_category_id'),
                        'kandang_id'          => $req->input('kandang_id'),
                        'capacity'            => $req->input('capacity'),
                        'farm_type'           => $req->input('farm_type'),
                        'period'              => $req->input('period'),
                        'pic'                 => $req->input('pic'),
                        'fcr_id'              => $req->input('fcr_id'),
                        'target_depletion'    => $req->input('target_depletion'),
                        'total_budget'        => $req->input('total_budget'),
                        'chickin_status'      => array_search('Belum', Constants::PROJECT_CHICKIN_STATUS),
                        'project_status'      => array_search('Pengajuan', Constants::PROJECT_STATUS),
                        'created_by'          => Auth::user()->user_id ?? '',
                    ]);

                    $kandang = Kandang::findOrFail($project->kandang_id);
                    $kandang->update([
                        'project_status' => true,
                    ]);

                    $projectId = $project->project_id;
                    if ($req->has('budget')) {
                        $arrBudget = $req->input('budget');
                        foreach ($arrBudget as $key => $value) {
                            $arrBudget[$key]['qty']        = str_replace(',', '', $value['qty']);
                            $arrBudget[$key]['price']      = str_replace(',', '', $value['price']);
                            $arrBudget[$key]['project_id'] = $projectId;
                        }
                        ProjectBudget::insert($arrBudget);
                    }
                });

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('project.list.index')->with($success);
            }

            return view('project.list.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $project = Project::with(['kandang', 'product_category', 'fcr', 'project_phase', 'project_budget', 'project_recording', 'project_recording.uom'])->findOrFail($req->id);
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
                    return redirect()->back()->with('error', 'Data Anggaran tidak boleh kosong')->withInput($input);
                }

                DB::transaction(function() use ($req, $project) {
                    $project->update([
                        'product_category_id' => $req->input('product_category_id'),
                        'kandang_id'          => $req->input('kandang_id'),
                        'capacity'            => $req->input('capacity'),
                        'farm_type'           => $req->input('farm_type'),
                        'period'              => $req->input('period'),
                        'pic'                 => $req->input('pic'),
                        'fcr_id'              => $req->input('fcr_id'),
                        'target_depletion'    => $req->input('target_depletion'),
                        'total_budget'        => $req->input('total_budget'),
                    ]);

                    $projectId = $project->project_id;
                    if ($req->has('budget')) {
                        ProjectBudget::where('project_id', $projectId)->delete();
                        $arrBudget = $req->input('budget');
                        foreach ($arrBudget as $key => $value) {
                            $arrBudget[$key]['qty']        = str_replace(',', '', $value['qty']);
                            $arrBudget[$key]['price']      = str_replace(',', '', $value['price']);
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
            $project = Project::with(['kandang', 'product_category', 'fcr', 'project_phase', 'project_budget', 'project_recording', 'project_recording.uom'])->findOrFail($req->id);
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
            $project = Project::with(['kandang', 'product_category', 'fcr', 'project_phase', 'project_budget', 'project_recording', 'project_recording.uom'])->findOrFail($req->id);
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
            $project = Project::findOrFail($req->id);
            $project->update([
                'project_status' => array_search('Aktif', Constants::PROJECT_STATUS),
                'approval_date'  => date('Y-m-d H:i:s'),
            ]);

            $success = ['success' => 'Project berhasil disetujui'];

            return redirect()->route('project.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $project = Project::findOrFail($req->id);
            $kandang = Kandang::find($project->kandang_id);
            $kandang->update([
                'project_status' => false,
            ]);
            $project->delete();
            $projectChickIn = ProjectChickIn::where('project_id', $req->id);
            $projectChickIn->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('project.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchProject(Request $request)
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
}
