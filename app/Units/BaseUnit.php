<?php namespace App\Units;

use App\Libraries\Outcome;
use App\Libraries\Schedule;

abstract class BaseUnit
{
	/**
	 * Default data for this instance.
	 *
	 * @var object
	 */
	protected $default;

	/**
	 * Current data for this instance.
	 *
	 * @var object
	 */
	protected $current;

	/**
	 * The Schedule to use for this hero's actions.
	 *
	 * @var Queue
	 */
	private $schedule;
	
	// Lazy load data from the source
	abstract protected function ensureData();

	/**
	 * Assign a schedule.
	 *
	 * @param Schedule $schedule  The schedule to use for this unit's actions
	 *
	 * @return $this
	 */
	public function setSchedule(Schedule &$schedule): self
	{
		$this->schedule = $schedule;
		
		return $this;
	}

	/**
	 * Load (if necessary) and return this unit's schedule.
	 *
	 * @return Schedule
	 */
	public function schedule(): Schedule
	{
		if ($this->schedule === null)
		{
			$this->schedule = \Config\Services::schedule(true);
		}
		
		return $this->schedule;
	}

	/**
	 * Locate the latest patch file with validate data.
	 *
	 * @param string $file  Glob of the file to locate
	 *
	 * @return string  Path to the latest patch matching $file
	 */
    public function getPath($file): string
    {
    	if (! is_dir(HEROES_DATA_PATH))
    	{
    		throw new RuntimeException('Unable to locate Heroes data directory! Did you run "composer install"?');
    	}

		$files = glob(HEROES_DATA_PATH  . '*/data/' . $file);

    	if (! is_array($files))
    	{
    		throw new RuntimeException('Unable to locate the data file! Something is wrong with your data directory.');
    	}
    	
    	return end($files);
    }

	/**
	 * Resets data to their defaults.
	 *
	 * @return $this
	 */
    public function reset(): self
    {
    	$this->current = clone $this->default;

    	return $this;
    }

	/**
	 * Format and return an Outcome
	 *
	 * @param mixed $data  The data generated by the action
	 * @param bool $keep   Whether this outcome believes it should be recorded
	 *
	 * @return Outcome
	 */
	protected function outcome($data, $keep = null): Outcome
	{
		return new Outcome($this->schedule()->timestamp(), $this, $data, $keep);
	}

	/**
	 * Load (if necessary) and return a value from the data set.
	 *
	 * @param string $name  Name of the key to look for
	 *
	 * @return mixed  Value from the data set
	 */
    public function &__get(string $name)
    {
    	$this->ensureData();
    	
    	return $this->current->$name;
    }

	/**
	 * Complimentary property checker to __isset()
	 *
	 * @param string $name  Name of the key to look for
	 *
	 * @return bool  Whether the property exists
	 */
	public function __isset(string $name): bool
	{
    	$this->ensureData();

		return isset($this->current->$name);
	}

	/**
	 * Update a value in current data.
	 *
	 * @param string $name   Name of the key to change
	 * @param string $value  New value for $name
	 *
	 * @return $this
	 */
    public function __set(string $name, $value): self
    {
    	$this->ensureData();
    	
    	$this->current->$name = $value;
    	
    	return $this;
    }
}
