<?php

namespace Tdom\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TdomUserBundle extends Bundle
{
    public function getParent() {
        return 'FOSUserBundle';
    }
}
