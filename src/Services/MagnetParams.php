<?php

namespace Terranet\Administrator\Services;

use Illuminate\Http\Request;

class MagnetParams
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var bool
     */
    private $strictMode = true;

    /**
     * Set extra parameters.
     *
     * @var
     */
    private $extra = [];

    /**
     * MagnetParams constructor.
     *
     * @param Request $request
     * @param array $params
     */
    public function __construct(Request $request, array $params = [])
    {
        $this->params = (array) $params;
        $this->request = $request;
    }

    public function with(array $extra = [])
    {
        $this->extra = $extra;

        return $this;
    }

    public function toQuery()
    {
        return http_build_query($this->toArray());
    }

    public function toArray()
    {
        $params = array_build($this->params, function ($index, $param) {
            if (\is_string($param)) {
                return [$param, $this->request->get($param, null)];
            }
        });

        if ($this->isStrictMode()) {
            $params = array_filter($params, function ($item) {
                return null !== $item;
            });
        }

        return array_merge($this->extra, $params);
    }

    public function isStrictMode()
    {
        return $this->strictMode;
    }

    public function setStrictMode($flag = false)
    {
        $this->strictMode = (bool) $flag;

        return $this;
    }
}
