<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DataMaster\Supplier;
use App\Models\Project\Project;
use App\Models\Project\ProjectChickIn;
use App\Constants;
use App\Helpers\FileHelper;
use DB;

class ChickinController extends Controller
{
    private const VALIDATION_RULES = [
        'chick_in.*.travel_letter_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120'
    ];  

    private const VALIDATION_MESSAGES = [
            'chick_in.*.travel_letter_document.file' => 'File tidak valid',
            'chick_in.*.travel_letter_document.mimes' => 'File hanya boleh pdf,jpeg,png,jpg',
            'chick_in.*.travel_letter_document.max' => 'File melebihi kapasitas 5 MB'
    ];

    public function index(Request $req) {
        try {
            $data = Project::with(['project_chick_in', 'kandang', 'product'])->get();
            $param = [
                'title' => 'Project > Chick-In',
                'data' => $data
            ];

            return view('project.chick-in.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function detail(Request $req) {
        try {
            $data = Project::with(['project_chick_in', 'kandang', 'product'])->findOrFail($req->id);
            $param = [
                'title' => 'Project > Chick-In > Detail',
                'data' => $data
            ];

            return view('project.chick-in.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $project = Project::with(['kandang', 'product', 'project_chick_in'])->findOrFail($req->id);
            if( !$project->approval_date ) {
                return redirect()->back()->with('error', 'Project ini belum disetujui');
            }
            $param = [
                'title' => 'Project > chick-in > Tambah',
                'data' => $project,
            ];
            
            if ($req->isMethod('post')) {
                $input = $req->all();
                $validator = Validator::make($input, self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    if(isset($input['chick_in'])) {
                        foreach ($input['chick_in'] as $key => $value) {
                            $input['chick_in'][$key]['supplier_name'] = Supplier::find($value['supplier_id'])->name;
                        }
                    }
                    return redirect()->back()
                        ->with('error', 'File gagal diupload')
                        ->withInput($input);
                }

                $dataInsert = $input['chick_in']??[];
                if (count($dataInsert) > 0) {
                    foreach ($dataInsert as $key => $value) {
                        $dataInsert[$key]['project_id'] = $req->id;
                        $dataInsert[$key]['total_chickin'] = str_replace(',', '', $value['total_chickin']);
                        $dataInsert[$key]['chickin_date'] = date('Y-m-d', strtotime($value['chickin_date']));
                        $document = '';
                        if ($value['travel_letter_document']) {
                            $docUrl = FileHelper::upload($value['travel_letter_document'], constants::CHICKIN_DOC_PATH);
                            if (!$docUrl['status']) {
                                return redirect()->back()->with('error', $docUrl['message'].' '.$value['travel_letter_document'])->withInput();
                            }
                            $document = $docUrl['url'];
                        }
                        $dataInsert[$key]['travel_letter_document'] = $document;
                    }

                    ProjectChickIn::insert($dataInsert);
                    $project->update([ 
                        'chickin_status' => array_search('Pengajuan', Constants::PROJECT_CHICKIN_STATUS)
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

    public function edit(Request $req) {
        try {
            $project = Project::with(['kandang', 'product'])
            ->with('project_chick_in', function ($query) use ($req) {
                $query->with('supplier');
            })
            ->findOrFail($req->id);

            $param = [
                'title' => 'Project > chick-in > Edit',
                'data' => $project,
            ];
            
            if ($req->isMethod('post')) {
                $input = $req->all();
                if ($req->isMethod('post')) {
                    $validator = Validator::make($input, self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
                    if ($validator->fails()) {
                        return redirect()->back()->with('error', 'File gagal diupload');
                    }
                }
                $dataInsert = $input['chick_in']??[];
                if (count($dataInsert) > 0) {
                    foreach ($dataInsert as $key => $value) {
                        $dataInsert[$key]['project_id'] = $req->id;
                        $dataInsert[$key]['total_chickin'] = str_replace(',', '', $value['total_chickin']);
                        $dataInsert[$key]['chickin_date'] = date('Y-m-d', strtotime($value['chickin_date']));
                        $document = '';
                        $existingDoc = $project->project_chick_in[$key]->travel_letter_document??null;
                        if (isset($value['travel_letter_document'])) {
                            if ($existingDoc) {
                                FileHelper::delete($existingDoc);
                            }
                            $docUrl = FileHelper::upload($value['travel_letter_document'], constants::CHICKIN_DOC_PATH);
                            if (!$docUrl['status']) {
                                return redirect()->back()->with('error', $docUrl['message'].' '.$value['travel_letter_document'])->withInput();
                            }
                            $document = $docUrl['url'];
                        } else {
                            $document = $existingDoc;
                        }
                        $dataInsert[$key]['travel_letter_document'] = $document;
                    }

                    DB::transaction(function () use ($req, $dataInsert, $project) {
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

    public function approve(Request $req) {
        try {
            $project = Project::with('project_chick_in')->findOrFail($req->id);
            if (count($project->project_chick_in) === 0) {
                return redirect()->back()->with('error', 'Data chick in belum diisi');
            }
            $project->update([
                'chickin_status' => array_search('Sudah', Constants::PROJECT_CHICKIN_STATUS),
                'chickin_approval_date' => date('Y-m-d H:i:s')
            ]);

            $success = ['success' => 'Data berhasil disetujui'];
            return redirect()->route('project.chick-in.detail', $req->id)->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $projectChickIn = ProjectChickIn::where('project_id', $req->id);
            $projectChickIn->delete();
            $project = Project::findOrFail($req->id);
            $project->update([ 
                'chickin_status' => array_search('Belum', Constants::PROJECT_CHICKIN_STATUS),
                'chickin_approval_date' => null
            ]);

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('project.chick-in.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
