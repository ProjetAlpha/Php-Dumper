<?php

namespace Dumper;

require_once(__DIR__.'/autoload.php');

use Dumper\Dumper;

$hello = ['hi', 'hey', ['holo', 'hihi', 'jhg' => ['kku' => 'da', 'ojo' => 'fez', 'ihu' => ['kok' => 'gteg', 'ojij' => 'bgb', 'okijfr' => ['eroigjre' => 'pojer', 'efi' => 'ezf', 'oiezjffij' => ['fefijfe' => 'erih','ihefz'=>'oerg', 'iuhfze' => ['epojge' => 'reger', 'ofiegjre' => 'eorjg', 'reg' => 'greoi', 'zefiuh' => ['egoregi' => 'erogh']]]]]]], 'salut' => 'bonjour', 'kola' => 'coca', 'ha' => ['oieeef' => 'oizefj', 'zepfo' => 'oizejf', 'oijzef' => ['jij' => 'oizehf']]];
class Strong
{
    private $strong = ['hi', 'hey', ['holo', 'hihi']];
    private $hey = ['hi', 'hey', ['holo', 'hihi' => ['test84', 'test89']]];

    public function small()
    {
        return $this;
    }

    public function another()
    {
        return $this;
    }
}
interface World
{
    public function recall();
}


class Demo extends Strong implements World
{
    public static $modern = ['cafe', 'vanille', 'cerise'];
    private $hello = ['hi', 'hey', ['holo', 'hihi', 'jhg' => ['kku' => 'da', 'ojo' => 'fez', 'ihu' => ['kok' => 'gteg', 'ojij' => 'bgb', 'okijfr' => ['eroigjre' => 'pojer', 'efi' => 'ezf', 'oiezjffij' => ['fefijfe' => 'erih','ihefz'=>'oerg', 'iuhfze' => ['epojge' => 'reger', 'ofiegjre' => 'eorjg', 'reg' => 'greoi', 'zefiuh' => ['egoregi' => 'erogh']]]]]]]];
    public $cake = ['chocolat', 'bananas'];
    protected $fruit = [1.56, true];
    private $collection = [];

    public function recall()
    {
        return $this->recall = $this;
    }

    public function go()
    {
        return $this->go = $this->small();
        /*(function(){

            yield new static;
        })($this);

        new class() {

            public function func()
            {
                return $this->obj = new stdClass;

            }
        };*/
    }

    public function call()
    {
        return $this->collection = $this->small();
    }

    public function yolo()
    {
        return $this->call()->recall()->go();
    }

    public static function keppo()
    {
        return static::$modern;
    }
}
$inst = new Dumper();

$test = (new Demo)->yolo();
$dump = $inst->toHtml($test);

?>

<html>

<body>

<div style="width:100%;height:5%">
</div>

<?php echo($inst->html); ?>

<div style="width:100%;height:5%">
</div>

</body>

</html>
