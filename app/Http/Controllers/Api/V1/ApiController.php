<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiResponses;

    public function include(string $relationship) : bool
    {
        $param = request()->get('include');

        if (! isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($relationship));

        return in_array(strtolower($relationship), $includeValues);
    }

    protected string $namespace = 'App\\Policies\\V1';

    public function __construct()
    {
        Gate::guessPolicyNamesUsing(fn (string $modelClass) =>
            "{$this->namespace}\\" . class_basename($modelClass) . "Policy"
        );
    }
}
