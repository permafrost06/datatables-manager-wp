<?php

namespace DtManager\App\Http\Policies;

use DtManager\Framework\Request\Request;
use DtManager\Framework\Foundation\Policy;

class UserPolicy extends Policy
{
    /**
     * Check user permission for any method
     * @param  DtManager\Framework\Request\Request $request
     * @return Boolean
     */
    public function verifyRequest(Request $request)
    {
        return current_user_can('manage_options');
    }

    /**
     * Check user permission for any method
     * @param  DtManager\Framework\Request\Request $request
     * @return Boolean
     */
    public function create(Request $request)
    {
        return current_user_can('manage_options');
    }
}
