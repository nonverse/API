<?php

namespace App\Repositories;

use App\Contracts\Repository\InviteRepositoryInterface;
use App\Models\Invite;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class InviteRepository implements InviteRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function index($uuid = null)
    {
        if ($uuid) {
            return Invite::query()->where('invited_by', $uuid)->get();
        }

        return Invite::all();
    }

    /**
     * @inheritDoc
     */
    public function get($email)
    {
        return Invite::query()->find($email);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Invite
    {
        $invite = new Invite;
        $invite->email = $data['email'];
        $invite->invite_key = $data['invite_key'];
        $invite->invited_by = $data['invited_by'];
        $invite->key_expiry = $data['key_expiry'];

        $query = $invite->save();

        return $invite;
    }

    /**
     * @inheritDoc
     */
    public function update($email, array $data)
    {
        try {
            $invite = Invite::query()->findOrFail($email);
        } catch (ModelNotFoundException $e) {
            return false;
        }

        try {
            $invite->fill($data);
            if ($invite->isDirty()) {
                $invite->save();
            }
        } catch (QueryException $e) {
            return false;
        }

        return $invite;
    }

    /**
     * @inheritDoc
     */
    public function delete($email): bool
    {
        try {
            $invite = Invite::query()->findOrFail($email);
            $invite->delete();
        } catch (QueryException $e) {
            return false;
        }

        return true;
    }
}
