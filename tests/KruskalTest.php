<?php
/**
 * This file is part of the mgrechanik/kruskal library
 *
 * @copyright Copyright (c) Mikhail Grechanik <mike.grechanik@gmail.com>
 * @license https://github.com/mgrechanik/kruskal/blob/main/LICENSE.md
 * @link https://github.com/mgrechanik/kruskal
 */

declare(strict_types=1);

namespace mgrechanik\kruskal\tests;

use mgrechanik\kruskal\Kruskal;
use Yoast\PHPUnitPolyfills\Helpers\AssertAttributeHelper;
use LogicException;

class KruskalTest extends \PHPUnit\Framework\TestCase
{
    use AssertAttributeHelper;
    
    public function testCreatingSmallMatrix() {
        $matrix = [1];
        $this->expectException(LogicException::class);
        $kruskal = new Kruskal($matrix);
    }     
    
    public function testCreatingWrongMatrixFormat() {
        $matrix = [1 => [1,1], 2 => [1,1]];
        $this->expectException(LogicException::class);
        $kruskal = new Kruskal($matrix);
    } 
    
    public function testCreatingWrongMatrixFormat2() {
        $matrix = [[1,1],[1=>1,2=>2]];
        $this->expectException(LogicException::class);
        $kruskal = new Kruskal($matrix);
    }     
    
    public function testCreatingCorrect() {
        $matrix = $this->getMatrix();
        $kruskal = new Kruskal($matrix);
        $this->assertNotNull($kruskal);
    }
    
    public function testOrder() {
        $matrix = $this->getMatrix();
        $kruskal = new Kruskal($matrix);
        $order = $kruskal->buildAscendingOrder();
        $this->assertCount(6, $order);
        $this->assertEquals(157, $order[0]['distance']);
        $this->assertEquals(184, $order[1]['distance']);
        $this->assertEquals(335, $order[5]['distance']);
    }    
    
    public function testOrderWhenWeHaveEquals() {
        $matrix = $this->getMatrix();
        $matrix[2][3] = 184;
        $matrix[3][2] = 184;
        $kruskal = new Kruskal($matrix);
        $order = $kruskal->buildAscendingOrder();
        $this->assertCount(6, $order);
    }     
    
    public function testRunDistance() {
        $matrix = $this->getMatrix();
        $kruskal = new Kruskal($matrix);
        $this->assertNull($kruskal->getDistance());
        $run = $kruskal->run();
        $this->assertTrue($run);
        $this->assertEquals(600, $kruskal->getDistance());
        $this->assertEquals(600, $kruskal->getDistance());
    }
    
    public function testRunTree() {
        $matrix = $this->getMatrix();
        $kruskal = new Kruskal($matrix);
        $kruskal->run();
        $tree = $kruskal->getMinimumSpanningTree();
        $this->assertCount(3, $tree);
        $this->assertEquals(0, $tree[0][0]);
        $this->assertEquals(2, $tree[0][1]);
        $this->assertEquals(2, $tree[1][0]);
        $this->assertEquals(3, $tree[1][1]);
        $this->assertEquals(1, $tree[2][0]);
        $this->assertEquals(3, $tree[2][1]);
    }    
    
    public function testRunSmallerTree() {
        $matrix = $this->getMatrixSmaller();
        $kruskal = new Kruskal($matrix);
        $kruskal->run();
        $tree = $kruskal->getMinimumSpanningTree();
        $this->assertCount(2, $tree);
    }     


    protected function getMatrix() {
        return [
            [ 0 , 263, 184, 335],
            [263,  0 , 287, 157],
            [184, 287,  0 , 259],
            [335, 157, 259,  0]
        ];
    }   
    
    protected function getMatrixSmaller() {
        return [
            [ 0 , 263, 184],
            [263,  0 , 287],
            [184, 287,  0 ]
        ];
    }     
    
}