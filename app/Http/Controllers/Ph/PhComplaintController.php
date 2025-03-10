<?php

namespace App\Http\Controllers\Ph;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Area;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Location;
use App\Models\DataMaster\Product;
use App\Models\DataMaster\Supplier;
use App\Models\Ph\PhChickIn;
use App\Models\Ph\PhComplaint;
use App\Models\Ph\PhMortality;
use App\Models\UserManagement\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PhComplaintController extends Controller
{
    private const VALIDATION_RULES = [
        'product_id'         => 'required|max:50',
        'type'               => 'required',
        'population'         => 'required|integer',
        'investigation_date' => 'required',
        'description'        => 'required|max:100',
        'symptoms'           => 'required',
        'supplier_id'        => 'required',
        'kandang_id'         => 'required',
        'culling_pic'        => 'required',

    ];

    private const VALIDATION_MESSAGES = [
        'product_id.required'         => 'Produk tidak boleh kosong',
        'product.max'                 => 'Produk melebihi 50 karakter',
        'type.required'               => 'Tipe tidak boleh kosong',
        'population.required'         => 'Populasi tidak boleh kosong',
        'population.integer'          => 'Populasi harus berupa angka',
        'investigation_date.required' => 'Tanggal investigasi tidak boleh kosong',
        'description.required'        => 'Deskripsi tidak boleh kosong',
        'description.max'             => 'Deskripsi melebihi 100 karakter',
        'symptoms.required'           => 'Gejala klinis tidak boleh kosong',
        'supplier_id.required'        => 'Vendor tidak boleh kosong',
        'kandang_id.required'         => 'Kandang tidak boleh kosong',
        'culling_pic.required'        => 'Manager Area tidak boleh kosong',
        'image.image'                 => 'Foto tidak valid',
        'image.mimes'                 => 'Foto tidak valid',
        'image.max'                   => 'Foto melebihi kapasitas 2 MB',
        'name.required'               => 'Nama user tidak boleh kosong',
        'name.max'                    => 'Nama user melebihi 50 karakter',
    ];

    public function index(Request $req)
    {
        try {
            $data  = PhComplaint::with(['kandang', 'supplier', 'createdby'])->get();
            $param = [
                'title' => 'Poultry Health > Report Komplain',
                'data'  => $data,
                'type'  => Constants::PH_FARMING_TYPE,
            ];

            return view('ph.report-complaint.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Poultry Health > Report Komplain > Tambah',
                'type'  => Constants::PH_FARMING_TYPE,
            ];
            if ($req->isMethod('post')) {
                $rules     = self::VALIDATION_RULES;
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->all();
                    if (isset($input['product_id'])) {
                        $input['product_name'] = Product::find($req->input('product_id'))->name;
                    }
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find($req->input('company_id'))->name;
                    }
                    if (isset($input['location_id'])) {
                        $input['location_name'] = Location::find($req->input('location_id'))->name;
                    }
                    if (isset($input['area_id'])) {
                        $input['area_name'] = Area::find($req->input('area_id'))->name;
                    }
                    if (isset($input['culling_pic'])) {
                        $input['culling_pic_name'] = User::find($req->input('culling_pic'))->name;
                    }
                    if (isset($input['kandang_id'])) {
                        $input['kandang_name'] = Kandang::find($req->input('kandang_id'))->name;
                    }
                    if (isset($input['supplier_id'])) {
                        $input['supplier_name'] = Supplier::find($req->input('supplier_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                $complaint = PhComplaint::create([
                    'product_id'         => $req->input('product_id'),
                    'type'               => $req->input('type'),
                    'population'         => $req->input('population'),
                    'investigation_date' => date('Y-m-d', strtotime($req->input('investigation_date'))),
                    'description'        => $req->input('description'),
                    'symptoms'           => json_encode($req->input('symptoms')),
                    'total_deaths'       => $req->input('total_deaths'),
                    'total_culling'      => $req->input('total_culling'),
                    'images'             => json_encode($req->images),
                    'kandang_id'         => $req->input('kandang_id'),
                    'supplier_id'        => $req->input('supplier_id'),
                    'culling_pic'        => $req->input('culling_pic'),
                    'created_by'         => Auth::user()->user_id ?? '',
                ]);

                if ($req->has('chick_in')) {
                    $arrChickIn = $req->input('chick_in');
                    foreach ($arrChickIn as $key => $value) {
                        $arrChickIn[$key]['ph_complaint_id'] = $complaint->ph_complaint_id;
                        $arrChickIn[$key]['date']            = date('Y-m-d', strtotime($value['date']));
                        $arrChickIn[$key]['duration']        = $value['duration'] < 0 ? '00:00' : Carbon::now()->startOfDay()->addMinutes($value['duration'])->format('H:i');
                    }
                    PhChickIn::insert($arrChickIn);
                }

                if ($req->has('mortality')) {
                    $arrMortality      = $req->input('mortality');
                    $filteredMortality = array_filter($arrMortality, function($entry) {
                        return ! (is_null($entry['death']) && is_null($entry['culling']));
                    });

                    $insertMortality = [];
                    foreach ($filteredMortality as $key => $value) {
                        $insertMortality[] = [
                            'ph_complaint_id' => $complaint->ph_complaint_id,
                            'day'             => $key,
                            'death'           => $value['death']   ?? 0,
                            'culling'         => $value['culling'] ?? 0,
                        ];
                    }
                    PhMortality::insert($insertMortality);
                }

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('ph.report-complaint.index')->with($success);
            }

            return view('ph.report-complaint.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function uploadImage(Request $req)
    {
        try {
            if ($req->has('image')) {
                $validator = Validator::make($req->all(), [
                    'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                ], self::VALIDATION_MESSAGES);

                if ($validator->fails()) {
                    $file      = $req->file('image');
                    $sizeInKB  = $file->getSize() / 1024;
                    $maxSizeKB = 2048; // 2 MB
                    if ($sizeInKB > $maxSizeKB || $sizeInKB === 0) {
                        return response()->json(['errors' => ['image' => ['Ukuran file melebihi 2 MB.']]], 422);
                    }

                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $imageUrl = FileHelper::upload($req->file('image'), constants::PH_IMAGE_PATH);
                if (! $imageUrl['status']) {
                    return response()->json(['errors' => ['image' => ['Upload gagal, hubungi administrator']]], 500);
                }
                $result = $imageUrl['url'];

                return response()->json(['data' => $result], 200);
            }

            return response()->json(['errors' => ['image' => ['No file uploaded']]], 400);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function edit(Request $req)
    {
        try {
            $phComplaint = PhComplaint::with([
                'product',
                'ph_chick_in',
                'ph_mortality',
                'kandang',
                'supplier',
                'createdby',
            ])->findOrFail($req->id);

            $param = [
                'title' => 'Poultry Health > Report Komplain > Ubah',
                'data'  => $phComplaint,
                'type'  => Constants::PH_FARMING_TYPE,
            ];

            if ($req->isMethod('post')) {
                $rules     = self::VALIDATION_RULES;
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator);
                }

                $phComplaint->update([
                    'product_id'         => $req->input('product_id'),
                    'type'               => $req->input('type'),
                    'population'         => $req->input('population'),
                    'investigation_date' => date('Y-m-d', strtotime($req->input('investigation_date'))),
                    'description'        => $req->input('description'),
                    'symptoms'           => json_encode($req->input('symptoms')),
                    'total_deaths'       => $req->input('total_deaths'),
                    'total_culling'      => $req->input('total_culling'),
                    'culling_pic'        => $req->input('culling_pic'),
                    'images'             => json_encode($req->images),
                    'kandang_id'         => $req->input('kandang_id'),
                    'supplier_id'        => $req->input('supplier_id'),
                ]);

                if ($req->has('chick_in')) {
                    $phChickIn = PhChickIn::where('ph_complaint_id', $req->id);
                    $phChickIn->delete();
                    $arrChickIn = $req->input('chick_in');
                    foreach ($arrChickIn as $key => $value) {
                        $arrChickIn[$key]['ph_complaint_id'] = $req->id;
                        $arrChickIn[$key]['date']            = date('Y-m-d', strtotime($value['date']));
                        $arrChickIn[$key]['duration']        = $value['duration'] < 0 ? '00:00' : Carbon::now()->startOfDay()->addMinutes($value['duration'])->format('H:i');
                    }
                    PhChickIn::insert($arrChickIn);
                }

                if ($req->has('mortality')) {
                    $phMortality = PhMortality::where('ph_complaint_id', $req->id);
                    $phMortality->delete();
                    $arrMortality      = $req->input('mortality');
                    $filteredMortality = array_filter($arrMortality, function($entry) {
                        return ! (is_null($entry['death']) && is_null($entry['culling']));
                    });

                    $insertMortality = [];
                    foreach ($filteredMortality as $key => $value) {
                        $insertMortality[] = [
                            'ph_complaint_id' => $req->id,
                            'day'             => $key,
                            'death'           => $value['death']   ?? 0,
                            'culling'         => $value['culling'] ?? 0,
                        ];
                    }
                    PhMortality::insert($insertMortality);
                }

                $success = ['success' => 'Data Berhasil dirubah'];

                return redirect()->route('ph.report-complaint.index')->with($success);
            }

            return view('ph.report-complaint.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function detail(Request $req)
    {
        try {
            $phComplaint = PhComplaint::with([
                'ph_chick_in',
                'ph_mortality',
                'kandang',
                'supplier',
                'createdby',
            ])->findOrFail($req->id);

            $param = [
                'title' => 'Poultry Health > Report Komplain > Detail',
                'data'  => $phComplaint,
                'type'  => Constants::PH_FARMING_TYPE,
                'sign'  => Constants::PH_SIGNATURE,
            ];

            return view('ph.report-complaint.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function download(Request $req)
    {
        try {
            $phComplaint = PhComplaint::with([
                'ph_chick_in',
                'ph_mortality',
                'kandang',
                'supplier',
                'createdby',
            ])->findOrFail($req->id);
            $managerArea = User::with(['role', 'department'])
                ->whereHas('role', function($query) {
                    $query->where('name', 'Manager Area');
                })
                ->whereHas('department', function($query) use ($phComplaint) {
                    $query->with('location');
                    $query->whereHas('location', function($query) use ($phComplaint) {
                        $query->where('area_id', $phComplaint->kandang->location->area_id);
                    });
                })->first();

            $param = [
                'title'        => 'Poultry Health > Report Komplain > Detail',
                'data'         => $phComplaint,
                'type'         => Constants::PH_FARMING_TYPE,
                'manager_area' => $managerArea,
                'sign'         => Constants::PH_SIGNATURE,
            ];

            return view('ph.report-complaint.download', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $phComplaint = PhComplaint::findOrFail($req->id);
            $phComplaint->delete();
            $phChickIn = PhChickIn::where('ph_complaint_id', $req->id);
            $phChickIn->delete();
            $phMortality = PhMortality::where('ph_complaint_id', $req->id);
            $phMortality->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('ph.report-complaint.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchAudit(Request $request)
    {
        $search = $request->input('q');
        $query  = PhComplaint::where('name', 'like', "%{$search}%");
        $data   = $query->get();

        return response()->json($data->map(function($val) {
            return ['id' => $val->kandang_id, 'text' => $val->name];
        }));
    }
}
