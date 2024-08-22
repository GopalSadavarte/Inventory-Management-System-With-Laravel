<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SubGroup;
use Exception;
use Illuminate\Http\Request;

interface GroupInterface
{
    public function index();
    public function store(Request $request);
    public function show(string $id);
    public function update(Request $request, string $id);
    public function destroy(string $id);
    public function getSubGroup(string $id);
    public function newSubGroup(Request $request);
    public function updateSubGroup(Request $request, string $id);
    public function deleteSubGroup(string $id);
}
class GroupController extends Controller implements GroupInterface
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lastGroup = Group::select('group_id')->orderBy('group_id', 'DESC')->limit(1)->get();
        $lastSubGroup = SubGroup::select('sub_group_id')->orderByDesc('sub_group_id')->limit(1)->get();
        $groups = Group::all();
        $lastGroupNo = ($lastGroup->count() <= 0) ? 1 : $lastGroup[0]->group_id + 1;
        $lastSubGroupNo = ($lastSubGroup->count() <= 0) ? 1 : $lastSubGroup[0]->sub_group_id + 1;

        return view('subSections.groups', compact('groups', 'lastGroupNo', 'lastSubGroupNo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'group' => 'required|regex:/^[a-zA-Z\s]+$/',
        ], [
            'group.required' => 'The Group Name not should empty..!',
            'group.regex' => 'The Group Name must contains Characters only.',
        ]);

        $res = Group::insert([
            'group_name' => $request->group,
            'created_at' => now('Asia/Kolkata'),
            'updated_at' => now('Asia/Kolkata'),
        ]);
        $msg = $res ? '<b>Success!</b> Group added successfully..!' : '<b>Alert!</b> Group cannot added..!';
        return redirect()->route('group.index')->with('group_success', $msg);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Group::where('group_id', $id)->get();
        $error = [
            ['error' => 'Invalid ! Group Id does not exist..!'],
        ];
        $res = ($data->count() == 1) ? $data : $error;
        return response()->json($res);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'group' => 'required|regex:/^[a-zA-Z\s]+$/',
        ], [
            'group.required' => 'Group name not should be empty..!',
            'group.regex' => 'Group name must contains only characters..!',
        ]);

        $res = Group::where('group_id', $id)->update([
            'group_name' => $request->group,
        ]);

        $msg = $res ? '<b>Success! </b>Group updated successfully..!' : '<b>Failed! </b>Group cannot updated..!';
        return redirect()->route('group.index')->with('group_update', $msg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $res = Group::destroy($id);
            $msg = $res ? '<b>Success! </b>Group Removed Successfully..!' : '<b>Failed! </b>Group cannot removed..!';
        } catch (Exception $exception) {
            $msg = '<b>Failed! </b>Group has assigned to other..!';
        }
        return redirect()->route('group.index')->with('group_delete', $msg);
    }

    public function getSubGroup(string $id)
    {
        $subGroup = SubGroup::with('group')->where('sub_group_id', $id)->get();
        $error = [
            ['error' => 'Invalid! Sub group id..!'],
        ];
        $data = ($subGroup->count() == 1) ? $subGroup : $error;
        return response()->json($data);
    }

    public function newSubGroup(Request $request)
    {
        $request->validate([
            'sub_group_name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'group_name_sub' => 'required',
        ], [
            'sub_group_name.required' => 'Sub Group Name is required..!',
            'sub_group_name.regex' => 'Sub Group Name must contains characters..!',
            'group_name_sub.required' => 'Group Name is required..!',
        ]);

        $res = SubGroup::insert([
            'sub_group_name' => $request->sub_group_name,
            'group_no' => $request->group_name_sub,
        ]);

        $msg = $res ? '<b>Success! </b>Sub group added successfully..!' : '<b>Failed! </b>Sub group cannot added..!';
        return redirect()->route('group.index')->with('sub_group_success', $msg);
    }

    public function updateSubGroup(Request $request, string $id)
    {
        $request->validate([
            'sub_group_name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'group_name_sub' => 'required',
        ], [
            'sub_group_name.required' => 'Sub Group Name is required..!',
            'sub_group_name.regex' => 'Sub Group Name must contains characters..!',
            'group_name_sub.required' => 'Group Name is required..!',
        ]);

        $update = SubGroup::where('sub_group_id', $id)->update([
            'sub_group_name' => $request->sub_group_name,
            'group_no' => $request->group_name_sub,
        ]);

        $msg = $update ? '<b>Success! </b>Sub group updated successfully..!' : '<b>Failed! </b>Sub group cannot updated..!';
        return redirect()->route('group.index')->with('sub_group_update', $msg);
    }

    public function deleteSubGroup(string $id)
    {
        try {
            $res = SubGroup::destroy($id);
            $msg = $res ? '<b>Success! </b>Sub Group deleted successfully..!' : '<b>Failed! </b>Sub group cannot deleted..!';
        } catch (Exception $exc) {
            $msg = '<b>Failed! </b>Sub group cannot deleted,it assigned to other..!';
        }
        return redirect()->route('group.index')->with('sub_group_delete', $msg);
    }
}
