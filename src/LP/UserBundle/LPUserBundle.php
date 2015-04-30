<?php

namespace LP\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LPUserBundle extends Bundle
{
	public function getParent()
  	{
    	return 'FOSUserBundle';
  	}
}
