<?php

namespace Terranet\Administrator\Contracts;

interface Badge
{
    /**
     * Badge icon.
     *
     * @return string
     */
    public function icon();

    /**
     * Describing message.
     *
     * @return string
     */
    public function message();

    /**
     * Total items count.
     *
     * @return int
     */
    public function count();

    /**
     * Badge body template.
     *
     * @return mixed
     */
    public function template();

    /**
     * Badge items collection.
     *
     * @return mixed string|\Illuminate\View\View
     */
    public function collection();

    /**
     * Link to full collection.
     *
     * @return string
     */
    public function linkTo();

    /**
     * Badge status.
     *
     * @return string
     */
    public function status();
}
