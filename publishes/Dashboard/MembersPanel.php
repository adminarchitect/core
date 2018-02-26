<?php

use App\User;
namespace App\Http\Terranet\Administrator\Dashboard;

use Terranet\Administrator\Dashboard\Panels\MembersPanel as CoreMembersPanel;

class MembersPanel extends CoreMembersPanel
{
    //
    protected function createModel()
    {
        return (new User);
    }
}
