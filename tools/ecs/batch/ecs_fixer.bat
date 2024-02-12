:: Run easy-coding-standard (ecs) via this batch file inside your IDE e.g. PhpStorm (Windows only)
:: Install inside PhpStorm the  "Batch Script Support" plugin
cd..
cd..
cd..
cd..
cd..
cd..
php vendor\bin\ecs check vendor/markocupic/mathbuch-learning-objectives/src --fix --config vendor/markocupic/mathbuch-learning-objectives/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/mathbuch-learning-objectives/contao --fix --config vendor/markocupic/mathbuch-learning-objectives/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/mathbuch-learning-objectives/config --fix --config vendor/markocupic/mathbuch-learning-objectives/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/mathbuch-learning-objectives/templates --fix --config vendor/markocupic/mathbuch-learning-objectives/tools/ecs/config.php
php vendor\bin\ecs check vendor/markocupic/mathbuch-learning-objectives/tests --fix --config vendor/markocupic/mathbuch-learning-objectives/tools/ecs/config.php
