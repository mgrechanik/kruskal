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
use InvalidArgumentException;

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
        //$tree = $kruskal->getMinimumSpanningTree();
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
    
    /*
    public function testCreating() {
        $manager = new Manager();
        $finder = $manager->getFinder();
        $this->assertInstanceOf(AFinder::class, $finder);
        $this->assertInstanceOf(MathematicsInterface::class, $this->getPropertyValue( $manager, 'mathematics' ));
        $this->assertInstanceOf(DistanceInterface::class, $this->getPropertyValue( $manager, 'distanceStrategy' ));
        $this->assertInstanceOf(Task::class, $this->getPropertyValue( $manager, 'task' ));
        
        $this->assertNotNull($finder->getTask());
        $this->assertNotNull($finder->getMathematics());
        $this->assertEmpty($manager->getInnerPath());
    }
    
    public function testGetNamedFromIndexedPath() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->assertSame(['K', 'S'], $manager->getNamedFromIndexedPath([0, 1]));
    }
    
    public function testGetNamedFromIndexedPathWrong() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->expectException(LogicException::class);
        $manager->getNamedFromIndexedPath([0, 2]);
    }    
    
    public function testGetIndexedFromNamedPathh() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->assertSame([0, 1],$manager->getIndexedFromNamedPath(['K', 'S']));
    }
    
    public function testGetIndexedFromNamedPathWrong() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->expectException(LogicException::class);
        $manager->getIndexedFromNamedPath(['L']);
    }     
    
    public function testSetCitiesKeys() {
        $cities = [10 => new City(1,1, 'K'), new City(1,1, 'S')];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $cities = $manager->getCities();
        $this->assertSame([0, 1], array_keys($cities));
        $this->assertSame(['K' => 0, 'S' => 1], $this->getPropertyValue( $manager, 'nameIndex' ));
    }
    
    public function testSetCitiesNoNames() {
        $cities = [new City(1,1), new City(1,1)];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->assertSame(['0' => 0, '1' => 1], $this->getPropertyValue( $manager, 'nameIndex' ));
    }    
    
    public function testSetCitiesWrongNames() {
        $cities = [new City(1,1, 'K'), new City(1,1, 'K')];
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $manager->setCities(...$cities);
    }  
    
    public function testBuildMatrixFromCities() {
        $cities = [new City(1,1), new City(6,13)];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $matrix = $manager->getMatrix();
        $this->assertArrayHasKey(0, $matrix);
        $this->assertArrayHasKey(1, $matrix);

        $this->assertArrayHasKey(0, $matrix[0]);
        $this->assertArrayHasKey(1, $matrix[0]);        
        
        $this->assertArrayHasKey(0, $matrix[1]);
        $this->assertArrayHasKey(1, $matrix[1]);  
        
        $this->assertEquals(13, $matrix[0][1]);
        $this->assertEquals(13, $matrix[1][0]);
        
        $this->assertEquals(0, $matrix[0][0]);
        $this->assertEquals(0, $matrix[1][1]);
    }
    
    public function testSetMatrixNotEmpty() {
        $cities = [new City(1,1), new City(6,13)];
        $manager = new Manager();
        $manager->setCities(...$cities);
        $this->expectException(LogicException::class);
        $manager->setMatrix([]);
    }
    
    public function testSetMatrixWrongSize() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[1, 2], [1, 2], [1, 2]];
        $manager->setMatrix($matrix);
    }    
    
    public function testSetMatrixWrongType() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[1, []], [1, 2]];
        $manager->setMatrix($matrix);
    }  

    public function testSetMatrixWrongNegative() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[0, 0], [1, 2]];
        $manager->setMatrix($matrix);
    } 
    
    public function testSetMatrixCorrect() {
        $manager = new Manager();
        $matrixStart = [[0, 1], 
                        [1, 0]];
        $manager->setMatrix($matrixStart);
        $matrix = $manager->getMatrix();
        $this->assertEquals($matrixStart, $matrix);
        $this->assertSame(['0' => 0, '1' => 1], $this->getPropertyValue( $manager, 'nameIndex' ));
        $this->assertSame([0, 1], array_keys($manager->getCities()));
    }    
    
    public function testSetMatrixNameStart() {
        $manager = new Manager();
        $matrixStart = [[0, 1], 
                        [1, 0]];
        $manager->setMatrix($matrixStart, 5);
        $this->assertSame(['5' => 0, '6' => 1], $this->getPropertyValue( $manager, 'nameIndex' ));
    } 
    
    public function testUpdateMatrixWrongKeysX() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(0, 2, 4);
    }    
    
    public function testUpdateMatrixWrongKeysY() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(2, 0, 4);
    }     
    
    public function testUpdateMatrixWrongValue() {
        $manager = new Manager();
        $this->expectException(InvalidArgumentException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(0, 1, 0);
    }      
    
    public function testUpdateMatrixCorrect() {
        $manager = new Manager();
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(0, 1, 5);
        $matrix = $manager->getMatrix();
        $this->assertEquals(5, $matrix[0][1]);
        $this->assertEquals(5, $matrix[1][0]);
    }     
    
    public function testUpdateMatrixCorrectNoDouble() {
        $manager = new Manager();
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->updateMatrix(0, 1, 5, false);
        $matrix = $manager->getMatrix();
        $this->assertEquals(5, $matrix[0][1]);
        $this->assertEquals(1, $matrix[1][0]);
    }  
    
    public function testCountPathWrongPath() {
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->countPath([1]);
    }     
    
    public function testCountPathNoMatrixh() {
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $manager->countPath([1, 2]);
    }  
    
    public function testCountPathWrongElements() {
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $matrix = [[0, 1], [1, 0]];
        $manager->setMatrix($matrix);
        $manager->countPath([0, 2]);
    }        
    
    public function testCountPathCorrect() {
        $manager = new Manager();
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix);
        $this->assertEquals(27, $manager->countPath([0, 1, 0, 1]));
        $this->assertEquals(0, $manager->countPath([0, 0]));
    }      
    
    public function testCountPathCorrectIndexed() {
        $manager = new Manager();
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix, 100);
        $this->assertEquals(27, $manager->countPath(['100', '101', '100', '101'], true));
        $this->assertEquals(27, $manager->countPath([100, 101, 100, 101], true));
    }     
    
    public function testRunEmptyMatrix() {
        $manager = new Manager();
        $this->expectException(LogicException::class);
        $manager->run(1);
    }     
    
    public function testRunFinderResult() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('run')->willReturn('wrong');
        $manager = new Manager(finder : $finder);
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix);

        $this->assertNull($manager->run(1));
    }    
    
    public function testRunFinderNotFound() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('run')->willReturn([
            'path' => [],
            'distance' => 1000
        ]);
        $manager = new Manager(finder : $finder);
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix);
        $manager->run(1);
        
        $this->assertNull($manager->getDistance());
        $this->assertSame([], $manager->getInnerPath());
    }  
    
    public function testRunFinderFound() {
        $finder = $this->createStub(AFinder::class);
        $finder->method('run')->willReturn([
            'path' => [0, 1],
            'distance' => 9
        ]);
        $manager = new Manager(finder : $finder);
        $matrix = [[0, 9], [9, 0]];
        $manager->setMatrix($matrix, 10);
        $distance = $manager->run(1);
        
        $this->assertEquals(9, $distance);
        $this->assertEquals(9, $manager->getDistance());
        $this->assertSame([0, 1], $manager->getInnerPath());
        $this->assertEquals([10, 11], $manager->getNamedPath());
    }   
     * 
     */ 
}