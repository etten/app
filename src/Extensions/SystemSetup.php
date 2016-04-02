<?php

namespace Etten\App\Extensions;

use Nette;

class SystemSetup extends AbstractExtension
{

	public function onConfiguratorCreate(Nette\Configurator $configurator, array $config)
	{
		umask(0); // 0666 file, 0777 folder
		mb_internal_encoding('UTF-8'); // forces UTF-8 internal encoding
	}

}
