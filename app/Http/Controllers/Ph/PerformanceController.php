<?php

namespace App\Http\Controllers\Ph;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Ph\PhPerformance;
use App\Models\DataMaster\Area;
use App\Models\DataMaster\Location;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Supplier;
use App\Models\DataMaster\Kandang;
use App\Models\UserManagement\User;
use DB;

class PerformanceController extends Controller
{
    private const VALIDATION_RULES = [
        'kandang_id' => 'required',
        'chick_in_date' => 'required',
        'population' => 'required|integer',
        'supplier_id' => 'required',
        'hatchery' => 'required|max:50',
        'death' => 'required|integer',
        'culling' => 'required|integer',
        'bw' => 'required|integer',
        
    ];

    private const VALIDATION_MESSAGES = [
        'kandang_id.required' => 'Kandang tidak boleh kosong',
        'chick_in_date.required' => 'Tanggal Chick In tidak boleh kosong',
        'type.required' => 'Tipe tidak boleh kosong',
        'population.required' => 'Populasi tidak boleh kosong',
        'population.integer' => 'Populasi harus berupa angka',
        'supplier_id.required' => 'Vendor tidak boleh kosong',
        'kandang_id.required' => 'Kandang tidak boleh kosong',
        'death.required' => 'Total mati tidak boleh kosong',
        'death.integer' => 'Total mati harus berupa angka',
        'culling.required' => 'Total culling tidak boleh kosong',
        'culling.integer' => 'Total culling harus berupa angka',
        'hatchery.required' => 'Hatchery tidak boleh kosong',
        'bw.required' => 'BW tidak boleh kosong',
        'bw.integer' => 'BW harus berupa angka'
    ];

