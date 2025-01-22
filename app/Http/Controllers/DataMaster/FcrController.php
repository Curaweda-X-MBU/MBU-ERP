<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Fcr;
use App\Models\DataMaster\FcrStandard;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FcrController extends Controller
{
    private const VALIDATION_RULES = [
        'company_id' => 'required',
    ];

    private const VALIDATION_MESSAGES = [
        'name.required'       => 'Nama FCR tidak boleh kosong',
        'name.unique'         => 'Nama FCR sudah digunakan',
        'company_id.required' => 'Unit bisnis tidak boleh kosong',
    ];

    public function index(Request $req)
    {
        try {

            $param = [
                'title' => 'Master Data > FCR',
                'data'  => Fcr::with(['company', 'fcr_standard'])->get(),
            ];

            return view('data-master.fcr.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ])->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Master Data > FCR > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required',
                    Rule::unique('fcr')->where(function($query) use ($req) {
                        $query->where('company_id', $req->company_id);
                        $query->whereNull('deleted_at');

                        return $query;
                    }),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->input();
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find($req->input('company_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                DB::beginTransaction();
                $fcr = Fcr::create([
                    'company_id' => $req->input('company_id'),
                    'name'       => $req->input('name'),
                    'created_by' => auth()->user()->user_id,
                ]);

                $arrStandard = $req->input('fcr_standard') ?? [];
                if (count($arrStandard) > 0) {
                    foreach ($arrStandard as $key => $value) {
                        $weight       = str_replace('.', '', $value['weight']);
                        $dailyGain    = str_replace('.', '', $value['daily_gain']);
                        $avgDailyGain = str_replace('.', '', $value['avg_daily_gain']);
                        $dailyIntake  = str_replace('.', '', $value['daily_intake']);
                        $cumIntake    = str_replace('.', '', $value['cum_intake']);
                        FcrStandard::create([
                            'fcr_id'         => $fcr->fcr_id,
                            'day'            => $value['day'],
                            'weight'         => $weight,
                            'daily_gain'     => $dailyGain,
                            'avg_daily_gain' => $avgDailyGain,
                            'daily_intake'   => $dailyIntake,
                            'cum_intake'     => $cumIntake,
                            'fcr'            => $cumIntake / $weight,
                        ]);
                    }
                } else {
                    DB::rollback();
                    throw ValidationException::withMessages([
                        'fcr_standard' => 'Standar FCR tidak boleh kosong',
                    ]);
                }

                DB::commit();
                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('data-master.fcr.index')->with($success);
            }

            return view('data-master.fcr.add', $param);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $fcr   = Fcr::with(['company', 'fcr_standard'])->findOrFail($req->id);
            $param = [
                'title' => 'Master Data > FCR > Ubah',
                'data'  => $fcr,
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required',
                    Rule::unique('fcr')->where(function($query) use ($req) {
                        $query->where('company_id', $req->company_id);
                        $query->whereNull('deleted_at');

                        return $query;
                    })->ignore($fcr->fcr_id, 'fcr_id'),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                DB::beginTransaction();
                $fcr->update([
                    'company_id' => $req->input('company_id'),
                    'name'       => $req->input('name'),
                ]);

                $arrStandard = $req->input('fcr_standard') ?? [];
                if (count($arrStandard) > 0) {
                    FcrStandard::where('fcr_id', $fcr->fcr_id)->delete();
                    foreach ($arrStandard as $key => $value) {
                        $weight       = str_replace('.', '', $value['weight']);
                        $dailyGain    = str_replace('.', '', $value['daily_gain'] ?? 0);
                        $avgDailyGain = str_replace('.', '', $value['avg_daily_gain'] ?? 0);
                        $dailyIntake  = str_replace('.', '', $value['daily_intake'] ?? 0);
                        $cumIntake    = str_replace('.', '', $value['cum_intake'] ?? 0);
                        FcrStandard::create([
                            'fcr_id'         => $fcr->fcr_id,
                            'day'            => $value['day'],
                            'weight'         => $weight,
                            'daily_gain'     => $dailyGain,
                            'avg_daily_gain' => $avgDailyGain,
                            'daily_intake'   => $dailyIntake,
                            'cum_intake'     => $cumIntake,
                            'fcr'            => $cumIntake / $weight,
                        ]);
                    }
                } else {
                    DB::rollback();
                    throw ValidationException::withMessages([
                        'fcr_standard' => 'Standar FCR tidak boleh kosong',
                    ]);
                }

                DB::commit();
                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('data-master.fcr.index')->with($success);
            }

            return view('data-master.fcr.edit', $param);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            DB::beginTransaction();
            $fcrStd = FcrStandard::where('fcr_id', $req->id);
            $fcr    = Fcr::findOrFail($req->id);
            $fcrStd->delete();
            $fcr->delete();

            DB::commit();
            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('data-master.fcr.index')->with($success);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchFcr(Request $request)
    {
        $search      = $request->input('q');
        $fcrs        = Fcr::where('name', 'like', "%{$search}%");
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            $fcrs->where($key, $value);
        }

        $fcrs = $fcrs->get();

        return response()->json($fcrs->map(function($fcr) {
            return ['id' => $fcr->fcr_id, 'text' => $fcr->name];
        }));
    }

    public function searchFcrStandard(Request $request)
    {
        $data = Fcr::with('fcr_standard')->find($request->fcr_id);

        return response()->json($data);
    }
}
