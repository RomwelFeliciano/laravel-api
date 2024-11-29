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
            'image' => 'required|mimes:jpg,jpeg,png,bmp|max:2048',
        ]);

        $imageName = '';

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->store('images', $imageName);
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/uploads'), $imageName);
        }

        $student = Student::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'section' => $request->section,
            'image' => $imageName,
        ]);

        return response()->json(
            [
                'message' => 'Student created successfully!',
                'student' => $student,
            ],
            201,
        );
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
        // Find the student by ID
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // Validate the incoming request
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'section' => 'required|string',
            'image' => 'nullable|mimes:jpg,jpeg,png,bmp|max:2048',
        ]);

        // Store the current image name
        $oldImageName = $student->image;

        // Initialize new image name
        $imageName = $oldImageName;

        // Check if a new image is uploaded
        if ($request->hasFile('image')) {
            // If a new image is uploaded, delete the old image if it exists
            if ($oldImageName) {
                $oldImagePath = public_path('images/uploads/' . $oldImageName);

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete the old image
                }
            }

            // Process the new image
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/uploads'), $imageName);
        }

        // Prepare data for update
        $data = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'section' => $request->section,
            'image' => $imageName, // Update with new image name
        ];

        // Update the student record
        $student->update($data);

        return response()->json(
            [
                'message' => 'Student updated successfully!',
                'student' => $student,
            ],
            200,
        );
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