    public function index(Request $req) {
        try {
            $data = PhPerformance::select(
                DB::raw('MONTH(chick_in_date) as month'),
                DB::raw('YEAR(chick_in_date) as year'),
                DB::raw('SUM(population) as total_population'),
                DB::raw('SUM(death) as total_death'),
                DB::raw('SUM(culling) as total_culling'),
                DB::raw('SUM(death + culling) as total_depletion'),
                DB::raw('ROUND(SUM(death + culling) / NULLIF(SUM(population), 0) * 100, 2) as percentage_depletion'),
                DB::raw('AVG(bw) as average_bw')
            )
            ->groupBy(DB::raw('YEAR(chick_in_date)'), DB::raw('MONTH(chick_in_date)'))
            ->orderBy(DB::raw('YEAR(chick_in_date)'))
            ->orderBy(DB::raw('MONTH(chick_in_date)'))
            ->get();

            $param = [
                'title' => 'Poultry Health > Performance 7 Hari',
                'data' => $data
            ];
            return view('ph.performance.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Poultry Health > Performance 7 Hari > Tambah'
            ];
            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->all();
                    if(isset($input['company_id'])) {
                        $input['company_name'] = Company::find($req->input('company_id'))->name;
                    }
                    if(isset($input['location_id'])) {
                        $input['location_name'] = Location::find($req->input('location_id'))->name;
                    }
                    if(isset($input['area_id'])) {
                        $input['area_name'] = Area::find($req->input('area_id'))->name;
                    }
                    if(isset($input['kandang_id'])) {
                        $input['kandang_name'] = Kandang::find($req->input('kandang_id'))->name;
                    }
                    if(isset($input['supplier_id'])) {
                        $input['supplier_name'] = Supplier::find($req->input('supplier_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                $population = $req->input('population')??0;
                $death = $req->input('death')??0;
                $culling = $req->input('culling')??0;
                $depletion = $death + $culling;
                $percentageDepletion = $depletion / $population * 100;

                $performance = PhPerformance::create([
                    'kandang_id' => $req->input('kandang_id'),
                    'chick_in_date' => date('Y-m-d', strtotime($req->input('chick_in_date'))),
                    'population' => $population,
                    'supplier_id' => $req->input('supplier_id'),
                    'hatchery' => $req->input('hatchery'),
                    'death' => $death,
                    'culling' => $culling,
                    'depletion' => $depletion,
                    'percentage_depletion' => number_format($percentageDepletion, 2),
                    'bw' => $req->input('bw'),
                    'created_by' => Auth::user()->user_id??'',
                ]);

                $month = date('m', strtotime($performance->chick_in_date));
                $year = date('Y', strtotime($performance->chick_in_date));
                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('ph.performance.detail', ['month'=>$month, 'year'=>$year])->with($success);
            }

            return view('ph.performance.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $phPerformance = PhPerformance::with([ 'kandang', 'supplier', 'createdby' ])
                ->findOrFail($req->id);
            $month = date('m', strtotime($phPerformance->chick_in_date));
            $year = date('Y', strtotime($phPerformance->chick_in_date));
            $monthName = Carbon::parse($year.'-'.$month.'-01')->format('F');

            $param = [
                'title' => 'Poultry Health > Performance 7 Hari > '.$monthName.' '.$year.' > edit',
                'data' => $phPerformance,
                'month' => $month,
                'year' => $year,
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator);
                }

                $population = $req->input('population')??0;
                $death = $req->input('death')??0;
                $culling = $req->input('culling')??0;
                $depletion = $death + $culling;
                $percentageDepletion = $depletion / $population * 100;

                $phPerformance->update([
                    'kandang_id' => $req->input('kandang_id'),
                    'chick_in_date' => date('Y-m-d', strtotime($req->input('chick_in_date'))),
                    'population' => $population,
                    'supplier_id' => $req->input('supplier_id'),
                    'hatchery' => $req->input('hatchery'),
                    'death' => $death,
                    'culling' => $culling,
                    'depletion' => $depletion,
                    'percentage_depletion' => number_format($percentageDepletion, 2),
                    'bw' => $req->input('bw')
                ]);

                $success = ['success' => 'Data Berhasil dirubah'];
                return redirect()->route('ph.performance.detail', ['month'=>$month, 'year'=>$year])->with($success);
            }

            return view('ph.performance.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function detail(Request $req) {
        try {
            $param = $this->detailData($req);
            return view('ph.performance.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    function detailData($req) {
        $month = $req->month;
        $year = $req->year;
        $monthName = Carbon::parse($year.'-'.$month.'-01')->format('F');

        $phPerformance = PhPerformance::with([ 'kandang', 'supplier', 'createdby' ])
            ->select(
                DB::raw('*'),
                DB::raw('(death + culling) as total_depletion'),
                DB::raw('ROUND((death + culling) / NULLIF(population, 0) * 100, 2) as percentage_depletion'),
            );

        if ($req->has("area_id")) {
            if ($req->area_id > 0) {
                $phPerformance->whereHas('kandang', function ($query) use ($req) {
                    $query->with('location');
                    $query->whereHas('location', function ($query) use ($req) {
                        $query->where('area_id', $req->area_id);
                    });
                });
            }
        }

        $phPerformance
            ->whereYear('chick_in_date', $year)
            ->whereMonth('chick_in_date', $month)
            ->orderBy('supplier_id')
            ->orderBy('hatchery')
            ->orderBy('kandang_id');
        $data = $phPerformance->get();

        $param = [
            'title' => 'Poultry Health > Performance 7 Hari > '.$monthName.' '.$year,
            'data' => $data,
            'month' => $month,
            'year' => $year,
        ];

        return $param;
    }

    public function download(Request $req) {
        try {
            $param = $this->detailData($req);
            return view('ph.performance.download', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $phPerformance = PhPerformance::findOrFail($req->id);
            $year = $req->year;
            $month = $req->month;
            $phPerformance->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('ph.performance.detail', ['month'=>$month, 'year'=>$year])->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchPerformance(Request $request) {
        $search = $request->input('q');
        $query = PhPerformance::where('name', 'like', "%{$search}%");
        $data = $query->get();

        return response()->json($data->map(function ($val) {
            return ['id' => $val->kandang_id, 'text' => $val->name];
        }));
    }
}
