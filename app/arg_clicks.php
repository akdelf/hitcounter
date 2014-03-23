<?

require '../hitcounter.php';

define('FPCDIR', __DIR__.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);

$counter =  new hitcounter('1069168');
echo $counter->find('312140', '2014-01-15 16:02:58')->total(); //cчитаем с даты создания


