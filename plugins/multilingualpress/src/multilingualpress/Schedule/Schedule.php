<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Schedule;

use Inpsyde\MultilingualPress\Schedule\Delay\OneSecondEveryGivenSteps;

/**
 * Class Schedule
 * @package Inpsyde\MultilingualPress\Schedule
 */
class Schedule
{
    const STARTED = 'started';
    const RUNNING = 'running';
    const DONE = 'done';
    const TIMEZONE = 'UTC';

    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTimeInterface
     */
    private $started;

    /**
     * @var int
     */
    private $allSteps;

    /**
     * @var int
     */
    private $stepsDone;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \DateTimeInterface|null
     */
    private $lastUpdate;

    /**
     * @var \DateTimeInterface|null
     */
    private $estimated;

    /**
     * @var Delay\Delay
     */
    private $delay = null;

    /**
     * @var array
     */
    private $args;

    /**
     * Create a new multi-step schedule.
     *
     * @param int $steps
     * @param Delay\Delay|null $delay
     * @param array $args
     * @return Schedule
     */
    public static function newMultiStepInstance(
        int $steps,
        Delay\Delay $delay = null,
        array $args = []
    ): Schedule {

        $instance = new static(
            wp_generate_uuid4(),
            new \DateTimeImmutable('now', new \DateTimeZone(self::TIMEZONE)),
            $args,
            $steps
        );

        $instance->delay = $delay ?? OneSecondEveryGivenSteps::default();

        return $instance;
    }

    /**
     * Create a new single-step schedule.
     *
     * @return Schedule
     */
    public static function newMonoStepInstance(): Schedule
    {
        return static::newMultiStepInstance(1);
    }

    /**
     * @param array $data
     * @return Schedule
     * @throws \RuntimeException
     */
    public static function fromArray(array $data): Schedule
    {
        $id = $data['id'] ?? null;
        if (!$id || !\is_string($id)) {
            throw new \RuntimeException('Invalid schedule data.');
        }

        $args = $data['args'] ?? [];
        $stepsDone = $data['stepsDone'] ?? null;
        $allSteps = $data['allSteps'] ?? null;
        $startedTs = $data['started'] ?? null;
        $lastUpdateTs = $data['lastUpdate'] ?? null;

        if (
            !$allSteps
            || !$startedTs
            || !is_numeric($stepsDone)
            || !is_numeric($allSteps)
            || !is_numeric($startedTs)
            || ($lastUpdateTs && !is_numeric($lastUpdateTs))
        ) {
            throw new \RuntimeException("Invalid schedule data for {$id}.");
        }

        $timezone = new \DateTimeZone(self::TIMEZONE);

        $started = (new \DateTimeImmutable('now', $timezone))->setTimestamp($startedTs);
        if (!$started) {
            throw new \RuntimeException("Invalid schedule data for {$id}.");
        }

        $lastUpdate = $lastUpdateTs ? new \DateTimeImmutable('now', $timezone) : null;
        $lastUpdate and $lastUpdate = $lastUpdate->setTimestamp($lastUpdateTs);

        $instance = new static(
            $id,
            $started,
            $args,
            (int)$allSteps,
            (int)$stepsDone
        );

        $lastUpdate and $instance->lastUpdate = $lastUpdate;

        return $instance;
    }

    /**
     * @param string $id
     * @param \DateTimeInterface $started
     * @param array $args
     * @param int $steps
     * @param int $stepsDone
     */
    private function __construct(
        string $id,
        \DateTimeInterface $started,
        array $args = [],
        int $steps = 1,
        int $stepsDone = 0
    ) {

        $this->id = $id;
        $this->started = $started;
        $this->allSteps = $steps;
        $this->stepsDone = $stepsDone;
        $this->args = $args;

        switch (true) {
            case ($this->stepsDone === 0):
                $this->status = self::STARTED;
                break;
            case ($this->allSteps > $this->stepsDone):
                $this->status = self::RUNNING;
                break;
            default:
                $this->status = self::DONE;
        }
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return \DateTimeInterface
     */
    public function startedOn(): \DateTimeInterface
    {
        return $this->started;
    }

    /**
     * @return \DateTimeInterface
     */
    public function lastUpdate(): \DateTimeInterface
    {
        return $this->lastUpdate ?? $this->startedOn();
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function estimatedFinishTime()
    {
        if ($this->estimated) {
            return $this->estimated;
        }

        if (!$this->stepsDone || !$this->lastUpdate || !$this->isMultiStep() || $this->isDone()) {
            return null;
        }

        $stepsToDo = $this->stepToFinish();
        if ($stepsToDo < 1) {
            return null;
        }

        $timeStarted = $this->startedOn();
        $timeElapsed = $this->lastUpdate->getTimestamp() - $timeStarted->getTimestamp();
        if ($timeElapsed <= 0) {
            return null;
        }

        $mediumTimeForOneStep = (int)round($timeElapsed / $this->stepsDone);

        try {
            $now = new \DateTimeImmutable('now', new \DateTimeZone(self::TIMEZONE));
            $estimated = $now->setTimestamp(time() + ($mediumTimeForOneStep * $stepsToDo));
            if (!$estimated) {
                return null;
            }
        } catch (\Throwable $exception) {
            return null;
        }

        $this->estimated = $estimated;

        return $this->estimated;
    }

    /**
     * @return string
     */
    public function estimatedRemainingTime(): string
    {
        if ($this->isDone()) {
            return '';
        }

        $estimated = $this->estimatedFinishTime();
        if (!$estimated) {
            return __('Unknown', 'multilingualpress');
        }

        $timestamp = $estimated->getTimestamp();
        $now = time();
        if (($timestamp - $now) < MINUTE_IN_SECONDS) {
            return __('Less than one minute', 'multilingualpress');
        }

        return human_time_diff($now, $timestamp);
    }

    /**
     * @return bool
     */
    public function isMultiStep(): bool
    {
        return $this->allSteps > 1;
    }

    /**
     * @return int
     */
    public function stepToFinish(): int
    {
        if ($this->isDone()) {
            return 0;
        }

        return $this->allSteps - $this->stepsDone;
    }

    /**
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->status === self::DONE;
    }

    /**
     * Force schedule to done status
     *
     * @return Schedule
     */
    public function done(): Schedule
    {
        if (!$this->isDone()) {
            $this->stepsDone = $this->allSteps;
            $this->status = self::DONE;
            $this->lastUpdate = new \DateTimeImmutable('now', new \DateTimeZone(self::TIMEZONE));
            $this->estimated = null;
        }

        return $this;
    }

    /**
     * @return Schedule
     */
    public function nextStep(): Schedule
    {
        if (!$this->isDone()) {
            $done = min($this->stepsDone + 1, $this->allSteps);
            $this->stepsDone = $done;
            $this->status = $done === $this->allSteps ? self::DONE : self::RUNNING;
            $this->lastUpdate = new \DateTimeImmutable('now', new \DateTimeZone(self::TIMEZONE));
            $this->estimated = null;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'stepsDone' => $this->stepsDone,
            'allSteps' => $this->allSteps,
            'started' => $this->startedOn()->getTimestamp(),
            'lastUpdate' => $this->lastUpdate ? $this->lastUpdate->getTimestamp() : null,
            'args' => $this->args,
        ];
    }

    /**
     * @return Delay\Delay
     */
    public function delay(): Delay\Delay
    {
        $this->delay or $this->delay = new Delay\Zero();

        return $this->delay;
    }
}
