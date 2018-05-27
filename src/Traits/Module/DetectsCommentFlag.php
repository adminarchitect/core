<?php

namespace Terranet\Administrator\Traits\Module;

trait DetectsCommentFlag
{
    /**
     * Search class doc comment for specific flag.
     *
     * @param      $flag
     * @param null $reflection
     *
     * @return bool
     */
    protected function hasCommentFlag($flag, $reflection = null)
    {
        if (null === $reflection) {
            $reflection = new \ReflectionClass(new static());
        }

        $flag = "@{$flag}";

        $lines = explode("\n", $reflection->getDocComment());

        foreach ($lines as $line) {
            if (false !== stripos($line, $flag)) {
                $props = array_filter(explode(' ', $line), function ($prop) {
                    return '' !== trim($prop) && '*' !== $prop;
                });

                if (head($props) !== $flag) {
                    continue;
                }

                return $props ? array_values($props) : true;
            }
        }

        return false;
    }
}
