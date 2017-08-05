<?php

namespace Terranet\Administrator\Dashboard\Panels;

use Terranet\Administrator\Dashboard\DashboardPanel;
use Terranet\Administrator\Traits\Stringify;

class BlankPanel extends DashboardPanel
{
    use Stringify;

    /**
     * Widget contents
     *
     * @return mixed
     */
    public function render()
    {
        return
        <<<OUT
        <div class="panel panel-announcement">
            <div class="panel-heading">
                <h4 class="panel-title">Welcome to AdminArchitect.</h4>
            </div>
            <div class="panel-body">
                <p class="mt20">
                    This is the default dashboard page.
                    To manage dashboard panels, review <em>\App\Http\Terranet\Administrator\Dashboard\Factory</em> class.
                </p>
            </div>
        </div>
OUT;
    }
}
