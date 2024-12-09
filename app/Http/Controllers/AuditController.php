<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Helpers\FileHelper;
use App\Models\Audit;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Department;
use App\Models\DataMaster\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuditController extends Controller
{
    private const VALIDATION_RULES = [
        'department_id' => 'required',
        'description'   => 'nullable',
        'document'      => 'required|file|mimes:pdf|max:5120',
        'category'      => 'required',
        'priority'      => 'required',

    ];

    private const VALIDATION_MESSAGES = [
        'department_id.required' => 'Department tidak boleh kosong',
        'title.unique'           => 'Judul sudah digunakan',
        'title.required'         => 'Judul tidak boleh kosong',
        'title.max'              => 'Judul tidak boleh lebih dari 100 karkter',
        'document.required'      => 'Dokumen tidak boleh kosong',
        'document.file'          => 'Dokumen harus berbentuk file',
        'document.mimes'         => 'Dokumen harus berbentuk file PDF',
        'document.max'           => 'Ukuran dokumen lebih dari 5 MB',
        'category.required'      => 'Kategori tidak boleh kosong',
        'priority.required'      => 'Prioritas tidak boleh kosong',
    ];

    public function index(Request $req)
    {
        try {
            $data  = Audit::with('department')->get();
            $param = [
                'title'          => 'Manajemen Audit',
                'data'           => $data,
                'category'       => Constants::AUDIT_DOC_CATEGORY,
                'priority'       => Constants::AUDIT_DOC_PRIORITY,
                'priority_color' => Constants::AUDIT_DOC_PRIORITY_COLOR,
            ];

            return view('audit.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title'    => 'Manajemen Audit > Tambah',
                'category' => Constants::AUDIT_DOC_CATEGORY,
                'priority' => Constants::AUDIT_DOC_PRIORITY,
            ];
            if ($req->isMethod('post')) {
                $rules          = self::VALIDATION_RULES;
                $rules['title'] = ['required', 'string', 'max:50',
                    Rule::unique('audits')->where(function($query) use ($req) {
                        $query->where('department_id', $req->department_id);
                        $query->whereNull('deleted_at');

                        return $query;
                    }),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->all();
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find($req->input('company_id'))->name;
                    }
                    if (isset($input['location_id'])) {
                        $input['location_name'] = Location::find($req->input('location_id'))->name;
                    }
                    if (isset($input['department_id'])) {
                        $input['department_name'] = Department::find($req->input('department_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                $document = '';
                if ($req->has('document')) {
                    $docUrl = FileHelper::upload($req->file('document'), constants::AUDIT_DOC_PATH);
                    if (! $docUrl['status']) {
                        return redirect()->back()->with('error', $docUrl['message'])->withInput();
                    }
                    $document = $docUrl['url'];
                }

                Audit::create([
                    'title'         => $req->input('title'),
                    'category'      => $req->input('category'),
                    'priority'      => $req->input('priority'),
                    'description'   => $req->input('description'),
                    'document'      => $document,
                    'department_id' => $req->input('department_id'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('audit.index')->with($success);
            }

            return view('audit.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $audit = Audit::with('department')->findOrFail($req->id);
            $param = [
                'title'    => 'Manajemen Audit > Ubah',
                'data'     => $audit,
                'category' => Constants::AUDIT_DOC_CATEGORY,
                'priority' => Constants::AUDIT_DOC_PRIORITY,
            ];

            if ($req->isMethod('post')) {
                $dataUpdate = [
                    'title'         => $req->input('title'),
                    'description'   => $req->input('description'),
                    'department_id' => $req->input('department_id'),
                    'category'      => $req->input('category'),
                    'priority'      => $req->input('priority'),
                ];

                $rules          = self::VALIDATION_RULES;
                $rules['title'] = ['required', 'string', 'max:50',
                    Rule::unique('audits')->where(function($query) use ($req) {
                        $query->where('department_id', $req->department_id);
                        $query->whereNull('deleted_at');

                        return $query;
                    })->ignore($audit->audit_id, 'audit_id'),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                if ($req->has('document')) {
                    if ($audit->document) {
                        $delete = FileHelper::delete($audit->document);
                    }
                    $docUrl = FileHelper::upload($req->file('document'), constants::AUDIT_DOC_PATH);
                    if (! $docUrl['status']) {
                        return redirect()->back()->with('error', $docUrl['message'])->withInput();
                    }
                    $dataUpdate['document'] = $docUrl['url'];
                }

                $audit->update($dataUpdate);

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('audit.index')->with($success);
            }

            return view('audit.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $audit = Audit::findOrFail($req->id);
            $audit->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('audit.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchAudit(Request $request)
    {
        $search = $request->input('q');
        $query  = Audit::where('name', 'like', "%{$search}%");
        $data   = $query->get();

        return response()->json($data->map(function($val) {
            return ['id' => $val->audit_id, 'text' => $val->title];
        }));
    }
}
