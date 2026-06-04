<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    protected HelperController $helper;

    protected Site $model;

    public function __construct()
    {
        $this->helper = new HelperController;
        $this->model = new Site;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // declare the search variable
            $search = request('search') ?? '';

            // declare the limit variable
            $limit = request('limit') ?? 10;

            // get all data from the model join with searching
            $sites = $this->model->with('district:id,district_name_la,district_name_en')->with('province:id,province_name_la,province_name_en')->with('user:id,username')->where(function ($query) use ($search) {
                if ($search) {
                    $query->where('site_name', 'like', '%'.$search.'%');
                }
            });

            // paginate the data
            $data = $this->helper->paginate($sites, $limit);

            // return success message
            return $this->helper->response('Retrieving all data is successfully', $data, 200);
        } catch (\Throwable $th) {
            // return error message
            return $this->helper->response($th->getMessage(), '', 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // define the validation rule
            $rule = [
                'site_name' => 'required|string|max:100',
                'district_id' => 'required|exists:districts,id',
                'province_id' => 'required|exists:provinces,id',
                'created_by' => 'required|integer',
            ];

            // check the validated data from request
            $this->helper->validated($request, $rule);

            // generate the array data
            $data = [
                'site_name' => $request->site_name,
                'district_id' => $request->district_id,
                'province_id' => $request->province_id,
                'created_by' => $request->created_by,
            ];

            // create the data from generated array
            $create_data = $this->model->create($data);

            // return success message
            return $this->helper->response('Created data successfully', $create_data, 201);
        } catch (\Throwable $th) {
            // return error message
            return $this->helper->response($th->getMessage(), '', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $site_id)
    {
        try {
            // find the data by id
            $site = $this->model->query()->where('id', $site_id)->with('district:id,district_name_la,district_name_en')->with('province:id,province_name_la,province_name_en')->with('user:id,username')->first();

            // check if the data is not found
            if (! $site) {
                return $this->helper->response('Data not found', '', 404);
            }

            // return success message
            return $this->helper->response('Retrieving data is successfully', $site, 200);
        } catch (\Throwable $th) {
            // return error message
            return $this->helper->response($th->getMessage(), '', 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $site_id)
    {
        try {
            // find the data by id
            $update_data = $this->model->query()->where('id', $site_id)->first();

            // check if the data is not found
            if (! $update_data) {
                return $this->helper->response('Data not found', '', 404);
            }

            // define the validation rule
            $rule = [
                'site_name' => 'required|string|max:100',
                'district_id' => 'required|exists:districts,id',
                'province_id' => 'required|exists:provinces,id',
                'is_active' => 'required|boolean',
                'updated_by' => 'required|integer',
            ];

            // check the validation data from request
            $this->helper->validated($request, $rule);

            // generate the array data
            $obj = [
                'site_name' => $request->site_name,
                'district_id' => $request->district_id,
                'province_id' => $request->province_id,
                'is_active' => $request->is_active,
                'updated_by' => $request->updated_by,
            ];

            // update the data on the found instance
            $update_data->fill($obj)->save();

            // return success message
            return $this->helper->response('Updated data successfully', $update_data, 200);
        } catch (\Throwable $th) {
            // return error message
            return $this->helper->response($th->getMessage(), '', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $site_id)
    {
        try {
            // find the data by id
            $delete_data = $this->model->findOrFail($site_id);

            // check if the data id is not found
            if (! $delete_data) {
                return $this->helper->response('Data not found', '', 404);
            }

            // delete the data
            $delete_data->delete();

            // return success message
            return $this->helper->response('Deleted data successfully', $delete_data, 200);
        } catch (\Throwable $th) {
            // return error message
            return $this->helper->response($th->getMessage(), '', 500);
        }
    }
}
