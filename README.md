# Kruskal's algorithm to find a minimum spanning tree

## Demo <span id="demo"></span>

Building a minimum spanning tree on this graph:
![Using this ACO library to solve the travelling salesman problem](https://raw.githubusercontent.com/mgrechanik/kruskal/main/docs/dots.jpg "Using this ACO library to solve the travelling salesman problem")

## Installing <span id="installing"></span>

#### Installing through composer::

The preferred way to install this library is through composer.

Either run
```
composer require --prefer-dist mgrechanik/kruskal
```

or add
```
"mgrechanik/kruskal" : "~1.0.0"
```
to the require section of your `composer.json`.

## How to use  <span id="use"></span> 

Run the next code:
```php
use mgrechanik\kruskal\Kruskal;

$matrix = [
    [ 0 , 263, 184, 335],
    [263,  0 , 287, 157],
    [184, 287,  0 , 259],
    [335, 157, 259,  0]
];

if ($kruskal->run()) {
    // 1)
    var_dump($kruskal->getMinimumSpanningTree());
    // 2)
    var_dump($kruskal->getDistance());
}
```
We will get:

1) Spanning tree as an array of edges
```
Array
(
    [0] => Array
        (
            [0] => 0
            [1] => 2
        )

    [1] => Array
        (
            [0] => 2
            [1] => 3
        )

    [2] => Array
        (
            [0] => 1
            [1] => 3
        )

)
```

2) Distance of all tree

```
600
```