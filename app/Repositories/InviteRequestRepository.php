<?php

namespace App\Repositories;

use App\Contracts\Repository\InviteRequestRepositoryInterface;
use App\Models\InviteRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class InviteRequestRepository implements InviteRequestRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function index()
    {
        return InviteRequest::all();
    }

    /**
     * @inheritDoc
     */
    public function get($email) {
        return InviteRequest::query()->find($email);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): InviteRequest
    {
        $request = new InviteRequest;
        $request->email = $data['email'];
        $request->name = $data['name'];

        $query = $request->save();

        return $request;
    }

    /**
     * @inheritDoc
     */
    public function update($email, array $data)
    {
        try {
            $request = InviteRequest::query()->findOrFail($email);
        } catch (ModelNotFoundException $e) {
            return false;
        }

        try {
            $request->fill($data);
            if ($request->isDirty()) {
                $request->save();
            }
        } catch (QueryException $e) {
            return false;
        }

        return $request;
    }

    /**
     * @inheritDoc
     */
    public function delete($email): bool
    {
        try {
            $request = InviteRequest::query()->findOrFail($email);
            $request->delete();
        } catch (QueryException $e) {
            return false;
        }

        return true;
    }
}
