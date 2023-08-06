<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\UserStore;

class DuplicateUserInStore implements Rule
{
    protected $userId;
    protected $storeId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId, $storeId)
    {
        $this->userId = $userId;
        $this->storeId = $storeId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return UserStore::where('user_id', $this->userId)
            ->where('store_id', $this->storeId)
            ->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'User already exists in the store';
    }
}
