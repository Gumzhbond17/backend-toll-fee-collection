<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    protected HelperController $helper;

    protected Province $model;

    public function __construct()
    {
        $this->helper = new HelperController;
        $this->model = new Province;
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
            $provinces = $this->model->with('user:id,username')
                ->where('province_name_la', 'like', '%'.$search.'%')
                ->orWhere('province_name_en', 'like', '%'.$search.'%');
            // paginate the data
            $data = $this->helper->paginate($provinces, $limit);

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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // validate the request
            $request->validate([
                'province_name_la' => 'required|string|max:100',
                'province_name_en' => 'required|string|max:100',
                'created_by' => 'required|integer',
            ]);

            // create the data
            $obj = [
                'province_name_la' => $request->province_name_la,
                'province_name_en' => $request->province_name_en,
                'created_by' => $request->created_by,
            ];

            // save the data
            $create_data = $this->model->create($obj);

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
    public function show(string $province_id)
    {
        try {
            // find the data by id
            $province = $this->model->query()->where('id', $province_id)->with('user:id,username')->first();

            // check if the data is not found
            if (! $province) {
                return $this->helper->response('Data is not found', '', 404);
            }

            // return success message
            return $this->helper->response('Retrieved data successfully', $province, 200);
        } catch (\Throwable $th) {
            // return error message
            return $this->helper->response($th->getMessage(), '', 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Province $province)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $province_id)
    {
        try {
            // find the data by id
            $update_data = $this->model->query()->where('id', $province_id)->first();

            // check if the data is not found
            if (! $update_data) {
                return $this->helper->response('Data is not found', '', 404);
            }

            // define the validation rule
            $rule = [
                'province_name_la' => 'required|string|max:100',
                'province_name_en' => 'required|string|max:100',
                'updated_by' => 'required|integer',
            ];

            // check the validated data from request
            $this->helper->validated($request, $rule);

            // generate the array data
            $obj = [
                'province_name_la' => $request->province_name_la,
                'province_name_en' => $request->province_name_en,
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
    public function destroy(string $province_id)
    {
        try {
            // find the data by id
            $delete_data = $this->model->findOrFail($province_id);

            // check if the data id is not found
            if (! $delete_data) {
                return $this->helper->response('Data not found', '', 404);
            }

            // delete the data
            $delete_data->delete();

            // return success message
            return $this->helper->response('Deleted data successfully', '', 200);
        } catch (\Throwable $th) {
            // return error message
            return $this->helper->response($th->getMessage(), '', 500);
        }
    }
}
