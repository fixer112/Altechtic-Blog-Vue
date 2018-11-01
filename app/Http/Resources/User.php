<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'permissions' => $this->getPermissionsViaRoles(),
            'roles' => $this->getRoleNames(),
        ];
        
        return parent::toArray($request);
        /*return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'username' => $this->username,
            'photo' => $this->photo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'permissions' => $this->getPermissionsViaRoles(),
            'roles' => $this->getRoleNames(),

        ];*/
    }
}
