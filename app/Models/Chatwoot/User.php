<?php

namespace App\Models\Chatwoot;

class User extends BaseModel
{
    protected $table = 'mbi_chatwoot.users';

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}
