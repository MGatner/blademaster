<?php namespace App\Libraries;

use App\Units\BaseUnit;
use App\Libraries\Outcome;

/**
 * Class Action
 *
 * An action for a unit to enact.
 */
class Action
{
	/**
	 * The unit performing the action.
	 *
	 * @var Unit
	 */
	public $unit;

	/**
	 * The actual method to call.
	 *
	 * @var callback
	 */
	protected $callback;

	/**
	 * Save the parameters.
	 *
	 * @param Unit $unit  The unit issuing this action
	 * @param $callback   The callback to perform
	 */
	public function __construct(BaseUnit $unit, $callback)
	{
		$this->unit     = $unit;
		$this->callback = $callback;
	}

	/**
	 * Run the callback and return the results.
	 *
	 * @return mixed
	 */
	public function run()
	{
		return call_user_func($this->callback);
	}
}
