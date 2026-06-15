<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use App\Models\Contact;

class AdminController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $contacts = Contact::query();

        if ($request->filled('keyword')) {
            $contacts->where(function ($query) use ($request) {
                $query->where('first_name', 'LIKE', "%{$request->keyword}%")
                    ->orWhere('last_name', 'LIKE', "%{$request->keyword}%")
                    ->orWhere('email', 'LIKE', "%{$request->keyword}%");
            });
        }

        if ($request->gender) {
            $contacts->where('gender', $request->gender);
        }

        if ($request->category_id) {
            $contacts->where('category_id', $request->category_id);
        }

        if ($request->date) {
            $contacts->whereDate('created_at', $request->date);
        }

        $contacts = $contacts->paginate(7);
        $categories = Category::all();

        return view('admin.index', compact('contacts', 'categories'));
    }

    public function show(Contact $contact)
    {
        return view('admin.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.index');
    }
}
