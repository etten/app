<?php

namespace Etten\App\Extensions;

class SystemSetup extends AbstractExtension
{

	public function onAppLoad(array $config)
	{
		umask(0); // 0666 file, 0777 folder
		mb_internal_encoding('UTF-8'); // forces UTF-8 internal encoding
	}

}
