<?php

namespace Dumper;

return [
    //Overwrite default colors. Empty string = none.
    //Orange : #ff7736. type = #f22613
    'color' =>
        [
         'property' =>
            [
              'public' => '#0074D9',
              'private' => '#FF4136',
              'protected' => '#B10DC9'
            ],
         'background' => '#000000',
         'type' => '#FFFFFF',
         'key' => '#ff7736',
         'value' => '#2ECC40',
         'arrow' => '#FFFFFF',
         'bracket' => '#FFFFFF'
        ],
    //font : weight size/height family
    'font' =>
        [
         'weight' => '500',
         'size' => 'small',
         'height' => '1.0',
         'family' => 'Sans-Serif'
        ],
    //true : <span></span> | false : <pre></pre>
    'tag' => true,
    //space value | handle que html => todo : pre. Convertir ' ' = &nbsp...
    'indent' => '&emsp;&emsp;'
];
