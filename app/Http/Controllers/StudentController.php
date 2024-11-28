<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Student::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'section' => 'required',
        ]);

        return Student::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Student::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::find($id);

        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'section' => 'required',
        ]);

        $student->update($request->all());

        return $student;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Student::destroy($id);
        return ['message' => 'Student successfully deleted!'];
    }

    public function search($name)
    {
        return Student::where('firstname', 'like', '%' . $name . '%')
            ->orWhere('lastname', 'like', '%' . $name . '%')
            ->get();
    }
}
