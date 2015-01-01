<?php

date_default_timezone_set('Europe/London');

// inj Framework v3

// inj Framework v3: System Constants
require dirname(__FILE__).'/../upload/lib/Cms/Core/Constants/System.php';

// inj Framework v3: Autoloader
require ABS_ROOT.'/lib/Cms/Core/Autoloader/Base.php';

// Test Framework
require dirname(__FILE__).'/lib/Test/Cms/Di/ContainerBase.php';
