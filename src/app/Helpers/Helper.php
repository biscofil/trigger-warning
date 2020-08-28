<?php

use App\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;

/**
 * Combines SQL and its bindings
 *
 * @param QueryBuilder|EloquentBuilder $query
 * @return string
 */
function getEloquentSqlWithBindings($query)
{
    return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
        return is_numeric($binding) ? $binding : "'{$binding}'";
    })->toArray());
}

/**
 * @return User
 * @throws Exception
 */
function getAuthUser(): User
{
    if (!Auth::check()) {
        throw new Exception("Not loged");
    }

    /** @var User $user */
    $user = auth()->user();

    return $user;
}

/**
 * @param $path
 * @param bool|null $secure
 * @return string
 */
function nocacheasset($path, $secure = true)
{

    $string = config('misc.asset_anticache_string');

    if (is_null($string)) {
        $string = '';
    } else {
        $string = '?' . $string;
    }

    return asset($path, $secure) . $string;
}
