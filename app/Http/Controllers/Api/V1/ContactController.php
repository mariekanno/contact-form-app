<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::with(['category', 'tags']);

        if (request()->filled('keyword')) {
            $contacts->where(function ($query) {
                $query->where('first_name', 'LIKE', '%'.request('keyword').'%')
                    ->orWhere('last_name', 'LIKE', '%'.request('keyword').'%')
                    ->orWhere('email', 'LIKE', '%'.request('keyword').'%');
            });
        }

        if (request()->filled('gender')) {
            $contacts->where('gender', request('gender'));
        }

        if (request()->filled('category_id')) {
            $contacts->where('category_id', request('category_id'));
        }

        if (request()->filled('date')) {
            $contacts->whereDate('created_at', request('date'));
        }

        $perPage = request('per_page', 20);

        $contacts = $contacts
            ->latest()
            ->paginate($perPage);

        return ContactResource::collection($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create($request->validated());

        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
        }

        return (new ContactResource($contact->load(['category', 'tags'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contact = Contact::with(['category', 'tags'])
            ->findOrFail($id);

        return new ContactResource($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, string $id)
    {
        $contact = Contact::findOrFail($id);

        $contact->update($request->validated());

        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
        }

        return new ContactResource(
            $contact->load(['category', 'tags'])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contact = Contact::findOrFail($id);

        $contact->delete();

        return response()->noContent();
    }
}
