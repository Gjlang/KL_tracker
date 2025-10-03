<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\ClientCompany;
use App\Models\Client;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientCompanyController extends Controller
{
    public $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('web')->user();
            return $next($request);
        });
    }

    /**
     * Show the client company page.
     */
    public function index()
    {
        if (is_null($this->user) || !$this->user->can('client.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any client. Contact system admin for access !');
        }

        $clientcompany = ClientCompany::all();

        return view('client_company.index', compact('clientcompany'));
    }

    /**
     * Show the client company list.
     */
    public function list(Request $request)
    {

        $status = $request->input('status');

        $columns = [
            0 => 'name',
            1 => 'address',
            2 => 'phone',
            3 => 'status',
            4 => 'id',
        ];

        $limit = $request->input('length', 25);
        $start = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumnName = $columns[$orderColumnIndex] ?? 'name';
        $orderDirection = $request->input('order.0.dir', 'asc');

        // Build query
        $query = ClientCompany::with('clients')->where('status', 1);

        // if ($status != "all") {
        //     $query->where('status', $status);
        // }

        // Total records before filtering
        $totalData = $query->count();

        // Search filter
        $searchValue = trim($request->input('search.value'));
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%{$searchValue}%")
                ->orWhere('address', 'LIKE', "%{$searchValue}%")
                ->orWhere('phone', 'LIKE', "%{$searchValue}%");
            });
        }

        // Total records after filtering
        $totalFiltered = $query->count();

        // Apply ordering and pagination
        $companies = $query->orderBy($orderColumnName, $orderDirection)
                        ->skip($start)
                        ->take($limit)
                        ->get();

        // Prepare DataTables response
        $data = [];
        foreach ($companies as $company) {
            $data[] = [
                'name'           => $company->name,
                'address'        => $company->address,
                'phone'          => $company->phone,
                'status'         => $company->status,
                'id'             => $company->id,
                'pics'           => $company->clients->map(function ($client) {
                    return [
                        'name'        => $client->name,
                        'email'       => $client->email,
                        'phone'       => $client->phone,
                        'designation' => $client->designation,
                    ];
                }),
            ];
        }

        $json_data = [
            "draw"            => intval($request->input('draw', 1)),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }


    /**
     * Create client company.
     */
    public function create(Request $request)
    {
        $name           = $request->name;
        $address        = $request->address;
        $companyPhone   = $request->companyPhone;
        $pics           = $request->pics;       // person-in-charge

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'unique:client_companies,name',
                    'max:255',
                ],
                'address' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'companyPhone' => [
                    'nullable',
                    'regex:/^\+?[0-9]+$/',
                    'max:255',
                ],
                'pics' => 'nullable|array|min:1',
                'pics.*.name' => 'nullable|string|max:255',
                'pics.*.email' => 'nullable|string|email|max:255',
                'pics.*.phone' => 'nullable|string|max:255',
                'pics.*.designation' => 'nullable|string|max:255',
            ],
            [
                'name.required' => 'The "Company Name" field is required.',
                'name.string' => 'The "Company Name" must be a string.',
                'name.max' => 'The "Company Name" must not be greater than :max characters.',

                'address.string' => 'The "Address" must be a string.',
                'address.max' => 'The "Address" must not be greater than :max characters.',

                'phone.regex' => 'The "Phone No." field must only contain "+" symbol and numbers.',
                'phone.max' => 'The "Phone No." field must not be greater than :max characters.',
            ]
        );

        // Handle failed validations
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Ensure all queries successfully executed
            DB::beginTransaction();

            // Insert new client company
            $company = ClientCompany::create([
                'name'              => $name,
                'address'           => $address ?? null,
                'phone'             => $companyPhone,
            ]);

            foreach ($pics as $pic) {
                Client::create([
                    'company_id'      => $company->id,
                    'name'            => $pic['name'],
                    'email'           => $pic['email'],
                    'phone'           => $pic['phone'],
                    'designation'     => $pic['designation'] ?? null,
                    'status'           => 1,
                ]);
            }

            // Ensure all queries successfully executed, commit the db changes
            DB::commit();

            return response()->json([
                "success"   => "success",
            ], 200);
        } catch (\Exception $e) {
            // If any queries fail, undo all changes
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Edit client company.
     */
    public function edit(Request $request)
    {        
        $name                   = $request->name;
        $id                     = $request->id;
        $address                = $request->address;
        $phone                  = $request->companyPhone;

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    Rule::unique('client_companies', 'name')->ignore($id), // Exclude original value but still checks for uniqueness
                    'max:255',
                ],
                'id' => [
                    'required',
                    'integer',
                    'exists:client_companies,id,status,1', // Ensure company status is 1 (active)
                ],
                'address' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'companyPhone' => [
                    'nullable',
                    'regex:/^\+?[0-9]+$/',
                    'max:255',
                ],
            ],
            [
                'name.required' => 'The "Company Name" field is required.',
                'name.string' => 'The "Company Name" must be a string.',
                'name.max' => 'The "Company Name" must not be greater than :max characters.',
                'name.unique' => 'The "Company Name" is already been taken.',

                'id.exists' => 'The selected Client Company was deleted from the system!',

                'address.string' => 'The "Address" must be a string.',
                'address.max' => 'The "Address" must not be greater than :max characters.',

                'companyPhone.regex' => 'The "Phone No." field must only contain "+" symbol and numbers.',
                'companyPhone.max' => 'The "Phone No." field must not be greater than :max characters.',
            ]
        );

        // Handle failed validations
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Ensure all queries successfully executed
            DB::beginTransaction();

            // Update client company
            ClientCompany::where('id', $id)
                ->update([
                    'name'              => $name,
                    'address'           => $address,
                    'phone'             => $phone,
                ]);

            // Ensure all queries successfully executed, commit the db changes
            DB::commit();

            return response()->json([
                "success"   => "success",
            ], 200);
        } catch (\Exception $e) {
            // If any queries fail, undo all changes
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Delete client company.
     */
    public function delete(Request $request)
    {
        
        $currentTime = now();
        $id = $request->id;

        // Validate input
        $validator = Validator::make(
            $request->all(),
            [
                'id' => ['required', 'integer', 'exists:client_companies,id'],
            ],
            [
                'id.exists' => 'The client company cannot be found.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            DB::beginTransaction();

            // Soft delete all PICs related to this client company
            Client::where('company_id', $id)
                ->update([
                    'status' => '0',
                    'deleted_at' => $currentTime
                ]);

            // Soft delete the client company
            ClientCompany::where('id', $id)
                ->update([
                    'status' => '0',
                    'deleted_at' => $currentTime
                ]);

            DB::commit();

            return response()->json([
                'success' => 'success',
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }


    public function getPICs(Request $request)
    {
        $companyId = $request->company_id;
        $pics = Client::where('company_id', $companyId)->get();

        return response()->json(['pics' => $pics]);
    }

    public function picCreate(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'company_id'  => 'required|exists:client_companies,id',
                'name'        => 'required|string|max:255',
                'phone'       => 'nullable|regex:/^\+?[0-9]+$/|max:50',
                'email'       => 'nullable|email|max:255',
                'designation' => 'nullable|string|max:100',
            ],
            [
                'company_id.required' => 'The "Company ID" field is required.',
                'company_id.exists'   => 'The selected Client Company was deleted from the system!',
                'name.required'       => 'The "PIC Name" field is required.',
                'name.string'         => 'The "PIC Name" must be a string.',
                'name.max'            => 'The "PIC Name" must not be greater than :max characters.',
                'phone.regex'         => 'The phone number may only contain digits and an optional leading +.',
            ]
        );

        // Handle failed validations
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $pic = Client::create($validator->validated());

        return response()->json([
            'success' => true,
            'pic' => $pic
        ]);
    }


    public function picUpdate(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id'          => 'required|exists:clients,id',
                'name'        => 'required|string|max:255',
                'phone'       => 'nullable|regex:/^\+?[0-9]+$/|max:50',
                'email'       => 'nullable|email|max:255',
                'designation' => 'nullable|string|max:100',
            ],
            [
                'id.required'   => 'The PIC ID is required.',
                'id.exists'     => 'The selected PIC does not exist.',

                'name.required' => 'The "PIC Name" field is required.',
                'name.string'   => 'The "PIC Name" must be a string.',
                'name.max'      => 'The "PIC Name" must not be greater than :max characters.',

                'phone.regex'   => 'The phone number may only contain digits and an optional leading +.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            $pic = Client::findOrFail($validator->validated()['id']);

            $pic->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'PIC updated successfully',
                'pic'     => $pic
            ]);
        } catch (\Exception $e) {
            \Log::error('PIC update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update PIC',
            ], 500);
        }
    }

    public function picDelete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:clients,id'
        ]);

        $pic = Client::find($request->id);

        if ($pic) {
            $pic->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }


}
