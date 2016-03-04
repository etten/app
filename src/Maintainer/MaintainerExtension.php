<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Maintainer;

interface MaintainerExtension
{

	public function attached(Maintainer $maintainer);

}
