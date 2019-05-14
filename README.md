You can test it with Demo.php <br />

Autoload.php is used to run all classes in this directory if you dont have composer. <br />

Otherwise, you can delete autoload.php. <br />

Example : <br />

<pre>
  require($yourPathToAutoload.'/autoload.php');
  use Dumper/Dumper;
  
  $dumperInstance = new Dumper();
  $arr = ['test' => 'cool'];
  
  $dumperInstance->load($arr);
  // if you want to clear the dumper you can pass a boolean, by default it will not clear previous variables dumps.
  $dumperInstance->load($arr, true);
</pre>

Demo.php file : <br />

<p align="center">

  <img src="/DemoImg/DumperDemo.png" alt="demo image">

  <img src="/DemoImg/DumperDemo2.png" alt="demo image">
</p>
