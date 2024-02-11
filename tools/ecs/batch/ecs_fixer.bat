:: Run easy-coding-standard (ecs) via this batch file inside your IDE e.g. PhpStorm (Windows only)
:: Install inside PhpStorm the  "Batch Script Support" plugin
cd..
cd..
cd..
cd..
cd..
cd..
php vendor\bin\ecs check vendor/markocupic/contao-altcha-antispam/src --fix --config vendor/markocupic/contao-altcha-antispam/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/contao-altcha-antispam/contao --fix --config vendor/markocupic/contao-altcha-antispam/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/contao-altcha-antispam/config --fix --config vendor/markocupic/contao-altcha-antispam/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/contao-altcha-antispam/templates --fix --config vendor/markocupic/contao-altcha-antispam/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/contao-altcha-antispam/tests --fix --config vendor/markocupic/contao-altcha-antispam/tools/ecs/config.php
