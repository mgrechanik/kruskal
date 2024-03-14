<?php

/**
 * This file is part of the mgrechanik/kruskal library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/kruskal/blob/main/LICENSE.md
 * @link https://github.com/mgrechanik/kruskal
 */
declare(strict_types=1);

namespace mgrechanik\kruskal;

/**
 * Kruskal's algorithm implementation
 * 
 * It's use:
 * ```
 * $matrix = [[100, 100],[100, 100]];
 * $kruskal = new Kruskal($matrix);
 * if ($kruskal->run()) {
 *     var_dump($kruskal->getMinimumSpanningTree());
 *     var_dump($kruskal->getDistance());
 * }
 * ```
 *
 * @author Mikhail Grechanik <mike.grechanik@gmail.com>
 * @since 1.0.1 
 */
class Kruskal
{
    /**
     * @var array Adjacency matrix. It is a source data representing our graph
     */
    protected array $matrix;
    
    /**
     * @var array Trees we build in a graph
     */
    protected array $trees = [];

    /**
     * @var array Index of node's belonging to a tree 
     */
    protected array $nodeIndex = [];
    
    /**
     * @var array Resulting tree
     */
    protected array $_res_tree = [];
    
    /**
     * @var null|numeric Resulting distance 
     */
    protected $_res_distance = null;
    
    /**
     * Constructor 
     * 
     * @param array $matrix
     * @throws \InvalidArgumentException
     */
    public function __construct(array $matrix) {
        $count = count($matrix);
        if ($count < 2) {
            throw new \InvalidArgumentException('Matrix format is wrong');
        }
        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < $count; $j++) {
                if (!isset($matrix[$i][$j]) || !is_scalar($matrix[$i][$j])) {
                    throw new \InvalidArgumentException('Matrix format is wrong');
                }                
            }
        }
        $this->matrix = $matrix;
    }
    
    /**
     * Running the building a tree
     * 
     * @return bool
     */
    public function run() : bool {
        $order = $this->buildAscendingOrder();
        return $this->buildTree($order);
    }    
    
    /**
     * Get the minimum spanning tree we found
     * 
     * @return array
     */
    public function getMinimumSpanningTree() : array {
        return $this->_res_tree;
    }
    
    /**
     * Get the distance of the minimum spanning tree we found
     * 
     * @return numeric|null
     */
    public function getDistance() {
        if (!is_null($this->_res_distance)) {
            return $this->_res_distance;
        }
        $d = null;
        if ($tree = $this->getMinimumSpanningTree()) {
            $d = 0;
            foreach ($tree as $path) {
                $d+= $this->matrix[$path[0]][$path[1]];
            }
        } 
        return $this->_res_distance = $d;
    }

    /**
     * Building a tree
     * 
     * @param array $order
     * @return bool Whether we are left with one tree and all nodes are in it
     */
    protected function buildTree($order) : bool {
        $found = false;
        foreach ($order as $val) {
            $nodes = $val['nodes'];
            $this->processNodes($nodes[0], $nodes[1]);
            if ($this->getIsTreeReady()) {
                $found = true;
                $tree = array_pop($this->trees);
                $this->_res_tree = $tree['paths'];
                break;
            }
        }
        return $found;
    }
    
    /**
     * Put two nodes into trees
     * 
     * @param int $node1
     * @param int $node2
     */
    protected function processNodes(int $node1, int $node2) {
        $node1Exists = isset($this->nodeIndex[$node1]);
        $node2Exists = isset($this->nodeIndex[$node2]);
        if (!$node1Exists && !$node2Exists) {
            // Creating a new tree from two free nodes
            $key = uniqid();
            $this->nodeIndex[$node1] = $key;
            $this->nodeIndex[$node2] = $key;
            $this->trees[$key] = [];
            $this->trees[$key]['all'] = [$node1, $node2];
            $this->trees[$key]['paths'] = [[$node1, $node2]];
        } elseif ($node1Exists && $node2Exists) {
            $key1 = $this->nodeIndex[$node1];
            $key2 = $this->nodeIndex[$node2];
            if ($key1 != $key2) {
                // join two different trees
                $this->trees[$key1]['all'][] = $node2;
                $this->trees[$key1]['paths'][] = [$node1, $node2];
                $this->trees[$key1]['all'] = array_merge($this->trees[$key1]['all'], $this->trees[$key2]['all']);
                $this->trees[$key1]['paths'] = array_merge($this->trees[$key1]['paths'], $this->trees[$key2]['paths']);
                foreach ( $this->trees[$key2]['all'] as $node) {
                    $this->nodeIndex[$node] = $key1;
                }
                unset($this->trees[$key2]);
            }
        } else {
            // Join free node to existing tree
            $key = $node1Exists ? $this->nodeIndex[$node1] : $this->nodeIndex[$node2];
            $nodeAdd = $node1Exists ? $node2 : $node1;
            $nodeExists = $node1Exists ? $node1 : $node2;
            $this->joinFreeNodeToTree($nodeAdd, $nodeExists, $key);            
        }
    }
    
    /**
     * Join free node to existing tree
     * 
     * @param int $node1 Node to join
     * @param int $node2
     * @param string $key Key of the tree
     */
    protected function joinFreeNodeToTree(int $node1, int $node2, string $key) {
            $this->nodeIndex[$node1] = $key;
            $this->trees[$key]['all'][] = $node1;
            $this->trees[$key]['paths'][] = [$node2, $node1];        
    }
    
    /**
     * Get matrix into assending order of edges by their paths distances
     * 
     * @return array
     */
    public function buildAscendingOrder(): array {
        $matrix = $this->matrix;
        $count = count($matrix);
        $res = [];
        $found = [];
        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < $count; $j++) {
                if ($i == $j) {
                    continue;
                }
                $key = $this->getCombinedKey($i, $j);
                if (isset($found[$key])) {
                    continue;
                }  
                $found[$key] = true;
                $distance = $matrix[$i][$j];
                $res[] = [
                    'distance' => $distance,
                    'nodes' => $i < $j ? [$i, $j] : [$j, $i]
                ];    
            }            
        }
        usort($res, [$this, 'compare']);
        return $res;        
    }
    
    /**
     * Combined key
     * 
     * @param int $i
     * @param int $j
     * @return string
     */
    protected function getCombinedKey(int $i, int $j) {
        return $i < $j ? $i . '_' . $j : $j . '_' . $i;
    }
    
    /**
     * Comparing elements to create an order
     * 
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    protected function compare(mixed $a, mixed $b) : int {
        $a = $a['distance'];
        $b = $b['distance'];
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;        
    }
    
    /**
     * Get whether we are left with one tree and all nodes are in it
     * 
     * @return bool
     */
    protected function getIsTreeReady(): bool {
        return (count($this->trees) == 1) && (count($this->nodeIndex) == count($this->matrix));
    }

}
