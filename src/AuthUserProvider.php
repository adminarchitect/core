<?php

namespace Terranet\Administrator;

use Illuminate\Auth\EloquentUserProvider;
use Terranet\Administrator\Traits\CallableTrait;

/**
 * Class AuthUserProvider.
 */
class AuthUserProvider extends EloquentUserProvider
{
    use CallableTrait;

    public function retrieveByCredentials(array $credentials)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if (!str_contains($key, 'password')) {
                // handle closures
                $value = $this->retrieveValue($value);

                $query = call_user_func_array([$query, $this->searchMethod($value)], [$key, $value]);
            }
        }

        return $query->first();
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function searchMethod($value)
    {
        return is_array($value) ? 'whereIn' : 'where';
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function retrieveValue($value)
    {
        if (is_callable($value)) {
            $value = $this->callback($value);
        }

        return $value;
    }
}
