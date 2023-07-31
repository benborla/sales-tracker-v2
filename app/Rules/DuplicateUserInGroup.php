<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\GroupTeamMember;

class DuplicateUserInGroup implements Rule
{
    protected $userId;
    protected $groupId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId, $groupId)
    {
        $this->userId = $userId;
        $this->groupId = $groupId;
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
        return GroupTeamMember::where('user_id', $this->userId)
            ->where('group_teams_id', $this->groupId)
            ->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'User already exists in the group';
    }
}
