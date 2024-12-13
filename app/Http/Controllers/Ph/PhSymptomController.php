<?php

namespace App\Http\Controllers\Ph;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Ph\PhSymptom;
use DB;

class PhSymptomController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama Gejala Klinis tidak boleh kosong',
        'name.max' => 'Nama Gejala Klinis melebihi 50 karakter',
        'name.unique' => 'Nama Gejala Klinis telah digunakan'
    ];

    public function index(Request $req) {
        try {
            
            $param = [
                'title' => 'Poultry Health > Gejala Klinis',
                'data' => PhSymptom::get()
            ];
            return view('ph.symptom.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage()
            ])->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Poultry Health > Gejala Klinis > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = ['name' => ['required', 'string', 'max:50',
                    Rule::unique('ph_symptoms')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })]
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                PhSymptom::create([
                    'name' => $req->input('name'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('ph.symptom.index')->with($success);
            }

            return view('ph.symptom.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $area = PhSymptom::findOrFail($req->id);
            $param = [
                'title' => 'Poultry Health > Gejala Klinis > Ubah',
                'data' => $area,
            ];

            if ($req->isMethod('post')) {
                $rules = ['name' => ['required', 'string', 'max:50',
                    Rule::unique('ph_symptoms')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })->ignore($area->ph_symptom_id, 'ph_symptom_id')]
                ];
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $area->update([
                    'name' => $req->input('name'),
                    
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('ph.symptom.index')->with($success);
            }

            return view('ph.symptom.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $area = PhSymptom::findOrFail($req->id);
            $area->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('ph.symptom.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchSymptom(Request $request) {
        $search = $request->input('q');
        $symptom = PhSymptom::where('name', 'like', "%{$search}%")->get();

        return response()->json($symptom->map(function ($area) {
            return ['id' => $area->ph_symptom_id, 'text' => $area->name];
        }));
    }
}
