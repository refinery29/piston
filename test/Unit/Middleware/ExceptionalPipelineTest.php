<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Test\Unit\Middleware;

use InvalidArgumentException;
use League\Pipeline\PipelineInterface;
use League\Pipeline\StageInterface;
use Refinery29\Piston\Middleware\ExceptionalPipeline;
use Refinery29\Test\Util\Faker\GeneratorTrait;

class ExceptionalPipelineTest extends \PHPUnit_Framework_TestCase
{
    use GeneratorTrait;

    public function testImplementsPipelineInterface()
    {
        $pipeline = new ExceptionalPipeline();

        $this->assertInstanceOf(PipelineInterface::class, $pipeline);
    }

    public function testConstructorRejectsInvalidStages()
    {
        $this->expectException(InvalidArgumentException::class);

        $stages = [
            $this->getStageMock(),
            $this->getStageMock(),
            new \stdClass(),
        ];

        new ExceptionalPipeline($stages);
    }

    public function testConstructorSetsStages()
    {
        $stages = [
            $this->getStageMock(),
            $this->getStageMock(),
        ];

        $pipeline = new ExceptionalPipeline($stages);

        $reflectionProperty = new \ReflectionProperty(ExceptionalPipeline::class, 'stages');
        $reflectionProperty->setAccessible(true);

        $this->assertSame($stages, $reflectionProperty->getValue($pipeline));
    }

    public function testPipeReturnsNewInstanceAndAddsStage()
    {
        $pipeline = new ExceptionalPipeline();

        $stage = $this->getStageMock();

        $new = $pipeline->pipe($stage);

        $this->assertInstanceOf(ExceptionalPipeline::class, $new);
        $this->assertNotSame($pipeline, $new);

        $reflectionProperty = new \ReflectionProperty(ExceptionalPipeline::class, 'stages');
        $reflectionProperty->setAccessible(true);

        $stages = $reflectionProperty->getValue($new);

        $this->assertCount(1, $stages);
        $this->assertSame($stage, array_shift($stages));
    }

    public function testProcessPipesPayloadAndReturnsResult()
    {
        $faker = $this->getFaker();

        $payload = $faker->unique()->word;

        $resultOne = $faker->unique()->word;
        $resultTwo = $faker->unique()->word;

        $stageOne = $this->getStageMock();

        $stageOne
            ->expects($this->once())
            ->method('process')
            ->with($this->identicalTo($payload))
            ->willReturn($resultOne)
        ;

        $stageTwo = $this->getStageMock();

        $stageTwo
            ->expects($this->once())
            ->method('process')
            ->with($this->identicalTo($resultOne))
            ->willReturn($resultTwo)
        ;

        $pipeline = new ExceptionalPipeline([
            $stageOne,
            $stageTwo,
        ]);

        $this->assertSame($resultTwo, $pipeline->process($payload));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|StageInterface
     */
    private function getStageMock()
    {
        return $this->getMockBuilder(StageInterface::class)->getMock();
    }
}
